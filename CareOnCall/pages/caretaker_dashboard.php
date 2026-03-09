<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('caretaker')) {
    redirect('login.php');
}

$user = get_current_user();
$caretaker_id = $user['id'];

// Get caretaker details
$details_result = $conn->query("SELECT * FROM caretaker_details WHERE user_id = $caretaker_id");
$caretaker_details = $details_result->fetch_assoc();

// Get booking requests
$requests_result = $conn->query("
    SELECT br.*, b.*, c.name as client_name, c.phone as client_phone
    FROM booking_requests br
    JOIN bookings b ON br.booking_id = b.id
    JOIN users c ON b.client_id = c.id
    WHERE br.caretaker_id = $caretaker_id AND br.status = 'pending'
    ORDER BY br.created_at DESC
");

// Get accepted bookings
$bookings_result = $conn->query("
    SELECT b.*, c.name as client_name
    FROM bookings b
    JOIN users c ON b.client_id = c.id
    WHERE b.caretaker_id = $caretaker_id AND b.status != 'cancelled'
    ORDER BY b.booking_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caretaker Dashboard - CareOnCall</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>CareOnCall</h1>
            <ul>
                <li><a href="caretaker_dashboard.php">Dashboard</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="availability.php">Availability</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h2>Welcome, <?php echo $user['name']; ?></h2>
            <p>Status: 
                <?php 
                if ($caretaker_details['verification_status'] === 'approved') {
                    echo '<span style="color: green;">Verified ✓</span>';
                } else {
                    echo '<span style="color: orange;">Pending Verification</span>';
                }
                ?>
            </p>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Pending Requests</h3>
                <p>You have <strong><?php echo $requests_result->num_rows; ?></strong> pending booking requests</p>
                <p><a href="#requests" class="btn btn-small">View</a></p>
            </div>

            <div class="card">
                <h3>Upcoming Bookings</h3>
                <p><a href="my_bookings.php" class="btn btn-small">View Bookings</a></p>
            </div>

            <div class="card">
                <h3>Set Availability</h3>
                <p>Update your working hours</p>
                <p><a href="availability.php" class="btn btn-small">Manage</a></p>
            </div>
        </div>

        <div id="requests" class="pending-requests">
            <h3>Pending Booking Requests</h3>
            <?php if ($requests_result->num_rows > 0): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Hourly Rate</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($request = $requests_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $request['client_name']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($request['booking_date'])); ?><br><?php echo $request['start_time']; ?> - <?php echo $request['end_time']; ?></td>
                                <td><?php echo substr($request['location'], 0, 30); ?></td>
                                <td>$<?php echo $caretaker_details['hourly_rate']; ?>/hr</td>
                                <td>
                                    <a href="respond_booking.php?id=<?php echo $request['booking_id']; ?>&action=accept" class="btn btn-success btn-small">Accept</a>
                                    <a href="respond_booking.php?id=<?php echo $request['booking_id']; ?>&action=reject" class="btn btn-danger btn-small">Reject</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No pending requests.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
