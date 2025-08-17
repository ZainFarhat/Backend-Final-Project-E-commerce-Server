<?php

// connect to the database
include("../connection.php");

// get the product id from the URL
$product_id = $_GET["product_id"];

// write the SQL select query to find a product by id
$sql = "SELECT * FROM products WHERE id = ?";

$stmt = $pdo->prepare($sql);

// run the query with the product id
$stmt->execute([$product_id]);

// fetch the first matching product row from the query
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// if the product is not found, return an error message else, retern the product details
if(!$product){
    echo json_encode([
        "message" => "Product not found!"
    ]);
} else {
    echo json_encode($product);
}
