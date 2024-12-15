<?php 
session_start();
include_once('../../config/config.php');
// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: ../admin_login.php");
    exit();
}


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the query
$stmt = $conn->prepare("SELECT `id`, `violationID`, `resident_name`, `violators_name`, `type_violation`, `description`, `latitude`, `longitude`, `date`, `time`, `admin_approval`, `isActive` FROM `tbl_resident_report` WHERE isActive = 1");

$stmt->execute();
$result = $stmt->get_result();

// Fetch all rows as an associative array
$data = $result->fetch_all(MYSQLI_ASSOC);

// Encode the data to JSON and output it
echo json_encode($data);

// Close the statement and connection
$stmt->close();
$conn->close();
?>
