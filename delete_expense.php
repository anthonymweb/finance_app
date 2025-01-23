<?php
session_start();
$conn = new mysqli("localhost", "root", "", "finance_app");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Verify the user owns the expense
    $query = "DELETE FROM expenses WHERE id='$id' AND user_id='$user_id'";
    if ($conn->query($query) === TRUE) {
        header("Location: ../views/view_expenses.php?deleted=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
// After adding a new expense
$logQuery = "INSERT INTO activity_logs (user_id, activity_type, description) 
             VALUES (?, ?, ?)";
$stmt_log = $conn->prepare($logQuery);
$stmt_log->bind_param("iss", $user_id, $activity_type, $description);

// Example: Adding an expense
$activity_type = 'Added Expense';
$description = 'Added a new expense of UGX 5000 for groceries.';
$stmt_log->execute();

?>
