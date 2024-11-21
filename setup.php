<?php
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "therapy_app"; // Replace with your desired database name

try {
    // Connect to MySQL
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "Database created successfully<br>";

    // Select the database
    $conn->exec("USE $dbname");

    // Create Users table
    $usersTable = "
        CREATE TABLE IF NOT EXISTS Users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            phone_number VARCHAR(15),
            password VARCHAR(255) NOT NULL,
            role ENUM('customer', 'therapist', 'admin') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ";
    $conn->exec($usersTable);
    echo "Users table created successfully<br>";

    // Create Services table
    $servicesTable = "
        CREATE TABLE IF NOT EXISTS Services (
            service_id INT AUTO_INCREMENT PRIMARY KEY,
            service_name VARCHAR(100) NOT NULL,
            description TEXT,
            duration INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ";
    $conn->exec($servicesTable);
    echo "Services table created successfully<br>";

    // Create Appointments table
    $appointmentsTable = "
        CREATE TABLE IF NOT EXISTS Appointments (
            appointment_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            therapist_id INT NOT NULL,
            service_id INT NOT NULL,
            appointment_date DATE NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            status ENUM('pending', 'confirmed', 'completed', 'canceled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES Users(user_id),
            FOREIGN KEY (therapist_id) REFERENCES Users(user_id),
            FOREIGN KEY (service_id) REFERENCES Services(service_id)
        )
    ";
    $conn->exec($appointmentsTable);
    echo "Appointments table created successfully<br>";

    // Create Payments table
    $paymentsTable = "
        CREATE TABLE IF NOT EXISTS Payments (
            payment_id INT AUTO_INCREMENT PRIMARY KEY,
            appointment_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_method ENUM('cash', 'credit_card', 'paypal') NOT NULL,
            payment_status ENUM('paid', 'unpaid', 'refunded') DEFAULT 'unpaid',
            transaction_id VARCHAR(100),
            payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (appointment_id) REFERENCES Appointments(appointment_id)
        )
    ";
    $conn->exec($paymentsTable);
    echo "Payments table created successfully<br>";

    // Create Availability table
    $availabilityTable = "
        CREATE TABLE IF NOT EXISTS Availability (
            availability_id INT AUTO_INCREMENT PRIMARY KEY,
            therapist_id INT NOT NULL,
            date DATE NOT NULL,
            start_time TIME NOT NULL,
            end_time TIME NOT NULL,
            FOREIGN KEY (therapist_id) REFERENCES Users(user_id)
        )
    ";
    $conn->exec($availabilityTable);
    echo "Availability table created successfully<br>";

    // Create Reviews table
    $reviewsTable = "
        CREATE TABLE IF NOT EXISTS Reviews (
            review_id INT AUTO_INCREMENT PRIMARY KEY,
            appointment_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT CHECK (rating BETWEEN 1 AND 5),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (appointment_id) REFERENCES Appointments(appointment_id),
            FOREIGN KEY (user_id) REFERENCES Users(user_id)
        )
    ";
    $conn->exec($reviewsTable);
    echo "Reviews table created successfully<br>";

    // Create Promotions table (optional)
    $promotionsTable = "
        CREATE TABLE IF NOT EXISTS Promotions (
            promo_id INT AUTO_INCREMENT PRIMARY KEY,
            promo_code VARCHAR(50) UNIQUE NOT NULL,
            description TEXT,
            discount_percent DECIMAL(5,2) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL
        )
    ";
    $conn->exec($promotionsTable);
    echo "Promotions table created successfully<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
$conn = null;
?>