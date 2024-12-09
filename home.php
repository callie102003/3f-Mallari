<?php
// Start session to check login status
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'database.php';  // Ensure this file contains the proper PDO connection setup

// Fetch services
$stmt_services = $conn->prepare("SELECT * FROM services");
$stmt_services->execute();
$services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);

// Fetch upcoming appointments for logged-in user
$stmt_upcoming = $conn->prepare("
    SELECT a.appointment_id, s.service_name, t.full_name AS therapist_name, a.appointment_date, a.start_time, a.status 
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    JOIN users t ON a.therapist_id = t.user_id
    WHERE a.user_id = :user_id 
    AND a.appointment_date >= CURDATE()
    ORDER BY a.appointment_date
");
$stmt_upcoming->bindParam(':user_id', $_SESSION['user_id']);
$stmt_upcoming->execute();
$upcoming_appointments = $stmt_upcoming->fetchAll(PDO::FETCH_ASSOC);

// Fetch past appointments for logged-in user
$stmt_past = $conn->prepare("
    SELECT a.appointment_id, s.service_name, t.full_name AS therapist_name, a.appointment_date, a.start_time, a.status 
    FROM appointments a
    JOIN services s ON a.service_id = s.service_id
    JOIN users t ON a.therapist_id = t.user_id
    WHERE a.user_id = :user_id 
    AND a.appointment_date < CURDATE()
    ORDER BY a.appointment_date DESC
");
$stmt_past->bindParam(':user_id', $_SESSION['user_id']);
$stmt_past->execute();
$past_appointments = $stmt_past->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="home.css">
    <title>Home - User Dashboard</title>
</head>
<body>

    <!-- Hero Section -->
    <div class="hero">
        <h1>Welcome to Your Wellness Journey</h1>
        <p>Your path to better health and relaxation starts here.</p>
    </div>

    <!-- Services Overview -->
    <div class="services-overview">
        <h2>Our Services</h2>
        <div class="services-container">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <img src="deep.jpg" alt="<?php echo $service['service_name']; ?>">
                    <h3><?php echo $service['service_name']; ?></h3>
                    <p><?php echo $service['description']; ?></p>
                    <p><strong>Price: $<?php echo $service['price']; ?></strong></p>
                    <a href="booking.php?service_id=<?php echo $service['service_id']; ?>" class="btn">Book Now</a>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- See More Services Button -->
        <div class="see-more-services">
            <a href="services.php" class="btn">See More Services</a> <!-- Button to see more services -->
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="appointments-section">
        <h2>Upcoming Appointments</h2>
        <?php if ($upcoming_appointments): ?>
            <ul>
                <?php foreach ($upcoming_appointments as $appointment): ?>
                    <li>
                        <strong>Service:</strong> <?php echo $appointment['service_name']; ?><br>
                        <strong>Therapist:</strong> <?php echo $appointment['therapist_name']; ?><br>
                        <strong>Date:</strong> <?php echo $appointment['appointment_date']; ?><br>
                        <strong>Time:</strong> <?php echo $appointment['start_time']; ?><br>
                        <strong>Status:</strong> <?php echo $appointment['status']; ?><br>
                        <a href="cancel_appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn">Cancel</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No upcoming appointments.</p>
        <?php endif; ?>
    </div>

    <!-- Past Appointments -->
    <div class="appointments-section">
        <h2>Past Appointments</h2>
        <?php if ($past_appointments): ?>
            <ul>
                <?php foreach ($past_appointments as $appointment): ?>
                    <li>
                        <strong>Service:</strong> <?php echo $appointment['service_name']; ?><br>
                        <strong>Therapist:</strong> <?php echo $appointment['therapist_name']; ?><br>
                        <strong>Date:</strong> <?php echo $appointment['appointment_date']; ?><br>
                        <strong>Time:</strong> <?php echo $appointment['start_time']; ?><br>
                        <strong>Status:</strong> <?php echo $appointment['status']; ?><br>
                        <a href="review_appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn">Leave a Review</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No past appointments.</p>
        <?php endif; ?>
    </div>

    <!-- Call to Action Section -->
    <div class="cta">
        <p>Ready to start your journey with us? Book your appointment now!</p>
        <a href="booking.php" class="btn">Book Appointment</a>
    </div>

    <!-- Logout Button -->
    <div class="logout">
        <a href="logout.php" class="btn">Logout</a>
    </div>

</body>
</html>
