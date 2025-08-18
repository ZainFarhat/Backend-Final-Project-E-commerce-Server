<?php

include "connection.php";

$order_id = $_GET['order_id'];

// write an SQL select query to get this order’s info and all its items with product details 
$sql = "SELECT 
    o.id AS order_id,
    o.status,
    o.total_amount,
    p.id AS product_id,
    p.name AS product_name,
    p.price AS product_price,
    oi.quantity AS quantity_ordered
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN products p ON oi.product_id = p.id
WHERE o.id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$order_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    echo json_encode(["message" => "Order not found"]);
    exit();
}

// start with an empty items list , we will fill it below
$order = [
    "order_id" => $rows[0]["order_id"],
    "status" => $rows[0]["status"],
    "total_amount" => $rows[0]["total_amount"],
    "items" => []
];

// go through each row from the results
foreach ($rows as $row) {
    $order["items"][] = [
        "product_id" => $row["product_id"],
        "product_name" => $row["product_name"],
        "product_price" => $row["product_price"],
        "quantity" => $row["quantity_ordered"]
    ];
}
// return the order details 
echo json_encode($order);