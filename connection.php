<?php
    $conn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');

    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }
?>