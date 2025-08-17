<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? '';
if (!$admin_id) {
    header('location:admin_login.php');
    exit;
}

// Fetch admin name
$profile_stmt = $conn->prepare("SELECT name FROM admins WHERE id = ?");
$profile_stmt->execute([$admin_id]);
$fetch_profile = $profile_stmt->fetch(PDO::FETCH_ASSOC);

// Calculate stats
$total_pendings = 0;
$pendings_stmt = $conn->prepare("SELECT total_price FROM orders WHERE payment_status = ?");
$pendings_stmt->execute(['pending']);
while ($row = $pendings_stmt->fetch(PDO::FETCH_ASSOC)) {
    $total_pendings += $row['total_price'];
}

$total_completes = 0;
$completed_stmt = $conn->prepare("SELECT total_price FROM orders WHERE payment_status = ?");
$completed_stmt->execute(['completed']);
while ($row = $completed_stmt->fetch(PDO::FETCH_ASSOC)) {
    $total_completes += $row['total_price'];
}

$number_of_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$number_of_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$number_of_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
$number_of_admins = $conn->query("SELECT COUNT(*) FROM admins")->fetchColumn();
$number_of_messages = $conn->query("SELECT COUNT(*) FROM messages")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard</title>
<link rel="icon" href="images/ideeplogo.png" type="image/png" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
<link rel="stylesheet" href="../css/admin_style.css" />
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<button id="chart-toggle-btn">📊 Show Chart</button>

<div id="chart-container">
  <h2>Dashboard Status</h2>
  <canvas id="dashboardChart"></canvas>
</div>

<section class="dashboard">
  <h1 class="heading">Dashboard</h1>

  <div class="box-container">
    <div class="box">
      <h3>Welcome!</h3>
      <p><?= htmlspecialchars($fetch_profile['name'] ?? 'Admin'); ?></p>
      <a href="update_profile.php" class="btn">Update Profile</a>
    </div>

    <div class="box">
      <h3><span>Nrs.</span><?= number_format($total_pendings); ?><span>/-</span></h3>
      <p>Total Pendings</p>
      <a href="placed_orders.php" class="btn">See Orders</a>
    </div>

    <div class="box">
      <h3><span>Nrs.</span><?= number_format($total_completes); ?><span>/-</span></h3>
      <p>Completed Orders</p>
      <a href="placed_orders.php" class="btn">See Orders</a>
    </div>

    <div class="box">
      <h3><?= $number_of_orders ?></h3>
      <p>Orders Placed</p>
      <a href="placed_orders.php" class="btn">See Orders</a>
    </div>

    <div class="box">
      <h3><?= $number_of_products ?></h3>
      <p>Products Added</p>
      <a href="products.php" class="btn">See Products</a>
    </div>

    <div class="box">
      <h3><?= $number_of_users ?></h3>
      <p>Normal Users</p>
      <a href="users_accounts.php" class="btn">See Users</a>
    </div>

    <div class="box">
      <h3><?= $number_of_admins ?></h3>
      <p>Admin Users</p>
      <a href="admin_accounts.php" class="btn">See Admins</a>
    </div>

    <div class="box">
      <h3><?= $number_of_messages ?></h3>
      <p>New Messages</p>
      <a href="messages.php" class="btn">See Messages</a>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const chartToggleBtn = document.getElementById('chart-toggle-btn');
  const chartContainer = document.getElementById('chart-container');

  chartToggleBtn.addEventListener('click', () => {
    chartContainer.classList.toggle('open');
    chartToggleBtn.textContent = chartContainer.classList.contains('open') ? '❌ Close Chart' : '📊 Show Chart';
  });

  const ctx = document.getElementById('dashboardChart').getContext('2d');
  const dashboardChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Pendings', 'Completed', 'Orders', 'Products', 'Users', 'Admins', 'Messages'],
      datasets: [{
        label: 'Count / Amount',
        data: [
          <?= (int)$total_pendings ?>,
          <?= (int)$total_completes ?>,
          <?= (int)$number_of_orders ?>,
          <?= (int)$number_of_products ?>,
          <?= (int)$number_of_users ?>,
          <?= (int)$number_of_admins ?>,
          <?= (int)$number_of_messages ?>
        ],
        backgroundColor: [
          '#e74c3c', '#27ae60', '#2980b9', '#f39c12', '#8e44ad', '#2c3e50', '#c0392b'
        ],
        borderRadius: 6,
        barPercentage: 0.7
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          ticks: { font: { size: 16 } }
        },
        y: {
          beginAtZero: true,
          ticks: {
            font: { size: 16 },
            callback: function(value) {
              return value >= 1000 ? value.toLocaleString() : value;
            }
          }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: { bodyFont: { size: 16 } }
      }
    }
  });
</script>

<script src="../js/admin_script.js"></script>

<!-- Quick inline style fix if not added in external CSS -->
<style>
  #chart-toggle-btn {
    margin: 20px auto;
    display: block;
    padding: 10px 20px;
    font-size: 16px;
    background-color: var(--main-color);
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  #chart-toggle-btn:hover {
    background-color: #3d61c4;
  }

  #chart-container {
    max-width: 1000px;
    height: 400px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 0 15px rgba(0,0,0,0.1);
    display: none;
    transition: all 0.4s ease-in-out;
  }

  #chart-container.open {
    display: block;
  }

  #chart-container canvas {
    width: 100% !important;
    height: 100% !important;
  }
</style>

</body>
</html>