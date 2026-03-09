<?php
require_once '../php/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$search = isset($_GET['search']) ? escape_string($_GET['search']) : '';

$query = "
    SELECT u.id, u.name, u.phone, u.profile_picture, cd.skills, cd.hourly_rate, cd.experience_years
    FROM users u
    JOIN caretaker_details cd ON u.id = cd.user_id
    WHERE u.user_type = 'caretaker' AND cd.verification_status = 'approved'
";

if ($search) {
    $query .= " AND (u.name LIKE '%$search%' OR cd.skills LIKE '%$search%')";
}

$query .= " ORDER BY u.name";
$caretakers_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Caretakers - CareOnCall</title>
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

    <div class="browse-container">
        <h2>Browse Our Caretakers</h2>
        
        <div class="search-section">
            <form method="GET">
                <input type="text" name="search" placeholder="Search by name or skills..." value="<?php echo $search; ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <div class="caretakers-grid">
            <?php if ($caretakers_result->num_rows > 0): ?>
                <?php while ($caretaker = $caretakers_result->fetch_assoc()): ?>
                    <div class="caretaker-card">
                        <div class="profile-img">
                            <?php if ($caretaker['profile_picture']): ?>
                                <img src="<?php echo BASE_URL; ?>uploads/<?php echo $caretaker['profile_picture']; ?>" alt="<?php echo $caretaker['name']; ?>">
                            <?php else: ?>
                                <div class="placeholder">No Image</div>
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h3><?php echo $caretaker['name']; ?></h3>
                            <p class="rate">$<?php echo $caretaker['hourly_rate']; ?>/hour</p>
                            <p class="experience"><?php echo $caretaker['experience_years']; ?> years experience</p>
                            <p class="skills">Skills: <?php echo $caretaker['skills']; ?></p>
                            <p class="phone">Phone: <?php echo $caretaker['phone']; ?></p>
                            <a href="book_caretaker.php?caretaker_id=<?php echo $caretaker['id']; ?>" class="btn btn-primary btn-block">Book Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No caretakers found. Try a different search.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
