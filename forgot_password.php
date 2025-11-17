<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ==== Required files ====
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ==== Session Handling ====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==== Main Logic ====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        flash('error', 'Please enter your email address.');
        header('Location: forgot_password.php');
        exit;
    }

    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        flash('error', 'No account found with that email address.');
        header('Location: forgot_password.php');
        exit;
    }

    //  Create Reset Token 
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600); // valid for 1 hour

    // ✅ Fixed: Use user_id instead of email for reliability
    $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE user_id = ?");
    $update->execute([$token, $expires, $user['user_id']]);

    // Debug confirmation (optional)
    // echo "Rows affected: " . $update->rowCount(); exit;

    // ==== Build Reset Link ====
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") .
               "://{$_SERVER['HTTP_HOST']}/equigrade-frontend";
    $resetLink = "$baseUrl/reset_password.php?token=$token";

    // ==== Send Email using PHPMailer ====
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kkgg5895@gmail.com'; // your Gmail
        $mail->Password   = 'rvomfbxpzamntrfq'; // your App Password (16-char)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('kkg5895@gmail.com', 'EquiGrade Support');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request — EquiGrade';
        $mail->Body = "
            <div style='font-family:Arial,sans-serif;color:#333;'>
                <h2 style='color:#4f46e5;'>EquiGrade Password Reset</h2>
                <p>Hello,</p>
                <p>We received a request to reset your password. Click below to reset it:</p>
                <p>
                    <a href='$resetLink' style='background-color:#4f46e5;color:#fff;
                    padding:10px 20px;text-decoration:none;border-radius:6px;'>Reset Password</a>
                </p>
                <p>If the button doesn’t work, copy this link into your browser:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>— The EquiGrade Team</p>
            </div>
        ";

        $mail->send();
        flash('success', 'A password reset link has been sent to your email address.');
    } catch (Exception $e) {
        flash('error', 'Mailer Error: ' . $mail->ErrorInfo);
    }

    header('Location: forgot_password.php');
    exit;
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Forgot Password — EquiGrade</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-white flex items-center justify-center min-h-screen text-gray-800">
  <div class="bg-white p-10 rounded-2xl shadow-2xl w-full max-w-md border border-gray-100">
    <div class="text-center mb-6">
      <div class="text-indigo-600 text-3xl font-extrabold mb-2">EquiGrade</div>
      <h1 class="text-2xl font-semibold text-gray-800">Forgot your password?</h1>
      <p class="text-gray-500 text-sm">Enter your email to receive a reset link.</p>
    </div>

    <?php display_flash(); ?>

    <form method="POST" class="space-y-5">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
        <input id="email" name="email" type="email" required
               class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 p-2.5"
               placeholder="you@university.edu">
      </div>
      <button type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 transition text-white font-semibold py-2.5 rounded-lg shadow-md">
        Send Reset Link
      </button>
    </form>

    <div class="text-center mt-6 text-sm text-gray-600">
      <a href="index.php" class="text-indigo-600 hover:underline font-medium">← Back to Sign In</a>
    </div>
  </div>
</body>
</html>
