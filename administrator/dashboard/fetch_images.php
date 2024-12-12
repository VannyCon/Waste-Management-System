<?php
// load_images.php
if (isset($_GET['violationID'])) {
    $violationID = $_GET['violationID'];
    $photoDir = "../../documents/violation/$violationID/resident_photos/";

    if (is_dir($photoDir)) {
        $photos = array_diff(scandir($photoDir), ['.', '..']); // Remove . and .. from the list
        if (!empty($photos)) {
            foreach ($photos as $photo) {
                echo "
                    <div class='position-relative' style='width: 200px; height: 200px;'>
                        <img src='$photoDir$photo' 
                             class='img-thumbnail m-1' 
                             width='200' 
                             alt='Violation Photo' 
                             onclick='showEnlargedImage(\"$photoDir$photo\")'>
                    </div>
                ";
            }
        } else {
            echo "<p>No photos available for this violation.</p>";
        }
    } else {
        echo "<p>Photo folder not found for this violation.</p>";
    }
}
?>
