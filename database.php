<?php
// Database configuration
$host = 'localhost';
$db_name = 'blog';
$username = 'root'; // Change this to your database username
$password = ''; // Change this to your database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    echo "Connection error: " . $exception->getMessage();
}
?>