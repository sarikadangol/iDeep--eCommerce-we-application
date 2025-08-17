<?php

include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

// Delete user if 'delete' is triggered
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // First delete ratings, messages, orders if foreign key constraints exist (optional)
   $conn->prepare("DELETE FROM product_ratings WHERE user_id = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM messages WHERE user_id = ?")->execute([$delete_id]);

   // Then delete the user
   $delete_users = $conn->prepare("DELETE FROM users WHERE id = ?");
   $delete_users->execute([$delete_id]);

   header('location:users_accounts.php');
   exit;
}

$search = '';
if (isset($_GET['search'])) {
   $search = trim($_GET['search']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Users</title>
   <link rel="icon" href="images/ideeplogo.png" type="image/png" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="../css/admin_style.css" />
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contacts">

   <h2 class="heading">Users Details</h2>

   <div class="search-form">
      <form method="GET" action="users_accounts.php" novalidate>
         <input 
            type="text" 
            name="search" 
            placeholder="Search users by name, email, or number" 
            value="<?= htmlspecialchars($search) ?>"
            autocomplete="off"
         />
         <button type="submit"><i class="fas fa-search"></i></button>
      </form>
   </div>

   <?php
   if ($search !== '') {
      $search_sql = "%$search%";
      $select_users = $conn->prepare("SELECT * FROM `users` WHERE name LIKE ? OR email LIKE ? OR number LIKE ?");
      $select_users->execute([$search_sql, $search_sql, $search_sql]);
   } else {
      $select_users = $conn->prepare("SELECT * FROM `users`");
      $select_users->execute();
   }

   if ($select_users->rowCount() > 0) {
   ?>

   <table class="users-table">
      <thead>
         <tr>
            <th>User Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Action</th>
         </tr>
      </thead>
      <tbody>
         <?php while ($fetch_user = $select_users->fetch(PDO::FETCH_ASSOC)) { ?>
         <tr>
            <td data-label="User Id"><?= htmlspecialchars($fetch_user['id']); ?></td>
            <td data-label="Name"><?= htmlspecialchars($fetch_user['name']); ?></td>
            <td data-label="Email"><?= htmlspecialchars($fetch_user['email']); ?></td>
            <td data-label="Action">
               <a href="users_accounts.php?delete=<?= $fetch_user['id']; ?>" onclick="return confirm('Delete this user?');" class="delete-btn">Delete</a>
            </td>
         </tr>
         <?php } ?>
      </tbody>
   </table>

   <?php } else { ?>
      <p class="empty">No users found</p>
   <?php } ?>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
