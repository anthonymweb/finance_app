<?php
session_start();
require_once 'tcpdf/tcpdf.php';

$conn = new mysqli("localhost", "root", "", "finance_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch expenses or budget data
$type = isset($_GET['type']) ? $_GET['type'] : 'expenses';
$user_id = $_SESSION['user_id'];

if ($type === 'expenses') {
    $query = "SELECT category, amount, date, description FROM expenses WHERE user_id='$user_id' ORDER BY date DESC";
    $title = "Expenses Report";
} else {
    $query = "SELECT category, budget_limit, amount_spent FROM budgets WHERE user_id='$user_id'";
    $title = "Budget Report";
}

$result = $conn->query($query);

// Create PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

$pdf->Cell(0, 10, $title, 0, 1, 'C');

// Add Table Headers
if ($type === 'expenses') {
    $headers = ['Category', 'Amount', 'Date', 'Description'];
} else {
    $headers = ['Category', 'Budget Limit', 'Amount Spent'];
}

foreach ($headers as $header) {
    $pdf->Cell(45, 10, $header, 1);
}
$pdf->Ln();

// Add Data Rows
while ($row = $result->fetch_assoc()) {
    foreach ($row as $data) {
        $pdf->Cell(45, 10, $data, 1);
    }
    $pdf->Ln();
}

$pdf->Output("$title.pdf", 'D');
exit();
?>
