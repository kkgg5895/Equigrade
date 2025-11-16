<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'teacher') {
    flash('error', 'You must be logged in as a teacher.');
    header('Location: login.php');
    exit;
}

global $pdo; 
$userId = (int) $_SESSION['user_id'];

$q       = trim($_GET['q'] ?? '');
$courseF = trim($_GET['course'] ?? '');
$rubricF = trim($_GET['rubric_id'] ?? '');
$gradedF = trim($_GET['graded'] ?? 'all'); 

$courseRows = $pdo->query("
    SELECT DISTINCT course 
    FROM submissions 
    WHERE course IS NOT NULL AND course <> '' 
    ORDER BY course
")->fetchAll(PDO::FETCH_COLUMN);

$rubricStmt = $pdo->prepare("
    SELECT r.rubric_id,
           COALESCE(NULLIF(r.rubric_title,''), NULLIF(r.title,''), 'Untitled Rubric') AS title,
           COALESCE(r.course, '') AS course,
           r.created_at
    FROM rubrics r
    WHERE r.created_by = :uid
    ORDER BY r.created_at DESC
");
$rubricStmt->execute([':uid' => $userId]);
$rubricRows = $rubricStmt->fetchAll(PDO::FETCH_ASSOC);

$sql = "
SELECT
    s.submission_id,
    s.assignment_title,
    s.course,
    s.filename,
    s.submitted_at,
    u.full_name AS student_name,
    g.ai_score,
    g.confidence,
    g.feedback,
    g.final_score,
    g.rubric_id,
    COALESCE(NULLIF(r.rubric_title,''), NULLIF(r.title,''), '—') AS rubric_title
FROM submissions s
JOIN users u         ON u.user_id = s.user_id
LEFT JOIN grades g   ON g.submission_id = s.submission_id
LEFT JOIN rubrics r  ON r.rubric_id = g.rubric_id
WHERE 1=1
";

$params = [];

if ($q !== '') {
    $sql .= " AND (s.assignment_title LIKE :q OR s.filename LIKE :q OR u.full_name LIKE :q)";
    $params[':q'] = "%{$q}%";
}
if ($courseF !== '') {
    $sql .= " AND s.course = :course";
    $params[':course'] = $courseF;
}
if ($rubricF !== '') {
    $sql .= " AND g.rubric_id = :rubric";
    $params[':rubric'] = $rubricF;
}
if ($gradedF === 'graded') {
    $sql .= " AND g.final_score IS NOT NULL";
} elseif ($gradedF === 'ungraded') {
    $sql .= " AND g.final_score IS NULL";
}

$sql .= " ORDER BY s.submitted_at DESC LIMIT 400";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database error: ' . htmlspecialchars($e->getMessage()));
}

$gradedCount = 0;
$scoreSum = 0;
foreach ($submissions as $s) {
    if ($s['final_score'] !== null) {
        $gradedCount++;
        $scoreSum += (float) $s['final_score'];
    }
}
$avgScore = $gradedCount > 0 ? round($scoreSum / $gradedCount, 2) : null;
$totalSubmissions = count($submissions);

$myRubrics = $rubricRows;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Teacher Dashboard — EquiGrade</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800">

  <!-- Header -->
  <header class="bg-gradient-to-r from-indigo-700 to-purple-700 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <span class="text-2xl">👩🏻‍🏫</span>
        <h1 class="text-xl md:text-2xl font-semibold">
          Teacher Dashboard — <span class="opacity-90">EquiGrade</span>
        </h1>
      </div>
      <div class="flex items-center gap-3">
        <a href="rubric_create.php" class="bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg px-4 py-2 transition">➕ Create Rubric</a>
        <a href="logout.php" class="bg-rose-500 hover:bg-rose-600 text-white rounded-lg px-4 py-2 transition">Sign Out</a>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-6 md:py-8">
    <?php display_flash(); ?>

    <!-- 🔹 Added Summary Cards -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white p-5 rounded-lg shadow border-t-4 border-indigo-500">
        <p class="text-sm text-slate-600">Total Submissions</p>
        <p class="text-2xl font-bold text-slate-800"><?= $totalSubmissions ?></p>
      </div>
      <div class="bg-white p-5 rounded-lg shadow border-t-4 border-green-500">
        <p class="text-sm text-slate-600">Graded</p>
        <p class="text-2xl font-bold text-slate-800"><?= $gradedCount ?></p>
      </div>
      <div class="bg-white p-5 rounded-lg shadow border-t-4 border-amber-500">
        <p class="text-sm text-slate-600">Average Score</p>
        <p class="text-2xl font-bold text-slate-800"><?= $avgScore ? $avgScore . '%' : 'N/A' ?></p>
      </div>
    </section>

    <!-- Filters -->
    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 md:p-5 mb-6">
      <form method="get" class="grid grid-cols-1 md:grid-cols-12 gap-3 md:gap-4 items-end">
        <div class="md:col-span-4">
          <label class="block text-sm font-medium text-slate-600 mb-1">Search</label>
          <input type="text" name="q" value="<?= htmlspecialchars($q) ?>"
            placeholder="Search by student, file or assignment…"
            class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-medium text-slate-600 mb-1">Course</label>
          <select name="course" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">All Courses</option>
            <?php foreach ($courseRows as $c): ?>
              <option value="<?= htmlspecialchars($c) ?>" <?= $courseF===$c?'selected':'' ?>><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="md:col-span-3">
          <label class="block text-sm font-medium text-slate-600 mb-1">Rubric</label>
          <select name="rubric_id" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="">All Rubrics</option>
            <?php foreach ($rubricRows as $r): ?>
              <option value="<?= (int)$r['rubric_id'] ?>" <?= $rubricF===(string)$r['rubric_id']?'selected':'' ?>>
                <?= htmlspecialchars($r['title']) ?><?= $r['course'] ? ' — ['.htmlspecialchars($r['course']).']':'' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
          <select name="graded" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
            <option value="all"      <?= $gradedF==='all'?'selected':'' ?>>All</option>
            <option value="graded"   <?= $gradedF==='graded'?'selected':'' ?>>Graded</option>
            <option value="ungraded" <?= $gradedF==='ungraded'?'selected':'' ?>>Ungraded</option>
          </select>
        </div>
        <div class="md:col-span-12 flex items-center gap-3">
          <button class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 transition">Apply Filters</button>
          <a href="teacher-dashboard.php" class="text-slate-600 hover:text-slate-800 underline underline-offset-2">Reset</a>
          <!-- Added Refresh -->
          <button type="button" onclick="window.location.reload();" class="bg-gray-200 hover:bg-gray-300 rounded-lg px-3 py-2 ml-2 text-gray-700">🔁 Refresh</button>

          <div class="ml-auto text-sm text-slate-600">
            <span class="inline-flex items-center gap-1 mr-4">📄 <strong><?= $totalSubmissions ?></strong> submissions</span>
            <span class="inline-flex items-center gap-1">✅ graded: <strong><?= $gradedCount ?></strong></span>
            <?php if ($avgScore !== null): ?>
              <span class="inline-flex items-center gap-1 ml-4">📊 avg score: <strong><?= $avgScore ?>%</strong></span>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </section>

    <!-- Submissions Table (unchanged, just hover polish) -->
    <section class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-10 transition-all duration-300">
      <div class="px-4 py-3 border-b bg-slate-50 text-slate-700 font-semibold">Student Submissions</div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50">
            <tr class="text-left text-slate-600">
              <th class="px-4 py-3">Student</th>
              <th class="px-4 py-3">Assignment</th>
              <th class="px-4 py-3">Course</th>
              <th class="px-4 py-3">File</th>
              <th class="px-4 py-3">AI Score</th>
              <th class="px-4 py-3">Confidence</th>
              <th class="px-4 py-3">Rubric</th>
              <th class="px-4 py-3">Final</th>
              <th class="px-4 py-3">Feedback</th>
              <th class="px-4 py-3">Submitted</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <?php if (!$submissions): ?>
              <tr><td colspan="11" class="px-4 py-6 text-slate-500 text-center">No submissions found.</td></tr>
            <?php else: ?>
              <?php foreach ($submissions as $row): ?>
                <?php
                  $ai   = $row['ai_score'] !== null ? round((float)$row['ai_score'], 2) . '%' : '—';
                  $conf = $row['confidence'] !== null ? round((float)$row['confidence'], 2) . '%' : '—';
                  $fin  = $row['final_score'] !== null ? round((float)$row['final_score'], 2) . '%' : '—';
                  $fb   = $row['feedback'] ? mb_strimwidth((string)$row['feedback'], 0, 80, '…') : '—';
                  $rub  = $row['rubric_title'] ?: '—';
                ?>
                <tr class="hover:bg-indigo-50 transition">
                  <td class="px-4 py-3"><?= htmlspecialchars($row['student_name']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['assignment_title'] ?: '—') ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($row['course'] ?: '—') ?></td>
                  <td class="px-4 py-3">
                    <?php if (!empty($row['filename'])): ?>
                      <a class="text-indigo-600 hover:underline" href="<?= htmlspecialchars('/uploads/' . $row['filename']) ?>" target="_blank">View file</a>
                    <?php else: ?>—<?php endif; ?>
                  </td>
                  <td class="px-4 py-3"><?= $ai ?></td>
                  <td class="px-4 py-3"><?= $conf ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($rub) ?></td>
                  <td class="px-4 py-3 font-semibold"><?= $fin ?></td>
                  <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($fb) ?></td>
                  <td class="px-4 py-3 text-slate-600 whitespace-nowrap"><?= htmlspecialchars(date('Y-m-d H:i', strtotime($row['submitted_at']))) ?></td>
                  <td class="px-4 py-3 text-right">
                    <div class="inline-flex gap-2">
                      <a href="review.php?submission_id=<?= (int)$row['submission_id'] ?>" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">Review</a>
                      <a href="override.php?submission_id=<?= (int)$row['submission_id'] ?>" class="px-3 py-1.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition" title="Manually override score">Override</a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Your Rubrics (unchanged) -->
    <section>
      <h2 class="text-lg font-semibold text-slate-800 mb-3 flex items-center gap-2">
        📚 <span>Your Created Rubrics</span>
      </h2>
      <?php if (!$myRubrics): ?>
        <div class="bg-white border border-slate-200 rounded-xl p-6 text-slate-600">
          No rubrics yet. Create one to start grading.
        </div>
      <?php else: ?>
        <div class="grid md:grid-cols-2 gap-5">
          <?php foreach ($myRubrics as $rb): ?>
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 hover:shadow-md transition">
              <div class="text-base font-semibold mb-2">
                <?= htmlspecialchars($rb['title']) ?> <span class="text-slate-500 font-normal"><?= $rb['course'] ? ' ['.htmlspecialchars($rb['course']).']' : ' [No Course]' ?></span>
              </div>
              <div class="text-sm text-slate-600 mb-4">
                Created on <?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($rb['created_at']))) ?>
              </div>
              <div class="text-right">
                <a href="teacher-dashboard.php?rubric_id=<?= (int)$rb['rubric_id'] ?>" class="px-4 py-2 rounded-lg bg-violet-600 text-white hover:bg-violet-700 transition">Review Submissions</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <!-- 🔹 Footer Added -->
  <footer class="text-center py-6 text-slate-400 text-sm">
    © 2025 EquiGrade — FAIR-EVAL AI Grading Assistant
  </footer>

  <!-- 🔹 Scroll to Top -->
  <button onclick="window.scrollTo({top:0, behavior:'smooth'});" class="fixed bottom-6 right-6 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-full shadow-lg transition">
    ⬆ Top
  </button>

</body>
</html>
