<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile File</title>
    <link rel="icon" href="images/folder.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
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
            margin-right: 1110px;
        }
        .container {
            margin-top: 0px;
            margin-left: 280px;
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
        .back-button{
            margin-top: 170px;
            margin-left: 300px;
            display: flex;
        }
        .back-button i{
            font-weight: bold;
            font-size: 20px;
        }
        .back-button p{
            font-size: 25px;
            font-weight: bold;
        }
        .modal-body img{
            width: 100%;
            border-radius: 8px;
        }
        #downloadButton{
            margin: 0 auto;
            border-radius: 20px;
            background-color: none;
            color: #1E217C;
            border-color: #1E217C;
            margin-left: 37%;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="notification-icon">
        <i class="fas fa-bell" style="display:none;"></i>
        <span class="badge"></span>
    </div>
    <div class="profile-icon" onclick="toggleProfileDetails()">
        <i class="fas fa-user" style="color:#fff;"></i>
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
            <hr>
            <a href="UserProfile.php"><i class="bi bi-person"></i> Profile</a>
            <hr>
            <a href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-right"></i> Log Out</a>
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

        <img src="<?php echo $picture; ?>" width="80px" height="80px" alt="Profile Picture" onerror="this.style.display='none';">
        <p class="p"><?php echo $firstname . " " . $middlename . " " . $lastname; ?></p>
    </div>
    <div class="administrators">
        <p><em>Administrator</em></p>
    </div>
    <a href="DocumentDashboard.php" class="a1"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <a href="DocumentRepository.php" class="a1" id="dashb"><i class="fas fa-folder"></i> File Repository</a>
          <a href="FileMetadata.php" class="a1"><i class="fas fa-server"></i> File Metadata</a>
          <a href="RequestDetails.php" class="a1"><i class="fas fa-envelope"></i>Request Details</a>     
          <a href="FileUpload.php" class="a1"><i class="bi bi-upload"></i> File Upload</a>    
          <a href="#" onclick="confirmLogout()" class="a1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
</div>

<div class="title-with-icon">
    <a href="DocumentDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
    <p>File Repository</p>
</div>

<div class="back-button">
        <i class="bi bi-arrow-left" style="font-size: 24px; color: black;"></i>
        <p style="margin-left: 10px;">Profile File</p>
</div>

<div class="container">
    <div class="row">
        <!-- File Boxes -->
        <?php
        include 'connection.php';

        $sql = "SELECT * FROM profile_file";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $picture = $row["photos"];
                $picturePath = $picture; // Assuming $picture contains the file name

                echo '<div class="col-lg-4 col-md-6 mb-4">';
                echo '<div class="file-box" data-filepath="' . $picturePath . '" data-filetype="image/jpeg">';
                echo '<h5>' . $picture . '</h5>';
                echo '<img src="' . $picturePath . '" alt="Profile Photo">';
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

<!-- Modal -->
<div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="border:none;">
                <a id="downloadButton" href="#" download class="btn btn-light mt-3">Download</a>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="modalImage" src="" alt="File Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.file-box').click(function() {
            var filePath = $(this).data('filepath');
            var fileType = $(this).data('filetype');
            if (fileType.startsWith('image/')) {
                $('#modalImage').attr('src', filePath);
                $('#fileModal').modal('show');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', (event) => {
    var fileModal = document.getElementById('fileModal');
    var modalImage = document.getElementById('modalImage');
    var downloadButton = document.getElementById('downloadButton');

    fileModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var fileUrl = button.getAttribute('data-file-url'); // Extract info from data-* attributes

        modalImage.src = fileUrl; // Set the image source
        downloadButton.href = fileUrl; // Set the download link
    });

    // Optional: Reset modal on close to prevent showing previous image
    fileModal.addEventListener('hidden.bs.modal', function (event) {
        modalImage.src = ''; // Clear image source
        downloadButton.href = '#'; // Reset download link
    });

    // Download image functionality
    downloadButton.addEventListener('click', function (event) {
        // Prevent default link behavior (opening in new tab)
        event.preventDefault();

        // Create an anchor element to trigger download
        var link = document.createElement('a');
        link.href = modalImage.src;
        link.download = 'image.jpg'; // Optional: Set a specific file name
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});


function toggleProfileDetails() {
    var profileDetailsContainer = document.getElementById('profileDetailsContainer');
    if (profileDetailsContainer.style.display === 'block' || profileDetailsContainer.style.display === '') {
        profileDetailsContainer.style.display = 'none';
    } else {
        profileDetailsContainer.style.display = 'block';
    }
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
                    window.location.href = 'profilefile.php?logout=true';
                }
            });
        }

    document.addEventListener("DOMContentLoaded", function() {
        var backButton = document.querySelector(".back-button");
        backButton.addEventListener("click", function() {
            window.location.href = 'DocumentRepository.php';
        });
    });
</script>
</body>
</html>
