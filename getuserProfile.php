<?php

// connect to the database
include("./connection.php");

// get the user id that tells us which profile belongs to the user
$user_id = $_GET["user_id"];

// write an SQL select query to find a user with this id
$sql = "SELECT * FROM users WHERE id = ?";

// prepare the query before adding the value
$stmt = $pdo->prepare($sql);

// run the query with the user id to find the profile
$stmt->execute([$user_id]);

// fetch the first matching row from the query
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// if the user id is not found, return an error message else, return the profile details.
if(!$row){
    echo json_encode([
        "message" => "User not found!",
    ]);
} else {
    echo json_encode($row, true);
}
