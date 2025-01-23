<?php
session_start();
include('../connection/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user ID from session
$user_id = $_SESSION['user_id'];

// Process the "Add Money" form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount'])) {
    $amount = $_POST['amount'];

    // Validate the amount (ensure it's a positive number)
    if ($amount <= 0) {
        $error = "Amount must be greater than zero.";
    } else {
        // Add money to wallet
        $stmt = $conn->prepare("INSERT INTO wallet (user_id, amount) VALUES (?, ?)");
        $stmt->bind_param("id", $user_id, $amount);
        if ($stmt->execute()) {
            $success = "Money added successfully!";
        } else {
            $error = "Failed to add money. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Money to Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            background-color:rgba(115,137,147,255);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .floating-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .floating-form:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 10px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 10px;
            text-align: center;
        }

        .alert {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card floating-form">
                    <div class="card-header">
                        <h1>Add Money to Wallet</h1>
                    </div>
                    <div class="card-body">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success"><?= $success; ?></div>
                        <?php endif; ?>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount to Add (UGX)</label>
                                <input type="number" class="form-control" id="amount" name="amount" min="1" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Money</button>
                        </form>

                        <div class="mt-3">
                            <a href="dashboard.php" class="btn btn-secondary w-100">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
