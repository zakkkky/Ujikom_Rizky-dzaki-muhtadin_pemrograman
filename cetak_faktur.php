<?php
include '../koneksi.php';

if (!isset($_GET['no_faktur'])) {
    echo "Nomor faktur tidak ditemukan.";
    exit;
}

$no_faktur = $_GET['no_faktur'];

// Ambil data faktur
$queryFaktur = $koneksi->prepare("
    SELECT f.*, c.nama_customer, c.alamat, p.nama_perusahaan 
    FROM faktur f
    LEFT JOIN customer c ON f.id_customer = c.id_customer
    LEFT JOIN perusahaan p ON f.id_perusahaan = p.id_perusahaan
    WHERE f.no_faktur = ?
");
$queryFaktur->bind_param("i", $no_faktur);
$queryFaktur->execute();
$dataFaktur = $queryFaktur->get_result()->fetch_assoc();

if (!$dataFaktur) {
    echo "Data faktur tidak ditemukan.";
    exit;
}

// Ambil detail produk
$queryDetail = $koneksi->prepare("
    SELECT df.*, pr.nama_produk, pr.Price AS harga_produk 
    FROM detail_faktur df
    LEFT JOIN produk pr ON df.id_produk = pr.id_produk
    WHERE df.no_faktur = ?
");
$queryDetail->bind_param("i", $no_faktur);
$queryDetail->execute();
$resultDetail = $queryDetail->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Faktur #<?= htmlspecialchars($dataFaktur['no_faktur']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { margin: 40px; font-size: 14px; }
h2 { text-align: center; margin-bottom: 30px; }
.table th, .table td { vertical-align: middle; }
.no-border { border: none !important; }
@media print { .no-print { display: none; } }
</style>
</head>
<body>

<h2>Faktur Penjualan</h2>

<div class="mb-4">
    <table class="table table-borderless">
        <tr>
            <td><strong>No Faktur:</strong> <?= htmlspecialchars($dataFaktur['no_faktur']) ?></td>
            <td><strong>Tanggal Jatuh Tempo:</strong> <?= htmlspecialchars($dataFaktur['due_date']) ?></td>
        </tr>
        <tr>
            <td><strong>Customer:</strong> <?= htmlspecialchars($dataFaktur['nama_customer']) ?></td>
            <td><strong>Perusahaan:</strong> <?= htmlspecialchars($dataFaktur['nama_perusahaan']) ?></td>
        </tr>
        <tr>
            <td><strong>Metode Bayar:</strong> <?= htmlspecialchars($dataFaktur['metode_bayar']) ?></td>
            <td><strong>User Input:</strong> <?= htmlspecialchars($dataFaktur['user']) ?></td>
        </tr>
    </table>
</div>

<table class="table table-bordered">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>Produk</th>
            <th>Harga Satuan</th>
            <th>Qty</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $total = 0;
        while ($row = $resultDetail->fetch_assoc()):
            $subtotal = $row['harga_produk'] * $row['Qty'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
            <td>Rp <?= number_format($row['harga_produk'], 0, ',', '.') ?></td>
            <td><?= $row['Qty'] ?></td>
            <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-end">PPN (<?= $dataFaktur['ppn'] ?: 0 ?>%)</th>
            <th>
                <?php
                $ppnValue = $total * ($dataFaktur['ppn'] / 100);
                echo 'Rp ' . number_format($ppnValue, 0, ',', '.');
                ?>
            </th>
        </tr>
        <tr>
            <th colspan="4" class="text-end">DP</th>
            <th>Rp <?= number_format($dataFaktur['dp'], 0, ',', '.') ?></th>
        </tr>
        <tr>
            <th colspan="4" class="text-end">Grand Total</th>
            <th>Rp <?= number_format($total + $ppnValue - $dataFaktur['dp'], 0, ',', '.') ?></th>
        </tr>
    </tfoot>
</table>

<div class="text-center no-print mt-4">
    <button onclick="window.print()" class="btn btn-primary">🖨️ Cetak / Simpan PDF</button>
    <a href="detail_faktur.php" class="btn btn-secondary">⬅️ Kembali</a>
</div>

</body>
</html>
