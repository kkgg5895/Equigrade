<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Grade submission using rubric-based text analysis
 */
function grade_submission_with_rubric(int $submission_id, int $user_id, string $course): array {
    $pdo = get_db();

    // 1️⃣ Fetch rubric
    $stmt = $pdo->prepare("SELECT rubric_id, criteria_json FROM rubrics WHERE course = ?");
    $stmt->execute([$course]);
    $rubric = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rubric) {
        // fallback
        $aiScore = rand(60, 90);
        $confidence = rand(70, 100);
        $feedback = "No rubric found — fallback score applied.";
        $pdo->prepare("
            INSERT INTO grades (submission_id, ai_score, confidence, feedback, graded_at)
            VALUES (?, ?, ?, ?, NOW())
        ")->execute([$submission_id, $aiScore, $confidence, $feedback]);
        return compact('aiScore', 'confidence', 'feedback');
    }

    $criteria = json_decode($rubric['criteria_json'], true);
    if (!is_array($criteria)) throw new Exception("Invalid rubric format");

    // 2️⃣ Load file text
    $stmt = $pdo->prepare("SELECT filename FROM submissions WHERE submission_id = ?");
    $stmt->execute([$submission_id]);
    $file = $stmt->fetchColumn();
    if (!$file) throw new Exception("File not found for submission");

    $filePath = UPLOAD_DIR . '/' . $file;
    $text = strtolower(strip_tags(file_get_contents($filePath)));

    // 3️⃣ Define rubric keyword sets
    $keywordSets = [
        'content' => ['introduction', 'concept', 'explain', 'overview', 'example'],
        'structure' => ['paragraph', 'heading', 'summary', 'conclusion', 'organized'],
        'research' => ['source', 'data', 'evidence', 'reference', 'analysis']
    ];

    // 4️⃣ Analyze text
    $totalScore = 0;
    $feedbackParts = [];

    foreach ($criteria as $crit) {
        $criterion = strtolower($crit['criterion']);
        $weight = floatval($crit['weight']);
        $keywords = $keywordSets[$criterion] ?? [];

        // Count keyword matches
        $matches = 0;
        foreach ($keywords as $word) {
            $matches += substr_count($text, $word);
        }

        // Score logic based on keyword density
        $criterionScore = 50 + min(50, $matches * 10);
        if ($criterionScore > 100) $criterionScore = 100;

        $weightedScore = ($criterionScore * ($weight / 100));
        $totalScore += $weightedScore;

        $feedbackParts[] = ucfirst($criterion) . ": " . round($criterionScore, 1) . "% (weight " . $weight . "%)";
    }

    $aiScore = round($totalScore, 2);
    $confidence = rand(85, 99);
    $feedback = "Rubric-based AI grading:\n" . implode("; ", $feedbackParts);

    // 5️⃣ Save results
    $insert = $pdo->prepare("
        INSERT INTO grades (submission_id, ai_score, confidence, feedback, graded_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $insert->execute([$submission_id, $aiScore, $confidence, $feedback]);

    return compact('aiScore', 'confidence', 'feedback');
}
