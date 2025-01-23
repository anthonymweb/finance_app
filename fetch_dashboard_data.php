<?php
session_start();
include('../connection/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch wallet, budget, and expense data
$walletQuery = "SELECT SUM(amount) AS total_wallet FROM wallet WHERE user_id = ?";
$stmt_wallet = $conn->prepare($walletQuery);
$stmt_wallet->bind_param("i", $user_id);
$stmt_wallet->execute();
$walletResult = $stmt_wallet->get_result()->fetch_assoc();
$totalWallet = $walletResult['total_wallet'] ?? 0;

$expensesQuery = "SELECT SUM(amount) AS total_spent FROM expenses WHERE user_id = ?";
$stmt_expenses = $conn->prepare($expensesQuery);
$stmt_expenses->bind_param("i", $user_id);
$stmt_expenses->execute();
$expensesResult = $stmt_expenses->get_result()->fetch_assoc();
$totalSpent = $expensesResult['total_spent'] ?? 0;

$totalRemaining = $totalWallet - $totalSpent;

// Fetch spending data for line chart
$stmt_spending = $conn->prepare("
    SELECT MONTHNAME(date) AS month, SUM(amount) AS total
    FROM transactions
    WHERE user_id = ?
    GROUP BY MONTH(date)
    ORDER BY MONTH(date)
");
$stmt_spending->bind_param("i", $user_id);
$stmt_spending->execute();
$spending_data = $stmt_spending->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch category data for doughnut chart
$stmt_category = $conn->prepare("
    SELECT category, SUM(amount) AS total
    FROM transactions
    WHERE user_id = ?
    GROUP BY category
");
$stmt_category->bind_param("i", $user_id);
$stmt_category->execute();
$category_data = $stmt_category->get_result()->fetch_all(MYSQLI_ASSOC);

// Return data as JSON
echo json_encode([
    'wallet' => [
        'total_wallet' => $totalWallet,
        'total_spent' => $totalSpent,
        'total_remaining' => $totalRemaining,
    ],
    'spending_data' => $spending_data,
    'category_data' => $category_data,
]);
?>
