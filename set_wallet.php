<?php
session_start();
include('../connection/db.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = $_POST['amount'] ?? 0;

if ($amount <= 0) {
    echo json_encode(['error' => 'Invalid amount']);
    exit;
}

// Update wallet balance
$query = "INSERT INTO wallet (user_id, amount) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("id", $user_id, $amount);
$stmt->execute();

// Log the activity
$logQuery = "INSERT INTO activity_logs (user_id, activity_type, description) VALUES (?, 'Wallet Update', ?)";
$logStmt = $conn->prepare($logQuery);
$description = "Wallet balance set to UGX " . number_format($amount, 0);
$logStmt->bind_param("is", $user_id, $description);
$logStmt->execute();

echo json_encode(['success' => true, 'message' => 'Wallet updated successfully']);
?>
