<?php
echo '<link rel="stylesheet" href="css/style.css">';
?>


<?php

include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? '';

if (!$user_id) {
    echo '<p class="empty">Please login to see recommendations.</p>';
    return;
}

// Fetch user ratings
$stmt = $conn->prepare("SELECT product_id, rating FROM product_ratings WHERE user_id = ?");
$stmt->execute([$user_id]);
$user_ratings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

if (empty($user_ratings)) {
    echo '<p class="empty">Rate some products to get recommendations.</p>';
    return;
}

// Fetch all ratings
$stmt = $conn->prepare("SELECT user_id, product_id, rating FROM product_ratings");
$stmt->execute();
$all_ratings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build user-item rating matrix
$matrix = [];
foreach ($all_ratings as $row) {
    $matrix[$row['user_id']][$row['product_id']] = $row['rating'];
}

function cosineSimilarity($vec1, $vec2) {
    $dot = 0; $len1 = 0; $len2 = 0;
    foreach ($vec1 as $key => $val) {
        if (isset($vec2[$key])) $dot += $val * $vec2[$key];
        $len1 += $val ** 2;
    }
    foreach ($vec2 as $val) $len2 += $val ** 2;
    if ($len1 == 0 || $len2 == 0) return 0;
    return $dot / (sqrt($len1) * sqrt($len2));
}

// Predict ratings for products user hasn't rated yet
$predicted = [];

foreach ($matrix as $uid => $userRatings) {
    foreach ($userRatings as $pid => $rating) {
        if (isset($user_ratings[$pid])) continue; // already rated by current user
        if (!isset($predicted[$pid])) $predicted[$pid] = 0;

        $simSum = 0;
        $weightedSum = 0;

        foreach ($user_ratings as $rated_pid => $rated_val) {
            // Build vectors for similarity
            $vec1 = []; $vec2 = [];
            foreach ($matrix as $other_uid => $ratings) {
                if (isset($ratings[$pid]) && isset($ratings[$rated_pid])) {
                    $vec1[] = $ratings[$pid];
                    $vec2[] = $ratings[$rated_pid];
                }
            }

            if (count($vec1) > 0 && count($vec2) > 0) {
                $sim = cosineSimilarity($vec1, $vec2);

                // ✅ Apply similarity threshold
                if ($sim >= 0.5) {
                    $simSum += $sim;
                    $weightedSum += $sim * $rated_val;
                }
            }
        }

        if ($simSum > 0) {
            $predicted[$pid] = $weightedSum / $simSum;
        }
    }
}

// Sort and get top 4 recommendations
arsort($predicted);
$top_ids = array_slice(array_keys($predicted), 0, 4);

if (empty($top_ids)) {
    echo '<p class="empty" style="font-weight: bold; color: black; font-size: 20px;">No recommendations available yet.</p>';
    return;
}

// Fetch product details
$placeholders = implode(',', array_fill(0, count($top_ids), '?'));
$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($top_ids);
$recommended = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($recommended as $product): ?>
<form action="" method="post" class="recommend-box">
    <input type="hidden" name="pid" value="<?= $product['id']; ?>">
    <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']); ?>">
    <input type="hidden" name="price" value="<?= $product['price']; ?>">
    <input type="hidden" name="image" value="<?= $product['image_01']; ?>">

    <div class="recommend-image">
        <img src="uploaded_img/<?= $product['image_01']; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
    </div>
    <div class="recommend-details">
        <h3 class="recommend-name" style="font-weight: bold; color: black; font-size: 20px;"><?= htmlspecialchars($product['name']); ?></h3>
        <p class="recommend-price" style="font-weight: bold; color: black; font-size: 20px;">
            Nrs. <?= number_format($product['price']); ?> /–
        </p>
        <div class="recommend-buttons" style="display: flex; justify-content: space-between; gap: 10px;">
    <input type="submit" value="Add to Cart" class="btn" name="add_to_cart" style="padding: 5px 10px; font-size: 14px;">
    <input type="submit" value="Wishlist" class="option-btn" name="add_to_wishlist" style="padding: 5px 10px; font-size: 14px;">
</div>

    </div>
</form>
<?php endforeach; ?>

<?php
// 🆕 Fetch 4 most recent products the user hasn't rated or been recommended
$excluded_ids = array_merge(array_keys($user_ratings), array_column($recommended, 'id'));

if (!empty($excluded_ids)) {
    $placeholders = implode(',', array_fill(0, count($excluded_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id NOT IN ($placeholders) ORDER BY id DESC LIMIT 4");
    $stmt->execute($excluded_ids);
} else {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC LIMIT 4");
    $stmt->execute();
}

$recent_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (!empty($recent_products)): ?>
    <!-- <h2 class="section-title" style="margin-top: 30px; font-weight: bold; margin-left:100px">New Arrivals</h2> -->
    <?php foreach ($recent_products as $product): ?>
        <form action="" method="post" class="recommend-box">
            <input type="hidden" name="pid" value="<?= $product['id']; ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']); ?>">
            <input type="hidden" name="price" value="<?= $product['price']; ?>">
            <input type="hidden" name="image" value="<?= $product['image_01']; ?>">

            <div class="recommend-image">
                <img src="uploaded_img/<?= $product['image_01']; ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                <span class="badge-new" style  ="color: black">New</span>
            </div>
            <div class="recommend-details">
                <h3 class="recommend-name" style="font-weight: bold; color: black; font-size: 20px;"><?= htmlspecialchars($product['name']); ?></h3>
                <p class="recommend-price" style="font-weight: bold; color: black; font-size: 20px;">
                    Nrs. <?= number_format($product['price']); ?> /–
                </p>
                <div class="recommend-buttons" style="display: flex; justify-content: space-between; gap: 10px;">
    <input type="submit" value="Add to Cart" class="btn" name="add_to_cart" style="padding: 5px 10px; font-size: 14px;">
    <input type="submit" value="Wishlist" class="option-btn" name="add_to_wishlist" style="padding: 5px 10px; font-size: 14px;">
</div>

            </div>
        </form>
    <?php endforeach; ?>
<?php endif; ?>