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

$title = "Enforcement Records";
require_once("templates/headers.php");
require_once("templates/nav.php");

// Fetch records
$sql = "SELECT `id`, `violationID`, `resident_name`, `violators_name`, `description`, `violators_location`, `latitude`, `longitude`, `date`, `time`, `admin_approval`, `isActive` FROM `tbl_resident_report` WHERE 1";
$result = $conn->query($sql);
?>

<div class="content-body">
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4><b style="color: black;">VIOLATION MANAGEMENT</b></h4>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="Enforcement" class="table table-striped table-responsive-sm" style="min-width: 845px">
                    <thead>
                        <tr>
                            <th><b style="color: black;">No.</th>
                            <th><b style="color: black;">Violation ID</th>
                            <th><b style="color: black;">Resident Name</th>
                            <th><b style="color: black;">Violator's Name</th>
                            <th><b style="color: black;">Description</th>
                            <th><b style="color: black;">Location</th>
                            <th><b style="color: black;">Date</th>
                            <th><b style="color: black;">Time</th>
                            <th><b style="color: black;">Admin Approval</th>
                            <th><b style="color: black;">Active</th>
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
                                    <td><?php echo $row['violationID']; ?></td>
                                    <td><?php echo $row['resident_name']; ?></td>
                                    <td><?php echo $row['violators_name']; ?></td>
                                    <td><?php echo $row['description']; ?></td>
                                    <td><?php echo $row['violators_location']; ?></td>
                                    <td><?php echo $row['date']; ?></td>
                                    <td><?php echo $row['time']; ?></td>
                                    <td><?php echo $row['admin_approval'] ? 'Yes' : 'No'; ?></td>
                                    <td style="color: <?php echo $row['isActive'] ? 'green' : 'red'; ?>">
                                        <?php echo $row['isActive'] ? 'Active' : 'Inactive'; ?>
                                    </td>

                                    <td>
                                        <button class="btn btn-info view-images" data-toggle="modal" 
                                                data-target="#imagesModal-<?php echo $row['violationID']; ?>"
                                                data-violation-id="<?php echo $row['violationID']; ?>">
                                            Check
                                        </button>
                                    </td>
                                    <td>
                                         <!-- Edit Button -->
                                     <button class="btn btn-warning" data-toggle="modal" 
                                            data-target="#updateModal"
                                                data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                                
                                        <!-- Delete Button -->
                                        <button class="btn btn-danger" data-toggle="modal" 
                                            data-target="#deleteModal" 
                                            data-id="<?php echo $row['id']; ?>">
                                            <i class="bi bi-trash"></i>
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
                                                    $photoDir = "../../documents/violation/$violationID/resident_photos/";
                                                    
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
                                <td colspan="11" class="text-center">No records found.</td>
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

<!-- Update Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="resident_update.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="updateId" name="id">

                    <div class="form-group">
                        <label for="adminApproval">Admin Approval</label>
                        <select id="adminApproval" name="admin_approval" class="form-control">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="isActive">Active</label>
                        <select id="isActive" name="isActive" class="form-control">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="resident_delete.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Record</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="deleteId" name="id">
                    <p>Are you sure you want to delete this record?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- jQuery -->
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
// Handle update modal
$('#updateModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    $('#updateId').val(id);
});

// Handle delete modal
$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    $('#deleteId').val(id);
});

</script>

<?php require_once("templates/footer.php"); ?>