<?php
// Start the session and check if the admin is logged in
session_start();

// If the user is not an admin, redirect to login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'database.php'; // Include the database connection

// Fetch all bookings
$stmt_bookings = $conn->prepare("SELECT * FROM appointments ORDER BY appointment_date DESC");
$stmt_bookings->execute();
$bookings = $stmt_bookings->fetchAll(PDO::FETCH_ASSOC);

// Fetch all services
$stmt_services = $conn->prepare("SELECT * FROM services");
$stmt_services->execute();
$services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);

// Fetch all therapist availability
$stmt_availability = $conn->prepare("SELECT * FROM availability ORDER BY therapist_id, date");
$stmt_availability->execute();
$availability = $stmt_availability->fetchAll(PDO::FETCH_ASSOC);

// Fetch all payments
$stmt_payments = $conn->prepare("SELECT * FROM payments ORDER BY payment_date DESC");
$stmt_payments->execute();
$payments = $stmt_payments->fetchAll(PDO::FETCH_ASSOC);

// Close the database connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css"> <!-- Your admin-specific CSS -->
</head>
<body>

    <!-- Admin Sidebar and Navigation -->
    <aside class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="#manage_bookings">Manage Bookings</a></li>
            <li><a href="#manage_services">Manage Services</a></li>
            <li><a href="#therapist_schedule">Therapist Schedule</a></li>
            <li><a href="#payment_reports">Payments & Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="content">
        <!-- Manage Bookings -->
        <section id="manage_bookings">
            <h2>Manage Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Therapist</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo $booking['appointment_id']; ?></td>
                            <td><?php echo $booking['user_id']; ?></td>
                            <td><?php echo $booking['service_id']; ?></td>
                            <td><?php echo $booking['therapist_id']; ?></td>
                            <td><?php echo $booking['appointment_date']; ?></td>
                            <td><?php echo $booking['status']; ?></td>
                            <td>
                                <a href="approve_booking.php?id=<?php echo $booking['appointment_id']; ?>" class="btn">Approve</a>
                                <a href="cancel_booking.php?id=<?php echo $booking['appointment_id']; ?>" class="btn">Cancel</a>
                                <a href="reschedule_booking.php?id=<?php echo $booking['appointment_id']; ?>" class="btn">Reschedule</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- Manage Services -->
        <section id="manage_services">
            <h2>Manage Services</h2>
            <table>
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo $service['service_name']; ?></td>
                            <td><?php echo $service['description']; ?></td>
                            <td><?php echo $service['price']; ?></td>
                            <td>
                                <a href="edit_service.php?id=<?php echo $service['service_id']; ?>" class="btn">Edit</a>
                                <a href="delete_service.php?id=<?php echo $service['service_id']; ?>" class="btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Add New Service</h3>
            <form action="add_service.php" method="POST">
                <label for="service_name">Service Name:</label>
                <input type="text" name="service_name" required>
                <label for="description">Description:</label>
                <textarea name="description" required></textarea>
                <label for="price">Price:</label>
                <input type="number" name="price" step="0.01" required>
                <label for="duration">Duration (minutes):</label>
                <input type="number" name="duration" required>
                <button type="submit" class="btn">Add Service</button>
            </form>
        </section>

        <!-- Therapist Schedule -->
        <section id="therapist_schedule">
            <h2>Therapist Schedule</h2>
            <table>
                <thead>
                    <tr>
                        <th>Therapist</th>
                        <th>Date</th>
                        <th>Availability</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($availability as $availability_item): ?>
                        <tr>
                            <td><?php echo $availability_item['therapist_id']; ?></td>
                            <td><?php echo $availability_item['date']; ?></td>
                            <td><?php echo $availability_item['start_time'] . ' - ' . $availability_item['end_time']; ?></td>
                            <td>
                                <a href="edit_availability.php?id=<?php echo $availability_item['availability_id']; ?>" class="btn">Edit</a>
                                <a href="delete_availability.php?id=<?php echo $availability_item['availability_id']; ?>" class="btn">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Add Availability</h3>
            <form action="add_availability.php" method="POST">
                <label for="therapist_id">Therapist:</label>
                <input type="number" name="therapist_id" required>
                <label for="date">Date:</label>
                <input type="date" name="date" required>
                <label for="start_time">Start Time:</label>
                <input type="time" name="start_time" required>
                <label for="end_time">End Time:</label>
                <input type="time" name="end_time" required>
                <button type="submit" class="btn">Add Availability</button>
            </form>
        </section>

        <!-- Payments & Reports -->
        <section id="payment_reports">
            <h2>Payments & Reports</h2>
            <h3>Payments</h3>
            <table>
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Booking ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo $payment['payment_id']; ?></td>
                            <td><?php echo $payment['appointment_id']; ?></td>
                            <td><?php echo $payment['amount']; ?></td>
                            <td><?php echo $payment['status']; ?></td>
                            <td><?php echo $payment['payment_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Reports</h3>
            <p>Data visualizations for bookings, earnings, and customer satisfaction trends will be shown here.</p>
            <!-- You can integrate libraries like Chart.js to show graphs -->
        </section>
    </main>

</body>
</html>
