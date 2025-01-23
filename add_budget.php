<?php include('../Templetes/session_check.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Set Budget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            background-color: rgba(115,137,147,255);
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card floating-form">
                    <div class="card-header">
                        <h3>Set Budget</h3>
                    </div>
                    <div class="card-body">
                        <form action="../php/add_budget.php" method="POST">
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="Tuition">Tuition</option>
                                    <option value="Food">Food</option>
                                    <option value="Transportation">Transportation</option>
                                    <option value="Entertainment">Entertainment</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="budget_limit" class="form-label">Budget Limit</label>
                                <input type="number" class="form-control" id="budget_limit" name="budget_limit" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Save Budget</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
