<?php
include('../Templetes/session_check.php');
$conn = new mysqli("localhost", "root", "", "finance_app");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user expenses with optional filtering
$user_id = $_SESSION['user_id'];

$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$date_filter = $_GET['date'] ?? '';

$query = "SELECT * FROM expenses WHERE user_id = '$user_id'";
$conditions = [];

if (!empty($search)) {
    $conditions[] = "(category LIKE '%$search%' OR description LIKE '%$search%')";
}
if (!empty($category_filter)) {
    $conditions[] = "category = '$category_filter'";
}
if (!empty($date_filter)) {
    $conditions[] = "DATE(date) = '$date_filter'";
}

if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY date DESC";
$result = $conn->query($query);

// Fetch unique categories for the filter dropdown
$category_query = "SELECT DISTINCT category FROM expenses WHERE user_id = '$user_id'";
$categories_result = $conn->query($category_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Expenses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet"> <!-- FontAwesome Icons -->
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
    flex: 1; /* Ensure the header text is centered */
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
          /* Add spacing for the filter and search section */
          .filter-section {
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
        }

        .filter-section .form-control, .filter-section .btn {
            min-width: 150px;
        }

        .table {
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1><i class="fas fa-coins"></i> Expenses</h1>
        <style>
            .header h1{
                text-align:center;
            }
        </style>
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
            <h3 class="text-center">Your Expenses</h3>

            <!-- Notification section for success message -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Expense added successfully!</div>
            <?php endif; ?>

            <!-- Notification section for edit and delete -->
            <?php if (isset($_GET['updated'])): ?>
                <div class="alert alert-success">Expense updated successfully!</div>
            <?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?>
                <div class="alert alert-success">Expense deleted successfully!</div>
            <?php endif; ?>

            <!-- Session notification -->
            <?php if (isset($_SESSION['notification'])): ?>
                <div class="alert alert-warning text-center">
                    <?php 
                        echo $_SESSION['notification']; 
                        unset($_SESSION['notification']); // Clear notification after displaying
                    ?>
                </div>
            <?php endif; ?>

             <!-- Search and Filter Section -->
             <form method="GET" class="filter-section">
                <input type="text" name="search" class="form-control" placeholder="Search by keyword..." value="<?php echo htmlspecialchars($search); ?>">
                
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <?php while ($category = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo $category['category']; ?>" <?php echo $category_filter == $category['category'] ? 'selected' : ''; ?>>
                            <?php echo $category['category']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date_filter); ?>">

                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Apply Filters</button>
                <a href="view_expenses.php" class="btn btn-secondary"><i class="fas fa-times"></i> Clear Filters</a>
            </form>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['category']; ?></td>
                                <td>UGX <?php echo number_format($row['amount'], 0); ?></td>
                                <td><?php echo $row['date']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td>
                                    <a href="edit_expense.php?id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="../php/delete_expense.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No expenses found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>


            <!-- Exporting report -->
            <div class="text-end">
                <a href="../php/export_csv.php?type=expenses" class="btn btn-success"><i class="fas fa-file-csv"></i> Export Expenses as CSV</a>
                <a href="../php/export_pdf.php?type=expenses" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Export Expenses as PDF</a>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
            <a href="add_expense.php" class="btn btn-primary">Add New Expense</a>
        </div>
    </div>

    <!-- JavaScript -->
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
    </script>
</body>
</html>
