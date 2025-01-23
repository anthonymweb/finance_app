<?php
session_start();
include('../connection/db.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch spending data (Monthly Spending from Expenses table)
$stmt_spending = $conn->prepare("
    SELECT MONTHNAME(date) AS month, SUM(amount) AS total 
    FROM expenses 
    WHERE user_id = ? 
    GROUP BY MONTH(date) 
    ORDER BY MONTH(date)
");
$stmt_spending->bind_param("i", $user_id);
$stmt_spending->execute();
$result_spending = $stmt_spending->get_result();
$spending_data = [];
while ($row = $result_spending->fetch_assoc()) {
    $spending_data[] = $row;
}

// Fetch category breakdown (Categories from Expenses table)
$stmt_category = $conn->prepare("
    SELECT category, SUM(amount) AS total 
    FROM expenses 
    WHERE user_id = ? 
    GROUP BY category
");
$stmt_category->bind_param("i", $user_id);
$stmt_category->execute();
$result_category = $stmt_category->get_result();
$category_data = [];
while ($row = $result_category->fetch_assoc()) {
    $category_data[] = $row;
}

// Return the data as JSON
echo json_encode([
    'spending_data' => $spending_data,
    'category_data' => $category_data,
]);
?>
