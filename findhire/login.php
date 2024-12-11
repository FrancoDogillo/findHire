<?php
require_once 'core/dbConfig.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Collect and sanitize user input
  $username = htmlspecialchars($_POST['username']);
  $password = htmlspecialchars($_POST['password']);

  // Prepare the SQL query to check the user credentials
  $query = "SELECT * FROM users WHERE username = :username";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':username', $username);
  $stmt->execute();

  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user && password_verify($password, $user['password'])) {
    // If password is correct, start the session and redirect
    session_start();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role']; // Store user role in session


    // Redirect based on user role
    if ($_SESSION['role'] == 'hr') {
      header("Location: components/hr_dashboard.php"); // HR Dashboard
    } else {
      header("Location: components/applicant_dashboard.php"); // Applicant Dashboard
    }
    exit();
  } else {
    $error = "Invalid username or password.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="bg-white p-5 rounded-3 shadow-lg w-100" style="max-width: 420px;">
      <form method="POST">
        <div class="text-center mb-4">
          <h2 class="fw-bold text-dark">Sign In</h2>
          <p class="text-muted">Access your account</p>
        </div>
        <?php if (isset($error) && $error != "") {
          echo "<div class='alert alert-danger text-center'>$error</div>";
        } ?>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input id="username" name="username" type="text" required class="form-control border-2 rounded-pill px-3 py-2"
            placeholder="Enter your username">
        </div>
        <div class="mb-4">
          <label for="password" class="form-label">Password</label>
          <input id="password" name="password" type="password" required
            class="form-control border-2 rounded-pill px-3 py-2" placeholder="Enter your password">
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-dark btn-lg rounded-pill">Login</button>
        </div>
        <p class="text-center text-muted mt-4">
          Don't have an account?
          <a href="registration.php" class="text-dark fw-semibold">Register here</a>
        </p>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>