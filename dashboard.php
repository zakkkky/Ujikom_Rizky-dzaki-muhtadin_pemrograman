<?php
session_start();
include 'koneksi.php'; // pastikan koneksi database benar

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil jumlah data ril dari database
$jmlPerusahaan = $koneksi->query("SELECT COUNT(*) as total FROM perusahaan")->fetch_assoc()['total'];
$jmlCustomer   = $koneksi->query("SELECT COUNT(*) as total FROM customer")->fetch_assoc()['total'];
$jmlProduk     = $koneksi->query("SELECT COUNT(*) as total FROM produk")->fetch_assoc()['total'];
$jmlFaktur     = $koneksi->query("SELECT COUNT(*) as total FROM faktur")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard | Program Faktur Apotek</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    * { font-family: "Poppins", sans-serif; }
    body {margin:0;background:#f8f9fa;display:flex;height:100vh;overflow:hidden;}
    .sidebar {width:220px;background:#0d6efd;color:white;display:flex;flex-direction:column;align-items:center;padding-top:20px;position:fixed;left:0;top:0;bottom:0;}
    .sidebar h4 {font-weight:600;margin-bottom:30px;}
    .sidebar a {color:white;text-decoration:none;display:block;padding:10px 15px;width:90%;border-radius:5px;text-align:left;}
    .sidebar a:hover {background:#0b5ed7;}
    .main {margin-left:220px;flex:1;display:flex;flex-direction:column;}
    header {background:white;border-bottom:1px solid #ddd;padding:15px 25px;display:flex;justify-content:space-between;align-items:center;}
    header h2 {margin:0;font-size:20px;font-weight:600;}
    .content {flex-grow:1;padding:30px;overflow-y:auto;}
    .dashboard-cards {display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;}
    .card {border-radius:12px;text-align:center;padding:25px;background:white;box-shadow:0 2px 6px rgba(0,0,0,0.1);transition:all 0.3s ease;}
    .card:hover {transform:translateY(-5px);}
    .card h5 {margin-bottom:10px;}
    footer {background:white;border-top:1px solid #ddd;padding:10px;text-align:center;font-size:14px;color:#666;}
</style>
</head>
<body>

<div class="sidebar">
    <h4>🧾 Faktur</h4>
    <a href="dashboard.php" style="background:#0b5ed7;">🏠 Dashboard</a>
    <a href="master/perusahaan.php">🏢 Perusahaan</a>
    <a href="master/customer.php">👥 Customer</a>
    <a href="master/produk.php">💊 Produk</a>
    <hr style="width:90%;border-color:white;">
    <a href="transaksi/faktur.php">📄 Faktur</a>
    <a href="transaksi/detail_faktur.php">📋 Detail Faktur</a>
    <hr style="width:90%;border-color:white;">
    <a href="logout.php" class="text-warning">🚪 Logout</a>
</div>

<div class="main">
<header>
    <h2>Dashboard Program Faktur Apotek</h2>
    <div>
        <span class="text-secondary">👤 <?php echo $_SESSION['username']; ?></span>
    </div>
</header>

<div class="content">
    <h4 class="mb-4">Selamat Datang di Sistem Faktur Apotek 👋</h4>
    <div class="dashboard-cards">
        <div class="card">
            <h5>🏢 Data Perusahaan</h5>
            <p class="display-6 fw-bold text-primary"><?php echo $jmlPerusahaan; ?></p>
        </div>
        <div class="card">
            <h5>👥 Data Customer</h5>
            <p class="display-6 fw-bold text-success"><?php echo $jmlCustomer; ?></p>
        </div>
        <div class="card">
            <h5>💊 Produk</h5>
            <p class="display-6 fw-bold text-info"><?php echo $jmlProduk; ?></p>
        </div>
        <div class="card">
            <h5>📄 Faktur</h5>
            <p class="display-6 fw-bold text-danger"><?php echo $jmlFaktur; ?></p>
        </div>
    </div>
</div>

<footer>
    © 2025 Program Faktur Apotek | Dibuat oleh Zax Desain 💙
</footer>
</div>

</body>
</html>
