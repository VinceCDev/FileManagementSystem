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

include 'connection.php';

// Function to fetch total rows per month from all specified tables
function getTotalRows() {
    global $conn;

    // List of tables to include in the count
    $tables = ['official_file', 'resident_file', 'form_file', 'document_folder', 'profile_file', 'request_file'];

    // Initialize array to store counts per month
    $counts = array_fill_keys(range(1, 12), 0);

    foreach ($tables as $table) {
        // Construct SQL query to count rows per month
        $sql = "SELECT COUNT(*) AS count, MONTH(created_date) AS month FROM $table WHERE YEAR(created_date) = YEAR(CURDATE()) GROUP BY MONTH(created_date)";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $month = intval($row['month']);
                $counts[$month] += intval($row['count']);
            }
        }
    }

    // Convert counts to array format suitable for JavaScript
    $countsArray = array_values($counts);

    return $countsArray;
}

$totalRows = getTotalRows();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .notification-icon{
            margin-left: 1200px;
        }
     .notification-icon i{
        color: #fff;
        font-size: 26px;
     }

     .dashboard-box-container1, .dashboard-box-container2{
    margin-left: 285px;
    z-index: -1;
  }
  
  .dashboard-box-container1{
    margin-top: 190px;
  }
  
  .dashboard-box-container2{
    margin-top: 20px;
  }
  
  .dashboard-box1,
  .dashboard-box2 {
    display: inline-block;
    width: 396px;
    height: 200px;
    margin: 10px;
    margin-right: 10px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    background-color: #fff;
  }
  
  .i {
    font-size: 100px;
    margin-right: 0px;
    margin-top: 0px;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    filter: blur(1px);
    width: 45%;
    left: 28%;
    border-radius: 8px;
  }
  
  .box-title7{
    text-align: center;
    margin-top: 15px;
    width: 45%;
    margin-left: 195px;
  }
  
  .box-title7 p{
    font-size: 40px;
    font-weight: 600;
    margin-bottom: 30px;
  }
  
  .dashboard-box1 h3,.dashboard-box2 h3 {
    font-size: 20px;
    width: 100%;
  }

  .chart-container {
    width: 500px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    margin-left: 10px;
  }

  .calendar-container {
            width: 20px;
            height: 550px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-left: 15px;
            margin-right: 30px;
        }

    /* Styling for the next and back buttons */
.fc-prev-button, .fc-next-button {
    background-color: #e91e63 !important;
    color: #ffffff !important; /* White color for the button text */
    border: #e91e63 !important;
}

/* Styling for today button */
.fc-today-button {
    background-color: #e91e63;
    color: #ffffff !important; /* White color for the button text */
}

/* Styling for the current day */
.fc-day-today {
    background-color: #e91e63 !important;
    color: #ffffff !important; /* White color for the current day text */
    box-shadow: none !important; /* Remove shadow */
}

/* Styling for the shade of current day */
.fc-day-today .fc-daygrid-day-frame {
    background-color: #1E63A2; /* Transparent background */
    box-shadow: none !important; /* Remove shadow */
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
                <img src="<?php echo $picture; ?>" alt="Barangay Hall of Paule 1" width="80px" height="80px" onerror="this.style.display='none';">
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
          <a href="DocumentDashboard.php" class="a1" id="dashb"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <a href="DocumentRepository.php" class="a1"><i class="fas fa-folder"></i> File Repository</a>
          <a href="FileMetadata.php" class="a1"><i class="fas fa-server"></i> File Metadata</a>
          <a href="RequestDetails.php" class="a1"><i class="fas fa-envelope"></i>Request Details</a>
          <a href="FileUpload.php" class="a1"><i class="bi bi-upload"></i> File Upload</a>            
          <a href="#" onclick="confirmLogout()" class="a1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
      </div>

      <div class="title-with-icon">
        <a href="AdminDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
        <p>Welcome, Admin.</p>
      </div>

      <div class="dashboard-box-container1" style="margin-left: 288px;">
        <div class="dashboard-box1" style="margin-right: 20px; z-index: 0;" id="db1">
            <?php
            include 'connection.php';
            
            $sql = "SELECT COUNT(*) AS totalOfficials FROM official_file";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $countOfficial = $row['totalOfficials'];
            } else {
                $countOfficial = 0;
            }
            $conn->close();
            ?>
          <div class="i" style="background-color: #1E63E9;"><i class="fas fa-landmark"></i></div>
          <div class="box-title7"><p><?php echo $countOfficial; ?></p><h3>TOTAL OFFICIALS FILE</h3></div>
      </div>
        <div class="dashboard-box1" style="margin-right: 20px; z-index: 0; background-color: #fff;" id="db2">
            <?php
            include 'connection.php';
            
            $sql = "SELECT COUNT(*) AS totalResidents FROM resident_file";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $rowCount = $row['totalResidents'];
            } else {
                $rowCount = 0;
            }
            
            $conn->close();
            ?>
            <div class="i" style="background-color: #1E6321;"><i class="fas fa-map-marker-alt"></i></div>
            <div class="box-title7"><p><?php echo $rowCount; ?></p><h3>TOTAL RESIDENTS FILE</h3></div>
        </div>
        <div class="dashboard-box1" style="z-index: 0; background-color: #fff;" id="db3">
            <?php
              include 'connection.php';
                      
              $sql = "SELECT COUNT(*) AS totalmessages FROM form_file ";
              $result = $conn->query($sql);
                      
              if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $countreceivemessages = $row['totalmessages'];
              } else {
                $countreceivemessages = 0;
              }

              $conn->close();
            ?>
            <div class="i" style="background-color:#8B8B00;"><i class="fas fa-file"></i></div>
            <div class="box-title7"><p><?php echo $countreceivemessages; ?></p><h3>TOTAL FORM FILE</h3></div>
        </div>
      </div>
      <div class="dashboard-box-container2" style="margin-left: 288px;">
        <div class="dashboard-box2" style="margin-right: 20px; z-index: 0; background-color: #fff;" id="db4">
            <?php
            include 'connection.php';
            
            $sql = "SELECT COUNT(*) AS totalBlotter FROM document_folder";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $countBlotter = $row['totalBlotter'];
            } else {
                $countBlotter = 0;
            }
            
            $conn->close();
            ?>
            <div class="i" style="background-color:#8B0045;"><i class="fas fa-running"></i></div>
            <div class="box-title7"><p><?php echo $countBlotter; ?></p><h3>TOTAL ACTIVITY FILE</h3></div>
        </div>
        <div class="dashboard-box2" style="margin-right: 20px; z-index: 0; background-color: #fff;" id="db5">
            <?php
            include 'connection.php';
            
            $sql = "SELECT COUNT(*) AS totalUsers FROM profile_file";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $countUsers = $row['totalUsers'];
            } else {
                $countUsers = 0;
            }
            
            $conn->close();
            ?>
            <div class="i" style="background-color:#45008B;"><i class="fas fa-user-tie"></i></div>
            <div class="box-title7"><p><?php echo $countUsers; ?></p><h3>TOTAL PROFILE FILE</h3></div>
        </div>
        <div class="dashboard-box2" style="z-index: 0; background-color: #fff;" id="db6">
            <?php
            include 'connection.php';
            $sql = "SELECT COUNT(*) AS totalActivity FROM request_file";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $countActivity = $row['totalActivity'];
            } else {
                $countActivity = 0;
            }
            
            $conn->close();
            ?>
            <div class="i" style="background-color:#8B4500;"><i class="fas fa-bell"></i></div>
            <div class="box-title7"><p><?php echo $countActivity; ?></p><h3>TOTAL REQUEST FILE</h3></div>
        </div>
      </div>
    </div>

    <div class="dashboard-flex-container" style="margin-left: 288px;">
    <div class="chart-container">
            <canvas id="lineChart" style="height: 500px;"></canvas>
        </div>
        <div class="calendar-container">
    <div id="calendar"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                    window.location.href = 'DocumentDashboard.php?logout=true';
                }
            });
        }

        var ctx = document.getElementById('lineChart').getContext('2d');
var lineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 
                 'July', 'August', 'September', 'October', 'November', 'December'],
        datasets: [{
            label: 'Total Documents',
            data: <?php echo json_encode($totalRows); ?>,
            borderColor: '#1E63A2',
            backgroundColor: 'rgba(30, 99, 162, 0.2)',
            borderWidth: 2,
            pointBackgroundColor: '#1E63A2',
            pointBorderColor: '#1E63A2',
            pointHoverBackgroundColor: '#1E63A2',
            pointHoverBorderColor: '#1E63A2',
            fill: {
                target: 'origin',
                above: 'rgba(30, 99, 162, 0.2)',
            },
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
       
   $(document).ready(function() {
   $('#calendar').fullCalendar();
});

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