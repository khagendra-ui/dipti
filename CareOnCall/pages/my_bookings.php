<?php
require_once '../php/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();

// Get bookings based on user type
if ($user['user_type'] === 'client') {
    $bookings_result = $conn->query("
        SELECT b.*, u.name as caretaker_name
        FROM bookings b
        JOIN users u ON b.caretaker_id = u.id
        WHERE b.client_id = {$user['id']}
        ORDER BY b.booking_date DESC
    ");
} else {
    $bookings_result = $conn->query("
        SELECT b.*, c.name as client_name
        FROM bookings b
        JOIN users c ON b.client_id = c.id
        WHERE b.caretaker_id = {$user['id']}
        ORDER BY b.booking_date DESC
    ");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - CareOnCall</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>CareOnCall</h1>
            <ul>
                <li><a href="<?php echo ($user['user_type'] === 'client') ? 'client_dashboard.php' : 'caretaker_dashboard.php'; ?>">Dashboard</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>My Bookings</h2>

        <?php if ($bookings_result->num_rows > 0): ?>
            <div class="bookings-list">
                <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                    <div class="booking-card">
                        <div class="booking-header">
                            <h3><?php echo ($user['user_type'] === 'client') ? $booking['caretaker_name'] : $booking['client_name']; ?></h3>
                            <span class="status-<?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></span>
                        </div>
                        <div class="booking-details">
                            <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></p>
                            <p><strong>Time:</strong> <?php echo $booking['start_time']; ?> - <?php echo $booking['end_time']; ?></p>
                            <p><strong>Location:</strong> <?php echo $booking['location']; ?></p>
                            <p><strong>Service:</strong> <?php echo ucfirst(str_replace('_', ' ', $booking['service_type'])); ?></p>
                            <p><strong>Total Cost:</strong> $<?php echo $booking['total_cost']; ?></p>
                            <?php if ($booking['special_requirements']): ?>
                                <p><strong>Special Requirements:</strong> <?php echo $booking['special_requirements']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="booking-actions">
                            <a href="booking_details.php?id=<?php echo $booking['id']; ?>" class="btn btn-primary btn-small">View Details</a>
                            <?php if ($booking['status'] === 'pending' && $user['user_type'] === 'client'): ?>
                                <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-danger btn-small" onclick="return confirm('Cancel this booking?');">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <p>No bookings found.</p>
                <?php if ($user['user_type'] === 'client'): ?>
                    <a href="browse_caretakers.php" class="btn btn-primary">Browse Caretakers</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .bookings-list {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }

        .booking-card {
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }

        .booking-header h3 {
            margin: 0;
        }

        .booking-details {
            margin-bottom: 15px;
        }

        .booking-details p {
            margin: 8px 0;
        }

        .booking-actions {
            display: flex;
            gap: 10px;
        }
    </style>
</body>
</html>
