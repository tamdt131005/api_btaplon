<?php
$host= 'localhost';
$username= 'root';
$password= '';
$database = 'btaplon';
$conn = mysqli_connect($host, $username, $password, $database);
mysqli_set_charset($conn, 'utf8');
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

?>