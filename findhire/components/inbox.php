<?php
include_once '../core/dbConfig.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Get the logged-in user's user_id from session
$receiverId = $_SESSION['user_id'];

// Check if email is set in the session, if not, fetch it from the database
if (!isset($_SESSION['email'])) {
  // Fetch the user's email from the database based on user_id
  $sql = "SELECT email FROM users WHERE user_id = :user_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':user_id', $receiverId, PDO::PARAM_INT);
  $stmt->execute();

  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $_SESSION['email'] = $user['email']; // Store the email in the session
    $receiverEmail = $user['email']; // Assign email to variable
  } else {
    echo "<p class='text-red-500'>User not found. Please log in again.</p>";
    exit();
  }
} else {
  $receiverEmail = $_SESSION['email']; // If email is already in session
}

// Fetch the received messages for the logged-in user
$query = "SELECT messages.message_id, messages.message, messages.sent_at, users.email AS sender_email 
          FROM messages 
          JOIN users ON messages.sender_id = users.user_id 
          WHERE messages.receiver_id = :receiverId 
          ORDER BY messages.sent_at DESC";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':receiverId', $receiverId, PDO::PARAM_INT);
$stmt->execute();

// Fetch messages
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inbox</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container-lg">

  <?php include_once 'navbar.php'; ?>

  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 text-dark">Inbox</h1>
      <a href="message.php" class="btn btn-primary">Compose Message</a>
    </div>

    <?php if (count($messages) > 0): ?>
      <div class="list-group">
        <?php foreach ($messages as $message): ?>
          <div class="list-group-item border rounded-lg mb-3">
            <p class="text-muted mb-1">From: <strong><?php echo htmlspecialchars($message['sender_email']); ?></strong></p>
            <p class="mb-2"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
            <p class="text-muted text-sm mb-0"><?php echo date("F j, Y, g:i a", strtotime($message['sent_at'])); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-muted">You have no messages in your inbox.</p>
    <?php endif; ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>