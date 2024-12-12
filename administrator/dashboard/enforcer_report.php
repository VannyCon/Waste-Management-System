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

$title = "enforcer";
require_once("templates/headers.php");
require_once("templates/nav.php");

// Fetch records from tbl_enforcer_report, ordered by ID in ascending order
$sql = "SELECT `id`, `violationID`, `resident_name`, `violators_name`, `violators_age`, `violators_gender`, `violators_location`, `violation_type`, `datetime`, `latitude`, `longitude`, `penalty` 
        FROM `tbl_enforcer_report` 
        WHERE `isPaid` = 0
        ORDER BY `id` ASC";

$result = $conn->query($sql);
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><b style="color: black;">ENFORCER REPORTS</b></h4>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="EnforcerReports" class="table table-striped table-responsive-sm" style="min-width: 845px">
                    <thead>
                        <tr>
                            <th><b style="color: black;">No.</th>
                            <th><b style="color: black;">Resident Name</th>
                            <th><b style="color: black;">Violator's Name</th>
                            <th><b style="color: black;">Violator's Age</th>
                            <th><b style="color: black;">Violator's Gender</th>
                            <th><b style="color: black;">Location</th>
                            <th><b style="color: black;">Violation Type</th>
                            <th><b style="color: black;">Date & Time</th>
                            <th><b style="color: black;">Penalty</th>
                            <th><b style="color: black;">Offense</th>
                            <th><b style="color: black;">Evidence</th>
                            <th><b style="color: black;">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                        <?php
                        $counter = 1; // Initialize a counter variable
                        if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $counter++; ?></td> <!-- Use the counter for ordered sequence -->
                                    <td><?php echo htmlspecialchars($row['resident_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['violators_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['violators_age']); ?></td>
                                    <td><?php echo ucfirst($row['violators_gender']); ?></td>
                                    <td><?php echo htmlspecialchars($row['violators_location']); ?></td>
                                    <td><?php echo htmlspecialchars($row['violation_type']); ?></td>
                                    <td><?php echo date('M j, Y, g:i a', strtotime($row['datetime'])); ?></td>
                                    <td>&#8369;<?php echo number_format($row['penalty'], 2); ?></td>
                                    <td><?php if($row['penalty'] == "500"){
                                        echo "<span class='badge badge-success'>First Offense</span>";
                                    }else if($row['penalty'] == "1000"){
                                        echo "<span class='badge badge-warning'>Second Offense</span>";
                                    } else if($row['penalty'] == "5000"){
                                        echo "<span class='badge badge-danger'>Third Offense</span>";
                                    }; ?></td>
                                    <td>
                                        <button class="btn btn-info view-images" data-toggle="modal" 
                                                data-target="#imagesModal-<?php echo $row['violationID']; ?>" 
                                                data-violation-id="<?php echo $row['violationID']; ?>">Check</button>
                                    </td>
                                    <td>
                                        <!-- Edit Button -->
                                        <button class="btn btn-warning" data-toggle="modal" 
                                                data-target="#updateModal" 
                                                data-id="<?php echo $row['id']; ?>"
                                                data-name="<?php echo $row['violators_name']; ?>"
                                                data-age="<?php echo $row['violators_age']; ?>"
                                                data-gender="<?php echo $row['violators_gender']; ?>"
                                                data-location="<?php echo $row['violators_location']; ?>"
                                                data-type="<?php echo $row['violation_type']; ?>" 
                                                data-penalty="<?php echo $row['penalty']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                            <!-- Delete Button -->
                                        <button class="btn btn-danger" data-toggle="modal" 
                                            data-target="#deleteModal" 
                                            data-id="<?php echo $row['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" data-toggle="modal" 
                                            data-target="#paidModal" 
                                            data-id="<?php echo $row['id']; ?>">
                                           Pay
                                        </button>
                                    </td>
                                </tr>

                                <!-- Separate Image Modal for each violation -->
                                <div class="modal fade" id="imagesModal-<?php echo $row['violationID']; ?>" 
                                     tabindex="-1" role="dialog" 
                                     aria-labelledby="imagesModalLabel-<?php echo $row['violationID']; ?>" 
                                     aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" 
                                                    id="imagesModalLabel-<?php echo $row['violationID']; ?>">
                                                    Evidence Images - Violation ID: <?php echo $row['violationID']; ?>
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="image-gallery" id="imageGallery-<?php echo $row['violationID']; ?>">
                                                    <?php
                                                    $violationID = $row['violationID'];
                                                    $photoDir = "../../documents/violation/$violationID/enforcer_photos/";
                                                    
                                                    if (is_dir($photoDir)) {
                                                        $photos = array_diff(scandir($photoDir), ['.', '..']);
                                                        if (!empty($photos)) {
                                                            foreach ($photos as $photo) {
                                                                echo "
                                                                    <img src='$photoDir$photo' 
                                                                        class='img-thumbnail m-1' 
                                                                        width='100' 
                                                                        alt='Photo' 
                                                                        onclick='showImage(\"$photoDir$photo\")'>
                                                                ";
                                                            }
                                                        } else {
                                                            echo "<p>No photos available for this violation.</p>";
                                                        }
                                                    } else {
                                                        echo "<p>Photo folder not found for this violation.</p>";
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13" class="text-center">No records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Large Image Modal -->
<div class="modal fade" id="largeImageModal" tabindex="-1" role="dialog" aria-labelledby="largeImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="largeImageModalLabel">Full Size Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="largeImage" src="" alt="Full size image" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateModalLabel">Update Enforcer Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateForm" method="POST" action="enforcer_update.php">
                    <input type="hidden" name="id" id="updateId">

                    <div class="form-group">
                        <label for="updateName">Violator's Name</label>
                        <input type="text" class="form-control" name="name" id="updateName" required>
                    </div>
                    <div class="form-group">
                        <label for="updateAge">Age</label>
                        <input type="number" class="form-control" name="age" id="updateAge" required>
                    </div>
                    <div class="form-group">
                        <label for="updateGender">Gender</label>
                        <select class="form-control" name="gender" id="updateGender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="updateLocation">Location</label>
                        <input type="text" class="form-control" name="location" id="updateLocation" required>
                    </div>
                    <div class="form-group">
                        <label for="updateType">Violation Type</label>
                        <input type="text" class="form-control" name="type" id="updateType" required>
                    </div>
                    <div class="form-group">
                        <label for="updatePenalty">Penalty</label>
                        <input type="number" class="form-control" name="penalty" id="updatePenalty" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Enforcer Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record?</p>
                <form id="deleteForm" method="POST" action="enforcer_delete.php">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="paidModal" tabindex="-1" role="dialog" aria-labelledby="paidModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paidModalLabel">Pay Violation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to violators paid?</p>
                <form id="paidForm" method="POST" action="paid_update.php">
                    <input type="hidden" name="id" id="paidID">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Yes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


<script>
function showImage(imageSrc) {
    $('#largeImage').attr('src', imageSrc);
    $('#largeImageModal').modal('show');
}

// Clean up when any images modal is closed
$('[id^="imagesModal-"]').on('hidden.bs.modal', function () {
    const violationId = $(this).attr('id').split('-')[1];
    $(`#imageGallery-${violationId}`).empty();
});

// Populate Update Modal
$('#updateModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#updateId').val(button.data('id'));
    $('#updateName').val(button.data('name'));
    $('#updateAge').val(button.data('age'));
    $('#updateGender').val(button.data('gender'));
    $('#updateLocation').val(button.data('location'));
    $('#updateType').val(button.data('type'));
    $('#updatePenalty').val(button.data('penalty'));
});

// Populate Delete Modal
$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#deleteId').val(button.data('id'));
});

// Populate Delete Modal
$('#paidModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    $('#paidID').val(button.data('id'));
});

</script>

<?php
$conn->close();
require_once("templates/footer.php");
?>