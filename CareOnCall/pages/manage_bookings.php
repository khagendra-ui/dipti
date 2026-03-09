<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('admin')) {
    redirect('login.php');
}

$status_filter = $_GET['status'] ?? 'all';

$query = "
    SELECT b.*, c.name as client_name, ct.name as caretaker_name
    FROM bookings b
    JOIN users c ON b.client_id = c.id
    JOIN users ct ON b.caretaker_id = ct.id
";

if ($status_filter !== 'all') {
    $status_filter = escape_string($status_filter);
    $query .= " WHERE b.status = '$status_filter'";
}

$query .= " ORDER BY b.booking_date DESC";
$bookings_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - CareOnCall Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar admin-nav">
        <div class="nav-container">
            <h1>CareOnCall Admin</h1>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_users.php">Users</a></li>
                <li><a href="manage_caretakers.php">Caretakers</a></li>
                <li><a href="manage_bookings.php">Bookings</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>Manage Bookings</h2>

        <div class="filter-section">
            <a href="manage_bookings.php?status=all" class="btn <?php echo ($status_filter === 'all') ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
            <a href="manage_bookings.php?status=pending" class="btn <?php echo ($status_filter === 'pending') ? 'btn-primary' : 'btn-secondary'; ?>">Pending</a>
            <a href="manage_bookings.php?status=confirmed" class="btn <?php echo ($status_filter === 'confirmed') ? 'btn-primary' : 'btn-secondary'; ?>">Confirmed</a>
            <a href="manage_bookings.php?status=completed" class="btn <?php echo ($status_filter === 'completed') ? 'btn-primary' : 'btn-secondary'; ?>">Completed</a>
            <a href="manage_bookings.php?status=cancelled" class="btn <?php echo ($status_filter === 'cancelled') ? 'btn-primary' : 'btn-secondary'; ?>">Cancelled</a>
        </div>

        <div class="admin-section">
            <table class="table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Caretaker</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $booking['client_name']; ?></td>
                            <td><?php echo $booking['caretaker_name']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                            <td><?php echo $booking['start_time']; ?> - <?php echo $booking['end_time']; ?></td>
                            <td>$<?php echo $booking['total_cost']; ?></td>
                            <td><span class="status-<?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                            <td>
                                <a href="booking_details.php?id=<?php echo $booking['id']; ?>" class="btn btn-primary btn-small">Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php if ($bookings_result->num_rows === 0): ?>
                <p style="text-align: center; padding: 20px;">No bookings found.</p>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .admin-nav {
            background-color: #1976d2;
        }

        .filter-section {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .admin-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }
    </style>
</body>
</html>
