<?php
require_once '../php/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = escape_string($_POST['name']);
    $email = escape_string($_POST['email']);
    $password = hash_password($_POST['password']);
    $phone = escape_string($_POST['phone']);
    $user_type = escape_string($_POST['user_type']);
    
    // Check if email already exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        // Insert user
        $sql = "INSERT INTO users (name, email, password, phone, user_type, status) 
                VALUES ('$name', '$email', '$password', '$phone', '$user_type', 'active')";
        
        if ($conn->query($sql) === TRUE) {
            $user_id = $conn->insert_id;
            
            // If caretaker, create caretaker details record
            if ($user_type === 'caretaker') {
                $conn->query("INSERT INTO caretaker_details (user_id, verification_status) 
                             VALUES ($user_id, 'pending')");
            }
            
            $success = "Registration successful! Please login.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CareOnCall</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="register-container">
        <div class="register-box">
            <h1>CareOnCall - Register</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
                <a href="login.php" class="btn btn-primary">Go to Login</a>
            <?php else: ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Full Name:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="user_type">Register as:</label>
                        <select id="user_type" name="user_type" required>
                            <option value="">-- Select --</option>
                            <option value="client">Client (Looking for Caretaker)</option>
                            <option value="caretaker">Caretaker (Service Provider)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
                
                <div class="auth-links">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
