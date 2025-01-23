<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
include('../connection/db.php');

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt_user = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$username = $user['username'];

// Fetch wallet data (total wallet balance)
$walletQuery = "SELECT SUM(amount) AS total_wallet FROM wallet WHERE user_id = ?";
$stmt_wallet = $conn->prepare($walletQuery);
$stmt_wallet->bind_param("i", $user_id);
$stmt_wallet->execute();
$walletResult = $stmt_wallet->get_result();
$totalWallet = $walletResult->fetch_assoc()['total_wallet'] ?? 0; // Default to 0 if no result

// Fetch total spent amount from expenses
$walletQuery = "SELECT SUM(amount) AS total_wallet FROM wallet WHERE user_id = $user_id";
$walletResult = $conn->query($walletQuery);
$totalWallet = $walletResult->fetch_assoc()['total_wallet'] ?? 0;

$expensesQuery = "SELECT SUM(amount) AS total_spent FROM expenses WHERE user_id = $user_id";
$expensesResult = $conn->query($expensesQuery);
$totalSpent = $expensesResult->fetch_assoc()['total_spent'] ?? 0;

// Calculate Money Remaining (Total Wallet - Total Spent)
$totalRemaining =  $totalSpent - $totalWallet;

// Fetch spending data for the line chart (monthly spending)
$stmt_spending = $conn->prepare("SELECT MONTHNAME(date) AS month, SUM(amount) AS total FROM transactions WHERE user_id = ? GROUP BY MONTH(date) ORDER BY MONTH(date)");
$stmt_spending->bind_param("i", $user_id);
$stmt_spending->execute();
$result_spending = $stmt_spending->get_result();
$spending_data = [];
while ($row = $result_spending->fetch_assoc()) {
    $spending_data[] = $row;
}

// Fetch category breakdown for the doughnut chart
$stmt_category = $conn->prepare("SELECT category, SUM(amount) AS total FROM transactions WHERE user_id = ? GROUP BY category");
$stmt_category->bind_param("i", $user_id);
$stmt_category->execute();
$result_category = $stmt_category->get_result();
$category_data = [];
while ($row = $result_category->fetch_assoc()) {
    $category_data[] = $row;
}

// Fetch unread notifications count
$unreadQuery = "SELECT COUNT(*) as unread FROM notifications WHERE user_id = ? AND is_read = FALSE";
$stmt = $conn->prepare($unreadQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$unreadResult = $stmt->get_result()->fetch_assoc();
$query = "SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $updateQuery = "UPDATE notifications SET is_read = TRUE WHERE user_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        /* Styling for the header, sidebar, and main content */
        .header {
            width: 100%;
            height: 70px;
            background-color: #5ec4dc;
            color: black;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 1.5rem;
            text-align: center;
            flex-grow: 1;
        }

        .notification-bell {
            position: relative;
            cursor: pointer;
            color: black;
        }

        .notification-bell i {
            font-size: 1.5rem;
        }

        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
        }

        /* Sidebar Styles */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 70px;
            left: -250px; /* Initially hidden */
            background-color: #343a40;
            padding-top: 20px;
            transition: left 0.3s ease;
            z-index: 1000;
        }

        .sidebar.show {
            left: 0; /* Slide in */
        }

        .sidebar a {
            display: block;
            padding: 10px 20px;
            font-size: 18px;
            color:#ced4da;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .sidebar a:hover {
            color: #5ec4dc;
            background-color: #495057;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: 0;
            margin-top: 80px; /* Space for header */
            transition: margin-left 0.3s ease;
            padding: 20px;
        }

        .main-content.shift {
            margin-left: 250px; /* Shift when sidebar is shown */
        }

        /* Toggle Button Styles */
        .toggle-btn {
            font-size: 24px;
            cursor: pointer;
            background-color: #5ec4dc;
            color: white;
            border: none;
            padding: 10px 20px;
            position: static;
            top: 10px;
            left: 10px;
            z-index: 1001;
        }

        .main-content {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease;
            padding-top: 90px;
        }

        .main-content.shift {
            margin-left: 250px;
        }

        .wallet-totals {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            margin-top: 30px;
        }

        .wallet-total-card {
            padding: 15px;
            text-align: center;
            border-radius: 10px;
            width: 28%;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .wallet-total-card:hover {
            background-color: #f0f0f0;
            transform: scale(1.05);
        }

        .btn-section {
            margin-top: 30px;
            text-align: center;
        }

        .btn-section button {
            margin: 0 10px;
            padding: 10px 15px;
            font-size: 1rem;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-section button:hover {
            background-color: #007bff;
            color: #fff;
        }
          /* Custom styles for the graphs */
          .chart-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            margin-top: 30px;
        }

        .chart-container .card {
            width: 48%;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <button class="toggle-btn" onclick="toggleSidebar()">&#9776;</button>
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <div class="dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-bell"></i>
        <span class="badge bg-danger rounded-circle" id="notificationCount">
            <?php echo $unreadResult['unread']; ?>
        </span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
                <li class="dropdown-item">
                    <?php echo htmlspecialchars($notification['message']); ?>
                    <small class="text-muted d-block"><?php echo $notification['created_at']; ?></small>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="dropdown-item text-muted">No notifications</li>
        <?php endif; ?>
    </ul>
</div>
<form method="POST" action="dashboard.php">
    <button type="submit" name="mark_read" class="btn btn-link">Mark all as read</button>
</form>
</div>


    <script>
        function fetchNotifications() {
    fetch('php/fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('notificationCount').innerText = data.unreadCount;
            const dropdownMenu = document.querySelector('.dropdown-menu');
            dropdownMenu.innerHTML = data.notifications.map(notification => `
                <li class="dropdown-item">
                    ${notification.message}
                    <small class="text-muted d-block">${notification.created_at}</small>
                </li>
            `).join('');
        });
}

setInterval(fetchNotifications, 10000); // Fetch notifications every 10 seconds
    </script>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <!-- <a href="#" class="toggle-btn" onclick="toggleSidebar()">&#9776;</a> -->
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="view_expenses.php"><i class="fas fa-money-bill-wave"></i> Expenses</a>
        <a href="view_budget.php"><i class="fas fa-wallet"></i> Budgets</a>
        <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
<!-- Main Content -->
    <div class="main-content">
    <h1>Hi, <?php echo htmlspecialchars($username); ?> Let's start off from where you left off !</h1>
    <style>
        .main-content h1{
            font-size: 30px;
            font-style: italic;
        }
    </style>
        <!-- Wallet Totals Section -->
       
<div class="wallet-totals">
    <div class="card wallet-total-card">
        <p><strong>Total Wallet Balance:</strong></p>
        <p>UGX <?php echo number_format($totalWallet, 0); ?></p>
        
    </div>
    <div class="card wallet-total-card">
        <p><strong>Total Spent:</strong></p>
        <p>UGX <?php echo number_format($totalSpent, 0); ?></p>
    </div>
    <div class="card wallet-total-card">
        <p><strong>Total Remaining:</strong></p>
        <p>UGX <?php echo number_format($totalRemaining, 0); ?></p>
    </div>
</div>

        <!-- Button Section -->
        <div class="btn-section">
           <a href="add_money.php"><button class="btn btn-success">Add Money</button></a>  <!-- Link to Add Money page -->
            <a href="add_budget.php"><button class="btn btn-primary">Add to Budget</button></a>
            <a href="add_expense.php"><button class="btn btn-success">Add Expense</button></a>
        </div>

        <!-- Chart Section -->
        <div class="chart-container">
    <div class="card">
        <h3 class="text-center">Spending Overview</h3>
        <canvas id="spendingChart"></canvas>
    </div>
    <div class="card">
        <h3 class="text-center">Category Breakdown</h3>
        <canvas id="categoryDoughnutChart" style="max-width: 300px; max-height: 300px;"></canvas>
    </div>
</div>

    <!-- JavaScript -->
    <script>
        let sidebarOpen = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleButton = document.querySelector('.toggle-btn');
            const mainContent = document.querySelector('.main-content');

            if (sidebarOpen) {
                sidebar.classList.remove('show');
                toggleButton.style.left = '10px';
                mainContent.classList.remove('shift');
            } else {
                sidebar.classList.add('show');
                toggleButton.style.left = '260px';
                mainContent.classList.add('shift');
            }

            sidebarOpen = !sidebarOpen;
        }

       // Ensure data is passed correctly
       const spendingData = <?php echo json_encode($spending_data); ?>;
        const categoryData = <?php echo json_encode($category_data); ?>;


    
document.addEventListener("DOMContentLoaded", function () {
    // Fetch graph data
    fetch('../php/fetch_graph_data.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Line Chart for Spending Overview
            const spendingData = data.spending_data;
            const spendingChart = document.getElementById('spendingChart').getContext('2d');
            new Chart(spendingChart, {
                type: 'bar',
                data: {
                    labels: spendingData.map(item => item.month),
                    datasets: [{
                        label: 'Total Spending (UGX)',
                        data: spendingData.map(item => item.total),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Amount (UGX)' }
                        },
                        x: {
                            title: { display: true, text: 'Months' }
                        }
                    }
                }
            });

            // Doughnut Chart for Category Breakdown
            const categoryData = data.category_data;
            const categoryDoughnutChart = document.getElementById('categoryDoughnutChart').getContext('2d');
            new Chart(categoryDoughnutChart, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.category),
                    datasets: [{
                        data: categoryData.map(item => item.total),
                        backgroundColor: [
                            '#ff6384', '#36a2eb', '#ffcd56', '#4bc0c0', '#9966ff'
                        ],
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching graph data:', error));
});

</script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </script>
</body>
</html>
