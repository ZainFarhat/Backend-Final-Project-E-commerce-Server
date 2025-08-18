<?php

include("../connection.php");

// get the user_id for the cart we are showing
$user_id = $_POST["user_id"];


// write an SQL select query to check if the user exists
$sql = "SELECT id FROM users WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

// fetch the first matching row from the query
$foundUser = $stmt->fetch(PDO::FETCH_ASSOC);

// if the user does not exist, return an error
if(!$foundUser){
    echo json_encode(["message" => "User not found"]);
    exit;
}


// write an SQL select query to get this user's cart items with products and categories info
$sql = "SELECT
            c.id            AS cart_id,
            c.quantity      AS cart_quantity,
            p.id            AS product_id,
            p.name          AS product_name,
            p.description   AS product_description,
            p.price         AS product_price,
            p.category_id,
            cat.name        AS category_name,
            cat.description AS category_description
        FROM carts c
        JOIN products p        ON p.id = c.product_id
        LEFT JOIN categories cat ON cat.id = p.category_id
        WHERE c.user_id = ? AND p.is_active = 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if the cart has no items, return a message and stop
if (!$items) {
    echo json_encode([
        "message" => "Cart is empty",
        "items"   => []
    ]);
    exit;
}

// calculate the cart totals
$totalPrice      = 0;                 
$totalQuantity = 0; 

// go through each cart row one by one
foreach ($items as $item) {
    // calculate the cost for this product (price * quantity)
    $linePrice = (float)$item["product_price"] * (int)$item["cart_quantity"];

    
    $totalPrice += $linePrice;
    $totalQuantity += (int)$item["cart_quantity"];
}


// return the cart items with total details
echo json_encode([
    "items"          => $items,
    "total_quantity" => $totalQuantity,      
    "subtotal"       => $totalPrice       
]);
