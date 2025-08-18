<?php

// connect to the database
include("../connection.php");

// get the product id from the URL
$product_id = $_GET["product_id"];

// write the SQL to get the product and its category info
$sql = "SELECT
          p.id, p.name, p.description, p.price, p.stock_qty, p.category_id,
          c.name AS category_name,
          c.description AS category_description
        FROM products p
        JOIN categories c ON c.id = p.category_id
        WHERE p.id = ?";

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
