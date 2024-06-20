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
<title>Document Repository</title>
<link rel="icon" href="images/folder.png" type="image/x-icon">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
<link rel="stylesheet" href="css/DocumentRepository.css">
<style>
    .notification-icon {
        margin-left: 1220px;
    }

    .notification-icon i {
        color: #fff;
        font-size: 26px;
    }

    .title-with-icon p{
        margin-right: 1110px;
    }

    .folders-container {
        margin-top: 210px;
        margin-left: 137px;
        width: 100%; /* Adjusted margin top */
    }

    .folder {
        background-color: #ffff;
        padding: 20px;
        text-align: center;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px; /* Adjusted margin bottom for spacing */
        width: 100%;
    }

    .folder i {
        font-size: 70px;
        margin-bottom: 10px;
        color: #1E63E9; 
    }

    .folder a{
        text-decoration: none;
    }
    .folder a p {
        font-size: 20px;
        text-decoration: none;
    }
    .folder a:hover{
        color: #e91e63;
        text-decoration: none;
    }
    .folder a i:hover{
        color: #e91e63;
    }
</style>
</head>
<body>
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
                <img src="<?php echo $picture; ?>" alt="Barangay Hall of Paule 1" width="80px" height="80px" onerror="this.style.display='none';">
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

          <img src="<?php echo $picture; ?>" width="80px" height="80px" onerror="this.style.display='none';">
          <p class="p"><?php echo $firstname . " " . $middlename . " " . $lastname; ?></p>
      </div>
    <div class="administrators">
        <p><em> Administrator</em></p>
    </div>
    <a href="DocumentDashboard.php" class="a1"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <a href="DocumentRepository.php" class="a1" id="dashb"><i class="fas fa-folder"></i> File Repository</a>
          <a href="FileMetadata.php" class="a1"><i class="fas fa-server"></i> File Metadata</a>
          <a href="RequestDetails.php" class="a1"><i class="fas fa-envelope"></i>Request Details</a>     
          <a href="FileUpload.php" class="a1"><i class="bi bi-upload"></i> File Upload</a>    
          <a href="#" onclick="confirmLogout()" class="a1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
</div>

<div class="title-with-icon">
    <a href="AdminDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
    <p>File Repository</p>
</div>

<div class="container">
    <div class="row folders-container">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="folder">
                <a href="officialfile.php">
                    <i class="bi bi-folder"></i>
                    <p>Official Files</p>
                </a>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="folder">
                <a href="residentfile.php">
                    <i class="bi bi-folder"></i>
                    <p>Resident Files</p>
                </a>
            </div>
        </div>
        
        <!-- Folder 3 -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="folder">
                <a href="formfile.php">
                    <i class="bi bi-folder"></i>
                    <p>Form Files</p>
                </a>
            </div>
        </div>
        
        <!-- Folder 4 -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="folder">
            <a href="activityfile.php">
                    <i class="bi bi-folder"></i>
                    <p>Activity Files</p>
            </a>
            </div>
        </div>
        
        <!-- Folder 5 -->
        <div class="col-lg-4 col-md-6 mb-4">
        <div class="folder">
            <a href="profilefile.php">
                    <i class="bi bi-folder"></i>
                    <p>Profile Files</p>
            </a>
            </div>
        </div>
        
        <!-- Folder 6 -->
        <div class="col-lg-4 col-md-6 mb-4">
        <div class="folder">
            <a href="requestfile.php">
                    <i class="bi bi-folder"></i>
                    <p>Request Files</p>
            </a>
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
<script src="Admin.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.0/main.min.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.js"></script>

<script>
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
                    window.location.href = 'residentfile.php?logout=true';
                }
            });
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
