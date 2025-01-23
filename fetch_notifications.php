<?php
session_start();
include('db.php');

$user_id = $_SESSION['user_id'];
$query = "SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

$unreadQuery = "SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = FALSE";
$stmt_unread = $conn->prepare($unreadQuery);
$stmt_unread->bind_param("i", $user_id);
$stmt_unread->execute();
$unreadCount = $stmt_unread->get_result()->fetch_assoc();

echo json_encode([
    'notifications' => $notifications,
    'unreadCount' => $unreadCount['unread']
]);
?>
