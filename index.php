<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Finance Tracker</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- MDB CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.1/mdb.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom Styling */
        body {
            font-family: 'Roboto', sans-serif;
            overflow-x: hidden;
            opacity: 0; /* Initial opacity for transition */
            transform: translateX(-100%); /* Initial slide position */
            transition: transform 1s ease-out, opacity 1s ease-out; /* Smooth slide and fade-in */
        }

        body.loaded {
            opacity: 1;
            transform: translateX(0); /* Reset to normal position */
        }

        .carousel-item {
            height: 100vh;
            background-size: cover;
            background-position: center;
        }

        .carousel-caption {
            top: 50%;
            transform: translateY(-50%);
            animation: fadeIn 2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .carousel-caption h5 {
            font-size: 3rem;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.7);
        }

        .carousel-caption p {
            font-size: 1.5rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        .btn-custom {
            background-color: #f8c445;
            border: none;
            color: #000;
            font-size: 1.2rem;
            transition: all 0.4s ease;
        }

        .btn-custom:hover {
            background-color: #f7b600;
            color: white;
            transform: scale(1.1);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }

        footer {
            background-color: #5ec4dc;
            color: black;
            padding: 20px;
            text-align: center;
        }
        
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top" style="background-color: #5ec4dc;">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" style="color: black;">Finance Tracker</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- <li class="nav-item">
                    <a class="nav-link" href="#features" style="color: black;">Features</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about" style="color: black;">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-dark btn-sm" href="http://localhost/finance_app/views/login.php" style="color: black;">Login</a>
                    <a href=""></a> -->
                </li>
            </ul>
        </div>
    </div>
</nav>


<!-- Full Page Image Carousel -->
<div id="carouselExampleCaptions" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active" style="background-image: url('../images/Tracker.jpg');">
            <div class="carousel-caption text-center">
                <h5>Track Your Finances</h5>
                <p>Effortlessly monitor your income, expenses, and budgets.</p>
                <a href="register.php" class="btn btn-custom">Get Started</a>
            </div>
        </div>
        <!-- Slide 2 -->
        <div class="carousel-item" style="background-image: url('../images/reports.jpg');">
            <div class="carousel-caption text-center">
                <h5>Insightful Reports</h5>
                <p>Analyze your spending patterns with advanced charts.</p>
                <a href="login.php" class="btn btn-custom">Learn More</a>
            </div>
        </div>
        <!-- Slide 3 -->
        <div class="carousel-item" style="background-image: url('../images/security.jpg');">
            <div class="carousel-caption text-center">
                <h5>Secure and Private</h5>
                <p>Your financial data is always protected and accessible only to you.</p>
                <a href="login.php" class="btn btn-custom">Explore Features</a>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>

<!-- Features Section -->
<section id="features" class="container py-5">
    <h2 class="text-center mb-4">Key Features</h2>
    <div class="row text-center">
        <div class="col-md-4">
            <i class="feature-icon fas fa-wallet text-primary"></i>
            <h5>Budget Tracking</h5>
            <p>Set, manage, and optimize your budgets with ease.</p>
        </div>
        <div class="col-md-4">
            <i class="feature-icon fas fa-chart-pie text-success"></i>
            <h5>Expense Analysis</h5>
            <p>Get detailed insights into your spending habits.</p>
        </div>
        <div class="col-md-4">
            <i class="feature-icon fas fa-lock text-danger"></i>
            <h5>Secure Data</h5>
            <p>Your financial information is encrypted and safe.</p>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <p>&copy; 2024 FinanceApp. All Rights Reserved.</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Smooth scrolling for navbar links
    document.querySelectorAll('a.nav-link').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });

    // Slide-in transition on page load
    window.addEventListener('load', function () {
        document.body.classList.add('loaded');
    });
</script>
</body>
</html>
