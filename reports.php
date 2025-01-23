<?php
// Include session check and database connection
session_start();
include('../connection/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Fetch recent activity logs for the user
$query = "SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Export logs as CSV
if (isset($_GET['export_csv'])) {
    $filename = "activity_logs.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Activity Type', 'Description', 'Timestamp']); // Column headings

    $result = $conn->query("SELECT * FROM activity_logs WHERE user_id = $user_id ORDER BY created_at DESC");

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['activity_type'], $row['description'], $row['created_at']]);
    }

    fclose($output);
    exit;
}

// Export logs as PDF using TCPDF
if (isset($_GET['export_pdf'])) {
    require_once('../php/tcpdf/tcpdf.php');


    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    // Add table headers
    $pdf->Cell(60, 10, 'Activity Type', 1);
    $pdf->Cell(60, 10, 'Description', 1);
    $pdf->Cell(60, 10, 'Timestamp', 1);
    $pdf->Ln();

    $result = $conn->query("SELECT * FROM activity_logs WHERE user_id = $user_id ORDER BY created_at DESC");

    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(60, 10, $row['activity_type'], 1);
        $pdf->Cell(60, 10, $row['description'], 1);
        $pdf->Cell(60, 10, $row['created_at'], 1);
        $pdf->Ln();
    }

    $pdf->Output('activity_logs.pdf', 'D');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Activity Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <!-- Header -->
    <div class="header">
        <button class="toggle-btn" onclick="toggleSidebar()">&#9776;</button>
        <h1><i class="fas fa-chart-line"></i>Financial Statement</h1>
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

        <div class="container">
            <h3 class="text-center">Activity Report </h3>

            <!-- Notification Section -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Activity added successfully!</div>
            <?php endif; ?>

            <!-- Activity Logs Table -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Activity Type</th>
                        <th>Description</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Export Buttons -->
    <div class="text-end mt-4">
                <a href="?export_csv=true" class="btn btn-success"><i class="fas fa-file-csv"></i> Export as CSV</a>
                <a href="?export_pdf=true" class="btn btn-danger"><i class="fas fa-file-pdf"></i>Export as PDF</a>
            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
