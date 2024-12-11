<?php
include_once '../core/dbConfig.php';

// Fetch all job posts along with the username of the creator
$sql = "SELECT jp.job_post_id, jp.title, jp.description, jp.created_at, u.username, u.email
        FROM job_posts jp 
        INNER JOIN users u ON jp.created_by = u.user_id 
        ORDER BY jp.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// Check if there are any job posts
if ($stmt->rowCount() > 0) {
  // Fetch all results
  $jobPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  $jobPosts = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Job Posts</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container-lg">

  <?php include_once 'navbar.php'; ?>

  <div class="container py-5">
    <h1 class="display-4 text-center text-primary mb-5">Job Posts</h1>

    <!-- Display job posts -->
    <?php if (!empty($jobPosts)): ?>
      <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
        <?php foreach ($jobPosts as $post): ?>
          <div class="col">
            <div class="card shadow-sm border-light rounded-3">
              <div class="card-body">
                <h2 class="card-title text-dark mb-3"><?php echo htmlspecialchars($post['title']); ?></h2>
                <p class="card-text text-muted mb-4 text-truncate" style="max-height: 4.5rem; overflow: hidden;">
                  <?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
                <div class="d-flex flex-column text-muted">
                  <p class="mb-1">Posted by: <span class="fw-bold"><?php echo htmlspecialchars($post['username']); ?></span>
                  </p>
                  <p class="mb-1">Posted on:
                    <span><?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></span></p>
                  <p class="mb-0">Contact: <span class="text-primary"><?php echo htmlspecialchars($post['email']); ?></span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-muted mt-5">No job posts available at the moment.</p>
    <?php endif; ?>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>