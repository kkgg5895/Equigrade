<?php
session_start();
require_once "db.php"; // ✅ Ensure db.php defines global $pdo

// Restrict to teachers only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

// Handle rubric creation form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rubric_title = $_POST['rubric_title'];
    $course = $_POST['course'];
    $description = $_POST['description'] ?? '';
    $criteria_json = $_POST['criteria'];
    $created_by = $_SESSION['user_id'];

    try {
        global $pdo;

        $stmt = $pdo->prepare("
            INSERT INTO rubrics (rubric_title, course, title, description, criteria_json, created_by, created_at)
            VALUES (:rubric_title, :course, :title, :description, :criteria_json, :created_by, NOW())
        ");
        $stmt->execute([
            ':rubric_title' => $rubric_title,
            ':course' => $course,
            ':title' => $rubric_title,
            ':description' => $description,
            ':criteria_json' => $criteria_json,
            ':created_by' => $created_by
        ]);

        header("Location: teacher-dashboard.php?success=1");
        exit();
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Rubric | EquiGrade</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans leading-normal tracking-normal">

    <!-- Navbar -->
    <div class="flex justify-between items-center bg-indigo-700 text-white py-4 px-8 shadow-md">
        <h1 class="text-xl font-semibold">🧩 EquiGrade — FAIR-EVAL AI Grading Assistant</h1>
        <div>
            <a href="teacher-dashboard.php" class="bg-indigo-500 hover:bg-indigo-600 px-4 py-2 rounded-lg text-white text-sm font-medium transition-all">⬅ Back to Dashboard</a>
            <a href="logout.php" class="ml-2 bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg text-white text-sm font-medium transition-all">Sign Out</a>
        </div>
    </div>

    <!-- Form Container -->
    <div class="max-w-4xl mx-auto mt-12 bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center mb-6">
            <span class="text-3xl">🧩</span>
            <h2 class="text-2xl font-bold text-gray-800 ml-2">Create a New Rubric</h2>
        </div>

        <form method="POST" onsubmit="return prepareCriteriaJSON();" class="space-y-6">
            <!-- Rubric Title -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rubric Title</label>
                <input type="text" name="rubric_title" placeholder="e.g., Essay Evaluation Rubric" required
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <!-- Assign to Course -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assign to Course</label>
                <select name="course" required
                    class="w-full border border-gray-300 rounded-lg p-3 bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    <option value="">Select Course</option>
                    <option value="BN204">BN204</option>
                    <option value="BN206">BN206</option>
                    <option value="NIT3213">NIT3213</option>
                    <option value="NIT3003">NIT3003</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" placeholder="Brief description of this rubric"
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-indigo-500 focus:outline-none h-24"></textarea>
            </div>

            <!-- Dynamic Criteria Builder -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700">Criteria Builder</label>
                    <button type="button" onclick="loadTemplate()" 
                        class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1.5 rounded text-sm font-semibold shadow-md transition-all">
                        📋 Load FAIR-EVAL Template
                    </button>
                </div>

                <div id="criteriaContainer" class="space-y-2">
                    <div class="flex gap-3">
                        <input type="text" placeholder="Criterion (e.g., Content)" class="criterion border border-gray-300 rounded-lg p-2 flex-1">
                        <input type="number" placeholder="Weight %" class="weight border border-gray-300 rounded-lg p-2 w-32">
                        <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 text-sm font-semibold">✖</button>
                    </div>
                </div>

                <button type="button" onclick="addRow()" 
                    class="mt-2 bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                    ➕ Add Criterion
                </button>

                <p class="text-gray-500 text-xs mt-2">
                    Total weight must equal 100 %. Example: Content 40 + Structure 30 + Research 30 = 100
                </p>
            </div>

            <!-- Hidden JSON Field -->
            <textarea id="criteriaJSON" name="criteria" hidden></textarea>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg text-sm font-semibold shadow-md transition-all">
                    ✅ Create Rubric
                </button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-10 text-gray-500 text-sm">
        <p>&copy; 2025 EquiGrade — FAIR-EVAL AI Grading Assistant</p>
    </footer>

    <!-- JS for dynamic criteria builder -->
    <script>
    function addRow() {
        const container = document.getElementById('criteriaContainer');
        const div = document.createElement('div');
        div.className = 'flex gap-3';
        div.innerHTML = `
            <input type="text" placeholder="Criterion (e.g., Structure)" class="criterion border border-gray-300 rounded-lg p-2 flex-1">
            <input type="number" placeholder="Weight %" class="weight border border-gray-300 rounded-lg p-2 w-32">
            <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 text-sm font-semibold">✖</button>
        `;
        container.appendChild(div);
    }

    function removeRow(button) {
        button.parentElement.remove();
    }

    // ✅ Convert criteria to JSON
    function prepareCriteriaJSON() {
        const criteria = [];
        document.querySelectorAll('#criteriaContainer .flex').forEach(row => {
            const criterion = row.querySelector('.criterion').value.trim();
            const weight = parseInt(row.querySelector('.weight').value);
            if (criterion && !isNaN(weight)) {
                criteria.push({ criterion, weight });
            }
        });

        if (criteria.length === 0) {
            alert('Please add at least one criterion.');
            return false;
        }

        const total = criteria.reduce((sum, c) => sum + c.weight, 0);
        if (total !== 100) {
            alert('Total weight must equal 100 %. Currently: ' + total + '%');
            return false;
        }

        document.getElementById('criteriaJSON').value = JSON.stringify(criteria);
        return true;
    }

    // ✅ Load FAIR-EVAL default template
    function loadTemplate() {
        const defaultTemplate = [
            { criterion: "Content Accuracy", weight: 40 },
            { criterion: "Structure & Organization", weight: 20 },
            { criterion: "Critical Thinking", weight: 20 },
            { criterion: "Presentation & Clarity", weight: 10 },
            { criterion: "Originality & Fairness", weight: 10 }
        ];

        const container = document.getElementById('criteriaContainer');
        container.innerHTML = '';
        defaultTemplate.forEach(item => {
            const div = document.createElement('div');
            div.className = 'flex gap-3';
            div.innerHTML = `
                <input type="text" value="${item.criterion}" class="criterion border border-gray-300 rounded-lg p-2 flex-1">
                <input type="number" value="${item.weight}" class="weight border border-gray-300 rounded-lg p-2 w-32">
                <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 text-sm font-semibold">✖</button>
            `;
            container.appendChild(div);
        });
    }
    </script>

</body>
</html>
