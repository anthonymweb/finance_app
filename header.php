<?php
// header.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection if needed
include('../connection/db.php');

// Fetch username for display in the header
$user_id = $_SESSION['user_id'];
$stmt_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$username = $user['username'];
?>

<div class="header">
    <h1>Dashboard</h1>
    <div class="notification-bell dropdown">
        <i class="fas fa-bell" data-bs-toggle="dropdown" aria-expanded="false"></i>
        <span class="badge bg-danger rounded-circle" id="notification-count" style="font-size: 0.8rem; position: absolute; top: 0; right: -5px;">
            <?php
            $notifQuery = "SELECT COUNT(*) as count FROM notifications WHERE user_id = $user_id AND is_read = 0";
            $notifResult = $conn->query($notifQuery);
            $notifCount = $notifResult->fetch_assoc()['count'];
            echo $notifCount;
            ?>
        </span>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
            <?php
            $notifQuery = "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 10";
            $notifResult = $conn->query($notifQuery);
            if ($notifResult->num_rows > 0) {
                while ($row = $notifResult->fetch_assoc()) {
                    echo "<li class='notification-item'>" . htmlspecialchars($row['message']) . "</li>";
                }
            } else {
                echo "<li class='notification-item text-muted'>No notifications</li>";
            }
            ?>
        </ul>
    </div>
</div>
