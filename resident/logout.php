<?php
session_start(); // Start the session

// Unset only the 'resident' session variable
unset($_SESSION['resident']);

// Optionally, you can destroy the entire session if needed
// session_destroy(); 

// Redirect to the login page
header("Location: resident_login.php");
exit();
?>
