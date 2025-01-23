<?php
include('../Templetes/session_check.php');
$conn = new mysqli("localhost", "root", "", "finance_app");

// Fetch budgets and expenses
$user_id = $_SESSION['user_id'];
$query = "SELECT b.category, b.budget_limit, b.amount_spent, (b.budget_limit - b.amount_spent) AS remaining
          FROM budgets b WHERE b.user_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$categories = [];
$limits = [];
$spent = [];
$remainings = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = $row['category'];
    $limits[] = $row['budget_limit'];
    $spent[] = $row['amount_spent'];
    $remainings[] = $row['remaining'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome Icons -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
  /* General Styles */
  body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        /* Header Styles */
        .header {
            width: 100%;
            height: 70px;
            background-color: #5ec4dc;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center; /* Center items horizontally */
            padding: 0 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 1.5rem;
            margin: 0;
            text-align: center;
        }

        .header .user-info {
            font-size: 1rem;
            position: absolute;
            right: 20px; /* Position user info to the right */
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
            color: #ced4da;
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
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1001;
        }

        /* Table Styles */
        .table {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            vertical-align: middle;
        }


         /* Button Styles */
         .btn {
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
         /* Responsiveness */
         @media (max-width: 767px) {
            .header h1 {
                font-size: 1.2rem;
            }

            .header .user-info {
                font-size: 0.8rem;
            }

            .sidebar a {
                font-size: 16px;
            }

            .toggle-btn {
                font-size: 20px;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
<body>
    <!-- Header -->
    <div class="header">
        <h1><i class="fas fa-wallet"></i> Budget Management</h1>
        <div class="user-info">Logged in as: <?php echo $_SESSION['username']; ?></div>
    </div>
   
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <a href="#" class="toggle-btn" onclick="toggleSidebar()">&#9776;</a>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="view_expenses.php"><i class="fas fa-money-bill-wave"></i> Expenses</a>
        <a href="view_budget.php"><i class="fas fa-wallet"></i> Budgets</a>
        <a href="reports.php"><i class="fas fa-chart-line"></i> Reports</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

   <!-- Main Content -->
   <div class="main-content">
        <button class="toggle-btn" onclick="toggleSidebar()">&#9776;</button>

        <div class="container mt-5">
            <h3 class="text-center">Budget Status</h3>
            <?php if (isset($_SESSION['notification'])): ?>
                <div class="alert alert-warning text-center">
                    <?php 
                        echo $_SESSION['notification']; 
                        unset($_SESSION['notification']); // Clear notification after displaying
                    ?>
                </div>
            <?php endif; ?>

        <!-- Budget Table -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>Category</th>
            <th>Budget Limit</th>
            <th>Amount Spent</th>
            <th>Remaining</th>
            <th>Status</th> <!-- New Status Column -->
        </tr>
    </thead>
    <tbody>
        <?php for ($i = 0; $i < count($categories); $i++): ?>
            <tr>
                <td><?php echo $categories[$i]; ?></td>
                <td>UGX <?php echo number_format($limits[$i], 0); ?></td>
                <td>UGX <?php echo number_format($spent[$i], 0); ?></td>
                <td>UGX <?php echo number_format($remainings[$i], 0); ?></td>
                <td>
                    <?php if ($remainings[$i] >= 0): ?>
                        <span class="badge bg-success">On Budget</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Over Budget</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>

            <!-- Budget vs Spending Chart -->
            <div class="mt-5">
                <h3 class="text-center">Budget vs Spending</h3>
                <canvas id="budgetChart"></canvas>
            </div>

            <!-- Export Buttons -->
            <div class="text-end mt-4">
                <a href="../php/export_csv.php?type=budgets" class="btn btn-success"><i class="fas fa-file-csv"></i> Export Budget as CSV</a>
                <a href="../php/export_pdf.php?type=budgets" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Export Budget as PDF</a>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
            <a href="add_budget.php" class="btn btn-primary">Add New budget</a>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let sidebarOpen = false;

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');

            if (sidebarOpen) {
                sidebar.classList.remove('show');
                mainContent.classList.remove('shift');
            } else {
                sidebar.classList.add('show');
                mainContent.classList.add('shift');
            }

            sidebarOpen = !sidebarOpen;
        }

        // Budget Chart
        const ctx = document.getElementById('budgetChart').getContext('2d');
        const budgetChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($categories); ?>,
                datasets: [
                    {
                        label: 'Budget Limit (UGX)',
                        data: <?php echo json_encode($limits); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    },
                    {
                        label: 'Amount Spent (UGX)',
                        data: <?php echo json_encode($spent); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (UGX)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Categories'
                        }
                    }
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
