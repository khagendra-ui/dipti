<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('client')) {
    redirect('login.php');
}

$user = get_current_user();
$client_id = $user['id'];

// Get client's bookings
$bookings_result = $conn->query("
    SELECT b.*, u.name as caretaker_name, u.phone as caretaker_phone
    FROM bookings b
    JOIN users u ON b.caretaker_id = u.id
    WHERE b.client_id = $client_id
    ORDER BY b.booking_date DESC
    LIMIT 10
");

// Get available caretakers
$caretakers_result = $conn->query("
    SELECT u.*, cd.skills, cd.hourly_rate, cd.experience_years
    FROM users u
    JOIN caretaker_details cd ON u.id = cd.user_id
    WHERE u.user_type = 'caretaker' AND cd.verification_status = 'approved'
    ORDER BY u.name
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - CareOnCall</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>CareOnCall</h1>
            <ul>
                <li><a href="client_dashboard.php">Dashboard</a></li>
                <li><a href="browse_caretakers.php">Browse Caretakers</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h2>Welcome back, <?php echo $user['name']; ?></h2>
            <p>Find and book reliable caretakers for your loved ones</p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Quick Stats</h3>
                <p>Total Bookings: <strong><?php echo $bookings_result->num_rows; ?></strong></p>
                <p><a href="my_bookings.php" class="btn btn-small">View All</a></p>
            </div>

            <div class="card">
                <h3>Find a Caretaker</h3>
                <p>Browse through our verified caretakers</p>
                <p><a href="browse_caretakers.php" class="btn btn-small">Browse</a></p>
            </div>

            <div class="card">
                <h3>New Booking</h3>
                <p>Create a new booking request</p>
                <p><a href="book_caretaker.php" class="btn btn-small">Book Now</a></p>
            </div>
        </div>

        <div class="recent-bookings">
            <h3>Recent Bookings</h3>
            <?php if ($bookings_result->num_rows > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Caretaker</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $booking['caretaker_name']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                                <td><?php echo $booking['start_time']; ?> - <?php echo $booking['end_time']; ?></td>
                                <td><span class="status-<?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                                <td><a href="booking_details.php?id=<?php echo $booking['id']; ?>" class="btn btn-small">Details</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No bookings yet. <a href="browse_caretakers.php">Start booking now!</a></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
