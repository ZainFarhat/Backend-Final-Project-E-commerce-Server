<?php

include("../connection.php");

// get the values (name and description) for the new category
$name = $_POST["name"];
$description = $_POST["description"];

// write the SQL query to insert a new category (name and description) using placeholders
$sql = "INSERT INTO categories 
            (name, description)
            VALUES (?, ?)";

$stmt = $pdo->prepare($sql);

// run the query for name and description
$stmt->execute([$name, $description]);

// return a success message 
echo json_encode(["message" => "Category created successfully"]);
