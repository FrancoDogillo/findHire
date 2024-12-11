<?php
include_once '../core/dbConfig.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Get the logged-in user's user_id from session
$senderId = $_SESSION['user_id'];

// Check if email is set in the session, if not, fetch it from the database
if (!isset($_SESSION['email'])) {
  // Fetch the user's email from the database based on user_id
  $sql = "SELECT email FROM users WHERE user_id = :user_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':user_id', $senderId, PDO::PARAM_INT);
  $stmt->execute();

  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $_SESSION['email'] = $user['email']; // Store the email in the session
    $senderEmail = $user['email']; // Assign email to variable
  } else {
    echo "<p class='text-red-500'>User not found. Please log in again.</p>";
    exit();
  }
} else {
  $senderEmail = $_SESSION['email']; // If email is already in session
}

// Check if the receiver email and message are provided
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_email']) && isset($_POST['message'])) {
  $receiverEmail = $_POST['receiver_email'];
  $messageContent = $_POST['message'];

  // Fetch the receiver's user ID based on the email
  $query = "SELECT user_id FROM users WHERE email = :receiverEmail";
  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':receiverEmail', $receiverEmail, PDO::PARAM_STR);
  $stmt->execute();

  if ($stmt->rowCount() > 0) {
    // Receiver found, get the user_id
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);
    $receiverId = $receiver['user_id'];

    // Insert the message into the messages table
    $insertQuery = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (:senderId, :receiverId, :messageContent)";
    $insertStmt = $pdo->prepare($insertQuery);
    $insertStmt->bindParam(':senderId', $senderId, PDO::PARAM_INT);
    $insertStmt->bindParam(':receiverId', $receiverId, PDO::PARAM_INT);
    $insertStmt->bindParam(':messageContent', $messageContent, PDO::PARAM_STR);

    if ($insertStmt->execute()) {
      $success = true; // Flag to show the modal on success
    } else {
      $error = "Failed to send the message.";
    }
  } else {
    $error = "Receiver email not found in the database.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Send Message</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script>
    // Function to redirect to the dashboard after modal closes
    function redirectToDashboard() {
      window.location.href = "<?php echo ($_SESSION['role'] === 'applicant') ? 'applicant_dashboard.php' : 'hr_dashboard.php'; ?>";
    }
  </script>
</head>

<body class="bg-light">

  <!-- Container for centering the form -->
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <!-- Message form -->
    <div class="card shadow-lg" style="max-width: 600px; width: 100%;">
      <div class="card-header text-center bg-primary text-white">
        <h1 class="fs-3 fw-bold"><a href="inbox.php">&#8592;</a> Send Message</h1>
      </div>
      <div class="card-body">
        <form action="message.php" method="POST">
          <!-- Receiver email -->
          <div class="mb-4">
            <label for="receiver_email" class="form-label">Receiver's Email:</label>
            <input type="email" name="receiver_email" id="receiver_email" required class="form-control">
          </div>

          <!-- Message text area -->
          <div class="mb-4">
            <label for="message" class="form-label">Your Message:</label>
            <textarea name="message" id="message" rows="4" required class="form-control"></textarea>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary w-100 py-2">
              Send Message
            </button>
          </div>
        </form>

        <!-- Back to Inbox button -->
        <div class="mt-2 text-center">
          <a href="inbox.php" class="btn btn-secondary w-100 py-2">Back to Inbox</a>
        </div>
      </div>
    </div>


  </div>

  <!-- Success Modal -->
  <?php if (isset($success) && $success): ?>
    <div id="successModal" class="modal d-block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title">Success!</h5>
          </div>
          <div class="modal-body text-center">
            <p>Your message was sent successfully.</p>
            <button onclick="redirectToDashboard()" class="btn btn-success w-100">
              Go to Dashboard
            </button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Error Modal -->
  <?php if (isset($error)): ?>
    <div id="errorModal" class="modal d-block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Error!</h5>
          </div>
          <div class="modal-body text-center">
            <p><?php echo htmlspecialchars($error); ?></p>
            <button onclick="window.history.back()" class="btn btn-danger w-100">
              Go Back
            </button>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

</body>

</html>