<?php

// connect to the database
include("../connection.php");

// get the category id from the URL
$category_id = $_GET["category_id"];

// write the SQL select query to find products by category id
$sql = "SELECT * FROM products WHERE category_id = ?";

$stmt = $pdo->prepare($sql);

// run the query with the category id
$stmt->execute([$category_id]);

// fetch all product rows that match the category id
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if no products found, return an error message else, return the products
if(!$products){
    echo json_encode([
        "message" => "No products found in this category"
    ]);
} else {
    echo json_encode($products);
}