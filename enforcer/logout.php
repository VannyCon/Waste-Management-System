<?php
session_start(); // Start the session

// Unset only the 'resident' session variable
unset($_SESSION['enforcer']);

// Optionally, you can destroy the entire session if needed
// session_destroy(); 

// Redirect to the login page
header("Location: enforcer_login.php");
exit();
?>



