<?php

// connect to the database
include("../connection.php");

// get user_id, product_id, and the sent quantity, if it wasn't sent, use 1
$user_id    = $_POST["user_id"];
$product_id = $_POST["product_id"];
$quantity   = $_POST["quantity"] ?? 1;


// write an SQL select query to check if the user exists
$sql = "SELECT id FROM users WHERE id = ?";

$stmt = $pdo->prepare($sql);

// run the query with the user id
$stmt->execute([$user_id]);

// fetch the first matching row from the query
$foundUser = $stmt->fetch(PDO::FETCH_ASSOC);

// if we can’t find this user, return a message and stop
if(!$foundUser){
    echo json_encode(["message" => "User not found"]);
    exit;
}


// write an SQL select query to check if this product is already in the cart
$sql = "SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ?";

$stmt = $pdo->prepare($sql);

// run the query with user_id and product_id
$stmt->execute([$user_id, $product_id]);

// fetch the first matching row from the query
$foundCartItem = $stmt->fetch(PDO::FETCH_ASSOC);

// if the item is not in the cart, return a message and stop
if(!$foundCartItem){
    echo json_encode(["message" => "Item not in cart"]);
    exit;
}


// get how many of this product are already in the cart
$cartQuantity = (int)$foundCartItem["quantity"];

// calculate how many should remain after removing
$remainingQuantity = $cartQuantity - (int)$quantity;


// if there is remaining quantity , update it
if($remainingQuantity > 0){
    // write the SQL query to update the quantity
    $updateSql = "UPDATE carts SET quantity = ? WHERE id = ?";

    
    $stmt = $pdo->prepare($updateSql);
    $stmt->execute([$remainingQuantity, (int)$foundCartItem["id"]]);

    // return a success message with the remaining quantity
    echo json_encode([
        "message"    => "Cart updated",
        "product_id" => (int)$product_id,
        "quantity"   => $remainingQuantity
    ]);
    exit;
}

// Otherwise,write an SQL delete query to remove this item from the cart
$deleteSql = "DELETE FROM carts WHERE id = ?";

$stmt = $pdo->prepare($deleteSql);
$stmt->execute([(int)$foundCartItem["id"]]);

echo json_encode([
    "message"    => "Item removed from cart",
    "product_id" => (int)$product_id
]);
exit;
