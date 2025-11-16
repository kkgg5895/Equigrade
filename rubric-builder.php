<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
session_start();

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    flash('error', 'Access denied.');
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Rubric - EquiGrade</title>
  <link rel="stylesheet" href="css/styles.css">
  <style>
    .criteria-list { margin-top: 10px; }
    .criteria-item { display: flex; gap: 10px; margin-bottom: 5px; }
    .criteria-item input { flex: 1; padding: 6px; }
  </style>
</head>
<body>
  <main class="container">
    <header class="header">
      <a class="brand" href="teacher-dashboard.php"><strong>EquiGrade</strong></a>
      <nav>
        <a href="teacher-dashboard.php">Dashboard</a>
        <a href="logout.php">Sign out</a>
      </nav>
    </header>

    <section class="card">
      <h2>🧩 Create a New Rubric</h2>
      <p>Assign a rubric to a specific course and define its grading criteria.</p>
      <?php display_flash(); ?>

      <form method="POST" action="rubric_create.php" onsubmit="return prepareCriteriaJSON();">
        <div class="form-row column">
          <label for="rubric_title">Rubric Title</label>
          <input id="rubric_title" name="rubric_title" type="text" required placeholder="e.g., Essay Evaluation Rubric">
        </div>

        <div class="form-row column">
          <label for="course">Assign to Course</label>
          <select id="course" name="course" required>
            <option value="">Select Course</option>
            <option value="BN204">BN204 Database Technologies</option>
            <option value="BN206">BN206 System Administration</option>
            <option value="NIT3213">NIT3213 Android Development</option>
            <option value="NIT3222">NIT3222 Virtualisation in Computing</option>
          </select>
        </div>

        <div class="form-row column">
          <label>Criteria</label>
          <div id="criteriaList" class="criteria-list">
            <div class="criteria-item">
              <input type="text" placeholder="Criterion (e.g., Content)" class="criterion">
              <input type="number" placeholder="Weight %" class="weight">
            </div>
          </div>
          <button type="button" class="btn btn-secondary" onclick="addCriterion()">+ Add Criterion</button>
          <input type="hidden" id="criteria" name="criteria">
        </div>

        <button type="submit" class="btn btn-primary">Create Rubric</button>
      </form>
    </section>
  </main>

  <script>
    function addCriterion() {
      const container = document.getElementById('criteriaList');
      const item = document.createElement('div');
      item.className = 'criteria-item';
      item.innerHTML = `
        <input type="text" placeholder="Criterion (e.g., Grammar)" class="criterion">
        <input type="number" placeholder="Weight %" class="weight">
        <button type="button" onclick="this.parentElement.remove()">✖</button>
      `;
      container.appendChild(item);
    }

    function prepareCriteriaJSON() {
      const criteria = [];
      document.querySelectorAll('.criteria-item').forEach(row => {
        const name = row.querySelector('.criterion').value.trim();
        const weight = parseFloat(row.querySelector('.weight').value);
        if (name && !isNaN(weight)) {
          criteria.push({ criterion: name, weight: weight });
        }
      });

      if (criteria.length === 0) {
        alert("Please add at least one valid criterion with weight.");
        return false;
      }

      document.getElementById('criteria').value = JSON.stringify(criteria);
      return true;
    }
  </script>
</body>
</html>
