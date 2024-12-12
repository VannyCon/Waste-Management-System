<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Management Violation Monitoring System with Mapping</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('image/brgy.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            height: 120vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            text-align: center;
            width: 400px;
            border: none;
            border-radius: 20px;
            background: transparent;
            padding: 30px;
            border-radius: 8px;
            backdrop-filter: blur(20px) brightness(110%);
            
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            width: 200px;
            height: auto;
        }
        .system-title {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .login-buttons {
            display: flex;
            flex-direction: column;
        }
        .login-buttons a {
            text-decoration: none;
            color: darkblue;
            background-color: lightgreen;
            padding: 10px 0;
            margin: 5px 0;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }
        .login-buttons a:hover {
            background-color: whitesmoke;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="system-title">
            <b>Waste Management Violation Monitoring </b><br>
            <b>System With Mapping</b><br>
        </div>      
        <div class="login-buttons">
            <a href="administrator/admin_login.php">Administrator</a>
            <a href="enforcer/enforcer_login.php"> Enforcer</a>
            <a href="resident/resident_login.php">Resident</a>
            <a href="createnewaccount/register.php">Create New Account</a>
        </div>
    </div>
</body>
</html>
