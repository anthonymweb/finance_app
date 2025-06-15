Personal Finance Management Application: Project Documentation

# App Name : Finance Tracker

# Table of Contents
1. Introduction
2. Objectives
3. Requirements
4. System Architecture
5. Modules and Features
6. Development Workflow
7. Database Design
8. Code Overview
9. Testing and Debugging
10. Challenges and Solutions
11. Conclusion and Future Enhancements


# 1. Introduction

The Personal Finance Management Application is designed to help users/students track their finances effectively. The application allows users to manage wallets, set budgets, track expenses, generate reports, and visualize data through interactive graphs. 

The application is a web app, built using PHP, MySQL, Bootstrap, and JavaScript, and ensures secure user authentication and responsive design.


# 2. Objectives

- Primary Goals:
  - secure authentication system
  - Enable users to manage wallets and set budgets.
  - Allow tracking of expenses.
  - Visualize financial data through graphs and charts.
  - Provide exportable reports in CSV and PDF formats.
  - Maintain activity logs for auditing user actions.

- Secondary Goals:
  - Ensure secure user authentication.
  - Implement responsive design for multi-device accessibility.
  - Log activities to provide insights into user behavior.



# 3. Requirements

# 3.1 Functional Requirements
- User Registration and Login.
- Wallet Management: Adding, viewing, and updating balances.
- Budget Management: Setting and tracking budgets.
- Expense Management: Adding, editing, and deleting expenses.
- Interactive Dashboard: Displaying financial insights using charts.
- Reports: Exporting data as CSV and PDF.
- Notifications: Real-time updates for key actions.

# 3.2 Non-Functional Requirements
- Responsive Design: Optimized for mobile and desktop devices.
- Secure Authentication: Password hashing and session management.
- Scalability: Modular design for future feature enhancements.



# 4. System Architecture

# 4.1 Frontend
- Technologies: HTML, CSS, JavaScript, Bootstrap.
- Purpose: Create a responsive and interactive user interface.

# 4.2 Backend
- Technologies: PHP, MySQL.
- Purpose: Handle business logic, database operations, and user authentication.

# 4.3 Database
- Technology: MySQL.
- Purpose: Store and manage data for users, expenses, budgets, and activity logs.



# 5. Modules and Features

# 5.1 User Management
- Features:
  - Registration with email validation.
  - Login with session management.
  - Password hashing for security.

# 5.2 Wallet Management
- Features:
  - Adding funds to wallets.
  - Deducting funds for budgets or expenses.
  - Viewing total wallet balance.

# 5.3 Budget Management
- Features:
  - Setting category-specific budgets.
  - Viewing budget limits and remaining amounts.

# 5.4 Expense Management
- Features:
  - Adding expenses with descriptions and categories.
  - Viewing and editing expense records.
  - Ensuring expenses do not exceed wallet balance.

# 5.5 Dashboard
- Features:
  - Interactive line and doughnut charts.
  - Real-time updates for expenses and budgets.
  - Overview of wallet, spending, and remaining balance.

# 5.6 Reports
- Features:
  - Exporting expenses and activity logs as CSV or PDF.
  - Viewing recent activity logs.

# 5.7 Notifications
- Features:
  - Bell icon for real-time updates.
  - Dropdown displaying recent notifications.



# 6. Development Workflow

1. Planning: Identified objectives and requirements.
2. UI Design: Created wireframes and prototypes.
3. Backend Setup: Designed database schema and implemented core logic.
4. Frontend Integration: Linked the backend with the user interface.
5. Testing: Debugged and validated features.
6. Deployment: Prepared the project for presentation.


# 7. Database Design

# 7.1 Tables
1. Users/students: 
   - Stores user credentials and details.
2. Wallet: 
   - Tracks wallet transactions.
3. Expenses: 
   - Stores expense details categorized by type.
4. Budgets: 
   - Tracks budgets set by the user/student.
5. Activity Logs: 
   - Maintains a log of user actions.


# 8. Code Overview

# 8.1 Registration
- Securely registers users and prevents duplicate emails.

# 8.2 Login
- Implements session-based authentication.

# 8.3 Wallet and Expenses
- Deducts expenses from wallets and validates balance.

# 8.4 Dashboard
- Displays real-time data using Chart.js.

# 8.5 Reports
- Generates exportable files using libraries like TCPDF.



# 9. Testing and Debugging

# 9.1 Testing
- Unit Tests: Validated individual functions.
- Integration Tests: Ensured seamless interaction between modules.
- User Acceptance Tests: Gathered feedback for improvements.

# 9.2 Debugging
- Addressed errors like "undefined variable" and SQL syntax issues.
- Fixed data inconsistencies in charts and reports.



# 10. Challenges and Solutions

# 10.1 Dynamic Data in Charts
Challenge: Linking real-time data with charts.  
Solution: Fetched and updated data using PHP and JSON.

# 10.2 Expense Deduction Validation
Challenge: Ensuring expenses did not exceed wallet balance.  
Solution: Validated wallet balance before processing expenses.

# 10.3 Export Functionality
Challenge: Formatting data for CSV and PDF exports.  
Solution: Utilized PHP libraries like TCPDF for PDF generation.



# 11. Conclusion and Future Enhancements

# 11.1 Conclusion
The project successfully achieves its goals of providing users with a robust personal finance management tool. The application offers an intuitive user experience, secure backend, and dynamic data visualization.

# 11.2 Future Enhancements
- Mobile Application: Extend functionality to mobile platforms.
- AI Recommendations: Provide spending and saving suggestions.
- Recurring Budgets: Automate monthly budget resets.
- Multi-Currency Support: Enable users to manage finances in different currencies.



## CODE FLOW


## 3. Database Design
The Financial Dashboard Web Application uses the following database tables:

### 3.1 Users Table
This table stores user information.

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 3.2 Expenses Table
This table stores the user's expenses.

```sql
CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    date DATE NOT NULL DEFAULT CURRENT_DATE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 3.3 Budget Table
This table stores the user's budget.

```sql
CREATE TABLE budget (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 3.4 Activity Logs Table (Optional)
This table logs the user's activities, such as adding or updating expenses.

```sql
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_type VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```



## 4. Backend Implementation
The backend of the application is built using PHP. Key components of the backend are described below.

### 4.1 Session Management
The application uses PHP sessions to handle user login and ensure that only authenticated users can access the dashboard.

```php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
```

### 4.2 Expense and Budget Management
PHP handles the addition of expenses and budgets, including inserting data into the database.

```php
// Example of adding an expense
$query = "INSERT INTO expenses (user_id, category, amount, description) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("isds", $user_id, $category, $amount, $description);
$stmt->execute();
```

### 4.3 Chart Data
Data for the charts (Expense Breakdown, Monthly Spending Trend) is fetched from the database and passed to the frontend using PHP.

```php
// Example query for expense breakdown
$expenseBreakdownQuery = "
    SELECT category, SUM(amount) AS total 
    FROM expenses 
    WHERE user_id = ? 
    GROUP BY category
";
```



## 5. Frontend Implementation
The frontend is built using HTML, CSS, Bootstrap, and Chart.js for displaying the financial data and graphs.

### 5.1 Dashboard Layout
The dashboard displays total balance, income, and expenses, along with graphs for expense breakdown and spending trends.

```html
<!-- Dashboard example with Bootstrap cards -->
<div class="col-md-4">
    <div class="card shadow-sm p-3 mb-4">
        <h5 class="card-title">Total Balance</h5>
        <h3 class="text-success">UGX <?php echo number_format($totalBalance, 0); ?></h3>
    </div>
</div>
```

### 5.2 Responsive Design
The application is designed using Bootstrap to ensure it looks good on various devices. For example:

```html
<div class="container mt-4">
    <div class="row text-center">
        <div class="col-md-4">
            <div class="card shadow-sm p-3 mb-4">
                <h5 class="card-title">Total Balance</h5>
                <h3 class="text-success">UGX <?php echo number_format($totalBalance, 0); ?></h3>
            </div>
        </div>
    </div>
</div>
```

### 5.3 Charts
Chart.js is used to display graphical representations of the data, such as pie charts for the expense breakdown and line charts for monthly spending trends.

```javascript
// Expense Breakdown Chart
const expenseCtx = document.getElementById('expenseChart').getContext('2d');
new Chart(expenseCtx, {
    type: 'pie',
    data: {
        labels: ['Food', 'Transport', 'Rent'],
        datasets: [{
            data: [400, 150, 300],
            backgroundColor: ['#4CAF50', '#FFC107', '#2196F3']
        }]
    }
});
```



## 6. Security
- Password Hashing: User passwords are securely stored using `password_hash()` to prevent storing plain text passwords.
- SQL Injection Prevention: Prepared statements are used for executing SQL queries, preventing SQL injection attacks.
- Session Management: PHP sessions are used for user authentication. If a user is not logged in, they are redirected to the login page.



## 7. How to Run the Application
### 7.1 Installation
1. Install XAMPP to set up a local server.
2. Create a new database (`finance_app`) in phpMyAdmin.
3. Run the SQL queries provided to create the necessary tables (`users`, `expenses`, `budget`, etc.).
4. Make sure that the `db.php` file has the correct credentials for connecting to your MySQL database.

### 7.2 Running the Application
1. Start the Apache and MySQL servers in XAMPP.
2. Open your browser and navigate to `http://localhost/finance_app/views/`.
