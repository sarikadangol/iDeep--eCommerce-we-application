<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_admins = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
   $delete_admins->execute([$delete_id]);
   header('location:admin_accounts.php');
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
   <title>Admin Accounts</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="../css/admin_style.css" />
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="accounts">

   <h1 class="heading">Admin Accounts</h1>

   <div class="search-form">
      <form method="GET" action="admin_accounts.php" novalidate>
         <input 
            type="text" 
            name="search" 
            placeholder="Search admin name..." 
            value="<?= htmlspecialchars($search) ?>" 
            autocomplete="off"
         />
         <button type="submit"><i class="fas fa-search"></i></button>
      </form>
   </div>

   <div class="table-container">
      <table class="admin-table">
         <thead>
            <tr>
               <th>Id</th>
               <th>Name</th>
               <th>Action</th>
            </tr>
         </thead>
         <tbody>
            <?php
            if ($search !== '') {
               $search_sql = "%$search%";
               $select_accounts = $conn->prepare("SELECT * FROM `admins` WHERE name LIKE ?");
               $select_accounts->execute([$search_sql]);
            } else {
               $select_accounts = $conn->prepare("SELECT * FROM `admins`");
               $select_accounts->execute();
            }

            if ($select_accounts->rowCount() > 0) {
               while ($fetch = $select_accounts->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <tr>
               <td data-label="Id"><?= $fetch['id']; ?></td>
               <td data-label="Name"><?= $fetch['name']; ?></td>
               <td data-label="Action">
                  <a href="admin_accounts.php?delete=<?= $fetch['id']; ?>" onclick="return confirm('Delete this account?')" class="delete-btn">Delete</a>
                  <?php if ($fetch['id'] == $admin_id): ?>
                     <a href="update_profile.php" class="option-btn">Update</a>
                  <?php endif; ?>
               </td>
            </tr>
            <?php
               }
            } else {
               echo '<tr><td colspan="3" class="empty">No admin accounts found!</td></tr>';
            }
            ?>
         </tbody>
      </table>
   </div>

</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
