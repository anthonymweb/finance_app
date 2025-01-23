<?php
include('../Templetes/session_check.php');
$conn = new mysqli("localhost", "root", "", "finance_app");

$id = $_GET['id'];
$query = "SELECT * FROM expenses WHERE id='$id' AND user_id='{$_SESSION['user_id']}'";
$result = $conn->query($query);
$expense = $result->fetch_assoc();

if (!$expense) {
    die("Expense not found or you don't have permission to edit this item.");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            background-color: #f8f9fa;
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

        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
            text-align: center;
        }

        .invalid-feedback {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card floating-form">
                    <div class="card-header">
                        <h3>Edit Expense</h3>
                    </div>
                    <div class="card-body">
                        <form action="../php/edit_expense.php" method="POST">
                            <input type="hidden" name="id" value="<?php echo $expense['id']; ?>">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="Tuition" <?php if ($expense['category'] == 'Tuition') echo 'selected'; ?>>Tuition</option>
                                    <option value="Food" <?php if ($expense['category'] == 'Food') echo 'selected'; ?>>Food</option>
                                    <option value="Transportation" <?php if ($expense['category'] == 'Transportation') echo 'selected'; ?>>Transportation</option>
                                    <option value="Entertainment" <?php if ($expense['category'] == 'Entertainment') echo 'selected'; ?>>Entertainment</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" value="<?php echo $expense['amount']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" value="<?php echo $expense['date']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description"><?php echo $expense['description']; ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Expense</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
