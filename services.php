<?php
// Include the database connection
include 'database.php';
session_start();

// Get filter and sorting options from the URL or set defaults
$filter_service_type = isset($_GET['service_type']) ? $_GET['service_type'] : '';
$filter_price_min = isset($_GET['price_min']) ? $_GET['price_min'] : 0;
$filter_price_max = isset($_GET['price_max']) ? $_GET['price_max'] : 1000;
$filter_duration_min = isset($_GET['duration_min']) ? $_GET['duration_min'] : 0;
$filter_duration_max = isset($_GET['duration_max']) ? $_GET['duration_max'] : 180;
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'price_asc';

// Prepare the SQL query with filters and sorting
$query = "SELECT * FROM services WHERE price BETWEEN :price_min AND :price_max AND duration BETWEEN :duration_min AND :duration_max";

// Add filters if necessary
if ($filter_service_type) {
    $query .= " AND service_type = :service_type";
}

// Apply sorting based on user choice
switch ($sort_by) {
    case 'price_asc':
        $query .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY price DESC";
        break;
    case 'duration_asc':
        $query .= " ORDER BY duration ASC";
        break;
    case 'duration_desc':
        $query .= " ORDER BY duration DESC";
        break;
    case 'popularity':
        $query .= " ORDER BY popularity DESC";
        break;
    default:
        $query .= " ORDER BY price ASC";
}

// Prepare the statement
$stmt = $conn->prepare($query);

// Bind parameters
$stmt->bindParam(':price_min', $filter_price_min, PDO::PARAM_INT);
$stmt->bindParam(':price_max', $filter_price_max, PDO::PARAM_INT);
$stmt->bindParam(':duration_min', $filter_duration_min, PDO::PARAM_INT);
$stmt->bindParam(':duration_max', $filter_duration_max, PDO::PARAM_INT);
if ($filter_service_type) {
    $stmt->bindParam(':service_type', $filter_service_type, PDO::PARAM_STR);
}

// Execute the query
$stmt->execute();

// Fetch all services
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="services.css">
    <title>Services List</title>
</head>
<body>

    <!-- Filters and Sorting Sidebar -->
    <div class="filters-sidebar">
        <h2>Filters</h2>
        <form method="GET" action="services.php">
            <!-- Filter by Service Type -->
            <label for="service_type">Service Type:</label>
            <select name="service_type" id="service_type">
                <option value="">All</option>
                <option value="Massage" <?php if ($filter_service_type == 'Massage') echo 'selected'; ?>>Massage</option>
                <option value="Facial" <?php if ($filter_service_type == 'Facial') echo 'selected'; ?>>Facial</option>
                <option value="Therapy" <?php if ($filter_service_type == 'Therapy') echo 'selected'; ?>>Therapy</option>
            </select>

            <!-- Filter by Price Range -->
            <label for="price_min">Price Min:</label>
            <input type="number" name="price_min" value="<?php echo $filter_price_min; ?>" id="price_min" required>

            <label for="price_max">Price Max:</label>
            <input type="number" name="price_max" value="<?php echo $filter_price_max; ?>" id="price_max" required>

            <!-- Filter by Duration -->
            <label for="duration_min">Duration Min (minutes):</label>
            <input type="number" name="duration_min" value="<?php echo $filter_duration_min; ?>" id="duration_min" required>

            <label for="duration_max">Duration Max (minutes):</label>
            <input type="number" name="duration_max" value="<?php echo $filter_duration_max; ?>" id="duration_max" required>

            <!-- Sorting Options -->
            <h2>Sort By</h2>
            <select name="sort_by">
                <option value="price_asc" <?php if ($sort_by == 'price_asc') echo 'selected'; ?>>Price: Low to High</option>
                <option value="price_desc" <?php if ($sort_by == 'price_desc') echo 'selected'; ?>>Price: High to Low</option>
                <option value="duration_asc" <?php if ($sort_by == 'duration_asc') echo 'selected'; ?>>Duration: Short to Long</option>
                <option value="duration_desc" <?php if ($sort_by == 'duration_desc') echo 'selected'; ?>>Duration: Long to Short</option>
                <option value="popularity" <?php if ($sort_by == 'popularity') echo 'selected'; ?>>Popularity</option>
            </select>

            <input type="submit" value="Apply Filters">
        </form>
    </div>

    <!-- Service Cards Section -->
    <div class="services-container">
        <?php foreach ($services as $service): ?>
            <div class="service-card">
                <img src="deep.jpg"  alt="<?php echo $service['service_name']; ?>">
                <h3><?php echo $service['service_name']; ?></h3>
                <p><strong>Price: $<?php echo $service['price']; ?></strong></p>
                <p><strong>Duration: <?php echo $service['duration']; ?> mins</strong></p>
                <p><?php echo $service['description']; ?></p>
                <a href="booking.php?service_id=<?php echo $service['service_id']; ?>" class="btn">Book Now</a>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
