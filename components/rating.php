<?php
include 'components/connect.php';

session_start();

// Read incoming JSON
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['rating'], $data['product_id'], $data['user_id'])) {
    $rating = (int)$data['rating'];
    $product_id = (int)$data['product_id'];
    $user_id = (int)$data['user_id'];

    // Check if user already rated this product
    $check_rating = $conn->prepare("SELECT * FROM product_ratings WHERE product_id = ? AND user_id = ?");
    $check_rating->execute([$product_id, $user_id]);

    if ($check_rating->rowCount() > 0) {
        // Update existing rating
        $update_rating = $conn->prepare("UPDATE product_ratings SET rating = ? WHERE product_id = ? AND user_id = ?");
        $update_rating->execute([$rating, $product_id, $user_id]);
        echo "Rating updated.";
    } else {
        // Insert new rating
        $insert_rating = $conn->prepare("INSERT INTO product_ratings (product_id, user_id, rating) VALUES (?, ?, ?)");
        $insert_rating->execute([$product_id, $user_id, $rating]);
        echo "Rating submitted.";
    }
} else {
    echo "Invalid data.";
}
?>
