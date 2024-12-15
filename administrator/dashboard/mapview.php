<?php
session_start();
include_once('../../config/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer library
require '../../vendor/autoload.php';
require '../../vendor/phpmailer/phpmailer/src/Exception.php'; // Adjust based on your structure
require '../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../vendor/phpmailer/phpmailer/src/SMTP.php';
include_once('../../config/config.php');
// Redirect if the 'admin' session is not set or is invalid
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: ../admin_login.php");
    exit();
}



$title = "Mapview";
require_once("templates/headers.php");
require_once("templates/nav.php");


// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['approved'], $_POST['violationID'], $_POST['enforcer_id'])) {
    $approved = $_POST['approved'];
    $violationID = $_POST['violationID'];
    $enforcerId = $_POST['enforcer_id'];

    // Update query for resident report
    $stmt = $conn->prepare("UPDATE tbl_resident_report SET `admin_approval` = ?, `enforcer_id` = ? WHERE `violationID` = ?");
    $stmt->bind_param("sss", $approved, $enforcerId, $violationID);

    // Execute and display appropriate message
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Report approved successfully!</div>";
        // Fetch the email and username of the enforcer based on the enforcerId
        $emailStmt = $conn->prepare("SELECT `email`, `Fullname` FROM `enforcers` WHERE `id` = ?");
        $emailStmt->bind_param("i", $enforcerId); // 'i' for integer
        $emailStmt->execute();
        $emailStmt->bind_result($recipientEmail, $fullname);
        $emailStmt->fetch();
        $emailStmt->close();

        // Send email based on approval status
        $mail = new PHPMailer(true);
        if ($approved == 'Approved') {
            try {
                // Gmail SMTP settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'vannycon001@gmail.com';
                $mail->Password   = 'cjrryybbsdnozeoz'; // Use app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
        
                // Recipients
                $mail->setFrom('vannycon001@gmail.com', 'Waste Management System');
                $mail->addAddress($recipientEmail); // Use the email fetched from the database
    
                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Report Violation';
                $mail->Body    = '<h1>Hi ' . htmlspecialchars($fullname) . '</h1><p>Your Account is You have task today.</p>';
                $mail->AltBody = 'Thank you'; // Plain text version
        
                $mail->send();
                echo "success"; // echo success response to JavaScript
            } catch (Exception $e) {
                echo "Error updating record: " . $conn->error;
            }
        } else {
            try {
                // Gmail SMTP settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'vannycon001@gmail.com';
                $mail->Password   = 'cjrryybbsdnozeoz'; // Use app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
        
                // Recipients
                $mail->setFrom('vannycon001@gmail.com', 'Waste Management System');
                $mail->addAddress($recipientEmail); // Use the email fetched from the database

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Report Violation';
                $mail->Body    = '<h1>Hi ' . htmlspecialchars($fullname) . '</h1><p>Your Account is You have task today.</p>';
                $mail->AltBody = 'Thank you'; // Plain text version

                        
                $mail->send();
                echo "success"; // echo success response to JavaScript
            } catch (Exception $e) {
                echo "Error updating record: " . $conn->error;
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Error updating record: " . $stmt->error . "</div>";
    }

    $stmt->close();
}


$conn->close();
?>
<!-- Styles and Scripts -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
<link rel="stylesheet" href="../../css/sidebar.css">
<link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
<style>
    #map { height: 800px; width: 100%; }
</style>

<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Map</h4></div>
                    <div class="card-body"><div id="map"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add this modal structure to your HTML -->
<div class="modal fade" id="violatorModal" tabindex="-1" aria-labelledby="violatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="violatorModalLabel">Violation Details</h5>
                <a type="button" href="mapview.php" class="btn-close"  aria-label="Close"></a>

            </div>
            <div class="modal-body">
                <div id="modalContent"></div>
                <div id="imageContainer" class="d-flex flex-wrap gap-2 mb-3"></div>
                <div id="violationDetails"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Enlarged Images -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Enlarged Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="enlargedImage" src="" alt="Enlarged Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Get the modal element
        const imageModal = document.getElementById("imageModal");

        // Get the close button
        const closeButton = imageModal.querySelector(".btn-close");

        // Add an event listener to the close button
        closeButton.addEventListener("click", function () {
            const bootstrapModal = bootstrap.Modal.getInstance(imageModal);
            if (bootstrapModal) {
                bootstrapModal.hide(); // Manually hide the modal
            }
        });

        // Optionally handle clicking outside the modal
        imageModal.addEventListener("click", function (event) {
            if (event.target === imageModal) {
                const bootstrapModal = bootstrap.Modal.getInstance(imageModal);
                if (bootstrapModal) {
                    bootstrapModal.hide(); // Hide modal if clicking outside of it
                }
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Map Initialization
    const map = L.map('map').setView([10.932871, 123.414262], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Custom marker icons
    const violationIcon = L.divIcon({
        className: 'red-marker',
        html: '<div style="background-color: red; border-radius: 50%; width: 32px; height: 32px; border: 2px solid white;"></div>',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
    });

    

    // Fetch GPS data
// Fetch GPS data
fetch('mapdata_json.php')
    .then(response => response.json())
    .then(data => {
        data.forEach(loc => {
            const { violationID, latitude, longitude, resident_name, type_violation, description, date, time, admin_approval } = loc;

            L.marker([latitude, longitude], { icon: violationIcon }).addTo(map)
                .bindPopup(`
                    <div>
                        <strong class="text-danger">Detect Report</strong><br><br>
                        <button type="button" class="btn btn-primary" 
                            onclick='showModal(${JSON.stringify({
                                violationID, 
                                resident_name, 
                                description, 
                                type_violation,
                                date, 
                                time, 
                                admin_approval
                            })})'>
                            Check
                        </button>
                    </div>
                `);
        });
    })
    .catch(error => console.error('Error fetching GPS data:', error));




    // Fetch Enforcer Data
    document.addEventListener('DOMContentLoaded', () => {
        fetch('enforcerdata_json.php')
            .then(response => response.json())
            .then(data => { window.enforcerList = data; })
            .catch(error => console.error('Error fetching enforcer data:', error));
    });

    function showModal(data) {
    const folderPath = `../../documents/violation/${data.violationID}/resident_photos/`;
    
    // Clear previous content
    const imageContainer = document.getElementById('imageContainer');
    const violationDetails = document.getElementById('violationDetails');
    imageContainer.innerHTML = '';
    violationDetails.innerHTML = '';

    // Show loading spinner
    imageContainer.innerHTML = `
        <div class="d-flex justify-content-center w-100">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    // Load images from directory
 // Use PHP to load images
    $.ajax({
            url: 'fetch_images.php', // PHP file that loads images from the server
            method: 'GET',
            data: { violationID: data.violationID },
            success: function(response) {
                imageContainer.innerHTML = response;
            },
            error: function() {
                imageContainer.innerHTML = '<div class="alert alert-danger">Error loading images</div>';
            }
        });

    // Populate violation details
    violationDetails.innerHTML = `
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title mb-4">Violation Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Resident Name:</strong> ${data.resident_name}</p>
                        <p><strong>Date:</strong> ${data.date}</p>
                        <p><strong>Time:</strong> ${data.time}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Type Of Violation:</strong> ${data.type_violation}</p>
                        <p><strong>Description:</strong> ${data.description}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge ${data.admin_approval == 1 ? 'bg-success' : 'bg-warning'}">
                                ${data.admin_approval == 1 ? 'Approved' : 'Pending'}
                            </span>
                        </p>
                    </div>
                </div>
                
                ${data.admin_approval == 0 ? `
                    <form action="" method="post" class="mt-3">
                        <div class="form-group mb-3">
                            <label for="enforcer_id" class="form-label">Assign Enforcer:</label>
                            <select id="enforcer_id" name="enforcer_id" class="form-select" required>
                                <option value="">Select an Enforcer</option>
                                ${window.enforcerList ? 
                                    window.enforcerList.map(enforcer => 
                                        `<option value="${enforcer.id}">${enforcer.Fullname}</option>`
                                    ).join('') : ''}
                            </select>
                        </div>
                        <input type="hidden" value="1" name="approved">
                        <input type="hidden" value="${data.violationID}" name="violationID">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-circle me-2"></i>Approve Violation
                        </button>
                    </form>
                ` : `
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle me-2"></i>This violation has been approved
                    </div>
                `}
            </div>
        </div>
    `;

    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('violatorModal'));
    modal.show();
}

function showEnlargedImage(src) {
    const enlargedImage = document.getElementById('enlargedImage');
    enlargedImage.src = src;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}
    // Load GeoJSON Layer
    fetch('oldsagay.php')
        .then(response => response.json())
        .then(geoData => {
            L.geoJSON(geoData, {
                style: { color: 'violet', fillColor: 'lightblue', fillOpacity: 0.2, weight: 2 }
            }).addTo(map);
        })
        .catch(error => console.error('Error fetching GeoJSON data:', error));
</script>
<?php require_once("templates/footer.php"); ?>