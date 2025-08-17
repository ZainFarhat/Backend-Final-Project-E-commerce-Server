<?php

include("../connection.php");

// write the SQL select query to get all categories
$sql = "SELECT * FROM categories";

$stmt = $pdo->prepare($sql);
$stmt->execute();

// fetch all categories from the query
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// return all categories 
echo json_encode($categories);