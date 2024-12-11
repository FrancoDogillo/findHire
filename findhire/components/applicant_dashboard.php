<?php
include_once '../core/dbConfig.php';

// Check if user is an applicant
if ($_SESSION['role'] != 'applicant') {
  header('Location: index.php');
  exit();
}

// Fetch all job posts along with the username of the creator
$sql = "SELECT jp.job_post_id, jp.title, jp.description, jp.created_at, u.username, u.email
        FROM job_posts jp 
        INNER JOIN users u ON jp.created_by = u.user_id 
        ORDER BY jp.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Check if there are any job posts
if ($stmt->rowCount() > 0) {
  $jobPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  $jobPosts = [];
}

// Handle job application
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
  $job_post_id = $_POST['job_post_id'];
  $applicant_id = $_SESSION['user_id'];

  // Insert the application into the database
  $applySql = "INSERT INTO applications (job_post_id, applicant_id) VALUES (?, ?)";
  $applyStmt = $pdo->prepare($applySql);
  $applyStmt->execute([$job_post_id, $applicant_id]);

  // Redirect to avoid reapplying on refresh
  header("Location: applicant_dashboard.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Applicant Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

  <?php
  include_once 'navbar.php';
  ?>

  <div class="container py-5">
    <h1 class="text-center text-primary mb-5">Job Posts</h1>

    <!-- Display job posts -->
    <?php if (!empty($jobPosts)): ?>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
        <?php foreach ($jobPosts as $post): ?>
          <div class="col">
            <div class="card shadow-sm border-light">
              <div class="card-body">
                <h5 class="card-title text-dark"><?php echo htmlspecialchars($post['title']); ?></h5>
                <p class="card-text text-muted"><?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
                <p class="text-secondary mb-2">Posted by:
                  <span class="fw-semibold text-dark"><?php echo htmlspecialchars($post['username']); ?></span>
                </p>
                <p class="text-secondary mb-2">Posted on:
                  <span class="fw-semibold"><?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></span>
                </p>
                <p class="text-secondary">Contact:
                  <span class="fw-semibold text-dark"><?php echo htmlspecialchars($post['email']); ?></span>
                </p>
                <!-- Apply Button -->
                <div class="mt-3">
                  <a href="apply_job.php?job_post_id=<?php echo $post['job_post_id']; ?>" class="btn btn-primary w-100">
                    Apply for Job
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-muted fs-5">No job posts available at the moment.</p>
    <?php endif; ?>
  </div>

</body>

</html>