<?php
include_once('../config/config.php');
session_start();

// Increase file size limits
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '20M');
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

// Redirect unauthenticated users
if (!isset($_SESSION['resident']) || $_SESSION['resident'] !== true) {
    header("Location: resident_login.php");
    exit();
}

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle report submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_violation'])) {
    $resident_name = htmlspecialchars($_POST['Resident_name']);
    $violators_name = htmlspecialchars($_POST['violators_name']);
    $type_violation = htmlspecialchars($_POST['type_violation']);
    $description = htmlspecialchars($_POST['description']);
    $violators_location = htmlspecialchars($_POST['violators_location']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);
    $date = htmlspecialchars($_POST['Violation_date']);
    $time = htmlspecialchars($_POST['Violation_time']);

    // Generate random violation ID
    $violationID = 'VIOLATION-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Create directory for storing images
    $targetDir = "../documents/violation/$violationID/resident_photos/";

    if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true)) {
        die("Failed to create directories.");
    }

    // Handle file uploads
    $uploadedFiles = [];
    foreach ($_FILES['photos']['name'] as $index => $photoName) {
        $photoTmpName = $_FILES['photos']['tmp_name'][$index];
        $photoError = $_FILES['photos']['error'][$index];
        $photoSize = $_FILES['photos']['size'][$index];
    
        // Debugging info
        echo "Processing file: $photoName<br>";
        echo "File size: $photoSize bytes<br>";
    
        // Validate upload errors
        if ($photoError != UPLOAD_ERR_OK) {
            echo "Error uploading $photoName. Error code: $photoError.<br>";
            continue;
        }
    
        // Validate file size (max 10MB)
        if ($photoSize > 10 * 5000 * 5000) {
            echo "File $photoName exceeds the 10MB limit.<br>";
            continue;
        }
    
        // Validate resolution
        list($width, $height) = getimagesize($photoTmpName);
        echo "Resolution: {$width}x{$height}<br>";
        if ($width > 5000 || $height > 5000) {
            echo "File $photoName exceeds the maximum resolution of 5000x5000.<br>";
            continue;
        }
    
        // Validate file type
        $fileType = mime_content_type($photoTmpName);
        echo "File type: $fileType<br>";
        if (!in_array($fileType, ['image/jpeg', 'image/png', 'image/gif'])) {
            echo "Invalid file type for $photoName.<br>";
            continue;
        }
    
        // Move uploaded file
        $targetFile = $targetDir . basename($photoName);
        if (move_uploaded_file($photoTmpName, $targetFile)) {
            echo "File $photoName uploaded successfully.<br>";
        } else {
            echo "Failed to upload $photoName.<br>";
        }
    }
    

    // Set isActive to true
    $isActive = true;

    // Insert report data into database
    $sql = "INSERT INTO tbl_resident_report (
        violationID, resident_name, violators_name, type_violation, description, 
        violators_location, latitude, longitude, date, time, isActive
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssi",
        $violationID,
        $resident_name,
        $violators_name,
        $type_violation,
        $description,
        $violators_location,
        $latitude,
        $longitude,
        $date,
        $time,
        $isActive
    );

    if ($stmt->execute()) {
        header("Location: reportform.php?success=true");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch user data
function getUserData($conn) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $fullname = htmlspecialchars($_POST['Fullname']);
    $age = htmlspecialchars($_POST['Age']);
    $gender = htmlspecialchars($_POST['Gender']);
    $contactnumber = htmlspecialchars($_POST['Contactnumber']);
    $address = htmlspecialchars($_POST['Address']);
    $username = htmlspecialchars($_POST['Username']);
    $password = htmlspecialchars($_POST['Password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET 
        Fullname = ?, Age = ?, Gender = ?, Contactnumber = ?, 
        Address = ?, Username = ?, Password = ? 
        WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $fullname, $age, $gender, $contactnumber, $address, $username, $hashedPassword, $_SESSION['user_id']);

    if ($stmt->execute()) {
        header("Location: reportform.php?updatesuccess=true");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Management Violation Report Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <!-- Logout and Update Profile -->
        <div class="text-right">
            <a href="./logout.php" class="btn btn-danger">Logout</a>
            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#updateProfileModal">Update Profile</button>
        </div>
        <hr>
        <br>
        <h1><b>RESIDENT REPORT WASTE MANAGEMENT VIOLATION</b></h1>
        <br>

        <!-- Success Message -->
        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div id="successMessage" class="alert alert-success text-center" style="display: none;">
                Your violation report has been submitted successfully!
            </div>
        <?php endif; ?>

        <!-- Update Success Message -->
        <?php if (isset($_GET['updatesuccess']) && $_GET['updatesuccess'] == 'true'): ?>
            <div id="updateMessage" class="alert alert-info text-center" style="display: none;">
                Your information has been updated successfully.
            </div>
        <?php endif; ?>

        <form action="reportform.php" method="POST" enctype="multipart/form-data" class="mt-4" onsubmit="return validateForm()">
            <input type="hidden" name="Resident_name" value="<?php echo $_SESSION['fullname']; ?>" required>

            <div class="form-group">
                <label for="violators_name"><b>VIOLATOR'S NAME</b></label>
                <input type="text" class="form-control" name="violators_name" placeholder="Enter a violator's name (optional)">
            </div>

            <?php
                // Query to fetch id and purok_name
                $query1 = "SELECT `id`, `Violation` FROM `manage_violation` WHERE 1";
                $result = $conn->query($query1);

                // Start building the dropdown HTML with Bootstrap classes
                echo '<label for="type_violation"><b>Type of Violation</b></label>';
                // Open the database connection
                echo '<select name="type_violation" id="type_violation" class="form-control mb-3">';
                echo '<option value="">-- Select type_violation --</option>';

                // Fetch rows and populate the dropdown
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row['Violation']) . '">' . htmlspecialchars($row['Violation']) . '</option>';
                    }
                }

                echo '</select>';

                // Close the database connection
                ?>

            <div class="form-group">
                <label for="description"><b>Description</b></label>
                <input type="text" class="form-control" name="description" placeholder="Enter the Description">
            </div>

            <?php
                // Query to fetch id and purok_name
                $query = "SELECT `id`, `purok_name` FROM `purok` WHERE 1";
                $result = $conn->query($query);

                // Start building the dropdown HTML with Bootstrap classes
                echo '<label for="purok"><b>VIOLATORS LOCATION</b></label>';
                // Open the database connection
                $conn = new mysqli($servername, $username, $password, $dbname);
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


            <input type="hidden" id="latitude" name="latitude" readonly required>
            <input type="hidden" id="longitude" name="longitude" readonly required>

            <!-- Get Location Button -->
            <button 
                type="button" 
                class="btn btn-primary w-100 mb-3" 
                id="getLocationButton" 
                onclick="getLocation(this)">
                Get Location
            </button>

            <!-- Date and Time -->
            <div class="form-group">
                <label for="Violation_date"><b>DATE</b></label>
                <input type="date" class="form-control" name="Violation_date" required>
            </div>

            <div class="form-group">
                <label for="Violation_time"><b>TIME</b></label>
                <input type="time" class="form-control" name="Violation_time" required>
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <label for="photos"><b>UPLOAD PHOTOS</b> (Max 10MB each, JPG/PNG only):</label>
                <input 
                type="file" 
                    id="photos" 
                    name="photos[]" 
                    accept="image/*" 
                    multiple 
                    class="form-control" 
                    required>
                <small id="file-status" class="form-text text-muted"></small>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" name="report_violation" class="btn btn-success w-100">Submit</button>
            </div>
        </form>
    </div>

    <!-- Update Profile Modal -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateProfileLabel">Update Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php
                    $user_data = getUserData($conn); // Fetch user data
                    ?>
                    <form method="POST" action="reportform.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="Fullname">Full Name:</label>
                            <input type="text" name="Fullname" class="form-control" value="<?php echo $user_data['Fullname']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="Age">Age:</label>
                            <input type="number" name="Age" class="form-control" value="<?php echo $user_data['Age']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="Gender">Gender:</label>
                            <input type="text" name="Gender" class="form-control" value="<?php echo $user_data['Gender']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="Contactnumber">Contact Number:</label>
                            <input type="text" name="Contactnumber" class="form-control" value="<?php echo $user_data['Contactnumber']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="Address">Address:</label>
                            <input type="text" name="Address" class="form-control" value="<?php echo $user_data['Address']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="Username">Username:</label>
                            <input type="text" name="Username" class="form-control" value="<?php echo $user_data['Username']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="Password">Password:</label>
                            <input type="password" id="Password" name="Password" class="form-control">
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" id="showPassword" class="form-check-input"> Show Password
                            </label>
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('photos').addEventListener('change', function(event) {
            const files = event.target.files;
            if (files.length > 0) {
                console.log('File selected:', files[0].name);
            } else {
                console.log('No file selected');
            }
        });
        // Validate Form
        function validateForm() {
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;

            if (!latitude || !longitude) {
                alert("Please click 'Get Location' to fill in the location data.");
                return false;
            }

            const files = document.getElementById("photos").files;
            const maxSize = 20 * 5000 * 5000; // 10MB
            const maxResolution = 5000; // Max resolution: 5000x5000

            for (let i = 0; i < files.length; i++) {
                if (files[i].size > maxSize) {
                    alert(`File ${files[i].name} exceeds the 10MB size limit.`);
                    return false;
                }

                // Validate resolution
                const img = new Image();
                img.src = URL.createObjectURL(files[i]);
                img.onload = function () {
                    if (img.width > maxResolution || img.height > maxResolution) {
                        alert(`File ${files[i].name} exceeds the maximum resolution of ${maxResolution}x${maxResolution}.`);
                        return false;
                    }
                };
            }

            return true;
        }


        // Get Location
        function getLocation(button) {
            button.disabled = true;
            button.textContent = "Getting Location...";
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                        button.textContent = "Location Acquired";
                    },
                    (error) => {
                        alert("Location access is required. Please enable it.");
                        button.disabled = false;
                        button.textContent = "Get Location";
                    }
                );
            } else {
                alert("Geolocation is not supported by this browser.");
                button.disabled = false;
                button.textContent = "Get Location";
            }
        }

        // Show Password Toggle
        document.getElementById('showPassword').addEventListener('change', function () {
            const passwordField = document.getElementById('Password');
            passwordField.type = this.checked ? 'text' : 'password';
        });

        // Show success messages on page load
        window.onload = function () {
            const successMessage = document.getElementById('successMessage');
            const updateMessage = document.getElementById('updateMessage');
            if (successMessage) {
                successMessage.style.display = 'block';
                setTimeout(() => successMessage.style.display = 'none', 5000);
            }
            if (updateMessage) {
                updateMessage.style.display = 'block';
                setTimeout(() => updateMessage.style.display = 'none', 5000);
            }
        }


        // Function to compress image
        async function compressImage(file) {
            return new Promise((resolve, reject) => {
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                const reader = new FileReader();
                reader.readAsDataURL(file);
                
                reader.onload = function(event) {
                    const img = new Image();
                    img.src = event.target.result;
                    
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        
                        // Calculate aspect ratio
                        const aspectRatio = width / height;
                        
                        // Initialize quality
                        let quality = 0.9;
                        let dataUrl;
                        
                        // If the image is larger than 2000px in any dimension, resize it
                        const MAX_DIMENSION = 2000;
                        if (width > MAX_DIMENSION || height > MAX_DIMENSION) {
                            if (width > height) {
                                width = MAX_DIMENSION;
                                height = Math.round(width / aspectRatio);
                            } else {
                                height = MAX_DIMENSION;
                                width = Math.round(height * aspectRatio);
                            }
                        }
                        
                        canvas.width = width;
                        canvas.height = height;
                        
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);
                        
                        // Compress with gradually reducing quality until file size is under maxSize
                        function compressWithQuality() {
                            dataUrl = canvas.toDataURL('image/jpeg', quality);
                            
                            // Convert base64 string to blob to check size
                            const byteString = atob(dataUrl.split(',')[1]);
                            const ab = new ArrayBuffer(byteString.length);
                            const ia = new Uint8Array(ab);
                            for (let i = 0; i < byteString.length; i++) {
                                ia[i] = byteString.charCodeAt(i);
                            }
                            const blob = new Blob([ab], { type: 'image/jpeg' });
                            
                            if (blob.size > maxSize && quality > 0.1) {
                                quality -= 0.1;
                                compressWithQuality();
                            } else {
                                // Convert blob back to File object
                                const compressedFile = new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: new Date().getTime()
                                });
                                resolve(compressedFile);
                            }
                        }
                        
                        compressWithQuality();
                    };
                    
                    img.onerror = function() {
                        reject(new Error('Failed to load image'));
                    };
                };
                
                reader.onerror = function() {
                    reject(new Error('Failed to read file'));
                };
            });
        }

        // Function to handle file input change
        async function handleImageUpload(event) {
            const fileInput = event.target;
            const files = Array.from(fileInput.files);
            const compressedFiles = [];
            const fileStatus = document.getElementById('file-status');
            
            try {
                fileStatus.textContent = 'Compressing images...';
                
                // Process each file
                for (const file of files) {
                    if (!file.type.startsWith('image/')) {
                        compressedFiles.push(file);
                        continue;
                    }
                    
                    const compressedFile = await compressImage(file);
                    compressedFiles.push(compressedFile);
                }
                
                // Create a new FileList-like object
                const dataTransfer = new DataTransfer();
                compressedFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                
                // Update the file input with compressed files
                fileInput.files = dataTransfer.files;
                
                fileStatus.textContent = 'Images compressed successfully! Ready to upload.';
                setTimeout(() => {
                    fileStatus.textContent = '';
                }, 3000);
            } catch (error) {
                console.error('Error compressing images:', error);
                fileStatus.textContent = 'Error compressing images. Please try again.';
            }
        }

        // Add event listener to file input
        document.getElementById('photos').addEventListener('change', handleImageUpload);
    </script>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
