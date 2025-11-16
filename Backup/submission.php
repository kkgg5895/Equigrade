<?php
session_start();
header("Content-Type: application/json");

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/vendor/autoload.php';

use OpenAI;

if (empty($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

$title = trim($_POST['title'] ?? 'Untitled');
$course = trim($_POST['course'] ?? 'N/A');
$file = $_FILES['file'] ?? null;

// Ensure upload folder exists
$upload_dir = __DIR__ . '/uploads/';
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

// Validate file
if (!$file || $file['error'] !== 0) {
    echo json_encode(["error" => "File upload failed"]);
    exit;
}

$allowed_ext = ['pdf', 'docx', 'txt'];
$file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($file_ext, $allowed_ext)) {
    echo json_encode(["error" => "Invalid file type. Allowed: PDF, DOCX, TXT"]);
    exit;
}

$new_filename = uniqid("submission_", true) . '.' . $file_ext;
$target_path = $upload_dir . $new_filename;

if (!move_uploaded_file($file['tmp_name'], $target_path)) {
    echo json_encode(["error" => "Failed to save uploaded file"]);
    exit;
}

// ===== Extract file text =====
$content = '';
if ($file_ext === 'txt') {
    $content = file_get_contents($target_path);
} elseif ($file_ext === 'pdf') {
    $content = shell_exec("pdftotext " . escapeshellarg($target_path) . " -");
} elseif ($file_ext === 'docx') {
    $zip = new ZipArchive;
    if ($zip->open($target_path) === TRUE) {
        $xml = $zip->getFromName('word/document.xml');
        $content = strip_tags($xml);
        $zip->close();
    }
}
if (empty(trim($content))) {
    $content = "No readable text extracted from this file.";
}

// ===== AI Grading =====
$client = OpenAI::client(OPENAI_API_KEY);

$grading_prompt = <<<PROMPT
You are an AI file evaluator. Analyze the uploaded text and determine:
- clarity and coherence
- structure and logic
- accuracy or technical correctness
- creativity or insight
- overall quality

Return ONLY valid JSON with:
{
  "score": <0-100>,
  "confidence": <0-100>,
  "feedback": "<short constructive feedback>"
}

File content:
$content
PROMPT;

try {
    $response = $client->chat()->create([
        'model' => 'gpt-4-turbo',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You must output ONLY valid JSON. Never add extra text, markdown, or explanation.'
            ],
            [
                'role' => 'user',
                'content' => $grading_prompt
            ]
        ],
        'temperature' => 0.3,
        'response_format' => ['type' => 'json_object']
    ]);

    $ai_output = $response['choices'][0]['message']['content'] ?? '';
    $json = json_decode($ai_output, true);

    if (!$json || !isset($json['score'])) {
        $json = [
            'score' => rand(70, 95),
            'confidence' => rand(80, 99),
            'feedback' => "AI did not return valid JSON. Using fallback grading."
        ];
    }

    $ai_score = $json['score'];
    $confidence = $json['confidence'];
    $feedback = $json['feedback'];

    // Save to DB
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO submissions (user_id, assignment_title, course, filename, submitted_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$user_id, $title, $course, $new_filename]);
    $submission_id = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare("
        INSERT INTO grades (submission_id, ai_score, confidence, feedback, final_score)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt2->execute([$submission_id, $ai_score, $confidence, $feedback, $ai_score]);

    echo json_encode([
        "success" => true,
        "message" => "File graded successfully.",
        "ai_score" => $ai_score,
        "confidence" => $confidence,
        "feedback" => $feedback
    ]);

} catch (Exception $e) {
    echo json_encode([
        "error" => "AI Grading failed",
        "details" => $e->getMessage()
    ]);
}
?>
