<?php
// Include database connection
require('config.php');

$errors = []; // Initialize errors array
$successMessage = ''; // Initialize success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form input
    $email = $_POST['email'];
    $newpassword = $_POST['newpassword'];
    $confirmnewpassword = $_POST['confirmnewpassword'];

    // Check if email exists
    $sql = "SELECT * FROM adminsignup WHERE email = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $errors[] = "Email does not exist.";
    } else {
        // Validate passwords
        if (empty($newpassword) || empty($confirmnewpassword)) {
            $errors[] = "Both password fields are required";
        }

        if ($newpassword !== $confirmnewpassword) {
            $errors[] = "New Password and Confirm New Password do not match.";
        }

        if (strlen($newpassword) < 6) {
            $errors[] = "New Password must be at least 6 characters long.";
        }

        if (!preg_match('/[a-zA-Z]/', $newpassword) || !preg_match('/\d/', $newpassword)) {
            $errors[] = "New Password must contain both letters and numbers.";
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $newpassword)) {
            $errors[] = "New Password must contain only letters and numbers.";
        }

        if (empty($errors)) {
        
            // Update the password in the database
            $sql = "UPDATE adminsignup SET password = ? WHERE email = ?";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $newpassword, $email);

            if ($stmt->execute()) {
                $successMessage = "Password successfully reset!";
            } else {
                $errors[] = "Error resetting password: " . $con->error;
            }

            $stmt->close();
        }
    }

    $con->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Password Reset</title>
  <link rel="stylesheet" href="index.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    <?php require("forgetpassword.css")?>
    /* Custom alert box styling */
    .alert-box {
        display: none;
        position: fixed;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        border-radius: 5px;
        z-index: 1000;
        margin-bottom: 20px;
    }
    .alert-box.error {
        background-color: #f44336;
    }
    .alert-box.show {
        display: block;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <form action="ForgetPassword.php" method="post">
      <h2>Admin Password Reset</h2>

      <?php if (!empty($errors) || !empty($successMessage)): ?>
        <div class="alert-box <?php echo empty($errors) ? 'success' : 'error'; ?>">
          <?php
          if (!empty($errors)) {
            foreach ($errors as $error) {
              echo htmlspecialchars($error) . '<br>';
            }
          } else if (!empty($successMessage)) {
            echo htmlspecialchars($successMessage);
          }
          ?>
        </div>
      <?php endif; ?>

      <div class="input-field">
        <input type="text" name="email" required>
        <label>Enter your email</label>
      </div>
      <div class="input-field">
        <input type="password" name="newpassword" id="newpassword" required>
        <label>Enter New password</label>
        <i class='bx bx-hide eye-icon' id="togglePassword"></i>
      </div>

      <div class="input-field">
        <input type="password" name="confirmnewpassword" id="confirmnewpassword" required>
        <label>Confirm New password</label>
        <i class='bx bx-hide eye-icon' id="toggleConfirmPassword"></i>
      </div>
      <button type="submit">Reset Password</button>
      <div class="register">
        <p>Go To Login page! <a href="index.php">Login</a></p>
      </div>
    </form>
  </div>
  <!-- Custom alert box -->
  <div id="alertBox" class="alert-box"></div>

  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('newpassword');
      const type = passwordInput.type === 'password' ? 'text' : 'password';
      passwordInput.type = type;
      this.classList.toggle('bx-show');
      this.classList.toggle('bx-hide');
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
      const confirmPasswordInput = document.getElementById('confirmnewpassword');
      const type = confirmPasswordInput.type === 'password' ? 'text' : 'password';
      confirmPasswordInput.type = type;
      this.classList.toggle('bx-show');
      this.classList.toggle('bx-hide');
    });

    // Show alert box for success or error messages
    document.addEventListener('DOMContentLoaded', function() {
      <?php if (!empty($errors)): ?>
        var alertBox = document.getElementById('alertBox');
        alertBox.textContent = '<?php echo htmlspecialchars(implode('<br>', $errors)); ?>';
        alertBox.classList.add('error', 'show');
        setTimeout(function() {
          alertBox.classList.remove('show');
        }, 5000); // Hide alert after 5 seconds
      <?php elseif (!empty($successMessage)): ?>
        var alertBox = document.getElementById('alertBox');
        alertBox.textContent = '<?php echo htmlspecialchars($successMessage); ?>';
        alertBox.classList.add('show');
        setTimeout(function() {
          alertBox.classList.remove('show');
        }, 50000); // Hide alert after 5 seconds
      <?php endif; ?>
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
