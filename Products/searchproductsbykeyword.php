<?php

include("../connection.php");

// get the keyword from the input
$key_word = $_GET["key_word"];

// write the SQL query to search products by name or description using the keyword
$sql = "SELECT * FROM products 
            WHERE name LIKE '%$key_word%' 
            OR description LIKE '%$key_word%'";

$stmt = $pdo->prepare($sql);

// run the query
$stmt->execute();

// fetch all product rows from the query
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if no products found, return a message , else return the products
if(!$products){
    echo json_encode(["message" => "No results found!!"]);
}
else{
    echo json_encode($products);
}
