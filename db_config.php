<?php
$servername = "localhost";
$username = "rahul2002";  // Change if different
$password = "rahul@project3000";  // Enter your actual MySQL password
$dbname = "college";  // Ensure it matches your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
