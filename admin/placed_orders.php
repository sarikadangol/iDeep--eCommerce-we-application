<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit;
}

// DELETE ORDER
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
   $delete_order->execute([$delete_id]);
   header('location:placed_orders.php');
   exit;
}

// UPDATE PAYMENT STATUS
if (isset($_POST['update_payment'])) {
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $update_order = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
   $update_order->execute([$payment_status, $order_id]);
   $message[] = 'Payment status updated!';
}

// SEARCH
$search = '';
if (isset($_GET['search'])) {
   $search = trim($_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Placed Orders</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="stylesheet" href="../css/admin_style.css" />
   <style>
      body {
         margin: 0;
         padding: 0;
         background-color: var(--dark-bg);
      }

      .heading {
         text-align: center;
         font-size: 2.5rem;
         margin-bottom: 2rem;
         color: var(--white);
      }

      .search-form {
         text-align: center;
         margin-bottom: 2rem;
      }

      .search-form input[type="text"] {
         padding: 0.8rem 1.2rem;
         font-size: 1.5rem;
         border-radius: 0.4rem;
         border: var(--border);
         width: 300px;
      }

      .search-form button {
         background: #4a64e4;
         color: white;
         border: none;
         padding: 0.8rem 1.2rem;
         border-radius: 0.4rem;
         cursor: pointer;
      }

      .search-form button:hover {
         background: #3648b4;
      }

      .table-container {
         width: 100%;
         max-width: 100%;
         overflow-x: auto;
         margin: 0 auto;
         padding: 1rem 2rem;
      }

      .orders-table {
         width: 100%;
         min-width: 1100px;
         border-collapse: collapse;
         border-radius: 0.5rem;
         box-shadow: var(--box-shadow);
         background-color: var(--light-bg);
         font-size: 1.2rem;
         table-layout: fixed;
      }

      .orders-table thead {
         background: linear-gradient(145deg, #4a64e4, #6b8aff);
         color: var(--white);
         font-weight: 400;
      }

      .orders-table thead tr th {
         padding: 1.2rem 1.5rem;
         border-right: var(--border);
         text-align: left;
         white-space: nowrap;
         font-size: 1.6rem;
      }
/* Column width distribution - perfectly adjusted */
.orders-table thead tr th:nth-child(1) { width: 1.1%; }   /* User ID */
.orders-table thead tr th:nth-child(2) { width: 1.5%; }  /* Placed On */
.orders-table thead tr th:nth-child(3) { width: 1%; }  /* Name */
.orders-table thead tr th:nth-child(4) { width: 1.3%; }  /* Email */
.orders-table thead tr th:nth-child(5) { width: 1.6%; }  /* Number */
.orders-table thead tr th:nth-child(6) { width: 2%; }  /* Address */
.orders-table thead tr th:nth-child(7) { width: 2%; }   /* Total Products */
.orders-table thead tr th:nth-child(8) { width: 1.5%; }   /* Total Price */
.orders-table thead tr th:nth-child(9) { width: 1.2%; }   /* Method */
.orders-table thead tr th:nth-child(10) { width: 2%; }  /* Payment Status */
.orders-table thead tr th:nth-child(11) { width: 1.4%; }  /* Actions */

      .orders-table tbody tr {
         border-bottom: var(--border);
         background-color: rgba(255, 255, 255, 0.02);
      }

      .orders-table tbody tr:nth-child(even) {
         background-color: rgba(255, 255, 255, 0.05);
      }

      .orders-table tbody tr:hover {
         background-color: rgba(94, 127, 255, 0.15);
      }

      .orders-table tbody tr td {
         padding: 1.2rem 1.5rem;
         color: var(--white);
         vertical-align: middle;
         border-right: var(--border);
         white-space: normal;
         word-wrap: break-word;
         overflow-wrap: break-word;
         hyphens: auto;
      }

      .orders-table tbody tr td:last-child {
         border-right: none;
      }

      .table-form {
         display: flex;
         flex-direction: column;
         gap: 0.5rem;
      }

     .table-form .select {
   padding: 0.4rem 2.8rem 0.4rem 0.8rem; /* right space for arrow */
   font-size: 1.4rem;
   border-radius: 0.4rem;
   background-color: #fff;
   color: #222;
   border: 1px solid var(--border);
   appearance: none;
   -webkit-appearance: none;
   -moz-appearance: none;
   background-image: url("data:image/svg+xml;utf8,<svg fill='black' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
   background-repeat: no-repeat;
   background-position: right 0.8rem center;
   background-size: 1.2rem;
   cursor: pointer;
}

.table-form .select option {
   background-color: #fff;
   color: #222;
}

.table-form .select:focus {
   outline: 2px solid var(--blue);
}


      .option-btn, .delete-btn {
         padding: 0.5rem 1rem;
         font-size: 1.4rem;
         border-radius: 0.4rem;
         cursor: pointer;
         transition: 0.3s ease;
      }

      .option-btn {
         background-color: #3498db;
         color: white;
         border: none;
      }

      .option-btn:hover {
         background-color: #2980b9;
      }

      .delete-btn {
         background-color: #e74c3c;
         color: white;
         text-decoration: none;
         display: inline-block;
         text-align: center;
      }

      .delete-btn:hover {
         background-color: #c0392b;
      }

      @media (max-width: 1200px) {
         .orders-table {
            font-size: 1.3rem;
         }

         .table-form {
            flex-direction: column;
         }
      }
   </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="contacts">

   <h2 class="heading">Placed Orders</h2>

   <div class="search-form">
      <form method="GET" action="placed_orders.php">
         <input 
            type="text" 
            name="search" 
            placeholder="Search by name, email, or number..." 
            value="<?= htmlspecialchars($search) ?>" 
            autocomplete="off"
         />
         <button type="submit"><i class="fas fa-search"></i></button>
      </form>
   </div>

   <div class="table-container">
      <table class="orders-table">
         <thead>
            <tr>
               <th>User ID</th>
               <th>Placed On</th>
               <th>Name</th>
               <th>Email</th>
               <th>Number</th>
               <th>Address</th>
               <th>Total Products</th>
               <th>Total Price</th>
               <th>Method</th>
               <th>Payment Status</th>
               <th>Actions</th>
            </tr>
         </thead>
         <tbody>
         <?php
            $query = "SELECT * FROM `orders`";
            if (!empty($search)) {
               $query .= " WHERE name LIKE ? OR email LIKE ? OR number LIKE ?";
               $stmt = $conn->prepare($query);
               $search_term = "%$search%";
               $stmt->execute([$search_term, $search_term, $search_term]);
            } else {
               $stmt = $conn->prepare($query);
               $stmt->execute();
            }

            if ($stmt->rowCount() > 0) {
               while ($fetch_orders = $stmt->fetch(PDO::FETCH_ASSOC)) {
         ?>
         <tr>
            <td><?= $fetch_orders['user_id']; ?></td>
            <td><?= $fetch_orders['placed_on']; ?></td>
            <td><?= $fetch_orders['name']; ?></td>
            <td><?= $fetch_orders['email']; ?></td>
            <td><?= $fetch_orders['number']; ?></td>
            <td><?= $fetch_orders['address']; ?></td>
            <td><?= $fetch_orders['total_products']; ?></td>
            <td>Nrs.<?= $fetch_orders['total_price']; ?>/-</td>
            <td><?= $fetch_orders['method']; ?></td>
            <td>
               <form method="post" class="table-form">
                  <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                  <select name="payment_status" class="select">
                     <option selected disabled><?= $fetch_orders['payment_status']; ?></option>
                     <option value="pending">Pending</option>
                     <option value="completed">Completed</option>
                  </select>
            </td>
            <td>
               <div class="flex-btn">
                  <input type="submit" value="Update" class="option-btn" name="update_payment">
                   </div>
                   <div class="flex-btn">
                  <a href="placed_orders.php?delete=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('Delete this order?');">Delete</a>
               </div>
               </form>
            </td>
         </tr>
         <?php
               }
            } else {
               echo '<tr><td colspan="11" style="text-align:center; color:white;">No orders found!</td></tr>';
            }
         ?>
         </tbody>
      </table>
   </div>
</section>

</body>
</html>
