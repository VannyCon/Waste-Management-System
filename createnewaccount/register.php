<?php
session_start();
include_once("../config/config.php");
// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Fullname = $_POST['Fullname'];
    $Age = $_POST['Age'];
    $email = $_POST['email'];
    $Gender = $_POST['Gender'];
    $Contactnumber = $_POST['Contactnumber'];
    $Address = $_POST['Address'];
    $Username = $_POST['Username'];
    $Password = password_hash($_POST['Password'], PASSWORD_BCRYPT); // Hash the password

    // Handle file upload for IDPhoto
    $IDPhoto = $_FILES['IDPhoto'];
    $IDPhotoName = '';

    if ($IDPhoto['error'] == 0) {
        // Insert user details into the database
        $stmt = $conn->prepare("INSERT INTO users (Fullname, Age, Gender, Contactnumber, email, Address, Username, Password, status) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("ssssssss", $Fullname, $Age, $Gender, $Contactnumber, $email, $Address, $Username, $Password);

        if ($stmt->execute()) {
            // Get the ID of the inserted user
            $userId = $stmt->insert_id;

            // Save the ID photo with the user's ID as the file name
            $IDPhotoName = $userId . '.png'; // Adjust extension if needed
            $targetDirectory = "../documents/resident_ids/";

            if (move_uploaded_file($IDPhoto['tmp_name'], $targetDirectory . $IDPhotoName)) {
                // Update the user's IDPhoto field
                $updateStmt = $conn->prepare("UPDATE users SET IDPhoto = ? WHERE id = ?");
                $updateStmt->bind_param("si", $IDPhotoName, $userId);
                $updateStmt->execute();
                $updateStmt->close();

                // Redirect with a success status
                header("Location: " . $_SERVER['PHP_SELF'] . "?status=success");
                exit();
            } else {
                echo "Error moving the uploaded file.";
            }
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        /* Basic Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        /* Body Styles */
        body {
            font-family: Arial, sans-serif;
            background-image: url('../image/oldsagay1.jpg'); 
            background-size: cover; 
            background-position: center;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            position: relative;
        }
        /* Container for Form */
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 500px;
            width: 100%;
        }
        /* Form Header */
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        /* Form Labels */
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        /* Form Inputs */
        input[type="text"],
        input[type="number"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        /* Submit Button */
        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        /* Pop-Up Styles */
        .popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .popup-content {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .popup-content h3 {
            margin-bottom: 20px;
        }
        .popup-content a {
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .popup-content a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create an Account</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="registrationForm" enctype="multipart/form-data">
            <label for="Fullname">Full Name:</label>
            <input type="text" id="Fullname" name="Fullname" required>
            
            <label for="Age">Age:</label>
            <input type="number" id="Age" name="Age" required>
            
            <label for="Gender">Gender:</label>
            <input type="text" id="Gender" name="Gender" required>

            <label for="Email">Email</label>
            <input type="text" id="Email" name="email" required>

            <label for="Contactnumber">Contact Number:</label>
            <input type="text" id="Contactnumber" name="Contactnumber" required>
            
            <label for="Address">Address:</label>
            <input type="text" id="Address" name="Address" required>
            
            <label for="IDPhoto">ID Photo:</label>
            <input type="file" id="IDPhoto" name="IDPhoto" accept="image/*" required>
            
            <label for="Username">Username:</label>
            <input type="text" id="Username" name="Username" required>
            
            <label for="Password">Password:</label>
            <input type="password" id="Password" name="Password" required>
            
            <label for="ConfirmPassword">Confirm Password:</label>
            <input type="password" id="ConfirmPassword" name="ConfirmPassword" required>
            
            <label class="show-password">
                <input type="checkbox" id="showPassword"> Show Password
            </label>
            
            <input type="submit" value="Register">
        </form>
    </div>

    <!-- Pop-Up HTML -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <h3>Account created successfully! Wait for admin approval.</h3>
            <a href="../resident/resident_login.php" onclick="closePopup()">OK</a>
        </div>
    </div>

    <script>
        // Show pop-up if success status in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'success') {
            document.getElementById('popup').style.display = 'flex';
        }

        // Close pop-up
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        // Show/Hide password fields
        document.getElementById('showPassword').addEventListener('change', function() {
            const passwordField = document.getElementById('Password');
            const confirmPasswordField = document.getElementById('ConfirmPassword');
            const type = this.checked ? 'text' : 'password';
            passwordField.type = type;
            confirmPasswordField.type = type;
        });

        // Validate password match
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            const password = document.getElementById('Password').value;
            const confirmPassword = document.getElementById('ConfirmPassword').value;
            const confirmPasswordField = document.getElementById('ConfirmPassword');
            
            // Check if passwords match
            if (password !== confirmPassword) {
                // Show the error message below the Confirm Password field
                let errorMessage = document.querySelector('.password-error');
                if (!errorMessage) {
                    errorMessage = document.createElement('span');
                    errorMessage.className = 'password-error';
                    errorMessage.style.color = 'red';
                    errorMessage.textContent = "Passwords don't match.";
                    confirmPasswordField.parentElement.appendChild(errorMessage);
                }

                // Add red border to the Confirm Password field
                confirmPasswordField.style.border = '2px solid red';

                // Prevent form submission
                event.preventDefault();
            } else {
                // Reset the border and remove the error message if passwords match
                confirmPasswordField.style.border = '1px solid #ccc';
                const errorMessage = document.querySelector('.password-error');
                if (errorMessage) {
                    errorMessage.remove();
                }
            }
        });
    </script>
</body>
</html>
