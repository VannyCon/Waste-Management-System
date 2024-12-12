<?php

session_start();

include_once('../config/config.php');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user = $_POST['username'];
$pass = $_POST['password'];

// Prepare a statement to check if the username exists
$stmt = $conn->prepare("SELECT password FROM user_administrator WHERE username = ?");
$stmt->bind_param("s", $user);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
   
    $row = $result->fetch_assoc();
    if ($row['password'] === $pass) {
        $_SESSION['admin'] = true;
        $_SESSION['username'] = $user;
        header("Location: dashboard/index.php");
        exit();
    } else {
        // Password is incorrect
        $error = "Invalid password.";
        header("Location: admin_login.php?error=" . urlencode($error));
        exit();
    }
} else {
    // Username does not exist
    $error = "Invalid username.";
    header("Location: admin_login.php?error=" . urlencode($error));
    exit();
}


$stmt->close();
$conn->close();
?>
