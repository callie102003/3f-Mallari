<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellness Therapy - Book Your Session</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Your Wellness Journey Starts Here</h1>
            <div class="cta-buttons">
                <a href="booking.php" class="btn">Book Now</a>
                <a href="services.php" class="btn">View Services</a>
            </div>
        </div>
    </section>

    <!-- Services Overview Section -->
<section id="services" class="services-overview">
    <h2>Our Services</h2>
    <div class="service-cards">
        <?php
        include 'database.php'; // Include the database connection

        try {
            // Query to fetch the top 3 services for the overview
            $sql = "SELECT * FROM services LIMIT 3";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            // Check if any services were returned
            if ($stmt->rowCount() > 0) {
                // Loop through each service
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='service-card'>
                            <img src='deep.jpg {$row['service_name']}.jpg' alt='{$row['service_name']}'>
                            <h3>{$row['service_name']}</h3>
                            <p>{$row['description']}</p>
                            <p><strong>Price:</strong> \${$row['price']}</p>
                            <a href='booking.php?service_id={$row['service_id']}' class='btn'>Book Now</a>
                          </div>";
                }
            } else {
                echo "<p>No services available at the moment.</p>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage(); // Display error if query fails
        }

        // Close the database connection
        $conn = null;
        ?>
    </div>
</section>


    <!-- Testimonials Section -->
    <section class="testimonials">
        <h2>What Our Clients Say</h2>
        <div class="testimonial-slider">
            <div class="testimonial-card">
                <img src="sarah.jpg" alt="Customer 1">
                <div class="testimonial-text">
                    <p>"The therapy session was amazing! I feel so relaxed and rejuvenated!"</p>
                    <span>- Sarah L.</span>
                    <div class="rating">⭐⭐⭐⭐⭐</div>
                </div>
            </div>
            <div class="testimonial-card">
                <img src="mich.jpg" alt="Customer 2">
                <div class="testimonial-text">
                    <p>"A truly professional and serene experience. Highly recommend!"</p>
                    <span>- Michael T.</span>
                    <div class="rating">⭐⭐⭐⭐⭐</div>
                </div>
            </div>
            <div class="testimonial-card">
                <img src="isaac.jpg" alt="Customer 3">
                <div class="testimonial-text">
                    <p>"Grabe laking tulong para saakin to! Good service 5 star to saakin!"</p>
                    <span>- Isaac G.</span>
                    <div class="rating">⭐⭐⭐⭐⭐</div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="cta-content">
            <h2>Ready to Start Your Journey?</h2>
            <p>Create an account or schedule your first session today.</p>
            <a href="signup.php" class="btn">Create Account</a>
            <a href="booking.php" class="btn">Schedule Now</a>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Wellness Therapy</p>
    </footer>

</body>
</html>
