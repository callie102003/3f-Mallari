<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch the most recent appointment
$stmt = $conn->prepare("SELECT * FROM appointments WHERE user_id = :user_id ORDER BY appointment_id DESC LIMIT 1");

// Execute the query with parameters directly
$stmt->execute([':user_id' => $user_id]);

// Fetch the appointment details
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <title>Appointment Confirmation</title>
</head>
<body>
    <h1>Appointment Confirmed</h1>
    <p>Your appointment has been successfully booked.</p>

    <h3>Appointment Details:</h3>
    <ul>
        <li><strong>Service:</strong> 
            <?php 
                // Fetch service name from the services table
                $serviceStmt = $conn->prepare("SELECT service_name FROM services WHERE service_id = :service_id");
                $serviceStmt->execute([':service_id' => $appointment['service_id']]);
                $service = $serviceStmt->fetch(PDO::FETCH_ASSOC);
                echo $service['service_name']; 
            ?>
        </li>
        <li><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?></li>
        <li><strong>Time:</strong> <?php echo $appointment['start_time']; ?></li>
    </ul>

    <a href="home.php" class="btn">Go to Dashboard</a>
</body>
</html>
