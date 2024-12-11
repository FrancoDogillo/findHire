<?php
include_once '../core/dbConfig.php';

// Fetch applications based on status
$statuses = ['pending', 'accepted', 'rejected'];

$applications = [];
foreach ($statuses as $status) {
  $sql = "SELECT a.application_id, a.applicant_id, a.job_post_id, a.description, a.application_status, a.application_date, a.resume, u.username, jp.title 
            FROM applications a 
            INNER JOIN users u ON a.applicant_id = u.user_id 
            INNER JOIN job_posts jp ON a.job_post_id = jp.job_post_id 
            WHERE a.application_status = :status
            ORDER BY a.application_date DESC";

  $stmt = $pdo->prepare($sql);
  $stmt->execute(['status' => $status]);

  if ($stmt->rowCount() > 0) {
    $applications[$status] = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } else {
    $applications[$status] = [];
  }
}

// Handle action to update application status via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
  $application_id = $_POST['application_id'];
  $action = $_POST['action'];  // accepted, rejected, or pending

  // Update application status in the database
  $updateSql = "UPDATE applications SET application_status = :status WHERE application_id = :application_id";
  $updateStmt = $pdo->prepare($updateSql);
  $updateStmt->execute(['status' => $action, 'application_id' => $application_id]);

  // Redirect to the applicants page after the action
  header('Location: applicants.php');
  exit(); // Make sure the script stops after redirect
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Applications</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

</head>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Applications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<body class="container-lg">
  <?php
  include_once 'navbar.php';
  include_once '../index.php';
  ?>

  <div class="container py-5">
    <h1 class="display-4 text-center text-primary mb-5">Applications</h1>

    <!-- Display Pending Applications -->
    <section class="mb-5">
      <h2 class="h3 text-dark mb-4">Pending Applications</h2>
      <?php if (!empty($applications['pending'])): ?>
        <div class="table-responsive bg-white shadow rounded">
          <table class="table table-striped table-bordered">
            <thead class="table-dark">
              <tr>
                <th scope="col">Application ID</th>
                <th scope="col">Applicant</th>
                <th scope="col">Job Title</th>
                <th scope="col">Description</th>
                <th scope="col">Status</th>
                <th scope="col">Application Date</th>
                <th scope="col">Resume</th>
                <th scope="col" class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($applications['pending'] as $application): ?>
                <tr>
                  <td><?php echo htmlspecialchars($application['application_id']); ?></td>
                  <td><?php echo htmlspecialchars($application['username']); ?></td>
                  <td><?php echo htmlspecialchars($application['title']); ?></td>
                  <td><?php echo nl2br(htmlspecialchars($application['description'])); ?></td>
                  <td class="text-info"><?php echo htmlspecialchars($application['application_status']); ?></td>
                  <td><?php echo date('F j, Y, g:i a', strtotime($application['application_date'])); ?></td>
                  <td>
                    <a href="<?php echo htmlspecialchars($application['resume']); ?>" class="btn btn-link" download>Download
                      Resume</a>
                  </td>
                  <td class="text-center">
                    <form method="POST" class="d-inline"
                      id="application-form-<?php echo $application['application_id']; ?>">
                      <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                      <input type="hidden" name="action" value=""> <!-- Hidden action input -->

                      <?php if ($application['application_status'] == 'pending'): ?>
                        <button type="button"
                          onclick="confirmAction('accepted', <?php echo $application['application_id']; ?>)"
                          class="btn btn-success btn-sm">
                          <i class="fas fa-check-circle"></i> Accept
                        </button>
                        <button type="button"
                          onclick="confirmAction('rejected', <?php echo $application['application_id']; ?>)"
                          class="btn btn-danger btn-sm">
                          <i class="fas fa-times-circle"></i> Reject
                        </button>
                      <?php endif; ?>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted text-center mt-4">No pending applications at the moment.</p>
      <?php endif; ?>
    </section>

    <!-- Modal for Confirmation -->
    <div id="confirmation-modal" class="modal fade" tabindex="-1" aria-labelledby="confirmation-modal-label"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmation-modal-label">Are you sure?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <p id="modal-message"></p>
          </div>
          <div class="modal-footer">
            <button id="confirm-button" class="btn btn-success">Yes</button>
            <button id="cancel-button" class="btn btn-danger" data-bs-dismiss="modal">No</button>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function confirmAction(action, applicationId) {
      const modal = new bootstrap.Modal(document.getElementById('confirmation-modal'));
      const modalMessage = document.getElementById('modal-message');
      const confirmButton = document.getElementById('confirm-button');

      modalMessage.textContent = `Are you sure you want to ${action} this application?`;
      modal.show();

      confirmButton.onclick = function () {
        // Set the action field value
        const form = document.getElementById('application-form-' + applicationId);
        form.elements['action'].value = action;

        // Submit the form
        form.submit();

        // Hide the modal after submission
        modal.hide();
      };
    }
  </script>
</body>

</html>