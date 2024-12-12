<?php
include_once('../../config/config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $sql = "DELETE FROM tbl_enforcer_report WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: enforcer_report.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
