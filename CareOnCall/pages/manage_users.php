<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('admin')) {
    redirect('login.php');
}

$admin = get_current_user();
$filter = $_GET['filter'] ?? 'all';

$query = "SELECT * FROM users WHERE user_type != 'admin'";

if ($filter === 'client') {
    $query .= " AND user_type = 'client'";
} elseif ($filter === 'caretaker') {
    $query .= " AND user_type = 'caretaker'";
}

$query .= " ORDER BY created_at DESC";
$users_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - CareOnCall Admin</title>
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
        <h2>Manage Users</h2>

        <div class="filter-section">
            <a href="manage_users.php?filter=all" class="btn <?php echo ($filter === 'all') ? 'btn-primary' : 'btn-secondary'; ?>">All Users</a>
            <a href="manage_users.php?filter=client" class="btn <?php echo ($filter === 'client') ? 'btn-primary' : 'btn-secondary'; ?>">Clients</a>
            <a href="manage_users.php?filter=caretaker" class="btn <?php echo ($filter === 'caretaker') ? 'btn-primary' : 'btn-secondary'; ?>">Caretakers</a>
        </div>

        <div class="admin-section">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user_row = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user_row['name']; ?></td>
                            <td><?php echo $user_row['email']; ?></td>
                            <td><?php echo $user_row['phone']; ?></td>
                            <td><?php echo ucfirst($user_row['user_type']); ?></td>
                            <td><span class="status-<?php echo $user_row['status']; ?>"><?php echo ucfirst($user_row['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($user_row['created_at'])); ?></td>
                            <td>
                                <a href="user_details.php?id=<?php echo $user_row['id']; ?>" class="btn btn-primary btn-small">View</a>
                                <?php if ($user_row['status'] === 'active'): ?>
                                    <a href="block_user.php?id=<?php echo $user_row['id']; ?>" class="btn btn-danger btn-small" onclick="return confirm('Block this user?');">Block</a>
                                <?php else: ?>
                                    <a href="unblock_user.php?id=<?php echo $user_row['id']; ?>" class="btn btn-success btn-small">Unblock</a>
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
