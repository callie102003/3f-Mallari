<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Web</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Unlock the World, One Page at a Time</h1>
            <div class="cta-buttons">
                <button onclick="window.location.href='book-now.html'">Book Now</button>
                <button onclick="window.location.href='services.html'">View Services</button>
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="services">
        <h2>Popular Services</h2>
        <div class="service-cards">
            <!-- Service Cards will be inserted dynamically using PHP -->
            <?php include 'services.php'; ?>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2>What Our Customers Say</h2>
        <div class="testimonial-slider">
            <!-- Testimonial Cards (Example Static) -->
            <div class="testimonial-card">
                <img src="customer.jpg" alt="Customer photo">
                <p>"Great experience, very informative!"</p>
                <span>⭐⭐⭐⭐⭐</span>
            </div>
            <!-- More testimonials can go here -->
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta">
        <p>Ready to begin? <a href="register.html">Create an account</a> or <a href="schedule.html">schedule your first session</a>!</p>
    </section>

    <footer>
        <p>&copy; 2024 Library Web | All rights reserved.</p>
    </footer>

</body>
</html>
