<?php
session_start();// Start the session
include_once('../config/config.php');
// Check if the 'resident' session is not set or is false
if (!isset($_SESSION['enforcer']) || $_SESSION['enforcer'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: enforcer_login.php");
    exit();
}
$violationID = $_GET['violationID'];
$residentName = $_GET['residentName'];
$latitude = $_GET['latitude'];  
$longitude = $_GET['longitude']; 
$description = $_GET['description']; 


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['violators_name'];
    $age = $_POST['violators_age'];
    $gender = $_POST['violators_gender'];
    $location = $_POST['violators_location'];
    $violation_type = $_POST['violation_type'];
    $datetime = $_POST['datetime'];
    $offenses = $_POST['offenses'];
    $penalty = $_POST['penalty'];

    // Insert data into the database
    $sql = "INSERT INTO tbl_enforcer_report 
            (violationID, resident_name, violators_name, violators_age, violators_gender, violators_location, violation_type, datetime, offenses, latitude, longitude, penalty) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissssssdd", $violationID, $residentName, $name, $age, $gender, $location, $violation_type, $datetime, $offenses, $latitude, $longitude, $penalty);

    if ($stmt->execute()) {
        // Now that the insert is successful, update the isActive field
        $sql = "UPDATE tbl_resident_report SET isActive = '0' WHERE violationID = ?";
        $stmt1 = $conn->prepare($sql);
        $stmt1->bind_param("s", $violationID);
        
        if ($stmt1->execute()) {
            echo '<div class="alert alert-success mx-4 my-2">Report submitted successfully and status updated!</div>';
        } else {
            echo '<div class="alert alert-warning mx-4 my-2">Report submitted, but error updating status: ' . $stmt1->error . '</div>';
        }


        // Create directory for storing images
        $targetDir = "../documents/violation/$violationID/enforcer_photos/";

        // Create the directory if it doesn't exist
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // 0777 grants full permissions, 'true' creates nested directories
        }

        // Handle file uploads (multiple files allowed)
        
        // Check if files were uploaded
        if (isset($_FILES['photos'])) {
            foreach ($_FILES['photos']['name'] as $index => $photoName) {
                $photoTmpName = $_FILES['photos']['tmp_name'][$index];

                // Full path for the uploaded file
                $targetFile = $targetDir . basename($photoName);

                // Move each uploaded file to the target directory
                if (move_uploaded_file($photoTmpName, $targetFile)) {
                    echo "Uploaded: $photoName<br>";
                } else {
                    echo "Error uploading: $photoName<br>";
                }
            }
        } else {
            echo "No files uploaded.";
        }



        
        $stmt1->close(); // Always close the second statement
        header("Location: index.php"); // Redirect only after successful execution
        exit();
    } else {
        echo '<div class="alert alert-danger mx-4 my-2">Error: ' . $stmt->error . '</div>';
    }

    $stmt->close(); // Always close the first statement
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enforcer Report Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<a href="violator_location.php?violationID=<?php echo $violationID ?>&residentName=<?php echo $residentName?>&latitude=<?php echo $latitude?>&longitude=<?php echo $longitude?>&description=<?php echo $description?>" type="button" class="btn btn-danger m-2 mx-5 ">Back</a>
<div class="container mt-5">
    

    <div class="card p-4">
        
   
        <div class="card-header text-center">
            <h2>Enforcer Report Form</h2>
        </div>
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div id="successMessage" style="display: none; background-color: #4CAF50; color: white; padding: 15px; text-align: center;">
                Your violation report has been submitted successfully!
            </div>
        <?php endif; ?>

        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data" >
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="violators_name" class="form-label">Violator's Name</label>
                        <input type="text" class="form-control" id="violators_name" name="violators_name" required>
                    </div>
                    <div class="col-md-3">
                        <label for="violators_age" class="form-label">Age</label>
                        <input type="number" class="form-control" id="violators_age" name="violators_age" required>
                    </div>
                    <div class="col-md-3">
                        <label for="violators_gender" class="form-label">Gender</label>
                        <select class="form-select" id="violators_gender" name="violators_gender" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>

                <?php
                // Query to fetch id and purok_name
                $query1 = "SELECT `id`, `purok_name` FROM `purok` WHERE 1";
                $result = $conn->query($query1);

                // Start building the dropdown HTML with Bootstrap classes
                echo '<label for="purok"><b> Location</b></label>';
                // Open the database connection
                echo '<select name="violators_location" id="purok" class="form-control mb-3">';
                echo '<option value="">-- Select Purok --</option>';

                // Fetch rows and populate the dropdown
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['purok_name']) . '">' . htmlspecialchars($row['purok_name']) . '</option>';
                    }
                }

                echo '</select>';

                // Close the database connection
                ?>

                <?php
                    // Query to fetch id and purok_name
                    $query1 = "SELECT `id`, `Violation` FROM `manage_violation` WHERE 1";
                    $result = $conn->query($query1);

                    // Start building the dropdown HTML with Bootstrap classes
                    echo '<label for="violation_type"><b>Report Violation</b></label>';
                    // Open the database connection
                    echo '<select name="violation_type" id="violation_type" class="form-control mb-3">';
                    echo '<option value="">-- Select description --</option>';

                    // Fetch rows and populate the dropdown
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['Violation']) . '">' . htmlspecialchars($row['Violation']) . '</option>';
                        }
                    }

                    echo '</select>';

                    // Close the database connection
                ?>


                <div class="mb-3">
                    <label for="datetime" class="form-label">Date and Time</label>
                    <input type="datetime-local" class="form-control" id="datetime" name="datetime" required>
                </div>
                <div class="mb-3">
                    <label for="offenses" class="form-label">Offenses</label>
                    <select class="form-select" id="offenses" name="offenses" required>
                        <option value="" disabled selected hidden>Select Offenses</option>
                        <option value="First Offense">First Offense</option>
                        <option value="Second Offense">Second Offense</option>
                        <option value="Third Offense ">Third Offense </option>
                    </select>
                </div>
                <div class="mb-3">
                        <label for="penalty" class="form-label">Penalty</label>
                        <select class="form-select" id="penalty" name="penalty" required>
                            <option value="" disabled selected hidden>Select Penalty</option>
                            <option value="500">500</option>
                            <option value="1000">1000</option>
                            <option value="3000">3000</option>
                        </select>
                   </div>
                <div class="form-group mb-3">
                    <label for="IDPhotos">EVIDENCE:</label>
                    <input type="file" name="photos[]" multiple required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Submit Report</button>
            </form>
        </div>
    </div>
  
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
