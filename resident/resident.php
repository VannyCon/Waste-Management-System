<?php
include_once('../config/config.php');
session_start();


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$modalMessage = ''; // Modal message variable
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Username = $_POST['username'];
    $Password = $_POST['password'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("SELECT Id, Fullname, Username, Password, Status FROM users WHERE Username = ?");
    $stmt->bind_param("s", $Username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($Password, $user['Password'])) {
            $_SESSION['resident'] = true;
            $_SESSION['fullname'] = $user['Fullname'];
            $_SESSION['user'] = $user['Username'];
            $_SESSION['user_id'] = $user['Id']; 
            $status = strtolower($user['Status']); // Normalize case for comparison

            // Redirect based on approval status
            if ($status === 'approved') {
                header('Location: reportform.php');
                exit(); // Ensure no further code execution after redirection
            } else {
                $modalMessage = "Your account is pending approval.";
                header('Location: pending.php');
                exit(); // Ensure no further code execution after redirection
            }
        } else {
            $modalMessage = "Invalid password!";
        }
    } else {
        $modalMessage = "User not found!";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident</title>
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

        /* Modal container */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Black background with transparency */
        }

        /* Modal content */
        .modal-content {
            background-color: #fff;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 300px; 
            text-align: center;
            border-radius: 10px;
        }

        /* Success icon */
        .modal-content img {
            width: 50px;
            margin-bottom: 10px;
        }

        /* Success message */
        .modal-content p {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Resident</h2>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>

    <!-- Modal structure -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="error-icon">
            <p id="modalMessage"><?php echo $modalMessage; ?></p>
        </div>
    </div>

    <script>
        // Function to show the modal with a custom message
        function showModal() {
            var modalMessage = "<?php echo addslashes($modalMessage); ?>"; // Escape special characters
            if (modalMessage) {
                document.getElementById('loginModal').style.display = 'block';

                // Automatically hide the modal after 3 seconds
                setTimeout(function() {
                    document.getElementById('loginModal').style.display = 'none';
                }, 3000);
            }
        }

        // Show modal if there is a message
        window.onload = showModal;
    </script>

</body>
</html>