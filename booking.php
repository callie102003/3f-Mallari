<?php
// Include the database connection
include 'database.php';

// Start the session
session_start();

// Fetch services from the database
$stmt = $conn->prepare("SELECT * FROM services");
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch therapists from the database
$stmt2 = $conn->prepare("SELECT * FROM users WHERE role = 'therapist'");
$stmt2->execute();
$therapists = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Step 1: Handle service and therapist selection
if (isset($_POST['service_id']) && isset($_POST['therapist_id'])) {
    $_SESSION['service_id'] = $_POST['service_id'];
    $_SESSION['therapist_id'] = $_POST['therapist_id'];
}

// Step 2: Handle date and time selection
if (isset($_POST['appointment_date']) && isset($_POST['start_time'])) {
    $_SESSION['appointment_date'] = $_POST['appointment_date'];
    $_SESSION['start_time'] = $_POST['start_time'];
}

// Step 3: Handle payment
if (isset($_POST['payment_method']) && isset($_SESSION['service_id'])) {
    // Insert appointment into the database
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, therapist_id, service_id, appointment_date, start_time, payment_method) 
                            VALUES (:user_id, :therapist_id, :service_id, :appointment_date, :start_time, :payment_method)");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':therapist_id', $_SESSION['therapist_id']);
    $stmt->bindParam(':service_id', $_SESSION['service_id']);
    $stmt->bindParam(':appointment_date', $_SESSION['appointment_date']);
    $stmt->bindParam(':start_time', $_SESSION['start_time']);
    $stmt->bindParam(':payment_method', $_POST['payment_method']);
    $stmt->execute();

    // Redirect to confirmation page or success page
    header('Location: confirmation.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="booking.css">
    <title>Book an Appointment</title>
</head>
<body>
    <h1>Book an Appointment</h1>

    <!-- Step 1: Select Service and Therapist -->
    <form method="POST">
        <h2>Select Service</h2>
        <select name="service_id" required>
            <option value="">Choose a service</option>
            <?php foreach ($services as $service): ?>
                <option value="<?php echo $service['service_id']; ?>">
                    <?php echo $service['service_name']; ?> - $<?php echo $service['price']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <h2>Select Therapist</h2>
        <select name="therapist_id" required>
            <option value="">Choose a therapist</option>
            <?php foreach ($therapists as $therapist): ?>
                <option value="<?php echo $therapist['user_id']; ?>">
                    <?php echo $therapist['full_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="submit" value="Next Step: Choose Date & Time">
    </form>

    <?php if (isset($_SESSION['service_id']) && isset($_SESSION['therapist_id'])): ?>
        <!-- Step 2: Choose Date and Time -->
        <form method="POST">
            <h2>Select Date and Time</h2>
            <label for="appointment_date">Choose Date:</label>
            <input type="date" name="appointment_date" required>

            <label for="start_time">Choose Time:</label>
            <input type="time" name="start_time" required>

            <input type="submit" value="Next Step: Confirm & Pay">
        </form>
    <?php endif; ?>

    <?php if (isset($_SESSION['appointment_date']) && isset($_SESSION['start_time'])): ?>
        <!-- Step 3: Confirm and Payment -->
        <h2>Confirm Appointment</h2>
        <p>Service: <?php echo $services[array_search($_SESSION['service_id'], array_column($services, 'service_id'))]['service_name']; ?></p>
        <p>Therapist: <?php echo $therapists[array_search($_SESSION['therapist_id'], array_column($therapists, 'user_id'))]['full_name']; ?></p>
        <p>Date: <?php echo $_SESSION['appointment_date']; ?></p>
        <p>Time: <?php echo $_SESSION['start_time']; ?></p>
        <p>Price: $<?php echo $services[array_search($_SESSION['service_id'], array_column($services, 'service_id'))]['price']; ?></p>

        <!-- Payment Options -->
        <form method="POST">
            <h2>Select Payment Method</h2>
            <select name="payment_method" required>
                <option value="cash">Cash</option>
                <option value="credit_card">Credit Card</option>
                <option value="paypal">PayPal</option>
            </select>

            <label for="promo_code">Promo Code (Optional):</label>
            <input type="text" name="promo_code">

            <input type="submit" value="Confirm Appointment">
        </form>
    <?php endif; ?>

</body>
</html>
