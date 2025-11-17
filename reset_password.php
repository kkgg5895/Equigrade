<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//  Validate Token from URL 
$token = $_GET['token'] ?? '';

if (empty($token)) {
    flash('error', 'Invalid or missing token.');
    header('Location: forgot_password.php');
    exit;
}

global $pdo;

// Fetch user based on token 
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    flash('error', 'This reset link is invalid or has expired.');
    header('Location: forgot_password.php');
    exit;
}

// Handle Password Reset Submission ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm'] ?? '');

    if (empty($password) || empty($confirm)) {
        flash('error', 'Please fill in all password fields.');
    } elseif ($password !== $confirm) {
        flash('error', 'Passwords do not match.');
    } elseif (strlen($password) < 6) {
        flash('error', 'Password must be at least 6 characters long.');
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        // Update password, clear token
        $update = $pdo->prepare("UPDATE users 
            SET password = ?, reset_token = NULL, reset_expires = NULL 
            WHERE user_id = ?");
        $update->execute([$hashed, $user['user_id']]);

        flash('success', 'Your password has been successfully reset. Please sign in.');
        header('Location: index.php');
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reset Password — EquiGrade</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-white flex items-center justify-center min-h-screen text-gray-800">
  <div class="bg-white p-10 rounded-2xl shadow-2xl w-full max-w-md border border-gray-100">
    <div class="text-center mb-6">
      <div class="text-indigo-600 text-3xl font-extrabold mb-2">EquiGrade</div>
      <h1 class="text-2xl font-semibold text-gray-800">Reset Your Password</h1>
      <p class="text-gray-500 text-sm">Enter your new password below.</p>
    </div>

    <?php display_flash(); ?>

    <form method="POST" class="space-y-5">
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
        <input id="password" name="password" type="password" required
               class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
      </div>
      <div>
        <label for="confirm" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input id="confirm" name="confirm" type="password" required
               class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5">
      </div>
      <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 transition text-white font-semibold py-2.5 rounded-lg shadow-md">
        Reset Password
      </button>
    </form>

    <div class="text-center mt-6 text-sm text-gray-600">
      <a href="index.php" class="text-indigo-600 hover:underline font-medium">← Back to Sign In</a>
    </div>
  </div>
</body>
</html>
