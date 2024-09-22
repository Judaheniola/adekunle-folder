<?php
session_start(); // Start the session

require('config.php'); // Include your database connection

if (isset($_POST['upload'])) {
    $category = $_POST['category'];
    $image = $_FILES['image'];

    // Check if file was uploaded
    if ($image['error'] == UPLOAD_ERR_OK) {
        $imageName = basename($image['name']);
        $targetDirectory = "uploads/"; // Ensure this directory exists
        $targetFile = $targetDirectory . $imageName;
        
        // Make sure to store the relative path for easy access on the frontend
        $relativePath = "/" . $targetFile;
        // Move the uploaded file to the target directory
        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            // Prepare SQL query to insert image details into the database
            $stmt = $con->prepare("INSERT INTO images (image_name, category, path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $imageName, $category, $targetFile);

            // Execute the query
            if ($stmt->execute()) {
                $_SESSION['message'] = "Image uploaded and saved successfully.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $stmt->error;
                $_SESSION['message_type'] = "error";
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Error moving uploaded file.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Error uploading file.";
        $_SESSION['message_type'] = "error";
    }

    $con->close();

    header("Location: AdminDashboard.php"); // Redirect to the form page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php require("AdminDashboard.css")?>
    </style>
</head>
<body>
    <div class="navbar">

 
    <div class="logo">Photomage</div>
    <a href="AdminLogout.php" style=" font-size:25px"><i class="fas fa-sign-out-alt"></i></a>
 
       
        </h2>
        <div class="nav-items">
            <a href="AdminDashboard.php">Home Page</a>
            <a href="UploadedImages.php">Uploaded Images</a>
        </div>
    </div>

    <div class="upload-form">
        <?php
            if (isset($_SESSION['message'])) {
                $messageType = $_SESSION['message_type'];
                echo '<div class="message ' . $messageType . '">' . $_SESSION['message'] . '</div>';
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            }
        ?>
        <h2 style="text-align:center">Upload Image</h2>
        <form action="AdminDashboard.php" method="post" enctype="multipart/form-data">
            <label for="image">Select Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <label for="category">Category:</label>
            <select id="category" name="category" required>
                <option value="#">Select A Category</option>
                <option value="black_and_white">Black and White</option>
                <option value="wallpapers">Wallpapers</option>
                <option value="nature">Nature</option>
                <option value="travel">Travel</option>
                <option value="interior">Interior</option>
                <option value="patterns">Patterns</option>
                <option value="street_photography">Street Photography</option>
                <option value="film">Film</option>
                <option value="animal">Animal</option>
                <option value="archival">Archival</option>
                <option value="sports">Sports</option>
                <option value="fashion_and_beauty">Fashion and Beauty</option>
                <option value="spirituality">Spirituality</option>
                <option value="business_and_work">Business and Work</option>
                <option value="food_and_drinks">Food and Drinks</option>
                <option value="health_and_wellness">Health and Wellness</option>
            </select>

            <button type="submit" name="upload">Upload</button>
        </form>
    </div>
</body>
</html>
