<?php
require_once '../php/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = escape_string($_POST['name']);
    $phone = escape_string($_POST['phone']);
    $address = escape_string($_POST['address']);

    // Handle profile picture upload
    $profile_picture = $user['profile_picture'];
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "../uploads/";
        $file_ext = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
        $new_filename = $user_id . '_' . time() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = $new_filename;
        }
    }

    $sql = "UPDATE users SET name = '$name', phone = '$phone', address = '$address', profile_picture = '$profile_picture' 
            WHERE id = $user_id";

    if ($conn->query($sql) === TRUE) {
        // Update caretaker details if applicable
        if ($user['user_type'] === 'caretaker') {
            $skills = escape_string($_POST['skills'] ?? '');
            $experience_years = intval($_POST['experience_years'] ?? 0);
            $hourly_rate = floatval($_POST['hourly_rate'] ?? 0);

            $caretaker_sql = "UPDATE caretaker_details SET skills = '$skills', experience_years = $experience_years, hourly_rate = $hourly_rate 
                             WHERE user_id = $user_id";
            $conn->query($caretaker_sql);
        }

        $success = "Profile updated successfully!";
        $user = get_current_user(); // Refresh user data
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}

// Get caretaker details if applicable
$caretaker_details = null;
if ($user['user_type'] === 'caretaker') {
    $details_result = $conn->query("SELECT * FROM caretaker_details WHERE user_id = $user_id");
    $caretaker_details = $details_result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - CareOnCall</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h1>CareOnCall</h1>
            <ul>
                <li><a href="<?php echo ($user['user_type'] === 'client') ? 'client_dashboard.php' : 'caretaker_dashboard.php'; ?>">Dashboard</a></li>
                <li><a href="my_bookings.php">My Bookings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="../php/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="dashboard-container">
        <h2>My Profile</h2>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-form">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email (Cannot be changed):</label>
                        <input type="email" id="email" value="<?php echo $user['email']; ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" value="<?php echo $user['address']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="profile_picture">Profile Picture:</label>
                        <?php if ($user['profile_picture']): ?>
                            <div class="profile-image-preview">
                                <img src="<?php echo BASE_URL; ?>uploads/<?php echo $user['profile_picture']; ?>" alt="Profile">
                            </div>
                        <?php endif; ?>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                    </div>

                    <?php if ($user['user_type'] === 'caretaker' && $caretaker_details): ?>
                        <h3>Caretaker Information</h3>

                        <div class="form-group">
                            <label for="skills">Skills:</label>
                            <textarea id="skills" name="skills" rows="4"><?php echo $caretaker_details['skills']; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="experience_years">Years of Experience:</label>
                            <input type="number" id="experience_years" name="experience_years" value="<?php echo $caretaker_details['experience_years']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="hourly_rate">Hourly Rate ($):</label>
                            <input type="number" id="hourly_rate" name="hourly_rate" step="0.01" value="<?php echo $caretaker_details['hourly_rate']; ?>">
                        </div>

                        <div class="form-group">
                            <strong>Verification Status:</strong>
                            <?php 
                            $status = $caretaker_details['verification_status'];
                            if ($status === 'approved') {
                                echo '<span style="color: green;">✓ Verified</span>';
                            } elseif ($status === 'rejected') {
                                echo '<span style="color: red;">✗ Rejected</span>';
                            } else {
                                echo '<span style="color: orange;">⧗ Pending</span>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .profile-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .profile-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .profile-image-preview {
            margin-bottom: 15px;
            max-width: 200px;
        }

        .profile-image-preview img {
            width: 100%;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .profile-form h3 {
            margin-top: 30px;
            margin-bottom: 20px;
            color: var(--primary-color);
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }
    </style>
</body>
</html>
