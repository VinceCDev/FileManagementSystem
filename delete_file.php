<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['file_path'])) {
    $file_path = $_POST['file_path'];

    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            $sql = "DELETE FROM file_upload WHERE file_name = ?";
            $stmt = $conn->prepare($sql);
            $file_name = basename($file_path);
            $stmt->bind_param("s", $file_name);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete file from server.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'File does not exist.']);
    }

    $conn->close();
}
?>
