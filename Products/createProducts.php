<?php

include("../connection.php");

// get the values (name, description, price, stock, category id, and user id) for the new product
$name = $_POST['name'];
$description = $_POST['description'];
$price = $_POST['price'];
$stock_qty = $_POST['stock_qty'];
$category_id = $_POST['category_id'];
$user_id = $_POST['user_id'];

// write the SQL query to check if the user is an admin
$sql = "SELECT is_admin FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// run the insert query only if the user is admin
if($admin && $admin['is_admin']){
        $sql = "INSERT INTO products (name, description, price, stock_qty, category_id)
    VALUES (?,?,?,?,?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $description, $price, $stock_qty, $category_id]);

        echo json_encode(["message" => "Product created successfully"]);
    }

// if the user exists but is not admin, return a message
else if($admin && !$admin['is_admin']){
    echo json_encode(["message" => "User cannot create product"]);
}
// if the user does not exist, return an error message
else{
    echo json_encode(["message" => "User doesn't exist"]);
}