<?php
require_once __DIR__ . '/helpers.php';
$csrf = generate_csrf();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EquiGrade | Login</title>
  <link rel="stylesheet" href="css/styles.css">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col justify-center">

  <main class="max-w-md mx-auto bg-white shadow-lg rounded-2xl p-8 border-t-4 border-indigo-600">
    <div class="text-center mb-6">
      <img src="assets/logo.svg" alt="EquiGrade Logo" class="w-20 mx-auto mb-2">
      <h1 class="text-2xl font-bold text-indigo-700">EquiGrade</h1>
      <p class="text-sm text-gray-500">FAIR-EVAL AI Grading Assistant</p>
    </div>

    <form id="loginForm" method="POST" action="auth.php" class="space-y-5">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
      <input type="hidden" name="action" value="login">

      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
        <input type="email" id="email" name="email" required
               placeholder="you@university.edu"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
        <input type="password" id="password" name="password" required
               placeholder="••••••••"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
      </div>

      <!-- Submit -->
      <div class="text-center mt-6">
        <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition">
          🚀 Sign In
        </button>
      </div>

      <!-- Register Link -->
      <p class="text-sm text-center text-gray-500 mt-4">
        Don’t have an account?
        <a href="register.php" class="text-indigo-600 hover:underline">Create one</a>
      </p>
    </form>
  </main>

  <footer class="text-center py-4 text-gray-400 text-sm mt-8">
    © 2025 EquiGrade — FAIR-EVAL AI Grading Assistant
  </footer>
</body>
</html>
