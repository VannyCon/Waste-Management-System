<?php
session_start();
include_once('../../../config/config.php');
// Only allow admins to access this page
if (!isset($_SESSION['admin'])) {
    header("Location: ../adminLogin.php"); // Redirect to admin login if not logged in
    exit();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Approve/Reject actions
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $action = $_POST['action'];
    $user_id = $_POST['user_id'];
    
    if ($action == 'approve') {
        $sql = "UPDATE users SET status='approved' WHERE id=?";
    } elseif ($action == 'reject') {
        $sql = "UPDATE users SET status='rejected' WHERE id=?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all pending users
$sql = "SELECT * FROM users WHERE status = 'pending'";
$result = $conn->query($sql);

?>
