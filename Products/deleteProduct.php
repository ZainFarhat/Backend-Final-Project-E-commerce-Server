<?php

// connect to the database
include("../connection.php");

// get product_id (to delete) and user_id (for admin check)
$product_id = $_POST["product_id"];
$user_id    = $_POST["user_id"];

// write an SQL select query to check if the user is an admin
$sql = "SELECT is_admin FROM users WHERE id = ?";

$stmt = $pdo->prepare($sql);

// run the query with the user id
$stmt->execute([$user_id]);

// fetch the first matching row from the query
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// if the user exists and is admin, soft-delete the product
if ($admin && $admin['is_admin']) {
    // write an SQL update query for soft delete (only if currently active)
    $sql = "UPDATE products 
            SET is_active = 0
            WHERE id = ? AND is_active = 1";

    $stmt = $pdo->prepare($sql);

    // run the update query with the product id
    $stmt->execute([$product_id]);

    // get how many products were soft-deleted (set to inactive)
    $softDeletedCount = $stmt->rowCount();

    // if the product was active and is now inactive, return success
    if ($softDeletedCount > 0) {
        echo json_encode(["message" => "Product deleted successfully"]);
    } else {
        // write an SQL select query to check if the product exists
        $sql = "SELECT id, is_active FROM products WHERE id = ?";

        // prepare the query before adding the value
        $stmt = $pdo->prepare($sql);

        // run the query with the product id
        $stmt->execute([$product_id]);

        // fetch the first matching row from the query
        $foundProduct = $stmt->fetch(PDO::FETCH_ASSOC);

        // if found but already inactive, say already deleted; else say not found
        if ($foundProduct) {
            echo json_encode(["message" => "Product already deleted"]);
        } else {
            echo json_encode(["message" => "Product not found"]);
        }
    }
}
// if the user exists but is not admin, return a message
else if ($admin && !$admin['is_admin']) {
    echo json_encode(["message" => "User cannot delete product"]);
}
// if the user does not exist, return an error message
else {
    echo json_encode(["message" => "User not found"]);
}
