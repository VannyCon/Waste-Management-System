<?php
session_start();
include_once('../../config/config.php');

// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: ../admin_login.php");
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current admin data (for displaying the initial values)
$sql = "SELECT username, password FROM user_administrator WHERE id = 1";
$result = $conn->query($sql);
$adminData = $result->fetch_assoc();

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $newPassword = $_POST['password'];

    // Update query
    $updateSql = "UPDATE user_administrator SET username = ?, password = ? WHERE id = 1";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ss", $newUsername, $newPassword);

    if ($stmt->execute()) {
        // Redirect to index.php after saving changes
        header("Location: index.php");
        exit();
    } else {
        $message = "Error updating profile: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Modal for Editing Profile -->
<div class="modal show" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" style="display: block;" aria-modal="true" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center w-100" id="editModalLabel">
          <i class="fas fa-user-shield"></i><b>ADMIN PROFILE
        </h5>
        <button type="button" class="btn-close" aria-label="Close" onclick="window.location.href='index.php';"></button>
      </div>
      <div class="modal-body">
        <form id="editForm" method="POST">
            <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
            <div class="mb-3">
                <label for="newUsername" class="form-label">Username</label>
                <input type="text" class="form-control" id="newUsername" name="username" value="<?php echo $adminData['username']; ?>">
            </div>
            <div class="mb-3">
                <label for="newPassword" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="newPassword" name="password" value="<?php echo $adminData['password']; ?>">
                    <button class="btn btn-outline-secondary" type="button" id="showPasswordToggle">Show</button>
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php';">Close</button>
      </div>
        </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Show/Hide Password Toggle
    const passwordField = document.getElementById('newPassword');
    const toggleButton = document.getElementById('showPasswordToggle');

    toggleButton.addEventListener('click', () => {
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleButton.textContent = 'Hide';
        } else {
            passwordField.type = 'password';
            toggleButton.textContent = 'Show';
        }
    });
</script>
</body>
</html>
