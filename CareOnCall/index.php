<?php
require_once '../php/config.php';

// Check if user is logged in and redirect to appropriate dashboard
if (is_logged_in()) {
    $user = get_current_user();
    if ($user['user_type'] === 'admin') {
        redirect('pages/admin_dashboard.php');
    } elseif ($user['user_type'] === 'caretaker') {
        redirect('pages/caretaker_dashboard.php');
    } else {
        redirect('pages/client_dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareOnCall - On-Demand Caretaker Booking</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>CareOnCall</h1>
            <ul>
                <li><a href="#about">About</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="pages/login.php" class="btn btn-primary">Login</a></li>
                <li><a href="pages/register.php" class="btn btn-secondary">Register</a></li>
            </ul>
        </div>
    </nav>

    <div class="hero">
        <div class="hero-content">
            <h1>CareOnCall</h1>
            <h2>On-Demand Caretaker Booking System</h2>
            <p>Find trusted caretakers for your loved ones in just a few clicks</p>
            <div class="cta-buttons">
                <a href="pages/login.php" class="btn btn-primary btn-lg">Login</a>
                <a href="pages/register.php" class="btn btn-secondary btn-lg">Register Now</a>
            </div>
        </div>
    </div>

    <section id="features" class="features">
        <h2>Our Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <h3>For Clients</h3>
                <ul>
                    <li>Browse verified caretakers</li>
                    <li>Easy booking system</li>
                    <li>View caretaker profiles</li>
                    <li>Rate and review</li>
                </ul>
            </div>

            <div class="feature-card">
                <h3>For Caretakers</h3>
                <ul>
                    <li>Create professional profile</li>
                    <li>Set availability schedule</li>
                    <li>Receive booking requests</li>
                    <li>Showcase your experience</li>
                </ul>
            </div>

            <div class="feature-card">
                <h3>For Admins</h3>
                <ul>
                    <li>Verify caretakers</li>
                    <li>Manage all users</li>
                    <li>Monitor bookings</li>
                    <li>Activity logging</li>
                </ul>
            </div>
        </div>
    </section>

    <section id="about" class="about">
        <h2>About CareOnCall</h2>
        <p>CareOnCall is a comprehensive platform designed to connect reliable caretakers with families in need of care services. We ensure quality care through a verification process and maintain transparency through ratings and reviews.</p>
    </section>

    <footer>
        <p>&copy; 2024 CareOnCall. All rights reserved.</p>
    </footer>
</body>
</html>
