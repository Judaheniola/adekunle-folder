<?php
session_start();
require_once('config.php');

// Fetch categories
$category_sql = "SELECT DISTINCT category FROM images";
$category_result = $con->query($category_sql);

// Fetch images based on selected category or show all
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'all';
$image_sql = "SELECT * FROM images";
if ($selected_category !== 'all') {
    $image_sql .= " WHERE category = '" . $con->real_escape_string($selected_category) . "'";
}
$image_result = $con->query($image_sql);

// Fetch images
$images = [];
if ($image_result->num_rows > 0) {
    while ($row = $image_result->fetch_assoc()) {
        $images[] = $row;
    }
}

// Close the connection
$con->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
       <?php require("userdashboard.css")?>
    </style>
</head>
<body>
<div class="navbar">
    <div class="logo">Photomage</div>
    <a href="Userlogout.php" style="font-size:25px">
      <i class="fas fa-sign-out-alt"></i></a>
</div>

<!-- Welcome User Dashboard -->
<div class="heading">
    <h1>Welcome To User Dashboard</h1>
    <div style="display:flex; justify-content:center"> 
        <p>
            Discover a diverse collection of images tailored to your needs.
            Whether you’re looking for stunning visuals for your personal 
            or professional projects, you can easily download high-quality images from various categories:
        </p>
    </div>
</div>

<!-- Uploaded Images -->
<h1 style="text-align:center">All Images</h1>

<!-- Category Filter -->
<div class="filter-section">
    <form method="GET" action="">
        <label for="category">Filter by category:</label>
        <select name="category" id="category" onchange="this.form.submit()">
            <option value="all" <?php echo $selected_category == 'all' ? 'selected' : ''; ?>>All</option>
            <?php while ($cat = $category_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $selected_category == $cat['category'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['category']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>
</div>

<div class="image-container">
    <?php foreach ($images as $image): ?>
        <div class="image-item">
            <img src="../Admin/<?php echo htmlspecialchars($image['path']); ?>" alt="Uploaded Image" />
            <div class="category-label"><?php echo htmlspecialchars($image['category']); ?></div>
            <button class="download-btn" data-image-path="../Admin/<?php echo htmlspecialchars($image['path']); ?>">Download</button>
        </div>
    <?php endforeach; ?>
</div>

<!-- Footer Section -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-left">
            <h3>Photomage</h3>
            <p>© 2024 Adekunle. All rights reserved.</p>
        </div>
        <div class="footer-center">
            <h4>Contact Us</h4>
            <ul>
                <li><a href="mailto:support@example.com">support@example.com</a></li>
                <li><a href="tel:+1234567890">+123 456 7890</a></li>
            </ul>
        </div>
        <div class="footer-right">
            <h4>Follow Us</h4>
            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
        </div>
    </div>
</footer>


<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.download-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const imagePath = this.getAttribute('data-image-path');
            const a = document.createElement('a');
            a.href = imagePath;
            a.download = ''; // Use the default filename or set a specific filename
            document.body.appendChild(a);
            a.click();
            a.remove();
        });
    });
});
</script>

</body>
</html>
