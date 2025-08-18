<?php

include("../connection.php");

// get the user id placing the order
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

// write an SQL select query to join carts with products so we can read each product's price
$sql = "SELECT 
            c.product_id,
            c.quantity,
            p.price AS product_price
        FROM carts c
        JOIN products p ON p.id = c.product_id
        WHERE c.user_id = ?";


$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);

// fetch all cart rows from the query
$carts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if the cart is empty, return a message and stop
if (!$carts) {
    echo json_encode(["message" => "Cart is empty"]);
    exit;
}

$totalPrice = 0;

// go through each cart row one by one
foreach ($carts as $cart) {
    
    $linePrice = (float)$cart["product_price"] * (int)$cart["quantity"];

    $totalPrice += $linePrice;
}

// write the SQL insert query with placeholders 
$sql = "INSERT INTO orders (user_id, `status`, total_amount) VALUES (?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([(int)$user_id, "PENDING", $totalPrice]);

// get the new order id
$orderId = (int)$pdo->lastInsertId();

// write an SQL insert query for order_items
$sql = "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)";

// prepare the query before adding the values
$insertItemStmt = $pdo->prepare($sql);

// go through each cart row one by one
foreach ($carts as $cart) {
    $insertItemStmt->execute([
        $orderId,
        (int)$cart["product_id"],
        (int)$cart["quantity"]
    ]);
}

// write an SQL delete query to clear the cart
$sql = "DELETE FROM carts WHERE user_id = ?";

$stmt = $pdo->prepare($sql);

// run the delete with the user id
$stmt->execute([(int)$user_id]);

// return a success message with the order id and total amount
echo json_encode([
    "message"      => "Order Placed",
    "order_id"     => $orderId,
    "total_amount" => $totalPrice
]);
