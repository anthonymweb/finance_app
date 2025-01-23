<div>
    <div class="profile text-center mb-4">
        <img src="../images/profile.jpg" alt="User Profile" class="img-fluid rounded-circle" width="80">
        <h5 class="mt-2"><?php echo htmlspecialchars($username); ?></h5>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item mb-2">
            <a href="dashboard.php" class="nav-link active">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="view_expenses.php" class="nav-link">
                <i class="fas fa-coins me-2"></i> Expenses
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="view_budget.php" class="nav-link">
                <i class="fas fa-chart-line me-2"></i> Budgets
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="reports.php" class="nav-link">
                <i class="fas fa-file-alt me-2"></i> Reports
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="../php/logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>
