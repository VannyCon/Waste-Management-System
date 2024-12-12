<?php
session_start(); // Start the session

// Check if the 'resident' session is not set or is false
if (!isset($_SESSION['resident']) || $_SESSION['resident'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: resident_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Approval</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #f8f9fa;
      margin: 0;
    }

    .pending-box {
      text-align: center;
      background-color: white;
      padding: 50px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .pending-box i {
      font-size: 4rem;
      color: #ffc107;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

  <div class="pending-box">
    <i class="fas fa-hourglass-half"></i>
    <h2 class="mb-3">You're Pending</h2>
    <p class="text-muted">Waiting for admin approval...</p>
    <div class="text-right">
        <a type="button" class="btn btn-danger" href="resident_login.php">Logout</a>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
