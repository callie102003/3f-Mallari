<?php
// Start the session to check if the user is logged in
session_start();

// Check if the user is logged in, if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include 'database.php';

// Get user id from session
$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect form data
    $service_id = $_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];
    $start_time = $_POST['start_time'];

    // Validate the data (you can add more validations as needed)
    if (empty($service_id) || empty($appointment_date) || empty($start_time)) {
        echo "All fields are required!";
        exit();
    }

    // Prepare the SQL query to insert the appointment
    $query = "INSERT INTO appointments (user_id, service_id, appointment_date, start_time) 
              VALUES (:user_id, :service_id, :appointment_date, :start_time)";

    // Prepare the statement
    $stmt = $conn->prepare($query);

    // Execute the query with parameters directly
    $params = [
        ':user_id' => $user_id,
        ':service_id' => $service_id,
        ':appointment_date' => $appointment_date,
        ':start_time' => $start_time
    ];

    if ($stmt->execute($params)) {
        // Redirect to a confirmation page or the user's dashboard
        header("Location: confirmation.php");  // You can create a confirmation page or redirect to the homepage
        exit();
    } else {
        // If there was an error inserting the data
        echo "Error booking the appointment. Please try again later.";
    }
}
?>
