<?php
session_start();
$conn = new mysqli("localhost", "root", "", "finance_app");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch expenses or budget data
$type = isset($_GET['type']) ? $_GET['type'] : 'expenses';
$user_id = $_SESSION['user_id'];

if ($type === 'expenses') {
    $query = "SELECT category, amount, date, description FROM expenses WHERE user_id='$user_id' ORDER BY date DESC";
    $filename = "expenses_report.csv";
} else {
    $query = "SELECT category, budget_limit, amount_spent FROM budgets WHERE user_id='$user_id'";
    $filename = "budget_report.csv";
}

$result = $conn->query($query);

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=$filename");

$output = fopen("php://output", "w");

// Add column headers
if ($type === 'expenses') {
    fputcsv($output, ['Category', 'Amount', 'Date', 'Description']);
} else {
    fputcsv($output, ['Category', 'Budget Limit', 'Amount Spent']);
}

// Add data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
