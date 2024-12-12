
<?php
session_start(); // Start the session

// Check if the 'resident' session is not set or is false
if (isset($_SESSION['enforcer'])) {
    // Redirect to the login page if the user is not authenticated
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENFORCER</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../image/oldsagay.jpg'); /* Corrected the image path */
            background-size: cover; /* Ensures the image covers the entire background */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat; /* Prevents the image from repeating */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
            margin: 0;
            position: relative; /* Needed for the overlay effect */
        }
        .login-box {
            background-color: rgba(255, 255, 255, 0.3); /* White background with 30% transparency */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3); /* Slight shadow for better visibility */
            width: 300px;
            position: relative; /* Ensure this is above the overlay */
            z-index: 2; /* Place the login box above the overlay */
            backdrop-filter: blur(10px); /* Adds a blur effect for a frosted glass look */
        }
        .login-box h2 {
            margin: 0 0 20px;
            text-align: center;
            color: #fff; /* White text for contrast */
        }
        .login-box input[type="text"],
        .login-box input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .login-box input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .login-box input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .show-password {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        .show-password input {
            margin-right: 5px;
        }
    </style>
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</head>
<body>
    <div class="login-box">
        <h2>ENFORCER</h2>
        <form action="enforcer.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" id="password" required>
            <div class="show-password">
                <input type="checkbox" onclick="togglePasswordVisibility()"> Show Password
            </div>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>