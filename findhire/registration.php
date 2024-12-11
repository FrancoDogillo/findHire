<?php
require_once 'core/dbConfig.php';

// Define error variable
$error = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get form data
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $email = $_POST['email'];
  $role = $_POST['role']; // assuming role is selected (applicant or hr)

  // Validate password
  if ($password !== $confirm_password) {
    $error = "Passwords do not match!";
  } else {
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database using PDO
    $sql = "INSERT INTO users (username, password, role, email) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $hashed_password, $role, $email]);

    if ($stmt) {
      // Registration successful, redirect to login page
      header("Location: login.php");
      exit();
    } else {
      $error = "An error occurred while registering.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="bg-white p-5 rounded-3 shadow-lg w-100" style="max-width: 480px;">
      <form action="registration.php" method="POST">
        <div class="text-center mb-4">
          <h2 class="fw-bold text-primary">Register</h2>
          <p class="text-muted">Create your account</p>
        </div>
        <?php if (isset($error) && $error != "") {
          echo "<div class='alert alert-danger text-center'>$error</div>";
        } ?>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" name="username" id="username" required class="form-control border-2 rounded-pill px-3 py-2"
            placeholder="Enter your username">
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" required class="form-control border-2 rounded-pill px-3 py-2"
            placeholder="Enter your email">
        </div>
        <div class="mb-3">
          <label for="role" class="form-label">Role</label>
          <select name="role" id="role" required class="form-select border-2 rounded-pill px-3 py-2">
            <option value="applicant">Applicant</option>
            <option value="hr">HR</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" required
            class="form-control border-2 rounded-pill px-3 py-2" placeholder="Enter your password">
        </div>
        <div class="mb-4">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" id="confirm_password" required
            class="form-control border-2 rounded-pill px-3 py-2" placeholder="Confirm your password">
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary btn-lg rounded-pill">Register</button>
        </div>
        <p class="text-center text-muted mt-4">
          Already have an account?
          <a href="login.php" class="text-primary fw-semibold">Login here</a>
        </p>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>