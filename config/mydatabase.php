<?php
$conn = new mysqli("localhost", "root", "", "Bank");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
