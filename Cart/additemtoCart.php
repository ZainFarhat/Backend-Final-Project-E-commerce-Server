<?php

// connect to the database
include("../connection.php");

// get user_id, product_id, and the sent quantity,if it wasn't sent, use 1
$user_id    = $_POST["user_id"];
$product_id = $_POST["product_id"];
$quantity = $_POST['quantity'] ?? 1; 


// write an SQL select query to check if the user exists
$sql = "SELECT id FROM users WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

// fetch the first matching row from the query
$foundUser = $stmt->fetch(PDO::FETCH_ASSOC);

// if we can’t find this user , return a message and stop
if(!$foundUser){
    echo json_encode(["message" => "User not found"]);
    exit;
}

// write an SQL select query to get product status and stock
$sql = "SELECT id, is_active, stock_qty FROM products WHERE id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$product_id]);

// fetch the first matching row from the query
$foundProduct = $stmt->fetch(PDO::FETCH_ASSOC);

// if the product is not found or is inactive, stop
if(!$foundProduct || !$foundProduct["is_active"]){
    echo json_encode(["message" => "Product unavailable"]);
    exit;
}

// write an SQL select query to check if this product is already in the cart
$sql = "SELECT id, quantity FROM carts WHERE user_id = ? AND product_id = ?";

$stmt = $pdo->prepare($sql);

// run the query with user_id and product_id
$stmt->execute([$user_id, $product_id]);

// fetch the first matching row from the query
$foundCartItem = $stmt->fetch(PDO::FETCH_ASSOC);

// check if the item is in the cart; if not, treat it as 0
$cartQuantity = $foundCartItem ? (int)$foundCartItem["quantity"] : 0;

// calculate the new total quantity after adding
$updatedQuantity = $cartQuantity + (int)$quantity;

// get the available stock for this product
$availableStock = (int)$foundProduct["stock_qty"];


// if not enough stock, stop
if ($updatedQuantity > $availableStock) {
    echo json_encode(["message" => "Not enough stock"]);
    exit;
}

// the item is already in the cart
if ($foundCartItem) {
    
    $cartItemId = (int)$foundCartItem['id'];

    // write the SQL query to update the quantity
    $updateSql = "UPDATE carts SET quantity = ? WHERE id = ?";

    
    $stmt = $pdo->prepare($updateSql);
    $stmt->execute([$updatedQuantity, $cartItemId]);

    // retrun a success message with the final quantity
    echo json_encode([
        "message"    => "Cart updated",
        "product_id" => (int)$product_id,
        "quantity"   => $updatedQuantity
    ]);
    exit;
    // the item is not in the cart yet
} else {
    // write an SQL insert query to add this product to the user's cart
    $insertSql = "INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?)";

    $stmt = $pdo->prepare($insertSql);

    $stmt->execute([(int)$user_id, (int)$product_id, (int)$quantity]);

    // return a success message with the added quantity
    echo json_encode([
        "message"    => "Item added to cart",
        "product_id" => (int)$product_id,
        "quantity"   => (int)$quantity
    ]);
    exit; 
}
