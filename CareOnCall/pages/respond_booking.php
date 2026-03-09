<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('caretaker')) {
    redirect('login.php');
}

$user = get_current_user();
$booking_id = intval($_GET['id']);
$action = $_GET['action'] ?? '';

if (!in_array($action, ['accept', 'reject'])) {
    redirect('caretaker_dashboard.php');
}

// Get booking and request info
$result = $conn->query("
    SELECT br.*, b.*, c.name as client_name
    FROM booking_requests br
    JOIN bookings b ON br.booking_id = b.id
    JOIN users c ON b.client_id = c.id
    WHERE br.booking_id = $booking_id AND br.caretaker_id = {$user['id']}
");

if ($result->num_rows === 0) {
    redirect('caretaker_dashboard.php');
}

$request = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response_status = $action === 'accept' ? 'accepted' : 'rejected';
    $response_text = escape_string($_POST['response'] ?? '');

    // Update booking request
    $sql = "UPDATE booking_requests SET status = '$response_status', caretaker_response = '$response_text', responded_at = NOW() 
            WHERE booking_id = $booking_id";

    if ($conn->query($sql) === TRUE) {
        // Update booking status
        if ($action === 'accept') {
            $conn->query("UPDATE bookings SET status = 'confirmed' WHERE id = $booking_id");
            $message = "Booking accepted successfully!";
        } else {
            $conn->query("UPDATE bookings SET status = 'cancelled' WHERE id = $booking_id");
            $message = "Booking rejected.";
        }

        // Log action
        $conn->query("INSERT INTO admin_logs (admin_id, action, target_user_id, description) 
                     VALUES ({$user['id']}, 'Booking $response_status', {$request['client_id']}, 
                     'Caretaker responded to booking request #$booking_id')");

        $success = $message;
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
    <title><?php echo ucfirst($action); ?> Booking - CareOnCall</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>CareOnCall</h1>
            <ul>
                <li><a href="caretaker_dashboard.php">Dashboard</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2><?php echo ucfirst($action); ?> Booking Request</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="caretaker_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        <?php else: ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="booking-response-form">
                <div class="booking-summary">
                    <h3>Booking Details</h3>
                    <p><strong>Client:</strong> <?php echo $request['client_name']; ?></p>
                    <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($request['booking_date'])); ?></p>
                    <p><strong>Time:</strong> <?php echo $request['start_time']; ?> - <?php echo $request['end_time']; ?></p>
                    <p><strong>Location:</strong> <?php echo $request['location']; ?></p>
                    <p><strong>Total Hours:</strong> <?php echo $request['total_hours']; ?></p>
                    <p><strong>Total Cost:</strong> $<?php echo $request['total_cost']; ?></p>
                </div>

                <form method="POST" class="response-form">
                    <div class="form-group">
                        <label for="response">Your Response (Optional):</label>
                        <textarea id="response" name="response" rows="5" placeholder="Add any comments..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-<?php echo ($action === 'accept') ? 'success' : 'danger'; ?> btn-lg">
                            <?php echo ($action === 'accept') ? 'Accept Booking' : 'Reject Booking'; ?>
                        </button>
                        <a href="caretaker_dashboard.php" class="btn btn-secondary btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .booking-response-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .booking-summary {
            background-color: var(--light-color);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .booking-summary h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .booking-summary p {
            margin-bottom: 10px;
        }

        .response-form {
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
