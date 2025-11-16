<?php
session_start();

require_once 'config.php';
require_once 'db.php';

// ---- AUTH CHECK ----
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
global $pdo;

// ---- FILTERS ----
$search = trim($_GET['search'] ?? '');
$courseFilter = trim($_GET['course'] ?? '');

// ---- QUERY ----
$sql = "
    SELECT 
        s.submission_id,
        s.assignment_title,
        s.course,
        s.filename,
        s.submitted_at,
        
        g.ai_score,
        g.confidence,
        g.final_score,
        g.feedback
        
    FROM submissions s
    LEFT JOIN grades g 
        ON g.submission_id = s.submission_id
    WHERE s.user_id = :uid
";

$params = [':uid' => $user_id];

if ($courseFilter !== '') {
    $sql .= " AND s.course = :c";
    $params[':c'] = $courseFilter;
}

if ($search !== '') {
    $sql .= " AND (s.assignment_title LIKE :search OR s.filename LIKE :search OR s.course LIKE :search)";
    $params[':search'] = "%$search%";
}

$sql .= " ORDER BY s.submitted_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// FOR COURSE FILTER DROPDOWN
$courses = $pdo->query("SELECT DISTINCT course FROM submissions WHERE user_id = $user_id")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Submission Dashboard — EquiGrade</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800">

<!-- HEADER -->
<header class="bg-indigo-700 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold">
            🎓 Your Submissions — <span class="text-indigo-200">EquiGrade</span>
        </h1>
        
        <div class="flex items-center gap-3">
            <a href="submission.html" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg">
                ➕ Submit Assignment
            </a>
            <a href="view-rubrics.php" class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg">
                📘 View Rubrics
            </a>
            <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg">
                🚪 Logout
            </a>
        </div>
    </div>
</header>

<!-- MAIN -->
<main class="max-w-7xl mx-auto py-8 px-6">

    <!-- FILTER SECTION -->
    <form method="get" class="bg-white p-4 rounded-xl shadow flex flex-col md:flex-row gap-3 mb-6">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
            placeholder="🔍 Search by title or course"
            class="flex-1 border border-gray-300 rounded-lg px-3 py-2" />

        <select name="course" class="border border-gray-300 px-3 py-2 rounded-lg">
            <option value="">All Courses</option>
            <?php foreach ($courses as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $c == $courseFilter ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="bg-indigo-600 text-white px-5 py-2 rounded-lg">Apply</button>
        <a href="submission-dashboard.php" class="bg-gray-200 px-5 py-2 rounded-lg">Reset</a>
    </form>

    <!-- TABLE -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-xl font-bold mb-3">📂 Your Submitted Assignments</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border-t">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Assignment</th>
                        <th class="px-4 py-3 text-left">Course</th>
                        <th class="px-4 py-3 text-left">File</th>
                        <th class="px-4 py-3 text-left">AI Score</th>
                        <th class="px-4 py-3 text-left">Confidence</th>
                        <th class="px-4 py-3 text-left">Final Score</th>
                        <th class="px-4 py-3 text-left">Feedback</th>
                        <th class="px-4 py-3 text-left">Rubric</th>
                        <th class="px-4 py-3 text-left">Submitted</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    <?php if ($submissions): ?>
                        <?php foreach ($submissions as $s): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3"><?= htmlspecialchars($s['assignment_title']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($s['course']) ?></td>
                                <td class="px-4 py-3">
                                    <a href="uploads/<?= $s['filename'] ?>" target="_blank" class="text-indigo-600 underline">
                                        View File
                                    </a>
                                </td>
                                <td class="px-4 py-3"><?= $s['ai_score'] ? $s['ai_score']."%" : "—" ?></td>
                                <td class="px-4 py-3"><?= $s['confidence'] ? $s['confidence']."%" : "—" ?></td>
                                <td class="px-4 py-3 font-semibold"><?= $s['final_score'] ? $s['final_score']."%" : "—" ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($s['feedback'] ?: "No feedback yet") ?></td>

                                <!-- RESTORED: VIEW RUBRIC LINK -->
                                <td class="px-4 py-3">
                                    <a href="view-rubrics.php?course=<?= urlencode($s['course']) ?>"
                                       class="text-blue-600 underline">
                                       View Rubric
                                    </a>
                                </td>

                                <td class="px-4 py-3 text-gray-600"><?= htmlspecialchars($s['submitted_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="py-4 text-center text-gray-500">
                                No submissions found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</main>

<footer class="text-center text-gray-400 py-6">
    ©️ 2025 EquiGrade — FAIR-EVAL AI Grading Assistant
</footer>

</body>
</html>
