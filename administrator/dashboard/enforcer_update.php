<?php
include_once('../../config/config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $offenses = $_POST['offenses'];
    $penalty = $_POST['penalty'];

    $sql = "UPDATE tbl_enforcer_report 
            SET violators_name=?, violators_age=?, violators_gender=?, violators_location=?, violation_type=?, offenses=?, penalty=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssdi", $name, $age, $gender, $location, $type,  $offenses, $penalty, $id);

    if ($stmt->execute()) {
        header("Location: enforcer_report.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
