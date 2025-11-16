<?php
// auth.php
// ===================================================
// Handles user registration and login
// ===================================================

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

$pdo = get_db();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ---------------------------------------------------
    // REGISTER NEW USER
    // ---------------------------------------------------
    if ($action === 'register') {
        if (!check_csrf($_POST['csrf'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            exit;
        }

        $name = trim($_POST['fullname'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'student';

        if (!$name || !$email || !$password) {
            flash('error', 'All fields are required.');
            header('Location: register.php');
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$name, $email, $hash, $role]);
            flash('success', 'Account created successfully. Please log in.');
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                flash('error', 'Email already registered.');
            } else {
                flash('error', 'User creation failed: ' . $e->getMessage());
            }
            header('Location: register.php');
            exit;
        }
    }

    // ---------------------------------------------------
    // LOGIN EXISTING USER
    // ---------------------------------------------------
    if ($action === 'login') {
        if (!check_csrf($_POST['csrf'] ?? '')) {
            http_response_code(400);
            echo "Invalid CSRF token.";
            exit;
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT user_id, password_hash, role, full_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Secure session setup
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            // Redirect based on role
            if ($user['role'] === 'teacher') {
                header('Location: teacher-dashboard.php');
            } else {
                header('Location: submission.php');
            }
            exit;
        } else {
            flash('error', 'Invalid email or password.');
            header('Location: index.php');
            exit;
        }
    }
}
?>
