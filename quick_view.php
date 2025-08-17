
<?php

include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
   session_start();
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
   <title>Quick view</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
   <style>
      .recommendation {
         background-color: #f8f8f8;
         padding: 1rem;
         border-radius: 10px;
         margin-top: 2rem;
      }
      .recommend-boxes {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
         gap: 1rem;
      }
      .recommend-box {
         background: #fff;
         padding: 1rem;
         border: 1px solid #ccc;
         border-radius: 8px;
         text-align: center;
      }
      .recommend-box img {
         max-width: 100%;
         height: 200px;
         object-fit: contain;
         border-radius: 8px;
         margin-bottom: 0.5rem;
      }
   </style>
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="quick-view">

   <h1 class="heading">Quick view</h1>

   <?php
     $pid = $_GET['pid'];
     $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?"); 
     $select_products->execute([$pid]);
     if($select_products->rowCount() > 0){
      while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" method="post" class="box">
      <input type="hidden" name="pid" value="<?= $fetch_product['id']; ?>">
      <input type="hidden" name="name" value="<?= $fetch_product['name']; ?>">
      <input type="hidden" name="price" value="<?= $fetch_product['price']; ?>">
      <input type="hidden" name="image" value="<?= $fetch_product['image_01']; ?>">
      <div class="row">
         <div class="image-container">
            <div class="main-image">
               <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
            </div>
            <div class="sub-image">
               <img src="uploaded_img/<?= $fetch_product['image_01']; ?>" alt="">
               <img src="uploaded_img/<?= $fetch_product['image_02']; ?>" alt="">
               <img src="uploaded_img/<?= $fetch_product['image_03']; ?>" alt="">
            </div>
         </div>
         <div class="content">
            <div class="name"><?= $fetch_product['name']; ?></div>
            <div class="flex">
               <div class="price"><span>Nrs.</span><?= $fetch_product['price']; ?><span>/-</span></div>
               <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
            </div>
            <div class="details"><?= $fetch_product['details']; ?></div>
            <div class="flex-btn">
               <input type="submit" value="add to cart" class="btn" name="add_to_cart">
               <input class="option-btn" type="submit" name="add_to_wishlist" value="add to wishlist">
            </div>
         </div>
      </div>
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">no products added yet!</p>';
   }
   ?>

</section>

<!-- Include recommendation block -->
<section class="recommendation">
  <h2 style="font-weight: bold; color: black; font-size: 20px;">Recommended Products for you</h2>
  <div class="recommend-boxes">
    <?php include 'recommend.php'; ?>
  
</div>
  </div>
</section>
<hr style="border: none; border-top: 1px solid #ccc; margin: 5px 0;">
<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
