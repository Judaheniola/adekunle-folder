<?php
require("config.php");
session_start();

// Include your database connection code here

$error_message = ""; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the username exists in the single user table
    $email_query = "SELECT * FROM usersignup WHERE email='$email'";
    $email_result = mysqli_query($con, $email_query);

    if (mysqli_num_rows($email_result) == 1) {
        // Username exists, now check the password
        $password_query = "SELECT * FROM usersignup WHERE email='$email' AND password='$password'";
        $password_result = mysqli_query($con, $password_query);

        if (mysqli_num_rows($password_result) == 1) {
            // Successful single user login
            $row = mysqli_fetch_assoc($password_result);

            echo "<script>
                    window.location.href = './userdashboard.php';
                  </script>";
            exit();
        } else {
            // Failed login - Incorrect password
            $error_message = "Incorrect password";
        }
    } else {
            // Failed login - Username not found
            $error_message = "Email not found";
        }
    }

?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Login Page</title>
  <link rel="stylesheet" href="index.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
         <?php require("index.css")?>
         .error-container {
            color: red;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .error-message {
            margin: 0;
        }

        .input-field .eye-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size:20px;
    color:white;
  }

      </style>
</head>
<body>
  <div class="wrapper">
    <form action="index.php" method="post">
      <h2>User Login</h2>
      <?php if (!empty($error_message)): ?>
                    <div class="error-container">
                        <p class="error-message"><?php echo $error_message; ?></p>
                    </div>
                <?php endif; ?>
        <div class="input-field">
        <input type="text" name="email" required>
        <label>Enter your email</label>
      </div>
      <div class="input-field">
        <input type="password" name="password" id="password" required>
        <label>Enter your password</label>
        <i class='bx bx-hide eye-icon' id="togglePassword"></i>
      </div>
      <div class="forget">
        <a href="ForgetPassword.php">Forgot password?</a>
      </div>
      <button type="submit">Log In</button>
      <div class="register">
        <p>Don't have an account? <a href="UserRegistration.php">Register</a></p>
      </div>
    </form>
  </div>
</body>
</html>

<script>
      // Prevent form resubmission
      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }


        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      this.classList.toggle('bx-show');
      this.classList.toggle('bx-hide');
    });



</script>