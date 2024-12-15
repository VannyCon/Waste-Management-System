<?php
session_start();
include_once('../../config/config.php');

// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: ../admin_login.php");
    exit();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = "managepurok";
require_once("templates/headers.php");
require_once("templates/nav.php");

// Flag to indicate if insertion is successful
$inserted = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to prevent SQL injection
    if (isset($_POST['purok_name']) && !empty($_POST['purok_name'])) {
        $purok_name = mysqli_real_escape_string($conn, $_POST['purok_name']);

        // Insert the new Purok into the database
        $sql = "INSERT INTO purok (purok_name) VALUES ('$purok_name')";
        
        if ($conn->query($sql) === TRUE) {
            // Set the flag to true if insertion is successful
            $inserted = true;
        } else {
            // Display error if insertion fails
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Purok name cannot be empty!";
    }
}

// Fetch all the puroks from the database
$sql = "SELECT * FROM purok";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Purok</title>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        /* Container styling */
        .container-fluid {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }

        /* Table and form container styling */
        .form-container, .table-container {
            width: 48%;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Form styling */
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            margin: 20px 0;
        }

        form label {
            font-size: 16px;
            margin-bottom: 10px;
            display: block;
        }

        form input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Box-style popup */
        .popup-box {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 300px; /* Box width */
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .popup-box h3 {
            margin: 0 0 10px;
            color: #4CAF50;
        }

        .popup-box p {
            margin-bottom: 20px;
        }

        .popup-box .close-btn {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .popup-box .close-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

<div class="content-body">
    <div class="container-fluid">
        <div class="form-container">
            <h2>Add Purok</h2>

            <!-- Form to add a purok -->
            <form method="POST" action="">
                <label for="purok_name">Purok Address:</label>
                <input type="text" id="purok_name" name="purok_name" required>
                <input type="submit" value="Add Purok">
            </form>
        </div>

        <div class="table-container">
            <h3>List of Puroks</h3>
            <!-- Table to display the puroks -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Purok Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output each row of the database result
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr><td>" . $row['id'] . "</td><td>" . $row['purok_name'] . "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No purok found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Show box popup if insertion was successful
if ($inserted) {
    echo "<script type='text/javascript'>
            window.onload = function() {
                var popupBox = document.getElementById('popupBox');
                popupBox.style.display = 'block';
            }
          </script>";
}
?>

<!-- The Box-style Popup -->
<div id="popupBox" class="popup-box">
    <h3>Success</h3>
    <p>Purok successfully added!</p>
    <button class="close-btn" onclick="closePopup()">Close</button>
</div>

<script>
    // Close the popup
    function closePopup() {
        var popupBox = document.getElementById("popupBox");
        popupBox.style.display = "none";
    }
</script>

</body>
</html>

<?php
$conn->close(); 
require_once("templates/footer.php");
?>
