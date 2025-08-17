<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

if (isset($_POST['add_product'])) {
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
   $details = filter_var($_POST['details'], FILTER_SANITIZE_STRING);

   $image_01 = filter_var($_FILES['image_01']['name'], FILTER_SANITIZE_STRING);
   $image_size_01 = $_FILES['image_01']['size'];
   $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
   $image_folder_01 = '../uploaded_img/' . $image_01;

   $image_02 = filter_var($_FILES['image_02']['name'], FILTER_SANITIZE_STRING);
   $image_size_02 = $_FILES['image_02']['size'];
   $image_tmp_name_02 = $_FILES['image_02']['tmp_name'];
   $image_folder_02 = '../uploaded_img/' . $image_02;

   $image_03 = filter_var($_FILES['image_03']['name'], FILTER_SANITIZE_STRING);
   $image_size_03 = $_FILES['image_03']['size'];
   $image_tmp_name_03 = $_FILES['image_03']['tmp_name'];
   $image_folder_03 = '../uploaded_img/' . $image_03;

   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
   $select_products->execute([$name]);

   if ($select_products->rowCount() > 0) {
      $message[] = 'Product name already exists!';
   } else {
      $insert_products = $conn->prepare("INSERT INTO `products` (name, details, price, image_01, image_02, image_03) VALUES (?, ?, ?, ?, ?, ?)");
      $insert_products->execute([$name, $details, $price, $image_01, $image_02, $image_03]);

      if ($insert_products) {
         if ($image_size_01 > 2000000 || $image_size_02 > 2000000 || $image_size_03 > 2000000) {
            $message[] = 'Image size is too large!';
         } else {
            move_uploaded_file($image_tmp_name_01, $image_folder_01);
            move_uploaded_file($image_tmp_name_02, $image_folder_02);
            move_uploaded_file($image_tmp_name_03, $image_folder_03);
            $message[] = 'New product added!';
         }
      }
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // Step 1: Delete related ratings
   $conn->prepare("DELETE FROM product_ratings WHERE product_id = ?")->execute([$delete_id]);

   // Step 2: Delete images
   $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $delete_product_image->execute([$delete_id]);
   $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);
   if ($fetch_delete_image) {
      unlink('../uploaded_img/' . $fetch_delete_image['image_01']);
      unlink('../uploaded_img/' . $fetch_delete_image['image_02']);
      unlink('../uploaded_img/' . $fetch_delete_image['image_03']);
   }

   // Step 3: Delete product
   $conn->prepare("DELETE FROM products WHERE id = ?")->execute([$delete_id]);

   // Step 4: Delete related entries from cart and wishlist
   $conn->prepare("DELETE FROM cart WHERE pid = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM wishlist WHERE pid = ?")->execute([$delete_id]);

   header('location:products.php');
   exit;
}

// Search filter logic
$search_query = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
   $search_query = trim($_GET['search']);
   $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ?");
   $select_products->execute(['%' . $search_query . '%']);
} else {
   $select_products = $conn->prepare("SELECT * FROM `products`");
   $select_products->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Manage Products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="../css/admin_style.css" />
   <style>
      .products-search {
         display: flex;
         justify-content: center;
         align-items: center;
         margin: 2rem auto;
         gap: 1rem;
         flex-wrap: wrap;
      }

      .products-search input[type="text"] {
         padding: 1rem 1.5rem;
         font-size: 1.6rem;
         width: 300px;
         border: 1px solid var(--border-color);
         border-radius: .5rem;
         outline: none;
         background-color: var(--white);
         box-shadow: var(--box-shadow);
      }

      .products-search button {
         background-color: var(--main-color);
         color: #fff;
         border: none;
         padding: 1rem 2rem;
         border-radius: .5rem;
         cursor: pointer;
         font-size: 1.6rem;
         transition: 0.3s;
         box-shadow: var(--box-shadow);
         display: flex;
         align-items: center;
         gap: .5rem;
      }

      .products-search button:hover {
         background-color: var(--black);
      }

      .products-search i {
         font-size: 1.6rem;
      }
      
      .alert-message {
   position: fixed;
   top: 1rem;
   right: 1rem;
   background: #4caf50;
   color: white;
   padding: 1rem 2rem;
   border-radius: 0.5rem;
   box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
   z-index: 1000;
   display: flex;
   align-items: center;
   gap: 1rem;
   font-size: 1.6rem;
   transition: opacity 0.5s ease, transform 0.5s ease;
}

.alert-message.hide {
   opacity: 0;
   transform: translateY(-20px);
   pointer-events: none;
}

.alert-message .close-btn {
   cursor: pointer;
   font-size: 2rem;
}

   </style>
   

</head>

<body>

   <?php include '../components/admin_header.php'; ?>

   <section class="form-container">
      <form action="" method="post" enctype="multipart/form-data">
         <h3>Add Product</h3>
         <input type="text" name="name" required placeholder="Enter product name" maxlength="100" class="box" />
         <input type="number" name="price" required placeholder="Enter product price" min="0" max="9999999999" class="box" onkeypress="if(this.value.length == 10) return false;" />
         <textarea name="details" required placeholder="Enter product details" maxlength="500" cols="30" rows="4" class="box"></textarea>
         <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required />
         <input type="file" name="image_02" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required />
         <input type="file" name="image_03" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required />
         <input type="submit" value="Add Product" class="btn" name="add_product" />
      </form>
   </section>

   <section class="show-products">
      <h1 class="heading">Products Added</h1>

      <!-- 🔍 Search Form -->
      <form class="products-search" method="GET" action="">
         <input type="text" name="search" placeholder="Search product name..." value="<?= htmlspecialchars($search_query); ?>">
         <button type="submit"><i class="fas fa-search"></i></button>
      </form>

      <div class="table-container">
         <table class="products-table">
            <thead>
               <tr>
                  <th>SN</th>
                  <th>Image</th>
                  <th>Name</th>
                  <th>Price</th>
                  <th>Details</th>
                  <th>Actions</th>
               </tr>
            </thead>
            <tbody>
               <?php
               $counter = 1;
               if ($select_products->rowCount() > 0) {
                  while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
               ?>
                    <tr>
   <td data-label="SN" style="font-size:18px;"><?= $counter++; ?></td>
   <td data-label="Image" style="font-size:18px;">
      <img src="../uploaded_img/<?= htmlspecialchars($fetch_products['image_01']); ?>" alt="Product Image" />
   </td>
   <td data-label="Name" style="font-size:18px;"><?= htmlspecialchars($fetch_products['name']); ?></td>
   <td data-label="Price" style="font-size:18px;">Nrs. <?= htmlspecialchars($fetch_products['price']); ?>/-</td>
   <td data-label="Details" style="font-size:18px;"><?= htmlspecialchars($fetch_products['details']); ?></td>
   <td data-label="Actions" style="font-size:18px;">
      <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">Update</a>
      <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
   </td>
</tr>

               <?php
                  }
               } else {
                  echo '<tr><td colspan="6" class="empty">No products found!</td></tr>';
               }
               ?>
            </tbody>
         </table>
      </div>
   </section>



   <script src="../js/admin_script.js"></script>
</body>

</html>
