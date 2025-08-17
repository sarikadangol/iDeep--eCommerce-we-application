<?php

include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

// Handle deletion of message
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:messages.php');
   exit;
}

// Handle search filter
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
   <title>Messages</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="../css/admin_style.css" />
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contacts">

   <h2 class="heading">Messages</h2>

   <div class="search-form">
      <form method="GET" action="messages.php" novalidate>
         <input 
            type="text" 
            name="search" 
            placeholder="Search messages by name, email, number, or message" 
            value="<?= htmlspecialchars($search) ?>"
            autocomplete="off"
         />
         <button type="submit"><i class="fas fa-search"></i></button>
      </form>
   </div>

   <?php
 if($search !== ''){
   $search_sql = "%$search%";
   $select_messages = $conn->prepare("SELECT * FROM `messages` WHERE name LIKE ? OR email LIKE ? OR number LIKE ? OR message LIKE ?");
   $select_messages->execute([$search_sql, $search_sql, $search_sql, $search_sql]);
} else {
   $select_messages = $conn->prepare("SELECT * FROM `messages`");
   $select_messages->execute();
}


      if($select_messages->rowCount() > 0){
   ?>

   <table class="messages-table">
      <thead>
         <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Number</th>
            <th>Message</th>
            <th>Action</th>
         </tr>
      </thead>
      <tbody>
         <?php while($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)){ ?>
         <tr>
            <td data-label="User ID"><?= htmlspecialchars($fetch_message['user_id']); ?></td>
            <td data-label="Name"><?= htmlspecialchars($fetch_message['name']); ?></td>
            <td data-label="Email"><?= htmlspecialchars($fetch_message['email']); ?></td>
            <td data-label="Number"><?= htmlspecialchars($fetch_message['number']); ?></td>
            <td data-label="Message"><?= htmlspecialchars($fetch_message['message']); ?></td>
            <td data-label="Action">
               <a href="messages.php?delete=<?= $fetch_message['id']; ?>" onclick="return confirm('Delete this message?');" class="delete-btn">Delete</a>
            </td>
         </tr>
         <?php } ?>
      </tbody>
   </table>

   <?php } else { ?>
      <p class="empty">No messages found</p>
   <?php } ?>

</section>

<script src="../js/admin_script.js"></script>

</body>
</html>
