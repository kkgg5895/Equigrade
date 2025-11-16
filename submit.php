<?php
session_start();
header("Content-Type: application/json");

// Includes
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/vendor/autoload.php';

use OpenAI\Factory;

// Ensure user is logged in
if (empty($_SESSION['user_id'])) {
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

// Ensure POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$title = trim($_POST['assignment_title'] ?? 'Untitled');
$course = trim($_POST['course'] ?? 'N/A');
$file = $_FILES['assignment_file'] ?? null;

// Upload folder
$upload_dir = __DIR__ . '/uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

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
    echo json_encode(["error" => "Failed to move uploaded file"]);
    exit;
}

// Extract text
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

// ============= BALANCED STRICT AI GRADING =============
try {
    $client = (new Factory())
        ->withApiKey(OPENAI_API_KEY)
        ->make();

    $prompt = <<<PROMPT
You are "EquiGrade", a balanced but strict university academic marker.

### Balanced-Strict Grading Rules:
- Do NOT give high marks unless the work shows clear structure, accuracy, and critical thought.
- Be fair: do not punish harshly unless the content is truly weak.
- Reward good structure, clarity, and logical flow.
- Penalize unclear writing, weak reasoning, repetition, and shallow content.

### Scoring Guide (balanced strict):
- 72–82 = Excellent (rare but possible)
- 65–71 = Good
- 55–64 = Average
- 45–54 = Below average
- 30–44 = Weak
- 10–29 = Very weak

### Evaluation Criteria (Weights):
1. Content Accuracy (40%)
2. Structure & Organisation (20%)
3. Critical Thinking (20%)
4. Presentation & Clarity (10%)
5. Originality (10%)

### OUTPUT (STRICT JSON ONLY):
{
  "score": <0-100>,
  "confidence": <0-100>,
  "feedback": "<constructive helpful feedback>"
}

Evaluate this submission fairly and strictly:

"$content"
PROMPT;

    $response = $client->chat()->create([
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'system', 'content' => 'Return ONLY valid JSON. No comments, no markdown, no text outside JSON.'],
            ['role' => 'user', 'content' => $prompt],
        ],
        'temperature' => 0.15,
        'response_format' => ['type' => 'json_object']
    ]);

    $ai_output = $response['choices'][0]['message']['content'] ?? '';
    $json = json_decode($ai_output, true);

    // Balanced fallback
    if (!$json || !isset($json['score'])) {
        $json = [
            "score" => rand(50, 72), // fallback slightly strict
            "confidence" => rand(60, 90),
            "feedback" => "Fallback balanced evaluation applied due to formatting issue."
        ];
    }

    // Enforce realistic ranges
    $raw = $json['score'];

    if ($raw > 82) $raw = rand(74, 82);   // top range but possible
    if ($raw > 71) $raw = rand(72, 78);   // avoid too many 80+
    if ($raw < 10) $raw = rand(15, 30);   // avoid extremely low scores unless deserved

    $ai_score = round($raw, 2);
    $confidence = $json['confidence'];
    $feedback = $json['feedback'];

    // SAVE TO DB
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
        "message" => "Assignment uploaded and graded (balanced strict).",
        "ai_score" => $ai_score,
        "confidence" => $confidence,
        "feedback" => $feedback
    ]);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
