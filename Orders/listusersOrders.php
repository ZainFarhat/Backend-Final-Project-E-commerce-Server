<?php

// connect to the database
include("../connection.php");

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


// write an SQL select query to list orders with item_count and total_amount
$sql = "SELECT *
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.id DESC";

$stmt = $pdo->prepare($sql);

// run the query with the user id
$stmt->execute([$user_id]);

// fetch all matching rows from the query
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if there are no orders, return an empty list
if(!$orders){
    echo json_encode([
        "message" => "No orders found",
        "orders"  => []
    ]);
    exit;
}

// return the list of orders
echo json_encode(["orders"  => $orders]);
