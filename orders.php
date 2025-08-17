<?php
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

// Delete order if requested and payment_status is 'pending'
if(isset($_GET['delete']) && $user_id != ''){
   $delete_id = $_GET['delete'];

   // Check if order belongs to user and is pending
   $check_status = $conn->prepare("SELECT * FROM `orders` WHERE id = ? AND user_id = ? AND payment_status = 'pending'");
   $check_status->execute([$delete_id, $user_id]);

   if($check_status->rowCount() > 0){
      $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ? AND user_id = ?");
      $delete_order->execute([$delete_id, $user_id]);
      header('location:orders.php');
      exit;
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Orders</title>
   <link rel="icon" href="images/ideeplogo.png" type="image/png" />
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css" />

   <style>
      .flex-btn {
         display: flex;
         gap: 1rem;
         margin-top: 1rem;
      }
      .option-btn, .delete-btn {
         padding: 0.5rem 1rem;
         border-radius: 0.5rem;
         text-align: center;
         color: white;
         font-size: 1.4rem;
         text-decoration: none;
         user-select: none;
      }
      .option-btn {
         background-color: #2980b9;
      }
      .delete-btn {
         background-color: #e74c3c;
      }
      .delete-btn:hover {
         background-color: #c0392b;
      }
      .option-btn:hover {
         background-color: #1c5980;
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="orders">

   <h1 class="heading">Placed Orders.</h1>

   <div class="box-container">

   <?php
      if($user_id == ''){
         echo '<p class="empty">Please login to see your orders.</p>';
      }else{
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
         $select_orders->execute([$user_id]);
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>Placed on : <span><?= htmlspecialchars($fetch_orders['placed_on']); ?></span></p>
      <p>Name : <span><?= htmlspecialchars($fetch_orders['name']); ?></span></p>
      <p>Email : <span><?= htmlspecialchars($fetch_orders['email']); ?></span></p>
      <p>Phone Number : <span><?= htmlspecialchars($fetch_orders['number']); ?></span></p>
      <p>Address : <span><?= htmlspecialchars($fetch_orders['address']); ?></span></p>
      <p>Payment Method : <span><?= htmlspecialchars($fetch_orders['method']); ?></span></p>
      <p>Your orders : <span><?= htmlspecialchars($fetch_orders['total_products']); ?></span></p>
      <p>Total price : <span>Nrs.<?= htmlspecialchars($fetch_orders['total_price']); ?>/-</span></p>
      <p>Payment status : 
         <span style="color:<?= $fetch_orders['payment_status'] == 'pending' ? 'red' : 'green'; ?>">
            <?= htmlspecialchars($fetch_orders['payment_status']); ?>
         </span> 
      </p>

      <?php if($fetch_orders['payment_status'] == 'pending'){ ?>
       
            <a href="orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to cancel this order?');">Delete</a>
         </div>
      <?php } ?>

   </div>
   <?php
         }
      }else{
         echo '<p class="empty">No orders placed yet!</p>';
      }
   }
   ?>

   </div>

</section>
<hr style="border: none; border-top: 1px solid #ccc; margin: 5px 0;">
<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>
