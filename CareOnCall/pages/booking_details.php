<?php
require_once '../php/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();
$booking_id = intval($_GET['id']);

// Get booking details
$result = $conn->query("
    SELECT b.*, c.name as client_name, c.phone as client_phone, c.email as client_email,
           ct.name as caretaker_name, ct.phone as caretaker_phone, ct.email as caretaker_email
    FROM bookings b
    JOIN users c ON b.client_id = c.id
    JOIN users ct ON b.caretaker_id = ct.id
    WHERE b.id = $booking_id
");

if ($result->num_rows === 0) {
    redirect('my_bookings.php');
}

$booking = $result->fetch_assoc();

// Check authorization
if ($user['id'] != $booking['client_id'] && $user['id'] != $booking['caretaker_id'] && $user['user_type'] != 'admin') {
    redirect('index.php');
}

// Get reviews if any
$reviews_result = $conn->query("SELECT * FROM reviews WHERE booking_id = $booking_id");
$reviews = [];
while ($review = $reviews_result->fetch_assoc()) {
    $reviews[$review['reviewer_id']] = $review;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - CareOnCall</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>CareOnCall</h1>
            <ul>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>Booking Details</h2>

        <div class="booking-details-container">
            <div class="detail-card">
                <h3>Booking Information</h3>
                <table class="detail-table">
                    <tr>
                        <td><strong>Booking ID:</strong></td>
                        <td>#<?php echo $booking['id']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="status-<?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Time:</strong></td>
                        <td><?php echo $booking['start_time']; ?> - <?php echo $booking['end_time']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Duration:</strong></td>
                        <td><?php echo $booking['total_hours']; ?> hours</td>
                    </tr>
                    <tr>
                        <td><strong>Location:</strong></td>
                        <td><?php echo $booking['location']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Service Type:</strong></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $booking['service_type'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total Cost:</strong></td>
                        <td><strong>$<?php echo $booking['total_cost']; ?></strong></td>
                    </tr>
                </table>
            </div>

            <div class="detail-card">
                <h3>Client Information</h3>
                <table class="detail-table">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo $booking['client_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo $booking['client_email']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td><?php echo $booking['client_phone']; ?></td>
                    </tr>
                </table>
            </div>

            <div class="detail-card">
                <h3>Caretaker Information</h3>
                <table class="detail-table">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td><?php echo $booking['caretaker_name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo $booking['caretaker_email']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td><?php echo $booking['caretaker_phone']; ?></td>
                    </tr>
                </table>
            </div>

            <?php if ($booking['special_requirements']): ?>
                <div class="detail-card">
                    <h3>Special Requirements</h3>
                    <p><?php echo nl2br($booking['special_requirements']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($reviews[$user['id']])): ?>
                <div class="detail-card">
                    <h3>Your Review</h3>
                    <p><strong>Rating:</strong> <?php echo $reviews[$user['id']]['rating']; ?>/5</p>
                    <p><strong>Comment:</strong> <?php echo $reviews[$user['id']]['comment']; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 20px;">
            <a href="my_bookings.php" class="btn btn-secondary">Back to My Bookings</a>
        </div>
    </div>

    <style>
        .booking-details-container {
            display: grid;
            gap: 20px;
            margin-top: 20px;
        }

        .detail-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .detail-card h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table tr {
            border-bottom: 1px solid var(--border-color);
        }

        .detail-table td {
            padding: 10px 0;
        }

        .detail-table td:first-child {
            width: 180px;
            color: var(--text-color);
        }
    </style>
</body>
</html>
