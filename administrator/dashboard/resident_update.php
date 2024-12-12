<?php
include_once('../../config/config.php');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $admin_approval = $_POST['admin_approval'];
    $isActive = $_POST['isActive'];

    $sql = "UPDATE tbl_resident_report 
            SET admin_approval = ?, isActive = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $admin_approval, $isActive, $id);

    if ($stmt->execute()) {
        header("Location: resident_report.php?update=success");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
