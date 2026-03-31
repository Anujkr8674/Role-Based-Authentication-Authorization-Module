<?php
// Set timezone to India
date_default_timezone_set('Asia/Kolkata');

$host = 'localhost'; 
$db_user = 'root';   
$db_pass = '';       
$db_name = 'project1'; 




// $smtp_email = 'anujnov25@gmail.com';
// $smtp_app_password = 'awlr guxc kkaf wczo';


$smtp_email = 'anujnov25@gmail.com';
$smtp_app_password = 'tvixsgazzcsfwnje'; // paste without spaces


$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?> 