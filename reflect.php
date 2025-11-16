<?php
// reflect.php
require_once __DIR__.'/db.php';
require_once __DIR__.'/helpers.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: submission.php'); exit; }
if (!check_csrf($_POST['csrf'] ?? '')) { http_response_code(400); echo "Invalid CSRF"; exit; }
if (empty($_SESSION['user_id'])) { header('Location: index.html'); exit; }

$pdo = get_db();
$submission_id = intval($_POST['submission_id'] ?? 0);
$text = trim($_POST['reflection_text'] ?? '');
if ($text === '') { flash('error','Reflection cannot be empty.'); header('Location: submission.php'); exit; }

$stmt = $pdo->prepare("INSERT INTO reflections (submission_id, student_id, reflection_text, submitted_at) VALUES (?,?,?,NOW())");
$stmt->execute([$submission_id, $_SESSION['user_id'], $text]);

// notify teacher(s) - simplistic: find assignment creator
$q = $pdo->prepare("SELECT a.created_by FROM submissions s JOIN assignments a ON a.assignment_id = s.assignment_id WHERE s.submission_id = ?");
$q->execute([$submission_id]);
$r = $q->fetch();
if ($r) {
    $n = $pdo->prepare("INSERT INTO notifications (user_id, type, message, status, created_at) VALUES (?,?,?,?,NOW())");
    $n->execute([$r['created_by'], 'reflection', 'A student submitted a reflection.', 'unread']);
}

flash('success','Reflection saved.');
header('Location: submission.php');
exit;
?>
