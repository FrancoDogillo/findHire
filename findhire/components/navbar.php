<?php

$sql = "SELECT email FROM users WHERE user_id = " . $_SESSION['user_id'];
$stmt = $pdo->prepare($sql);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$email = $user['email'];

?>

<head>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</head>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'applicant_dashboard.php' || basename($_SERVER['PHP_SELF']) === 'hr_dashboard.php') ? 'text-primary' : 'text-secondary'; ?>"
            href="<?php echo ($_SESSION['role'] === 'applicant') ? 'applicant_dashboard.php' : 'hr_dashboard.php'; ?>">
            Dashboard
          </a>
        </li>
        <?php if ($_SESSION['role'] === 'hr'): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'add_job.php') ? 'text-primary' : 'text-secondary'; ?>"
              href="add_job.php">Add Job</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'applicants.php') ? 'text-primary' : 'text-secondary'; ?>"
              href="applicants.php">Applications</a>
          </li>
        <?php endif; ?>
        <?php if ($_SESSION['role'] === 'applicant'): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'my_application.php') ? 'text-primary' : 'text-secondary'; ?>"
              href="my_application.php">My Applications</a>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) === 'inbox.php') ? 'text-primary' : 'text-secondary'; ?>"
            href="inbox.php">Inbox</a>
        </li>
        <li class="nav-item">
          <button class="btn btn-link nav-link" onclick="openModal()">Sign Out</button>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Confirmation Modal -->
<div class="modal fade" id="signoutModal" tabindex="-1" aria-labelledby="signoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="signoutModalLabel">Confirm Sign Out</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to sign out?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="../logout.php" class="btn btn-danger">Yes, Sign Out</a>
      </div>
    </div>
  </div>
</div>

<script>
  // Function to open the sign-out confirmation modal
  function openModal() {
    var modal = new bootstrap.Modal(document.getElementById('signoutModal'));
    modal.show();
  }
</script>