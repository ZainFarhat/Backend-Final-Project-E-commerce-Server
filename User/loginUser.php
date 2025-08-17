<?php

// connect to the database
include("../connection.php");

// get the email and password that the user sent 
$email = $_POST["email"];
$password = $_POST["password"];

// write an SQL select query to find a user with this email and password
$sql = "SELECT * FROM users WHERE email = ? AND password_hash = ?";

// prepare the query before adding the values
$stmt = $pdo->prepare($sql);

// run the query for email and password
$stmt->execute([$email, $password]);

// fetch the first matching row from the query
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// if the login details are wrong,return an error message else,return the account details.
if (!$row) {
    echo json_encode([
        "message" => "Wrong email or password!",
    ]);
} else {
    echo json_encode($row);
}
