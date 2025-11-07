<?php
session_start();
include '../koneksi.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Detail Faktur | Program Faktur Apotek</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {margin:0;background:#f8f9fa;display:flex;height:100vh;overflow:hidden;font-family:"Poppins",sans-serif}
.sidebar {width:220px;background:#0d6efd;color:white;display:flex;flex-direction:column;align-items:center;padding-top:20px;position:fixed;left:0;top:0;bottom:0}
.sidebar a {color:white;text-decoration:none;display:block;padding:10px 15px;width:90%;border-radius:5px}
.sidebar a:hover {background:#0b5ed7}
.main {margin-left:220px;flex:1;display:flex;flex-direction:column}
header {background:white;border-bottom:1px solid #ddd;padding:15px 25px;display:flex;justify-content:space-between;align-items:center}
header h2 {margin:0;font-size:20px;font-weight:600}
.content {flex-grow:1;padding:25px;overflow-y:auto}
footer {background:white;border-top:1px solid #ddd;padding:10px;text-align:center;font-size:14px;color:#666}
.table thead {background:#0d6efd;color:white}
</style>
</head>
<body>

<div class="sidebar">
    <h4>🧾 Faktur</h4>
    <a href="../dashboard.php">🏠 Dashboard</a>
    <a href="../master/perusahaan.php">🏢 Perusahaan</a>
    <a href="../master/customer.php">👥 Customer</a>
    <a href="../master/produk.php">💊 Produk</a>
    <hr style="width:90%;border-color:white">
    <a href="faktur.php">📄 Faktur</a>
    <a href="detail_faktur.php" style="background:#0b5ed7;">📋 Detail Faktur</a>
    <hr style="width:90%;border-color:white">
    <a href="../logout.php" class="text-warning">🚪 Logout</a>
</div>

<div class="main">
<header>
    <h2>Detail Faktur</h2>
    <span>👤 <?php echo $_SESSION['username']; ?></span>
</header>

<div class="content">
    <div class="d-flex justify-content-between mb-3">
        <h4>Daftar Faktur</h4>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Perusahaan</th>
                <th>Produk</th>
                <th>Qty</th>
                <th>Harga</th>
                <th>PPN (%)</th>
                <th>DP</th>
                <th>Grand Total</th>
                <th>Cetak</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = $koneksi->query("
                SELECT f.no_faktur, f.due_date, f.metode_bayar, f.ppn, f.dp, f.grand_total,
                       c.nama_customer, p.nama_perusahaan, pr.nama_produk, df.Qty, df.Price
                FROM faktur f
                LEFT JOIN customer c ON f.id_customer = c.id_customer
                LEFT JOIN perusahaan p ON f.id_perusahaan = p.id_perusahaan
                LEFT JOIN detail_faktur df ON f.no_faktur = df.no_faktur
                LEFT JOIN produk pr ON df.id_produk = pr.id_produk
                ORDER BY f.no_faktur DESC
            ");
            if ($query->num_rows > 0) {
                while ($d = $query->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>{$d['no_faktur']}</td>
                        <td>{$d['due_date']}</td>
                        <td>{$d['nama_customer']}</td>
                        <td>{$d['nama_perusahaan']}</td>
                        <td>{$d['nama_produk']}</td>
                        <td>{$d['Qty']}</td>
                        <td>Rp " . number_format($d['Price'], 0, ',', '.') . "</td>
                        <td>{$d['ppn']}</td>
                        <td>Rp " . number_format($d['dp'], 0, ',', '.') . "</td>
                        <td>Rp " . number_format($d['grand_total'], 0, ',', '.') . "</td>
                        <td>
                            <a href='cetak_faktur.php?no_faktur={$d['no_faktur']}' target='_blank' class='btn btn-sm btn-success'>🖨 Cetak PDF</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='11' class='text-center'>Belum ada data faktur</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<footer>
    © 2025 Program Faktur Apotek | Dibuat oleh Zax Desain 💙
</footer>
</div>
</body>
</html>
