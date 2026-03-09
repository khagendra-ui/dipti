<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('admin')) {
    redirect('login.php');
}

$user = get_current_user();

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type != 'admin'")->fetch_assoc()['count'];
$pending_caretakers = $conn->query("SELECT COUNT(*) as count FROM caretaker_details WHERE verification_status = 'pending'")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc()['count'];
$completed_bookings = $conn->query("SELECT COUNT(*) as count FROM bookings WHERE status = 'completed'")->fetch_assoc()['count'];

// Get pending caretaker applications
$pending_result = $conn->query("
    SELECT u.id, u.name, u.email, u.phone, cd.skills, cd.experience_years, cd.certification_document
    FROM users u
    JOIN caretaker_details cd ON u.id = cd.user_id
    WHERE cd.verification_status = 'pending'
    ORDER BY u.created_at DESC
");

// Get all bookings
$bookings_result = $conn->query("
    SELECT b.*, c.name as client_name, ct.name as caretaker_name
    FROM bookings b
    JOIN users c ON b.client_id = c.id
    JOIN users ct ON b.caretaker_id = ct.id
    ORDER BY b.booking_date DESC
    LIMIT 20
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CareOnCall</title>
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
                <li><a href="admin_logs.php">Activity Logs</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h2>Admin Dashboard</h2>
            <p>System Overview & Management</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="stat-number"><?php echo $total_users; ?></div>
            </div>

            <div class="stat-card">
                <h3>Pending Caretaker Approvals</h3>
                <div class="stat-number"><?php echo $pending_caretakers; ?></div>
            </div>

            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="stat-number"><?php echo $total_bookings; ?></div>
            </div>

            <div class="stat-card">
                <h3>Completed Bookings</h3>
                <div class="stat-number"><?php echo $completed_bookings; ?></div>
            </div>
        </div>

        <?php if ($pending_caretakers > 0): ?>
        <div class="admin-section">
            <h3>Pending Caretaker Verifications</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Experience</th>
                        <th>Skills</th>
                        <th>Document</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($caretaker = $pending_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $caretaker['name']; ?></td>
                            <td><?php echo $caretaker['email']; ?></td>
                            <td><?php echo $caretaker['phone']; ?></td>
                            <td><?php echo $caretaker['experience_years']; ?> years</td>
                            <td><?php echo substr($caretaker['skills'], 0, 30); ?></td>
                            <td><?php echo $caretaker['certification_document'] ? '<a href="#">View</a>' : 'N/A'; ?></td>
                            <td>
                                <a href="verify_caretaker.php?id=<?php echo $caretaker['id']; ?>&action=approve" class="btn btn-success btn-small">Approve</a>
                                <a href="verify_caretaker.php?id=<?php echo $caretaker['id']; ?>&action=reject" class="btn btn-danger btn-small">Reject</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="admin-section">
            <h3>Recent Bookings</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Caretaker</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $booking['client_name']; ?></td>
                            <td><?php echo $booking['caretaker_name']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($booking['booking_date'])); ?></td>
                            <td><span class="status-<?php echo $booking['status']; ?>"><?php echo ucfirst($booking['status']); ?></span></td>
                            <td>$<?php echo $booking['total_cost']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
