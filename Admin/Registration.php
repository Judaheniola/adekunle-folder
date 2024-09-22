<?php
require('./config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [
        'username' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
        'password_letters_number' =>'',
    ];

    // Function to validate email
    function validateEmail($email) {
        return preg_match('/^[^\s@]+@(gmail\.com|yahoo\.com|outlook\.com)$/', $email);
    }

    // Validate form input
    if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['confirmpassword'])) {
        $username = mysqli_real_escape_string($con, $_POST['username']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirmpassword'];
// Validate passwords
if ($password !== $confirm_password) {
    $errors['confirm_password'] = "Password and Confirm Password do not match.";
}

if (strlen($password) < 6) {
    $errors['password'] = "Password must be at least 6 characters long.";
}

if (!preg_match('/[a-zA-Z]/', $password) || !preg_match('/\d/', $password)) {
    $errors['password_letters_number'] = "Password must contain both letters and numbers.";
}

if (!preg_match('/^[a-zA-Z0-9]+$/', $password)) {
    $errors['password'] = "Password must contain only letters and numbers.";
}


        // Validate email
        if (!validateEmail($email)) {
            $errors['email'] = "Please enter a valid email address (gmail.com, yahoo.com, or outlook.com).";
        }

        // Check if the username already exists
        $checkUsernameQuery = "SELECT * FROM adminsignup WHERE username = '$username'";
        $resultUsername = mysqli_query($con, $checkUsernameQuery);

        if (mysqli_num_rows($resultUsername) > 0) {
            $errors['username'] = "Username already exists!";
        }

        // Check if the email already exists
        $checkEmailQuery = "SELECT * FROM adminsignup WHERE email = '$email'";
        $resultEmail = mysqli_query($con, $checkEmailQuery);

        if (mysqli_num_rows($resultEmail) > 0) {
            $errors['email'] = "Email already exists!";
        }

        // If no errors, insert into database
        if (empty(array_filter($errors))) {
            // Hash the password before saving
        
            
            $sql = "INSERT INTO adminsignup (username, email, password) 
                    VALUES ('$username', '$email', '$password')";

            if (mysqli_query($con, $sql)) {
                header("Location: index.php");
                exit;
            } else {
                $errors['general'] = "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }
    } else {
        $errors['general'] = "All fields are required.";
    }

    // Close the database connection
    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Registration Page</title>

  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <style>
    <?php require("registration.css")?>
   
  </style>
</head>
<body>
  <div class="wrapper">
    <form class="form" action="Registration.php" method="post">
      <h2>Admin Registration</h2>

      <!-- General error message if present -->
      <?php if (!empty($errors['general'])): ?>
        <div class="error" id="generalError"><?php echo $errors['general']; ?></div>
      <?php endif; ?>

      <div class="input-field">
        <input type="text" name="username" id="username" value="<?php echo isset($username) ? $username : '' ?>" required>
        <label>Enter your Username</label>

        <!-- Username error message -->
        <?php if (!empty($errors['username'])): ?>
          <span class="error" id="usernameError"><?php echo $errors['username']; ?></span>
        <?php endif; ?>
      </div>
      
      <div class="input-field">
        <input type="text" name="email" id="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
        <label>Enter your email</label>

        <!-- Email error message -->
        <?php if (!empty($errors['email'])): ?>
          <span class="error" id="emailError"><?php echo $errors['email']; ?></span>
        <?php endif; ?>
      </div>

      <div class="input-field">
        <input type="password" name="password" id="password" required>
        <label>Enter your password</label>
        <i class='bx bx-hide eye-icon' id="togglePassword"></i>

        <!-- Password error message -->
        <?php if (!empty($errors['password'])): ?>
          <span class="error" id="passwordError"><?php echo $errors['password']; ?></span>
        <?php endif; ?>

        <!-- Password letters and numbers error message -->
        <?php if (!empty($errors['password_letters_number'])): ?>
          <span class="error" id="passwordLettersNumberError"><?php echo $errors['password_letters_number']; ?></span>
        <?php endif; ?>
      </div>

      <div class="input-field">
        <input type="password" name="confirmpassword" id="confirmpassword" required>
        <label>Confirm Your Password</label>
        <i class='bx bx-hide eye-icon' id="toggleConfirmPassword"></i>

        <!-- Confirm password error message -->
        <?php if (!empty($errors['confirm_password'])): ?>
          <span class="error" id="confirmPasswordError"><?php echo $errors['confirm_password']; ?></span>
        <?php endif; ?>
      </div>
      
      <button type="submit">Register</button>

      <div class="register">
        <p>Have an account? <a href="index.php">Login</a></p>
      </div>
    </form>
  </div>

  <script>
    // Hide specific error messages when input changes
    function hideErrorMessage(inputId, errorId) {
      const input = document.getElementById(inputId);
      const error = document.getElementById(errorId);

      input.addEventListener('input', function() {
        if (error) {
          error.style.display = 'none';
        }
      });
    }

    // Attach event listeners for each input field and error message
    hideErrorMessage('username', 'usernameError');
    hideErrorMessage('email', 'emailError');
    hideErrorMessage('password', 'passwordError');
    hideErrorMessage('confirmpassword', 'confirmPasswordError');
    hideErrorMessage('password', 'passwordLettersNumberError');

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

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
      const confirmPasswordInput = document.getElementById('confirmpassword');
      const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
      confirmPasswordInput.type = type;
      this.classList.toggle('bx-show');
      this.classList.toggle('bx-hide');
    });
  </script>
</body>
</html>


<script>
      // Prevent form resubmission
      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>