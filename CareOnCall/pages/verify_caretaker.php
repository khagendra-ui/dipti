<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('admin')) {
    redirect('login.php');
}

$admin = get_current_user();
$caretaker_id = intval($_GET['id']);
$action = $_GET['action'] ?? '';

if (!in_array($action, ['approve', 'reject'])) {
    redirect('admin_dashboard.php');
}

// Get caretaker info
$result = $conn->query("
    SELECT u.*, cd.skills, cd.experience_years, cd.certification_document, cd.verification_status
    FROM users u
    JOIN caretaker_details cd ON u.id = cd.user_id
    WHERE u.id = $caretaker_id AND u.user_type = 'caretaker'
");

if ($result->num_rows === 0) {
    redirect('admin_dashboard.php');
}

$caretaker = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $action === 'approve' ? 'approved' : 'rejected';
    $admin_notes = escape_string($_POST['admin_notes'] ?? '');

    $sql = "UPDATE caretaker_details SET verification_status = '$status', verification_date = NOW(), admin_notes = '$admin_notes' 
            WHERE user_id = $caretaker_id";

    if ($conn->query($sql) === TRUE) {
        // Log action
        $conn->query("INSERT INTO admin_logs (admin_id, action, target_user_id, description) 
                     VALUES ({$admin['id']}, 'Caretaker verification', $caretaker_id, 
                     'Caretaker application $status')");

        $success = "Caretaker " . ($action === 'approve' ? 'approved' : 'rejected') . " successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Caretaker - CareOnCall Admin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar admin-nav">
        <div class="nav-container">
            <h1>CareOnCall Admin</h1>
            <ul>
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="manage_caretakers.php">Caretakers</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>Verify Caretaker Application</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        <?php else: ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="verification-form">
                <div class="caretaker-details">
                    <h3>Applicant Information</h3>
                    <table class="table">
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td><?php echo $caretaker['name']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><?php echo $caretaker['email']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Phone:</strong></td>
                            <td><?php echo $caretaker['phone']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Experience (Years):</strong></td>
                            <td><?php echo $caretaker['experience_years']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Skills:</strong></td>
                            <td><?php echo $caretaker['skills']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Certification Document:</strong></td>
                            <td>
                                <?php 
                                if ($caretaker['certification_document']) {
                                    echo '<a href="' . BASE_URL . 'uploads/' . $caretaker['certification_document'] . '" target="_blank">View Document</a>';
                                } else {
                                    echo 'Not uploaded';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Application Date:</strong></td>
                            <td><?php echo date('M d, Y', strtotime($caretaker['created_at'])); ?></td>
                        </tr>
                    </table>
                </div>

                <form method="POST" class="verification-decision">
                    <div class="form-group">
                        <label for="admin_notes">Admin Notes:</label>
                        <textarea id="admin_notes" name="admin_notes" rows="5" placeholder="Add notes about your decision..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-<?php echo ($action === 'approve') ? 'success' : 'danger'; ?> btn-lg">
                            <?php echo ($action === 'approve') ? 'Approve Application' : 'Reject Application'; ?>
                        </button>
                        <a href="admin_dashboard.php" class="btn btn-secondary btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .admin-nav {
            background-color: #1976d2;
        }

        .verification-form {
            max-width: 700px;
            margin: 0 auto;
        }

        .caretaker-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .caretaker-details h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .verification-decision {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }
        }
    </style>
</body>
</html>
