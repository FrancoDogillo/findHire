<?php

require_once '../core/dbConfig.php';

if ($_SESSION['role'] != 'hr') {
  header('Location: index.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $hr_id = $_SESSION['user_id'];

  // Prepare the SQL statement
  $sql = "INSERT INTO job_posts (title, description, created_by) VALUES (:title, :description, :created_by)";

  // Prepare the statement
  $stmt = $pdo->prepare($sql);

  // Bind parameters
  $stmt->bindParam(':title', $title, PDO::PARAM_STR);
  $stmt->bindParam(':description', $description, PDO::PARAM_STR);
  $stmt->bindParam(':created_by', $hr_id, PDO::PARAM_INT);

  // Execute the statement
  if ($stmt->execute()) {
    header('Location: hr_dashboard.php');
    exit();
  } else {
    echo "Error creating job post";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<?php include_once 'navbar.php'; ?>

<body class="container-lg">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow border-primary">
          <div class="card-body">
            <h3 class="text-center mb-4 text-primary">Post a New Job</h3>
            <form method="POST">
              <div class="mb-3">
                <label for="title" class="form-label">Job Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Enter job title" required>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">Job Description</label>
                <textarea name="description" id="description" class="form-control" placeholder="Enter job description"
                  required></textarea>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary px-5 py-2">
                  Post Job
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>