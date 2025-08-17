<?php

include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

// Fetch logged-in user's ratings for all products (if logged in)
$user_ratings = [];
if ($user_id) {
    $stmt = $conn->prepare("SELECT product_id, rating FROM product_ratings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $user_ratings[$row['product_id']] = (int)$row['rating'];
    }
}


if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/wishlist_cart.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shop</title>
   <link rel="icon" href="images/ideeplogo.png" type="image/png" />
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="products">

   <h1 class="heading">Latest Products.</h1>

   <div class="box-container">


      <?php
         $select_products = $conn->prepare("SELECT * FROM `products` LIMIT 20");
         $select_products->execute();

         if ($select_products->rowCount() > 0) {
            while ($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)) {
               $product_id = $fetch_product['id'];

               // Get average rating
               $select_ratings = $conn->prepare("SELECT AVG(rating) AS average_rating FROM `product_ratings` WHERE `product_id` = :product_id");
               $select_ratings->execute(['product_id' => $product_id]);
               $fetch_rating = $select_ratings->fetch(PDO::FETCH_ASSOC);
               $average_rating = ($fetch_rating['average_rating'] > 0) ? round($fetch_rating['average_rating'], 1) : null;
      ?>

   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <button class="fas fa-heart" type="submit" name="add_to_wishlist"></button>
      <a href="quick_view.php?pid=<?= $fetch_product['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
     <div class="name" style="color: white; text-align: center; font-weight: bold;">
    <?= htmlspecialchars($fetch_product['name']); ?></div>
     <div class="ratings-wrapper">
    <div class="ratings" data-productid="<?= $fetch_product['id']; ?>">
        <span data-ratings="1" style="font-size: 25px;">&#9733;</span>
        <span data-ratings="2" style="font-size: 25px;">&#9733;</span>
        <span data-ratings="3" style="font-size: 25px;">&#9733;</span>
        <span data-ratings="4" style="font-size: 25px;">&#9733;</span>
        <span data-ratings="5" style="font-size: 25px;">&#9733;</span>
    </div>
</div>
      <?php if ($average_rating !== null): ?>
        <div class="average-rating" style="font-size: 13px;">Avg rating: <?= $average_rating; ?> / 5</div>

         <?php endif; ?>

         <div class="flex">
            <div class="price"><span>Nrs.</span><?= $fetch_product['price']; ?><span>/-</span></div>
            <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1" />
         </div>

   <div style="display: flex; justify-content: space-between; align-items: center; padding: 0 9px; gap: 10px;">
   <input type="submit" value="add to cart" class="btn" name="add_to_cart" style="padding: 9px 8px; font-size: 18px;">
   <input class="option-btn" type="submit" name="add_to_wishlist" value="add to wishlist" style="padding: 9px 8px; font-size: 18px; margin-right: 10px;">
</div>

   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products found!</p>';
   }
   ?>

   </div>

</section>


<!-- Rating Thank You Dialog -->
<div id="rating-dialog" style="
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    border: 2px solid #4CAF50;
    padding: 20px 30px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    border-radius: 10px;
    z-index: 1000;
    text-align: center;
    color: black; /* <-- This ensures all text inside is black */
">
    <p style="margin: 0; font-weight: bold; color:black">Thank you for rating!</p>
    <button onclick="document.getElementById('rating-dialog').style.display='none'" style="
        background: #4CAF50;
        color: black;
        border: none;
        padding: 8px 16px;
        margin-top: 10px;
        border-radius: 5px;
        cursor: pointer;
    ">OK</button>
</div>


<hr style="border: none; border-top: 1px solid #ccc; margin: 5px 0;">
<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const userId = <?= $user_id !== '' ? $user_id : 'null'; ?>;
    const userRatings = <?= json_encode($user_ratings); ?>;

    // Highlight stars function
    function highlightStars(container, rating) {
        const stars = container.querySelectorAll('span');
        stars.forEach(star => {
            star.style.color = parseInt(star.dataset.ratings) <= rating ? '#ffc107' : '#ccc';
        });
    }

    document.querySelectorAll('.ratings').forEach(ratingEl => {
        const productId = ratingEl.dataset.productid;

        // Fetch and highlight average rating (optional)
        fetch(`get_rating.php?product_id=${productId}`)
            .then(res => res.json())
            .then(data => {
                if (data.rating !== null) {
                    // Optionally highlight average rating in gray or leave it
                }
            });

        // Highlight logged-in user's own rating (from DB)
        if (userId !== null && userRatings[productId]) {
            highlightStars(ratingEl, userRatings[productId]);
        }

        // Allow user to rate if logged in
        if (userId !== null) {
            ratingEl.querySelectorAll('span').forEach(star => {
                star.style.cursor = 'pointer';
                star.addEventListener('click', () => {
                    const selectedRating = parseInt(star.dataset.ratings);

                    // Highlight visually immediately
                    highlightStars(ratingEl, selectedRating);

                    // Update userRatings in JS memory
                    userRatings[productId] = selectedRating;

                    // Show thank you dialog
                    document.getElementById('rating-dialog').style.display = 'block';

                    // Send rating to server to save in DB
                    fetch('rating.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            product_id: productId,
                            user_id: userId,
                            rating: selectedRating
                        })
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log('Rating saved:', data);
                    })
                    .catch(err => console.error('Error saving rating:', err));
                });
            });
        }
    });
});
</script>


</body>
</html>