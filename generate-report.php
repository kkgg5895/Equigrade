<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/vendor/autoload.php'; // For TCPDF or reportlib

if (!isset($_GET['id'])) {
    die("No submission ID provided.");
}

global $pdo;
$submission_id = intval($_GET['id']);

// ✅ FIXED QUERY — use correct column name
// Check your DB: if the column is submission_id (not id), use that.
$stmt = $pdo->prepare("
    SELECT s.submission_id, s.assignment_title, s.course, s.filename, s.submitted_at,
           g.ai_score, g.confidence, g.feedback
    FROM submissions s
    JOIN grades g ON s.submission_id = g.submission_id
    WHERE s.submission_id = ?
");
$stmt->execute([$submission_id]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) {
    die("Submission not found.");
}

// ✅ TCPDF setup
require_once(__DIR__ . '/tcpdf/tcpdf.php');
$pdf = new TCPDF();
$pdf->AddPage();

// Header
$pdf->SetFont('Helvetica', 'B', 18);
$pdf->Cell(0, 10, 'EquiGrade - AI Evaluation Report', 0, 1, 'C');
$pdf->Ln(8);

// Assignment info
$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(0, 10, "Assignment Title: " . $report['assignment_title'], 0, 1);
$pdf->Cell(0, 10, "Course: " . $report['course'], 0, 1);
$pdf->Cell(0, 10, "Submitted At: " . $report['submitted_at'], 0, 1);
$pdf->Ln(6);

// AI result
$pdf->SetFont('Helvetica', 'B', 14);
$pdf->Cell(0, 10, "AI Evaluation Results", 0, 1);
$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(0, 10, "AI Score: " . $report['ai_score'] . "%", 0, 1);
$pdf->Cell(0, 10, "Confidence: " . $report['confidence'] . "%", 0, 1);
$pdf->MultiCell(0, 10, "Feedback: " . $report['feedback'], 0, 1);
$pdf->Ln(8);

// Footer
$pdf->SetFont('Helvetica', 'I', 10);
$pdf->Cell(0, 10, "Generated on " . date("Y-m-d H:i:s") . " via EquiGrade", 0, 1, 'C');

// Output to browser
$pdf->Output('EquiGrade_Report_' . $submission_id . '.pdf', 'I');
?>
