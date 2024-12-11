<?php
require_once 'core/dbConfig.php';


// Redirect if the user is not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

?>