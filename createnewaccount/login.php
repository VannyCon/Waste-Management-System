<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
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
        </style>
    <script>
        function showError(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <div class="login-box">
        <h2>Administrator</h2>
        <form action="register.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
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