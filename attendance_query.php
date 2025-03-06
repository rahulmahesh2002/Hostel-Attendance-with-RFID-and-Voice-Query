<?php
include 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['name'])) {
        $name = $_GET['name'];

        // Get attendance status
        $stmt = $conn->prepare("SELECT status FROM students WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->bind_result($status);
        $stmt->fetch();

        if ($status) {
            echo "The user $name is $status.";
        } else {
            echo "User not found.";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Missing name parameter.";
    }
} else {
    echo "Invalid request.";
}
?>

