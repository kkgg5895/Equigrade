<?php
session_start();
require_once 'config.php';
require_once 'db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

global $pdo;

// Filters
$courseFilter = trim($_GET['course'] ?? '');
$search = trim($_GET['search'] ?? '');

$query = "SELECT * FROM rubrics WHERE 1=1";
$params = [];

if ($courseFilter !== '') {
    $query .= " AND course = :course";
    $params[':course'] = $courseFilter;
}
if ($search !== '') {
    $query .= " AND rubric_title LIKE :search";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$rubrics = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get distinct course list
$coursesStmt = $pdo->query("SELECT DISTINCT course FROM rubrics");
$courses = $coursesStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Rubrics — EquiGrade</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-gradient-to-r from-indigo-700 to-purple-700 text-white shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold">📘 View Rubrics — <span class="text-indigo-200">EquiGrade</span></h1>
      <div class="flex items-center gap-3">
        <a href="submission-dashboard.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg transition font-medium">
          ← Dashboard
        </a>
        <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition font-medium">
          🚪 Sign Out
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-6xl mx-auto px-6 py-10 flex-grow">
    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
      <span>📊 Rubric Repository</span>
    </h2>

    <!-- Filters -->
    <form method="get" class="flex flex-col sm:flex-row gap-3 mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
      <div class="flex-1">
        <label class="block text-gray-600 text-sm mb-1">Filter by Course:</label>
        <select name="course" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
          <option value="">All Courses</option>
          <?php foreach ($courses as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= $c === $courseFilter ? 'selected' : '' ?>>
              <?= htmlspecialchars($c) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="flex-1">
        <label class="block text-gray-600 text-sm mb-1">Search rubric title:</label>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
               placeholder="e.g. Android Lifecycle, Database Design..."
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
      </div>

      <div class="flex items-end gap-2">
        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg transition font-medium">
          🔍 Search
        </button>
        <a href="view-rubrics.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-lg transition font-medium">
          Reset
        </a>
      </div>
    </form>

    <p class="text-gray-500 mb-5">Below are all rubrics used for AI grading. Click a rubric name to view details.</p>

    <!-- Rubric Cards -->
    <div class="space-y-6">
      <?php if (count($rubrics) > 0): ?>
        <?php foreach ($rubrics as $r): ?>
          <div class="bg-white shadow-md hover:shadow-lg border border-gray-100 rounded-xl p-6 transition transform hover:-translate-y-1">
            <a href="rubric-details.php?id=<?= $r['rubric_id'] ?>" 
               class="text-indigo-700 font-semibold text-lg hover:underline">
              <?= htmlspecialchars($r['rubric_title']) ?> — <span class="text-gray-600 text-sm"><?= htmlspecialchars($r['course']) ?></span>
            </a>
            <div class="mt-3 space-y-2">
              <?php 
              $criteria = [
                'Content Accuracy' => $r['content_weight'] ?? 0,
                'Technical Depth' => $r['technical_weight'] ?? 0,
                'Clarity and Structure' => $r['clarity_weight'] ?? 0,
                'Writing Quality' => $r['writing_weight'] ?? 0,
              ];
              foreach ($criteria as $label => $weight): ?>
                <div>
                  <div class="flex justify-between text-sm font-medium text-gray-700">
                    <span><?= htmlspecialchars($label) ?></span>
                    <span><?= $weight ?>%</span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                    <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-blue-500" style="width: <?= $weight ?>%;"></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <p class="text-xs text-gray-400 mt-4">📅 Created on <?= htmlspecialchars($r['created_at']) ?></p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="text-center text-gray-500 mt-10">No rubrics found.</div>
      <?php endif; ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-center py-6 text-gray-400 text-sm border-t mt-10">
    © 2025 EquiGrade — FAIR-EVAL AI Grading Assistant
  </footer>

</body>
</html>
