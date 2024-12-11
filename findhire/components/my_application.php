<?php
include_once '../core/dbConfig.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php'); // Redirect to login if not logged in
  exit();
}

$applicant_id = $_SESSION['user_id']; // The applicant's ID from session

// Fetch the application details for the logged-in applicant
$sql = "SELECT a.application_id, a.application_status, a.description, a.application_date, a.resume, jp.title AS job_title
        FROM applications a
        JOIN job_posts jp ON a.job_post_id = jp.job_post_id
        WHERE a.applicant_id = :applicant_id
        ORDER BY a.application_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['applicant_id' => $applicant_id]);

$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Applications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="">

  <?php include_once 'navbar.php'; ?>

  <div class="container bg-white p-4 mt-5 rounded-lg shadow-sm">
    <h1 class="text-center text-info mb-5">My Applications</h1>

    <?php if (empty($applications)): ?>
      <p class="text-center text-muted">You haven't applied for any jobs yet.</p>
    <?php else: ?>
      <!-- Table (Visible on medium and large screens) -->
      <div class="table-responsive-lg mb-5">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>Application ID</th>
              <th>Job Title</th>
              <th>Description</th>
              <th>Status</th>
              <th>Application Date</th>
              <th>Resume</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($applications as $application): ?>
              <tr>
                <td><?php echo htmlspecialchars($application['application_id']); ?></td>
                <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($application['description'])); ?></td>
                <td class="text-success font-weight-bold">
                  <?php echo ucfirst(htmlspecialchars($application['application_status'])); ?>
                </td>
                <td><?php echo date('F j, Y, g:i a', strtotime($application['application_date'])); ?></td>
                <td>
                  <a href="<?php echo htmlspecialchars($application['resume']); ?>" class="btn btn-outline-primary btn-sm"
                    download>Download</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Mobile View: Card Layout (Visible only on small screens) -->
      <div class="d-block d-lg-none">
        <?php foreach ($applications as $application): ?>
          <div class="card mb-3 shadow-sm border-0">
            <div class="card-body">
              <h5 class="card-title text-dark">Application ID:
                <?php echo htmlspecialchars($application['application_id']); ?>
              </h5>
              <p><strong>Job Title:</strong> <?php echo htmlspecialchars($application['job_title']); ?></p>
              <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($application['description'])); ?></p>
              <p><strong>Status:</strong> <span
                  class="text-success"><?php echo ucfirst(htmlspecialchars($application['application_status'])); ?></span>
              </p>
              <p><strong>Application Date:</strong>
                <?php echo date('F j, Y, g:i a', strtotime($application['application_date'])); ?></p>
              <a href="<?php echo htmlspecialchars($application['resume']); ?>" class="btn btn-outline-success btn-sm"
                download>Download Resume</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>