<?php
include_once('../../config/config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $sql = "UPDATE tbl_enforcer_report 
            SET isPaid=1
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);

    if ($stmt->execute()) {
        header("Location: paided_violation.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
