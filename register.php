<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'student';

    if (empty($full_name) || empty($email) || empty($password)) {
        flash('error', 'All fields are required.');
        header('Location: register.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Invalid email format.');
        header('Location: register.php');
        exit;
    }

    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        flash('error', 'Email already exists. Please log in instead.');
        header('Location: register.php');
        exit;
    }

    // ✅ Secure password hashing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert into single column 'password'
    $stmt = $pdo->prepare("
        INSERT INTO users (full_name, email, password, role, created_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$full_name, $email, $hashed_password, $role]);

    flash('success', 'Account created successfully! You can now log in.');
    header('Location: index.php');
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Create Account — EquiGrade</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen text-gray-800">

  <main class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
    <div class="flex items-center justify-center gap-2 mb-6">
        <div class="bg-indigo-600 text-white font-bold text-lg rounded-full w-9 h-9 flex items-center justify-center">E</div>
        <h1 class="text-2xl font-semibold text-indigo-700">EquiGrade</h1>
    </div>

    <h2 class="text-xl font-bold mb-2">Create Account</h2>
    <p class="text-sm text-gray-500 mb-6">Join EquiGrade and start fair, transparent grading.</p>

    <?php display_flash(); ?>

    <form method="POST" action="register.php" class="space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Full Name</label>
        <input type="text" name="full_name" required
               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5"
               placeholder="Full Name">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Email Address</label>
        <input type="email" name="email" required
               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5"
               placeholder="you@university.edu">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Password</label>
        <input type="password" name="password" required minlength="6"
               class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5"
               placeholder="••••••••">
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Role</label>
        <select name="role" required
                class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
          <option value="student">Student</option>
          <option value="teacher">Teacher</option>
        </select>
      </div>

      <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg py-2.5 transition">
        Create Account
      </button>
    </form>

    <div class="text-center mt-5 text-sm">
      Already have an account? <a href="index.php" class="text-indigo-600 hover:underline">Sign in</a>
    </div>
  </main>

</body>
</html>
