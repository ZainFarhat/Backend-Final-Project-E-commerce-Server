<?php

// connect to the database
include("../connection.php");

// get the values that the user sent 
$full_name = $_POST["full_name"];
$email = $_POST["email"];
$password = $_POST["password"];
$is_admin = $_POST["is_admin"];
$addr_line1 = $_POST["addr_line1"];
$addr_city = $_POST["addr_city"];
$addr_country = $_POST["addr_country"];
$phone = $_POST["phone"];

// write the SQL insert query with placeholders
$sql = "INSERT INTO users 
            (email, password_hash, full_name, is_admin, addr_line1, addr_city, addr_country, phone)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

// prepare the query before adding the values
$stmt = $pdo->prepare($sql);

// run the query in the same order as the placeholders
$stmt->execute([
    $email,
    $password,
    $full_name,
    $is_admin,
    $addr_line1,
    $addr_city,
    $addr_country,
    $phone
]);

// return a success message 
echo json_encode(["message" => "User registered successfully"]);