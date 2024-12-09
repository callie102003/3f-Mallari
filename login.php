<?php
// Include database connection file
include('database.php');
session_start();
$error = ""; // Initialize error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Prepare the SQL query to fetch user based on email and role
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($query);

        // Bind the parameter to avoid SQL injection
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Check if the user exists
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password
            if (password_verify($password, $row['password'])) {
                // Start the session and store user details
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['role'] = $row['role'];

                // Redirect to different pages based on user role
                if ($row['role'] == 'admin') {
                    // Redirect admin to the admin dashboard
                    header("Location: admin_dashboard.php");
                } else {
                    // Redirect other users (customer or therapist) to their home page
                    header("Location: home.php");
                }
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No user found with that email.";
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Therapy App</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <!-- Login Form Section -->
    <div class="login-container">
        <form action="login.php" method="POST" class="login-form">
            <h2>Login to Your Account</h2>

            <!-- Display error message if any -->
            <?php if($error != ""): ?>
                <div class="error-message">
                    <p><?php echo htmlspecialchars($error); ?></p> <!-- Prevent XSS attacks -->
                </div>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>

            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </form>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
    </section>
</body>
</html>
