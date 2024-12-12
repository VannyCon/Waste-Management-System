<?php
include_once('../config/config.php');
session_start();

// Check if the 'enforcer' session is not set or is false
if (!isset($_SESSION['enforcer']) || $_SESSION['enforcer'] !== true) {
    header("Location: enforcer_login.php");
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = false;

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
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $residentName = "Enforcer";

    // Generate random violation ID
    $violationID = 'VIOLATION-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Insert data into the database
    $sql = "INSERT INTO `tbl_enforcer_report`(`violationID`, `resident_name`, `violators_name`, `violators_age`, `violators_gender`, `violators_location`, `violation_type`, `datetime`, `offenses`, `latitude`, `longitude`, `penalty`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissssssdd", $violationID, $residentName, $name, $age, $gender, $location, $violation_type, $datetime, $offenses, $latitude, $longitude, $penalty);

    if ($stmt->execute()) {
        $success = true;

        // Create directory for storing images
        $targetDir = "../documents/violation/$violationID/enforcer_photos/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Handle file uploads
        if (isset($_FILES['photos'])) {
            foreach ($_FILES['photos']['name'] as $index => $photoName) {
                $photoTmpName = $_FILES['photos']['tmp_name'][$index];
                $targetFile = $targetDir . basename($photoName);
                move_uploaded_file($photoTmpName, $targetFile);
            }
        }
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
    <title>Enforcer Report Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Back Button -->
    <a href="index.php" class="btn btn-danger mb-3">Back</a>

    <div class="card p-4">
        <div class="card-header text-center">
            <h2>Enforcer Report Form</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validateLocation()">
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
                <input type="hidden" id="latitude" name="latitude" required>
                <input type="hidden" id="longitude" name="longitude" required>
                <button type="button" class="btn btn-primary w-100 mb-3" id="getLocationButton" onclick="getLocation(this)">
                    Get Location
                </button>
                <div class="mb-3">
                    <label for="violators_location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="violators_location" name="violators_location" required>
                </div>
                <div class="mb-3">
                    <label for="violation_type" class="form-label">Violation</label>
                    <select class="form-control" name="violation_type" id="violation_type" required>
                    <option value="" disabled selected hidden>Select Violation</option>
                    <option value="Illegal Dumping">Illegal Dumping</option>
                    <option value="Burning of Waste">Burning of Plastic</option>
                </select>
            </div>
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
                   <div class="mb-3">
                    <label for="photos" class="form-label">Evidence</label>
                    <input type="file" class="form-control" id="photos" name="photos[]" multiple required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit Report</button>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Report submitted successfully!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function getLocation(button) {
        button.disabled = true;
        button.innerText = "Getting Location...";
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    button.innerText = "Location Acquired";
                },
                (error) => {
                    alert("Location access is required. Please enable it.");
                    button.disabled = false;
                    button.innerText = "Get Location";
                }
            );
        } else {
            alert("Geolocation is not supported by this browser.");
            button.disabled = false;
            button.innerText = "Get Location";
        }
    }

    function validateLocation() {
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;

        if (!latitude || !longitude) {
            alert("Please click the 'Get Location' button to fill the location data.");
            return false;
        }
        return true;
    }

    <?php if ($success): ?>
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    <?php endif; ?>
</script>
</body>
</html>
