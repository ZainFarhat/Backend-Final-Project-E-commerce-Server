<?php

// connect to the database
include("../connection.php");

// write the SQL select query to get all products
$sql = "SELECT * FROM products";

$stmt = $pdo->prepare($sql);

// run the query
$stmt->execute();

// fetch all product rows from the query
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// return all products
echo json_encode($products);