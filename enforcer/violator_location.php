<?php 
session_start(); // Start the session

// Check if the 'resident' session is not set or is false
if (!isset($_SESSION['enforcer']) || $_SESSION['enforcer'] !== true) {
    // Redirect to the login page if the user is not authenticated
    header("Location: enforcer_login.php");
    exit();
}
    $violationID = $_GET['violationID'];
    $residentName = $_GET['residentName'];
    $latitude = $_GET['latitude'];  // Violator's latitude
    $longitude = $_GET['longitude']; // Violator's longitude
    $description = $_GET['description'];

    // Define the folder path
    $photoDir = "../documents/violation/$violationID/resident_photos/";

    // Initialize an array to hold photo file names
    $photos = [];

    // Check if the folder exists and is readable
    if (is_dir($photoDir)) {
        $photos = array_diff(scandir($photoDir), ['.', '..']); // Get all files except '.' and '..'
    } else {
        echo "<p>Photo folder not found.</p>";
    }
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<style>
    #map { height: 400px; width: 100%; }
    .btn-locate { margin-top: 10px; }
</style>

<div class="content-body my-2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <a href="index.php" class="btn btn-danger my-1">Back</a>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Violation Details</h4>
                    </div>
                    <div class="card-body">
                        <div id="map"></div>

                        <button id="locateBtn" class="btn btn-info btn-locate w-100">Locate and Route</button>

                        <table class="table table-bordered table-striped mt-3">
                            <thead class="table-dark">
                                <tr>
                                    <th>Resident Name</th>
                                    <th>Evidence</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($residentName); ?></td>
                                    <td>
                                        <?php 
                                            if (!empty($photos)) {
                                                foreach ($photos as $photo) {
                                                    echo "
                                                        <img src='$photoDir$photo' 
                                                             class='img-thumbnail m-1' 
                                                             width='100' 
                                                             alt='Photo' 
                                                             data-bs-toggle='modal' 
                                                             data-bs-target='#imageModal' 
                                                             onclick='showImage(\"$photoDir$photo\")'>
                                                    ";
                                                }
                                            } else {
                                                echo "No photos available.";
                                            }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($description); ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <a href="report_form.php?violationID=<?php echo $violationID ?>&residentName=<?php echo $residentName?>&latitude=<?php echo $latitude?>&longitude=<?php echo $longitude?>&description=<?php echo $description?>" type="button" class="btn btn-success w-100 mt-2">Report</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Bootstrap Modal for Image View -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="modalImage" src="" alt="Full View" class="img-fluid">
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
      // Function to show the clicked image in the modal
      function showImage(src) {
        document.getElementById('modalImage').src = src;
    }
    // Initialize the map centered on violator's location
    var map = L.map('map').setView([<?php echo $latitude; ?>, <?php echo $longitude; ?>], 14);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker for the violator's location
    var violatorMarker = L.marker([<?php echo $latitude; ?>, <?php echo $longitude; ?>]).addTo(map)
        .bindPopup('<strong>Violator\'s Location</strong>').openPopup();

    // Button click to locate enforcer and show route
    document.getElementById('locateBtn').addEventListener('click', function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showRoute, showError);
        } else {
            alert('Geolocation is not supported by this browser.');
        }
    });

    // Function to show the route and calculate distance
    function showRoute(position) {
        var enforcerLat = position.coords.latitude;
        var enforcerLng = position.coords.longitude;

        // Add a marker for the enforcer's location
        var enforcerMarker = L.marker([enforcerLat, enforcerLng]).addTo(map)
            .bindPopup('<strong>Your Location (Enforcer)</strong>').openPopup();

        // Use Leaflet Routing Machine to draw the route
        L.Routing.control({
            waypoints: [
                L.latLng(enforcerLat, enforcerLng),
                L.latLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>)
            ],
            lineOptions: {
                styles: [{ color: 'orange', weight: 6 }]  // Orange path
            },
            createMarker: function() { return null; }  // No extra markers needed
        }).addTo(map);

        // Calculate distance in kilometers
        var distance = map.distance([enforcerLat, enforcerLng], [<?php echo $latitude; ?>, <?php echo $longitude; ?>]) / 1000;
    }

    // Handle errors from the Geolocation API
    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert('User denied the request for Geolocation.');
                break;
            case error.POSITION_UNAVAILABLE:
                alert('Location information is unavailable.');
                break;
            case error.TIMEOUT:
                alert('The request to get user location timed out.');
                break;
            case error.UNKNOWN_ERROR:
                alert('An unknown error occurred.');
                break;
        }
    }

    fetch('oldsagay.php')
        .then(response => response.json())
        .then(geoData => {
            // Define a style for the GeoJSON layer
            const geoJsonStyle = {
                color: 'violet', // Outline color
                fillColor: 'lightblue', // Fill color
                fillOpacity: 0.2, // Fill opacity (0.0 to 1.0)
                weight: 2 // Outline weight
            };

            // Add the GeoJSON layer to the map with the specified style
            L.geoJSON(geoData, { style: geoJsonStyle }).addTo(map);
        })
        .catch(error => {
            console.error('Error fetching GeoJSON data:', error);
        });

</script>
