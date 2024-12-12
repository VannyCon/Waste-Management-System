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



// Check if the 'admin' session is not set or is false
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: ../admin_login.php");
    exit();
}
// Start the session to store messages

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Approve/Decline Action via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $user_id = $_POST['user_id'] ?? 'not received';
    $action = $_POST['action'] ?? 'not received';
    $recipientEmail = $_POST['email'] ?? 'not received';

    // Log or output the received data for debugging
    echo "Received Data:\n";
    echo "User ID: $user_id\n";
    echo "Action: $action\n";
    echo "Email: $recipientEmail\n";


    // Determine new status based on action
    if ($action == 'approve') {
        $new_status = 'Approved';
    } elseif ($action == 'decline') {
        $new_status = 'Declined';
        $_SESSION['decline_message'] = "User with ID $user_id has been declined."; // Store message in session
    } else {
        echo "Invalid action";
        exit();
    }

    // Update the user's status in the database
    $sql = "UPDATE users SET Status = '$new_status' WHERE id = $user_id";

    if ($conn->query($sql) === TRUE) {
                // // // Validate email
        if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email address.";
            exit;
        }
    
        $mail = new PHPMailer(true);
        if($new_status == 'Approved') {
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
                $mail->addAddress($recipientEmail);
    
                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Account Approved';
                $mail->Body    = '<h1>Hi Beloving Resident</h1><p>Your Account is Successfully Approved now you can start report.</p>';
                $mail->AltBody = 'Thank you for supporting us'; // Plain text version
        
                $mail->send();
                echo "success"; // echo success response to JavaScript
            } catch (Exception $e) {
                echo "Error updating record: " . $conn->error;
            }
        }else{
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
                $mail->addAddress($recipientEmail);
    
                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Account Declined';
                $mail->Body    = '<h1>Hi Beloving Resident</h1><p>Your Account is Being Decline Please Go to Admin Office to ask for futher information.</p>';
                $mail->AltBody = 'Thank you for supporting us'; // Plain text version
        
                $mail->send();
                echo "success"; // echo success response to JavaScript
            } catch (Exception $e) {
                echo "Error updating record: " . $conn->error;
            }
        }
       

        echo "success"; // Return success response to JavaScript
    } else {
        echo "Error updating record: " . $conn->error;
    }
    exit(); // End script execution to prevent further output
}

$title = "Users";
require_once("templates/headers.php");
require_once("templates/nav.php");

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar Structure -->
<div class="quixnav">
    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">
            <li class="nav-label first">Main Menu</li>
            <!-- Dashboard Link -->
            <li>
                <a href="index.php"><i class="fa fa-map"></i><span class="nav-text">Dashboard</span></a>
            </li>
            <!-- Map View -->
            <li>
                <a href="mapview.php"><i class="fa fa-exclamation-triangle"></i><span class="nav-text">Map View</span></a>
            </li>
            <!-- Violation Management -->
            <li>
                <a href="resident_report.php"><i class="fa fa-flag"></i><span class="nav-text">Resident Reports</span></a>
            </li>
            <!-- Notifications & Alerts -->
            <li>
                <a href="enforcer_report.php"><i class="fa fa-users"></i><span class="nav-text">Enforcer Reports</span></a>
            </li>
            <!-- Paided Violation -->
            <li>
                <a href="paided_violation.php"><i class="fa fa-money"></i><span class="nav-text">Paided Violation</span></a>
            </li>
            <!-- Reports -->
            <li>
                <a href="report.php"><i class="fa fa-file-text"></i><span class="nav-text">Reports</span></a>
            </li>
            <!-- Users Dropdown -->
            <li class="nav-item">
                <a href="#usersDropdown" class="nav-link dropdown-toggle" data-toggle="collapse" aria-expanded="false">
                    <i class="fa fa-users"></i> <span class="nav-text">Users</span>
                </a>
                <ul class="collapse list-unstyled" id="usersDropdown">
                    <!-- Resident -->
                    <li>
                        <a href="user.php"><i class="fa fa-user"></i> Resident</a>
                    </li>
                    <!-- Enforcement -->
                    <li>
                        <a href="enforce.php"><i class="fa fa-file-text"></i> Enforcement</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><b style="color: black;">CONCERNED CITIZENS</b></h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="User" class="table table-striped table-responsive-sm" style="min-width: 845px">
                    <thead>
                        <tr>
                            <th><b style="color: black;">No.</b></th>
                            <th><b style="color: black;">Full Name</b></th>
                            <th><b style="color: black;">Age</b></th>
                            <th><b style="color: black;">Email</b></th>
                            <th><b style="color: black;">Gender</b></th>
                            <th><b style="color: black;">Contact Number</b></th>
                            <th><b style="color: black;">Address</b></th>
                            <th><b style="color: black;">ID Photo</b></th>
                            <th><b style="color: black;">Username</b></th>
                            <th><b style="color: black;">Date Registered</b></th>
                            <th><b style="color: black;">Status</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM users";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $no = 1;
                            while ($row = $result->fetch_assoc()) {
                                $row_id = "row_" . $row['id']; // Unique row ID for each user
                                echo "<tr id='$row_id'>";
                                echo "<td><b style='color: black;'>{$no}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Fullname']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Age']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['email']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Gender']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Contactnumber']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Address']}</b></td>";
                                 // Update the viewPhoto function call
                                echo "<td><button class='btn btn-info' onclick='viewPhoto({$row['id']})'><i class='fa fa-image'></i></button></td>";
                                echo "<td><b style='color: black;'>{$row['Username']}</b></td>";
                                echo "<td><b style='color: black;'>{$row['Created_at']}</b></td>";

                                // Display Approve and Decline buttons if the status is 'Pending'
                                if ($row['Status'] == 'Pending') {
                                    echo "<td id='status_{$row['id']}'>
                                                <button class='btn btn-success' onclick='updateUserStatus({$row['id']}, \"approve\", \"{$row['email']}\")'><b style='color: black;'>Approve</b></button>
                                                <button class='btn btn-danger' onclick='updateUserStatus({$row['id']}, \"decline\", \"{$row['email']}\")'><b style='color: black;'>Decline</b></button>
                                            </td>";

                                } else {
                                    // If not pending, show the current status and no buttons
                                    echo "<td id='status_{$row['id']}'><b style='color: black;'>{$row['Status']}</b></td>";
                                }

                                echo "</tr>";
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='12'><b style='color: black;'>No users found</b></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Photo Modal -->
<div class="modal fade" id="viewPhotoModal" tabindex="-1" role="dialog" aria-labelledby="viewPhotoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPhotoLabel"><b style="color: black;">View ID Photo</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <img id="photo" src="" class="img-fluid" alt="ID Photo">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><b style="color: black;">Close</b></button>
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationLabel"><b style="color: black;">Notification</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="notificationMessage"><b style="color: black;"><?php echo isset($_SESSION['decline_message']) ? $_SESSION['decline_message'] : ''; ?></b></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><b style="color: black;">Close</b></button>
            </div>
        </div>
    </div>
</div>

<script>
function viewPhoto(userId) {
    // Set the photo source to the user's ID photo
    const photoSrc = '../../documents/resident_ids/' + userId + '.png';
    document.getElementById('photo').src = photoSrc;

    // Show the modal
    $('#viewPhotoModal').modal('show');
}

function updateUserStatus(userId, action, email) {
    if (action === 'decline' && !confirm('Are you sure you want to decline this user?')) {
        return; // Exit if the user cancels the decline action
    }

    // Send an AJAX request to the server
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            action: action,
            user_id: userId,
            email: email,
        }),
    })
    .then(response => response.text())
    .then(result => {
        if (result.trim() === 'success') {
            // Update the status cell in the table dynamically
            const statusCell = document.getElementById(`status_${userId}`);
            const newStatus = action === 'approve' ? 'Approved' : 'Declined';
            statusCell.innerHTML = `<b style="color: black;">${newStatus}</b>`;
            location.reload(); // This will reload the page
            // Optionally show a notification (success message)
        } else {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred.');
    });
}

// Show the notification modal if there's a decline message in the session
$(document).ready(function() {
    <?php if (isset($_SESSION['decline_message'])): ?>
        $('#notificationModal').modal('show');
        <?php unset($_SESSION['decline_message']); // Clear the message after showing ?>
    <?php endif; ?>
});
</script>

<?php
require_once("templates/footer.php");
$conn->close();
?>
