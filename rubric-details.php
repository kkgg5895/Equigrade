<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
  die("Invalid rubric ID.");
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM rubrics WHERE rubric_id = ?");
$stmt->execute([$id]);
$rubric = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rubric) {
  die("Rubric not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($rubric['title'] ?: 'Rubric Details') ?> — EquiGrade</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
  <div class="max-w-3xl mx-auto mt-10 bg-white p-8 rounded-xl shadow">
    <h1 class="text-2xl font-bold text-indigo-700 mb-4">
      <?= htmlspecialchars($rubric['title'] ?: 'Untitled Rubric') ?>
    </h1>
    <p class="text-gray-600 mb-2">
      <strong>Course:</strong> <?= htmlspecialchars($rubric['course'] ?: 'No Course') ?>
    </p>
    <p class="text-gray-600 mb-4">
      <strong>Description:</strong> <?= htmlspecialchars($rubric['description'] ?: 'No description available.') ?>
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-3">Criteria</h2>
    <ul class="list-disc ml-6 text-gray-800">
      <?php
      $criteria = json_decode($rubric['criteria_json'], true);
      if ($criteria && is_array($criteria)) {
        foreach ($criteria as $criterion) {
          echo "<li><strong>" . htmlspecialchars($criterion['criterion']) . "</strong> — " . htmlspecialchars($criterion['weight']) . "%</li>";
        }
      } else {
        echo "<li><em>No criteria found.</em></li>";
      }
      ?>
    </ul>

    <p class="text-sm text-gray-500 mt-5">
      Created on <?= htmlspecialchars($rubric['created_at']) ?>
    </p>

    <a href="view-rubrics.php" class="mt-6 inline-block text-indigo-600 hover:underline">
      ← Back to Rubrics
    </a>
  </div>
</body>
</html>
