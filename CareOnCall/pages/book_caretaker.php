<?php
require_once '../php/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();
$caretaker_id = isset($_GET['caretaker_id']) ? intval($_GET['caretaker_id']) : 0;

// Get caretaker details
if ($caretaker_id > 0) {
    $caretaker_result = $conn->query("
        SELECT u.*, cd.hourly_rate, cd.skills
        FROM users u
        JOIN caretaker_details cd ON u.id = cd.user_id
        WHERE u.id = $caretaker_id AND cd.verification_status = 'approved'
    ");
    $caretaker = $caretaker_result->fetch_assoc();
} else {
    $caretaker = null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caretaker_id = intval($_POST['caretaker_id']);
    $booking_date = escape_string($_POST['booking_date']);
    $start_time = escape_string($_POST['start_time']);
    $end_time = escape_string($_POST['end_time']);
    $location = escape_string($_POST['location']);
    $service_type = escape_string($_POST['service_type']);
    $special_requirements = escape_string($_POST['special_requirements']);
    
    // Calculate hours and cost
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    $hours = ($end - $start) / 3600;
    
    // Get hourly rate
    $rate_result = $conn->query("SELECT hourly_rate FROM caretaker_details WHERE user_id = $caretaker_id");
    $rate_data = $rate_result->fetch_assoc();
    $total_cost = $hours * $rate_data['hourly_rate'];
    
    // Insert booking
    $sql = "INSERT INTO bookings (client_id, caretaker_id, booking_date, start_time, end_time, location, service_type, special_requirements, total_hours, total_cost, status)
            VALUES ($user[id], $caretaker_id, '$booking_date', '$start_time', '$end_time', '$location', '$service_type', '$special_requirements', $hours, $total_cost, 'pending')";
    
    if ($conn->query($sql) === TRUE) {
        $booking_id = $conn->insert_id;
        
        // Create booking request for caretaker
        $conn->query("INSERT INTO booking_requests (booking_id, caretaker_id, status) VALUES ($booking_id, $caretaker_id, 'pending')");
        
        $success = "Booking request sent! Waiting for caretaker confirmation.";
    } else {
        $error = "Error creating booking: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Caretaker - CareOnCall</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>CareOnCall</h1>
            <ul>
                <li><a href="client_dashboard.php">Dashboard</a></li>
                <li><a href="browse_caretakers.php">Browse</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="booking-container">
        <h2>Book a Caretaker</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <a href="my_bookings.php" class="btn btn-primary">View My Bookings</a>
        <?php else: ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="booking-form">
                <div class="form-group">
                    <label for="caretaker_id">Select Caretaker:</label>
                    <select id="caretaker_id" name="caretaker_id" required onchange="updateCaretaker()">
                        <option value="">-- Select a caretaker --</option>
                        <?php
                        $all_caretakers = $conn->query("
                            SELECT u.id, u.name, cd.hourly_rate
                            FROM users u
                            JOIN caretaker_details cd ON u.id = cd.user_id
                            WHERE cd.verification_status = 'approved'
                        ");
                        while ($ct = $all_caretakers->fetch_assoc()):
                        ?>
                            <option value="<?php echo $ct['id']; ?>" data-rate="<?php echo $ct['hourly_rate']; ?>">
                                <?php echo $ct['name']; ?> ($<?php echo $ct['hourly_rate']; ?>/hr)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="booking_date">Booking Date:</label>
                    <input type="date" id="booking_date" name="booking_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="start_time">Start Time:</label>
                        <input type="time" id="start_time" name="start_time" required>
                    </div>

                    <div class="form-group">
                        <label for="end_time">End Time:</label>
                        <input type="time" id="end_time" name="end_time" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Service Location:</label>
                    <input type="text" id="location" name="location" placeholder="Enter address" required>
                </div>

                <div class="form-group">
                    <label for="service_type">Service Type:</label>
                    <select id="service_type" name="service_type" required>
                        <option value="">-- Select --</option>
                        <option value="elderly_care">Elderly Care</option>
                        <option value="patient_care">Patient Care</option>
                        <option value="companionship">Companionship</option>
                        <option value="household_help">Household Help</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="special_requirements">Special Requirements (Optional):</label>
                    <textarea id="special_requirements" name="special_requirements" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label>Estimated Cost: <strong id="estimated_cost">$0.00</strong></label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg">Confirm Booking</button>
                <a href="browse_caretakers.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php endif; ?>
    </div>

    <script src="../js/booking.js"></script>
</body>
</html>
