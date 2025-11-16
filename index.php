<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        flash('error', 'Please enter both email and password.');
        header('Location: index.php');
        exit;
    }

    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ✅ Fixed: use 'password' (your real DB column)
    if ($user && !empty($user['password']) && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        if ($user['role'] === 'teacher') {
            header('Location: teacher-dashboard.php');
        } else {
            header('Location: submission-dashboard.php');
        }
        exit;
    } else {
        flash('error', 'Invalid email or password.');
        header('Location: index.php');
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>EquiGrade — Sign In</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen text-gray-800">

  <main class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
    <div class="flex items-center justify-center gap-2 mb-6">
        <div class="bg-indigo-600 text-white font-bold text-lg rounded-full w-9 h-9 flex items-center justify-center">E</div>
        <h1 class="text-2xl font-semibold text-indigo-700">EquiGrade</h1>
    </div>

    <h2 class="text-xl font-bold mb-2">Sign in</h2>
    <p class="text-sm text-gray-500 mb-6">Enter your credentials to access your dashboard.</p>

    <?php display_flash(); ?>

    <form method="post" action="" class="space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input id="email" name="email" type="email" required
                   class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5"
                   placeholder="you@university.edu">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium mb-1">Password</label>
            <input id="password" name="password" type="password" required
                   class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 p-2.5"
                   placeholder="••••••••">
        </div>

        <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg py-2.5 transition">
            Sign In
        </button>
    </form>

    <!-- Footer links -->
    <div class="flex justify-between items-center mt-5 text-sm">
        <a href="forgot_password.php" class="text-indigo-600 hover:underline">Forgot your password?</a>
        <a href="register.php" class="text-indigo-600 hover:underline">Create account</a>
    </div>
  </main>

</body>
</html>
