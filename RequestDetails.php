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
    <title>Request Details</title>
    <link rel="icon" href="images/folder.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.1.7/sweetalert2.min.css">
    <link rel="stylesheet" href="css/DocumentDashboard.css">
    <style>
        .notification-icon{
            margin-left: 1200px;
        }
     .notification-icon i{
        color: #fff;
        font-size: 26px;
     }

     .barangay {
  padding: 20px;
  margin-top: 190px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
  width: 81%;
  margin-left: 290px;
  border-radius: 10px;
  background-color: #fff;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  border-top: 9px solid #e91e63;
}

.barangay hr{
  width: 100%;
}

.title-with-icon1 {
  display: inline-flex;
  align-items: center; 
  margin-left: 0px;
}

.title-with-icon1 i {
  margin-right: 10px;
  font-size: 20px; 
}

.title-with-icon1 h3 {
  margin-right: 1100px;
}

.title-with-icon1 button {
  margin-left: -20px;
  background-color: #1E9E51;
  border-style: none; 
}

.title-with-icon1 button:hover {
  border-style: none;
  color: #fff;
  background-color: #0e6a38; 
}

.button-container button {
  margin-left: 10px; 
}

.heading-and-buttons {
  display: flex;
  justify-content: space-between;
  width: 100%; 
  margin-left: 270px;
}

.barangay table {
  width: 100%;
  border-collapse: collapse;
}
 
.barangay table th,
.barangay table td {
  padding: 15px;
  border: none; 
}

.barangay table tr {
  border-bottom: 1px solid #ddd; 
}

.table-no-border th {
  text-align: center; 
}
.heading-and-buttons {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.lab{
  margin-bottom: 10px;
}

.show-entries {
  display: flex;
  align-items: center;
  width: 30%;
  height: 20%;
  float: left;
  margin-top: 10px;
}

.show-entries label {
  margin-right: 10px;
  color: #607d8b; 
}

.show-entries input{
  width: 50px;
  text-align: center;
}

.search-bar {
  display: flex;
  align-items: center;
  width: 30%;
  height: 20%;
  float: right;
  margin-top: 10px;
}

.search-bar p {
    margin-right: 10px;
    margin-top: 14px;
    font-size: 16px; 
    color: #607d8b;
}

.search-bar input[type="text"] {
  flex: 1;
  border-radius: 8px;
  border:1px solid #607d8b;
}

.up-and-down{
  margin-left: -7px;
}

.btnedit{
  background-color: #1E63E9;
  border-style: none;
}

.action-buttons .btnedit:hover{
  background-color: #0c44a6;
  border-style: none;
  color: #fff;
}

.navigation-buttons{
  display: flex;
  margin-top: 20px;
}

.navigation-buttons p{
  font-size: 17px;
  color: #607d8b;
  margin-top: 5px;
  margin-left: 0;
}

.navigation-buttons a{
  width: 90px;
  height: 35px;
  text-align: center;
  font-size: 15px;
  border: 1px solid #607d8b;
  color: #607d8b;
  background-color: #fff;
 margin-top: 5px;
 margin-left: -100px;
}

.navigation-buttons a:hover{
  color: #fff;
  border-color: #fff;
}
    </style>
</head>
<body>
    <div>
    <div class="header">
      <div class="notification-icon">
      <i class="fas fa-bell" style="display:none;"></i>
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
          <a href="DocumentDashboard.php" class="a1"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          <a href="DocumentRepository.php" class="a1"><i class="fas fa-folder"></i> File Repository</a>
          <a href="FileMetadata.php" class="a1"><i class="fas fa-server"></i> File Metadata</a>
          <a href="RequestDetails.php" class="a1" id="dashb"><i class="fas fa-envelope"></i>Request Details</a>   
          <a href="FileUpload.php" class="a1"><i class="bi bi-upload"></i> File Upload</a>      
          <a href="#" onclick="confirmLogout()" class="a1"><i class="fas fa-sign-out-alt"></i> Log Out</a>
        </div>
      </div>

      <div class="title-with-icon">
        <a href="DocumentDashboard.php" title="Dashboard"><i class="bi bi-house"></i></a>
        <p>Request Details</p>
      </div>
        
      <?php
include 'connection.php';

$sql = "SELECT * FROM request_file";

if (isset($_GET['query'])) {
    $search_query = $_GET['query'];
    $sql .= " WHERE fullName LIKE '%$search_query%'";
}

$sqlCount = "SELECT COUNT(*) AS totalOfficials FROM request_file";
$resultCount = $conn->query($sqlCount);
$rowCount = $resultCount->fetch_assoc();
$totalOfficialsCount = $rowCount['totalOfficials'];

$limit = 5;
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

$offset = ($currentPage - 1) * $limit;

$sql .= " LIMIT $limit OFFSET $offset";

$resultOfficials = $conn->query($sql);

$officialsCountPerPage = $resultOfficials->num_rows;

$totalPages = ceil($totalOfficialsCount / $limit);

$conn->close();
?>

<div class="barangay" id="barangayOfficialsDashboard">
    <div class="title-with-icon1">
        <h3 style="width:100%; margin-right: 990px;">Requets File Details</h3>
    </div>
    <hr>
    <div class="heading-and-buttons">
        <div class="show-entries">
            <label for="entries">Show Entries: </label>
            <input type="number" title="number" placeholder="0" value="<?php echo $officialsCountPerPage; ?>">
        </div>
        <div class="search-bar">
            <p>Search: </p>
            <input type="text" id="searchInput" onkeyup="searchOfficial()" placeholder="Search for names..." style="padding-left: 10px;">
        </div>
    </div>
    <hr>
    <table class="table-no-border">
        <thead>
            <tr>
                <th>Request Number</th>
                <th>Person Requested</th>
                <th>Request Form</th>
                <th>Request Status </th>
                <th>Created Date </th>
                <th>Release Date </th>
                <th>Process</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($resultOfficials->num_rows > 0) {
                while ($row = $resultOfficials->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$row['request_number']."</td>";
                    echo "<td>".$row['person_requested']."</td>";
                    echo "<td>".$row['request_from']."</td>";
                    echo "<td>".$row['request_status']."</td>";
                    echo "<td>".$row['created_date']."</td>";
                    echo "<td>".$row['release_date']."</td>";
                    echo "<td>".$row['processed_by']."</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div class="navigation-buttons">
        <p style="margin-right: 970px;">Showing <?php echo $officialsCountPerPage; ?> of <?php echo $limit; ?> entries.</p>
        <a style="margin-right: 110px;" href="?page=<?php echo $currentPage > 1 ? $currentPage - 1 : 1; ?>" class="btn <?php echo $currentPage == 1 ? 'btn-secondary disabled' : 'btn-primary'; ?>">Previous</a>
        <a href="?page=<?php echo $currentPage < $totalPages ? $currentPage + 1 : $totalPages; ?>" class="btn <?php echo $currentPage == $totalPages ? 'btn-secondary disabled' : 'btn-primary'; ?>">Next</a>
    </div>
</div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Barangay Paule 1. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
                    window.location.href = 'RequestDetails.php?logout=true';
                }
            });
        }

        function searchOfficial() {
    var input = document.getElementById("searchInput").value.toLowerCase();
    var tableRows = document.getElementsByTagName("tr");
    var filteredRows = 0;

    for (var i = 1; i < tableRows.length; i++) {
      var cells = tableRows[i].getElementsByTagName("td");
      var activityName = cells[0].innerText.toLowerCase();

      if (activityName.indexOf(input) > -1) {
        tableRows[i].style.display = "";
        filteredRows++;
      } else {
        tableRows[i].style.display = "none";
      }
    }
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