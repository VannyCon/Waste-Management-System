<?php
 session_start();

 if (isset($_SESSION['admin'])) {
     header("Location: dashboard/index.php");
     exit();
 }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator</title>
    <style>
        body { 
            font-family: Arial, sans-serif;
            background-image: url('dashboard/images/logo1.png');
            background-color: #f0f0f0; 
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background-color: rgba(255, 255, 255, 0.3); /* White background with 30% transparency */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3); /* Slight shadow for better visibility */
            width: 300px;
            position: relative; /* Ensure this is above the overlay */
            z-index: 2; /* Place the login box above the overlay */
            backdrop-filter: blur(10px);
        } 
        
        .login-box h2 {
            margin: 0 0 20px;
            text-align: center;
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
        .login-box .show-password {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .login-box .show-password input {
            margin-right: 5px;
        }
    </style>
    <script>
        function showError(message) {
            alert(message);
        }

        function togglePassword() {
            var passwordInput = document.getElementById("password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script>
</head>
<body>
    <div class="login-box">
        <h2>Administrator</h2>
        <form action="admin.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <div class="show-password">
                <input type="checkbox" id="showPassword" onclick="togglePassword()">
                <label for="showPassword">Show Password</label>
            </div>
            <input type="submit" name="Login" value="Login"/>
        </form>
    </div>
    <?php
    if (isset($_GET['error'])) {
        echo "<script>showError('" . $_GET['error'] . "');</script>";
    }
    ?>
</body>
</html>
