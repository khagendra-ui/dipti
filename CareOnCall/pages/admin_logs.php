<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('admin')) {
    redirect('login.php');
}

// Get all admin logs with pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$total_result = $conn->query("SELECT COUNT(*) as count FROM admin_logs");
$total = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total / $limit);

$logs_result = $conn->query("
    SELECT al.*, u.name as admin_name
    FROM admin_logs al
    LEFT JOIN users u ON al.admin_id = u.id
    ORDER BY al.created_at DESC
    LIMIT $limit OFFSET $offset
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - CareOnCall Admin</title>
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
        <h2>Activity Logs</h2>

        <div class="admin-section">
            <p>Total Records: <strong><?php echo $total; ?></strong></p>

            <table class="table">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Target User</th>
                        <th>Description</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($log = $logs_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $log['admin_name'] ?? 'System'; ?></td>
                            <td><?php echo $log['action']; ?></td>
                            <td><?php echo $log['target_user_id'] ?? 'N/A'; ?></td>
                            <td><?php echo $log['description']; ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($log['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 20px; text-align: center;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="admin_logs.php?page=<?php echo $i; ?>" 
                           class="btn <?php echo ($i === $page) ? 'btn-primary' : 'btn-secondary'; ?> btn-small">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
        .admin-nav {
            background-color: #1976d2;
        }

        .admin-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .pagination {
            display: flex;
            gap: 5px;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</body>
</html>
