<?php
session_start();

// Unset the 'admin' session variable
unset($_SESSION['admin']);

// Redirect to the front page
header("Location: ../index.php");
exit();
?>
