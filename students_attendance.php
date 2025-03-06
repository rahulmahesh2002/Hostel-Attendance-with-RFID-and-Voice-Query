<?php
header("Content-Type: application/json");
require 'db_config.php'; // Ensure this file exists and is correctly configured

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if uid and status are provided
    if (!isset($_POST["uid"]) || !isset($_POST["status"])) {
        echo json_encode(["error" => "Missing parameters"]);
        exit;
    }

    $uid = trim($_POST["uid"]);
    $status = trim($_POST["status"]);
    
    // Debugging output
    error_log("POST Request Received: uid = $uid, status = $status");

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("UPDATE students SET status = ?, timestamp = NOW() WHERE uid = ?");
    $stmt->bind_param("ss", $status, $uid);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Attendance updated successfully"]);
    } else {
        echo json_encode(["error" => "Database update failed"]);
    }

    $stmt->close();
    $conn->close();

} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if uid is provided
    if (!isset($_GET["uid"])) {
        echo json_encode(["error" => "Missing UID parameter"]);
        exit;
    }

    $uid = trim($_GET["uid"]);
    
    // Debugging output
    error_log("GET Request Received: uid = $uid");

    // Fetch student record
    $stmt = $conn->prepare("SELECT * FROM students WHERE uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(["error" => "Student not found"]);
    }

    $stmt->close();
    $conn->close();

} else {
    echo json_encode(["error" => "Invalid request method"]);
}
?>
