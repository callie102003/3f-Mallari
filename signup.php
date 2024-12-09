<?php
// Include database connection file
include('database.php');
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $role = "customer"; // Default role for signup is customer

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if email already exists
        $checkEmailQuery = "SELECT * FROM users WHERE email = :email";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error = "Email already exists. Please login or use a different email.";
        } else {
            // Insert new user into database
            $query = "INSERT INTO users (full_name, email, phone_number, password, role, created_at, updated_at) 
                      VALUES (:full_name, :email, :phone_number, :password, :role, NOW(), NOW())";
            $stmt = $conn->prepare($query);

            // Bind parameters to the query
            $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);

            if ($stmt->execute()) {
                header("Location: login.php"); // Redirect to login page after successful signup
                exit();
            } else {
                $error = "Error: " . $stmt->errorInfo()[2];
            }
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
    <title>Signup - Therapy App</title>
    <link rel="stylesheet" href="signup.css">
</head>
<body>

    <div class="signup-container">
        <form action="signup.php" method="POST" class="signup-form">
            <h2>Create Your Account</h2>

            <!-- Display error message if any -->
            <?php if($error != ""): ?>
                <div class="error-message">
                    <p><?php echo htmlspecialchars($error); ?></p> <!-- Prevent XSS attacks -->
                </div>
            <?php endif; ?>

            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone_number" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Create Account</button>

            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>

</body>
</html>
