<?php
require_once '../php/config.php';

if (!is_logged_in() || !has_role('caretaker')) {
    redirect('login.php');
}

$user = get_current_user();
$caretaker_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day = escape_string($_POST['day_of_week']);
    $start_time = escape_string($_POST['start_time']);
    $end_time = escape_string($_POST['end_time']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    // Check if availability exists
    $check = $conn->query("SELECT id FROM caretaker_availability 
                          WHERE caretaker_id = $caretaker_id AND day_of_week = '$day'");
    
    if ($check->num_rows > 0) {
        $sql = "UPDATE caretaker_availability 
                SET start_time = '$start_time', end_time = '$end_time', is_available = $is_available
                WHERE caretaker_id = $caretaker_id AND day_of_week = '$day'";
    } else {
        $sql = "INSERT INTO caretaker_availability (caretaker_id, day_of_week, start_time, end_time, is_available)
                VALUES ($caretaker_id, '$day', '$start_time', '$end_time', $is_available)";
    }

    if ($conn->query($sql) === TRUE) {
        $success = "Availability updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

// Get existing availability
$availability_result = $conn->query("
    SELECT * FROM caretaker_availability 
    WHERE caretaker_id = $caretaker_id
    ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
");

$availability = [];
while ($row = $availability_result->fetch_assoc()) {
    $availability[$row['day_of_week']] = $row;
}

$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Availability - CareOnCall</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>CareOnCall</h1>
            <ul>
                <li><a href="caretaker_dashboard.php">Dashboard</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="availability.php">Availability</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>Set Your Availability</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="availability-section">
            <p>Set your working hours for each day of the week. Clients can only book slots during your available hours.</p>

            <div class="availability-grid">
                <?php foreach ($days as $day): ?>
                    <div class="day-card">
                        <h3><?php echo $day; ?></h3>
                        <form method="POST">
                            <input type="hidden" name="day_of_week" value="<?php echo $day; ?>">
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_available" 
                                        <?php echo (isset($availability[$day]) && $availability[$day]['is_available']) ? 'checked' : ''; ?>>
                                    Available
                                </label>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="start_<?php echo $day; ?>">Start Time:</label>
                                    <input type="time" id="start_<?php echo $day; ?>" name="start_time" 
                                        value="<?php echo isset($availability[$day]) ? $availability[$day]['start_time'] : '09:00'; ?>">
                                </div>

                                <div class="form-group">
                                    <label for="end_<?php echo $day; ?>">End Time:</label>
                                    <input type="time" id="end_<?php echo $day; ?>" name="end_time" 
                                        value="<?php echo isset($availability[$day]) ? $availability[$day]['end_time'] : '17:00'; ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-small">Update</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <style>
        .availability-section {
            margin-bottom: 30px;
        }

        .availability-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .day-card {
            background-color: #fff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .day-card h3 {
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .day-card form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .day-card .form-row {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .day-card .form-group {
            margin-bottom: 10px;
        }
    </style>
</body>
</html>
