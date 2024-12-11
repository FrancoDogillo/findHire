<?php
include_once '../core/dbConfig.php';

// Check if user is an applicant

if ($_SESSION['role'] != 'applicant') {
  header('Location: index.php');
  exit();
}

// Handle job application
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply'])) {
  $job_post_id = $_POST['job_post_id'];
  $applicant_id = $_SESSION['user_id'];
  $description = $_POST['description'];

  // Handle file upload (resume)
  if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
    $resume = $_FILES['resume'];
    $allowedExtensions = ['pdf'];
    $fileExtension = pathinfo($resume['name'], PATHINFO_EXTENSION);

    // Check file extension
    if (in_array(strtolower($fileExtension), $allowedExtensions)) {
      $uploadDirectory = '../uploads/resumes/';
      $fileName = uniqid('resume_') . '.' . $fileExtension;
      $filePath = $uploadDirectory . $fileName;

      // Move the uploaded file to the server
      if (move_uploaded_file($resume['tmp_name'], $filePath)) {
        // Insert the application into the database
        $applySql = "INSERT INTO applications (job_post_id, applicant_id, description, resume) VALUES (?, ?, ?, ?)";
        $applyStmt = $pdo->prepare($applySql);
        $applyStmt->execute([$job_post_id, $applicant_id, $description, $filePath]);

        // Redirect to the dashboard after applying
        header("Location: applicant_dashboard.php");
        exit();
      } else {
        $error = 'Error uploading the file. Please try again.';
      }
    } else {
      $error = 'Only PDF files are allowed for resume upload.';
    }
  } else {
    $error = 'Please upload a resume in PDF format.';
  }
}

// Fetch job post details for the application
if (isset($_GET['job_post_id'])) {
  $job_post_id = $_GET['job_post_id'];
  $jobPostSql = "SELECT * FROM job_posts WHERE job_post_id = ?";
  $jobPostStmt = $pdo->prepare($jobPostSql);
  $jobPostStmt->execute([$job_post_id]);
  $jobPost = $jobPostStmt->fetch(PDO::FETCH_ASSOC);

  if (!$jobPost) {
    // Redirect if the job post does not exist
    header('Location: applicant_dashboard.php');
    exit();
  }
} else {
  // Redirect if job post ID is not provided
  header('Location: applicant_dashboard.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apply for Job</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

  <?php include_once 'navbar.php'; ?>

  <div class="container bg-white p-5 mt-5 rounded shadow-lg">
    <h1 class="display-4 text-center text-primary mb-4">Apply for Job:
      <?php echo htmlspecialchars($jobPost['title']); ?>
    </h1>

    <!-- Error description -->
    <?php if (isset($error)): ?>
      <div class="alert alert-danger" role="alert">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <!-- Application Form -->
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="job_post_id" value="<?php echo $job_post_id; ?>">

      <!-- Cover Letter (Why you're the best fit) -->
      <div class="mb-4">
        <label for="description" class="form-label">Why are you the best fit for this role?</label>
        <textarea id="description" name="description" rows="4" class="form-control" required></textarea>
      </div>

      <!-- Resume Upload -->
      <div class="mb-4">
        <label for="resume" class="form-label">Upload Your Resume (PDF only)</label>
        <input type="file" name="resume" id="resume" accept="application/pdf" class="form-control" required>
      </div>

      <!-- Submit Button -->
      <div class="d-flex justify-content-end">
        <button type="submit" name="apply" class="btn btn-primary px-5 py-2">
          Submit Application
        </button>
      </div>
    </form>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>