<?php
require_once '../php/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();
$booking_id = intval($_GET['id']);

// Get booking
$result = $conn->query("SELECT * FROM bookings WHERE id = $booking_id AND client_id = {$user['id']}");

if ($result->num_rows === 0) {
    redirect('my_bookings.php');
}

$booking = $result->fetch_assoc();

// Only allow cancellation if pending
if ($booking['status'] !== 'pending') {
    redirect('my_bookings.php');
}

// Cancel the booking
$conn->query("UPDATE bookings SET status = 'cancelled' WHERE id = $booking_id");

// Log the action
$conn->query("INSERT INTO admin_logs (action, target_user_id, description) 
             VALUES ('Booking cancelled', {$user['id']}, 'Client cancelled booking #$booking_id')");

// Redirect with success message
session_start();
$_SESSION['success'] = "Booking cancelled successfully.";
redirect('my_bookings.php');
?>
