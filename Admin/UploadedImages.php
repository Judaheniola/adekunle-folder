<?php
require('config.php'); // Include your database connection

$message = ''; // Initialize an empty message

// Delete selected images
if (isset($_POST['delete'])) {
    if (!empty($_POST['image_ids'])) {
        $image_ids = implode(',', array_map('intval', $_POST['image_ids']));
        $delete_sql = "DELETE FROM images WHERE id IN ($image_ids)";
        if ($con->query($delete_sql)) {
            $message = "Images deleted successfully.";
        } else {
            $message = "Error deleting images.";
        }
    } else {
        $message = "No images selected.";
    }
}

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
    <title>Admin Uploaded Images</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include("UploadedImages.css"); ?>
    </style>
    <script>
        // Toggle all checkboxes when the "Select All" checkbox is clicked
        function toggleSelectAll(source) {
            checkboxes = document.getElementsByName('image_ids[]');
            for(var i=0, n=checkboxes.length; i<n; i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>
</head>
<body>
    <div class="navbar">
        <div class="logo">Photomage</div>
        <a href="AdminLogout.php" style="font-size:25px"><i class="fas fa-sign-out-alt"></i></a>
        <div class="nav-items">
            <a href="AdminDashboard.php">Home Page</a>
            <a href="UploadedImages.php">Uploaded Images</a>
        </div>
    </div>

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


    <div class="select-all">
                    <input type="checkbox" onclick="toggleSelectAll(this)"> Select All
    </div>
    <form method="POST" action="">
        <div class="image-gallery">
            <?php if (empty($images)): ?>
                <p>No images found.</p>
                
            <?php else: ?>
                
                <?php foreach ($images as $image): ?>
                    <div class="image-item">
                        <input type="checkbox" name="image_ids[]" value="<?php echo $image['id']; ?>">
                        <img src="<?php echo htmlspecialchars($image['path']); ?>" alt="<?php echo htmlspecialchars($image['image_name']); ?>">
                        <p><?php echo htmlspecialchars($image['image_name']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($images)): ?>
            <div class="delete-section">
                <button type="submit" name="delete" class="delete-btn">Delete Selected</button>
                <div class="message">
                    <?php if ($message != ''): ?>
                        <p><?php echo $message; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </form>
</body>
</html>
