<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

// Handle logout request
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Include database connection
include 'connection.php';

// Check if form is submitted with file
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $file_name = $_FILES["file"]["name"];
    $file_tmp = $_FILES["file"]["tmp_name"];

    // Directory where files will be saved
    $upload_directory = "file_uploads/";  // Adjusted directory path

    // Move uploaded file to directory
    $file_path = $upload_directory . $file_name;
    if (move_uploaded_file($file_tmp, $file_path)) {
        // File uploaded successfully to folder

        // Prepare SQL statement
        $sql = "INSERT INTO file_upload (file_name, file) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $file_name, $file_tmp);

        // Execute statement
        if ($stmt->execute()) {
            header("Location: FileUpload.php");
            exit;
        } else {
            // Error in SQL execution
            echo "Error uploading file metadata to database: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error uploading file to folder.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>File Upload</title>
<link rel="icon" href="images/folder.png" type="image/x-icon">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>
<link rel="stylesheet" href="css/DocumentDashboard.css">
<style>
.notification-icon {
            margin-left: 1200px;
        }
        .notification-icon i {
            color: #fff;
            font-size: 33px;
        }
        .title-with-icon p{
            margin-right: 1130px;
        }
        .container {
            margin-top: 20px;
            margin-left: 265px;
        }
        .file-box {
            background-color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            cursor: pointer;
            margin-left: 10px;
        }
        .file-box img {
            width: 350px;
            height: 300px;
            margin-top: 10px;
            border-radius: 8px;
        }
        .file-box h5{
            font-size: 15px;
            width: 90%;
            margin:0 auto;
        }
</style>
</head>
<body>
<div>
    <div class="header">
        <div class="notification-icon">
            <i class="fas fa-bell" style="display: none;"></i>
            <span class="badge"></span>
        </div>
        <div class="profile-icon" onclick="toggleProfileDetails()">
            <i class="fas fa-user" style="color: #fff;"></i>
            <div class="profile-details-container" id="profileDetailsContainer">
                <div class="profile">
                <?php
                include 'connection.php';

                $sql = "SELECT * FROM proof_of_identity WHERE id = (SELECT id FROM profiledata WHERE email = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $_SESSION['username']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $picture = $row["picture"];
                } else {
                    $picture = "";
                }

                $sql = "SELECT * FROM profiledata WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $_SESSION['username']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $firstname = $row["firstname"];
                    $middlename = $row["middlename"];
                    $lastname = $row["lastname"];
                } else {
                    $firstname = "";
                    $middlename = "";
                    $lastname = "";
                }

                $sql = "SELECT * FROM users WHERE userName = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $_SESSION['username']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $userType = $row["userType"];
                } else {
                    $userType = "";
                }

                $stmt->close();
                $conn->close();
                ?>
                <img src="<?php echo $picture; ?>" alt="Profile Picture" width="80px" height="80px" onerror="this.style.display='none';">
                <div class="adminname">
                    <p class="p1"><?php echo $firstname . " " . $middlename . " " . $lastname; ?></p>
                    <p class="p2"><?php echo $userType; ?></p>
                </div>
                </div>
                <hr style="background-color: black !important; color: black !important;">
                <a href="UserProfile.php"><i class="bi bi-person" style="color: black;"></i> Profile</a>
                <hr>
                <a href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-right" style="color: black;"></i> Log Out</a>
            </div>
        </div>
    </div>
    <div class="navigation" id="navigation">
        <div class="picfetch">
            <?php
            include 'connection.php';

            $sql = "SELECT * FROM proof_of_identity WHERE id = (SELECT id FROM profiledata WHERE email = ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $picture = $row["picture"];
            } else {
                $picture = "";
            }

            $sql = "SELECT * FROM profiledata WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_SESSION['username']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $firstname = $row["firstname"];
                $middlename = $row["middlename"];
                $lastname = $row["lastname"];
            } else {
                $firstname = "";
                $middlename = "";
                $lastname = "";
            }

            $stmt->close();
            $conn->close();
            ?>
            <img src="<?php echo $picture; ?>" width="80px" height="80px" onerror="this.style.display='none';">
            <p class="p"><?php echo $firstname . " " . $middlename . " " . $lastname; ?></p>
        </div>
        <div class="administrators">
            <p><em> Administrator</em></p>
        </div>
        <a href="DocumentDashboard.php" class="a1"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="DocumentRepository.php" class="a1"><i class="fas fa-folder"></i> File Repository</a>
        <a href="FileMetadata.php" class="a1"><i class="fas fa-server"></i> File Metadata</a>
        <a href="RequestDetails.php" class="a1"><i class="fas fa-envelope"></i>Request Details</a>
        <a href="FileUpload.php" class="a1" id="dashb"><i class="bi bi-upload"></i> File Upload</a>        
        <a href="#" onclick="confirmLogout()" class="a1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
</div>
</div>
</div>
</div>
</div>
<div class="title-with-icon">
<a href="AdminDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
<p>File Upload</p>
</div>
<div style="margin-top:180px; margin-left: 1450px;">
<form id="fileForm" method="POST" enctype="multipart/form-data">
        <label for="fileInput" class="btn btn-primary add-file-btn">
            <input id="fileInput" type="file" name="file" style="display: none;" onchange="submitForm()">
            <i class="bi bi-plus"></i> Upload File
        </label>
    </form>
</div>
<div class="container">
    <div class="row">
        <!-- File Boxes -->
        <?php
        include 'connection.php';

        $sql = "SELECT * FROM file_upload";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $picture = $row["file_name"];
                $picturePath = "file_uploads/" . $picture; // Assuming $picture contains the file name

                echo '<div class="col-lg-4 col-md-6 mb-4">';
                echo '<div class="file-box" data-filepath="' . $picturePath . '" data-filetype="image/jpeg">';
                echo '<h5>' . $picture . '</h5>';
                echo '<img src="' . $picturePath . '" alt="Activity Photo">';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<p>No data available.</p>";
        }

        $conn->close();
        ?>
    </div>
</div>

<div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="height:80vh;">
            <div class="modal-header" style="border:none;">
                <i class="bi bi-three-dots-vertical" id="optionsTrigger" style="font-size:20px; cursor: pointer;"></i>
                <div id="optionsContainer" style="display: none; position: absolute; top: 60px; left: 10px; background: white; border: 1px solid #ccc; border-radius: 5px; z-index: 1000;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li><a href="#" id="downloadOption" class="btn btn-light download-icon" style="display: block; padding: 10px;" download>Download</a></li>
                        <li><a href="#" id="deleteOption" class="btn btn-light" style="display: block; padding: 10px;">Delete</a></li>
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="modalImage" style="width: 100%; border-radius: 8px; height:63vh;" src="" alt="File Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<footer class="footer">
<div class="container">
<p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

$(document).ready(function() {
    $('.file-box').click(function() {
        var filePath = $(this).data('filepath');
        var fileType = $(this).data('filetype');
        if (fileType.startsWith('image/')) {
            $('#modalImage').attr('src', filePath);
            $('#downloadOption').attr('href', filePath);
            $('#deleteOption').data('filepath', filePath);
            $('#fileModal').modal('show');
        }
    });

    $('#optionsTrigger').click(function() {
        $('#optionsContainer').toggle();
    });

    $(document).click(function(event) {
        if (!$(event.target).closest('#optionsContainer, #optionsTrigger').length) {
            $('#optionsContainer').hide();
        }
    });

    $('#deleteOption').click(function() {
        var filePath = $(this).data('filepath');
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you really want to delete this file?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'No, keep it',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_file.php',
                    type: 'POST',
                    data: { file_path: filePath },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Your file has been deleted.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = 'FileUpload.php';
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error deleting file: ' + result.error,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const downloadIcons = document.querySelectorAll('.download-icon');

    downloadIcons.forEach(icon => {
        icon.addEventListener('click', function(event) {
            const fileBox = event.target.closest('.file-box');
            const filePath = fileBox.getAttribute('data-filepath');
            const fileName = fileBox.getAttribute('data-filename');
            downloadFile(filePath, fileName);
        });
    });
});

function downloadFile(filePath, fileName) {
    const a = document.createElement('a');
    a.href = filePath;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}


function confirmLogout() {
Swal.fire({
title: 'Are you sure?',
text: 'Are you sure you want to log out?',
icon: 'warning',
showCancelButton: true,
confirmButtonText: 'Yes, log out',
cancelButtonText: 'Cancel',
reverseButtons: true
}).then((result) => {
if (result.isConfirmed) {
window.location.href = 'DocumentDashboard.php?logout=true';
}
});
}

function submitForm() {
        document.getElementById('fileForm').submit();
    }

function toggleProfileDetails() {
var profileDetailsContainer = document.getElementById('profileDetailsContainer');
if (profileDetailsContainer.style.display === 'block' || profileDetailsContainer.style.display === '') {
profileDetailsContainer.style.display = 'none';
} else {
profileDetailsContainer.style.display = 'block';
}
}

</script>
</body>
</html>

