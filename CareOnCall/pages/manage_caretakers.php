<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('admin')) {
    redirect('login.php');
}

$filter = $_GET['filter'] ?? 'all';

$query = "
    SELECT u.id, u.name, u.email, u.phone, cd.skills, cd.hourly_rate, cd.experience_years, cd.verification_status
    FROM users u
    JOIN caretaker_details cd ON u.id = cd.user_id
    WHERE u.user_type = 'caretaker'
";

if ($filter === 'approved') {
    $query .= " AND cd.verification_status = 'approved'";
} elseif ($filter === 'pending') {
    $query .= " AND cd.verification_status = 'pending'";
} elseif ($filter === 'rejected') {
    $query .= " AND cd.verification_status = 'rejected'";
}

$query .= " ORDER BY u.created_at DESC";
$caretakers_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Caretakers - CareOnCall Admin</title>
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
        <h2>Manage Caretakers</h2>

        <div class="filter-section">
            <a href="manage_caretakers.php?filter=all" class="btn <?php echo ($filter === 'all') ? 'btn-primary' : 'btn-secondary'; ?>">All (<?php echo $conn->query("SELECT COUNT(*) as c FROM caretaker_details WHERE user_type='caretaker'")->fetch_assoc()['c'] ?? 0; ?>)</a>
            <a href="manage_caretakers.php?filter=pending" class="btn <?php echo ($filter === 'pending') ? 'btn-primary' : 'btn-secondary'; ?>">Pending</a>
            <a href="manage_caretakers.php?filter=approved" class="btn <?php echo ($filter === 'approved') ? 'btn-primary' : 'btn-secondary'; ?>">Approved</a>
            <a href="manage_caretakers.php?filter=rejected" class="btn <?php echo ($filter === 'rejected') ? 'btn-primary' : 'btn-secondary'; ?>">Rejected</a>
        </div>

        <div class="admin-section">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Experience</th>
                        <th>Rate</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($caretaker = $caretakers_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $caretaker['name']; ?></td>
                            <td><?php echo $caretaker['email']; ?></td>
                            <td><?php echo $caretaker['phone']; ?></td>
                            <td><?php echo $caretaker['experience_years']; ?> years</td>
                            <td>$<?php echo $caretaker['hourly_rate']; ?>/hr</td>
                            <td>
                                <?php 
                                $status = $caretaker['verification_status'];
                                if ($status === 'approved') {
                                    echo '<span style="background-color: #d4edda; color: #155724; padding: 5px 10px; border-radius: 4px;">Approved</span>';
                                } elseif ($status === 'pending') {
                                    echo '<span style="background-color: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 4px;">Pending</span>';
                                } else {
                                    echo '<span style="background-color: #f8d7da; color: #721c24; padding: 5px 10px; border-radius: 4px;">Rejected</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($caretaker['verification_status'] === 'pending'): ?>
                                    <a href="verify_caretaker.php?id=<?php echo $caretaker['id']; ?>&action=approve" class="btn btn-success btn-small">Approve</a>
                                    <a href="verify_caretaker.php?id=<?php echo $caretaker['id']; ?>&action=reject" class="btn btn-danger btn-small">Reject</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
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
