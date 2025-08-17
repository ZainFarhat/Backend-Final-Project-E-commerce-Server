<?php

include("../connection.php");

// get the values for updating the product
$product_id = $_POST['product_id'];
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

// run the update query only if the user is admin
if($admin && $admin['is_admin']){
    $sql = "UPDATE products 
            SET name = ?, description = ?, price = ?, stock_qty = ?, category_id = ?
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $description, $price, $stock_qty, $category_id, $product_id]);

    // return a success message
    echo json_encode(["message" => "Product updated successfully"]);
}

// if the user exists but is not admin, return a message
else if($admin && !$admin['is_admin']){
    echo json_encode(["message" => "User cannot update product"]);
}

// if the user does not exist, return an error message
else{
    echo json_encode(["message" => "User not found"]);
}
