<?php
session_start();
include '../koneksi.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Hapus customer
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM customer WHERE id_customer='$id'");
    header("Location: customer.php");
    exit;
}

// Ambil daftar perusahaan untuk dropdown
$perusahaanData = $koneksi->query("SELECT id_perusahaan, nama_perusahaan FROM perusahaan");

// Tambah / Ubah customer
$alert = '';
if (isset($_POST['simpan'])) {
    $id = $_POST['id_customer'];
    $nama = $_POST['nama_customer'];
    $id_perusahaan = $_POST['id_perusahaan'];
    $alamat = $_POST['alamat'];

    if ($id_perusahaan == '') {
        $alert = "Pilih perusahaan terlebih dahulu!";
    } else {
        if ($id == '') {
            $res = $koneksi->query("INSERT INTO customer (nama_customer, id_perusahaan, alamat) VALUES ('$nama','$id_perusahaan','$alamat')");
            if (!$res) $alert = "Error insert: " . $koneksi->error;
            else { header("Location: customer.php"); exit; }
        } else {
            $res = $koneksi->query("UPDATE customer SET nama_customer='$nama', id_perusahaan='$id_perusahaan', alamat='$alamat' WHERE id_customer='$id'");
            if (!$res) $alert = "Error update: " . $koneksi->error;
            else { header("Location: customer.php"); exit; }
        }
    }
}

// Ambil data untuk edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = $koneksi->query("SELECT * FROM customer WHERE id_customer='$id'")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Customer | Program Faktur Apotek</title>
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
    <a href="perusahaan.php">🏢 Perusahaan</a>
    <a href="customer.php" style="background:#0b5ed7;">👥 Customer</a>
    <a href="produk.php">💊 Produk</a>
    <hr style="width:90%;border-color:white">
    <a href="../transaksi/faktur.php">📄 Faktur</a>
    <a href="../transaksi/detail_faktur.php">📋 Detail Faktur</a>
    <hr style="width:90%;border-color:white">
    <a href="../logout.php" class="text-warning">🚪 Logout</a>
</div>

<div class="main">
<header>
    <h2>Data Customer</h2>
    <span>👤 <?php echo $_SESSION['username']; ?></span>
</header>

<div class="content">
    <?php if($alert != ''): ?>
        <div class="alert alert-danger"><?php echo $alert; ?></div>
    <?php endif; ?>

    <h4 class="mb-3">Daftar Customer</h4>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Customer</th>
                <th>Perusahaan</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $data = $koneksi->query("
                SELECT c.*, p.nama_perusahaan 
                FROM customer c 
                LEFT JOIN perusahaan p ON c.id_perusahaan = p.id_perusahaan
                ORDER BY c.id_customer ASC
            ");
            if ($data->num_rows > 0) {
                while ($d = $data->fetch_assoc()) {
                    $nama_perusahaan = $d['nama_perusahaan'] ?? '-';
                    echo "
                    <tr>
                        <td>{$d['id_customer']}</td>
                        <td>{$d['nama_customer']}</td>
                        <td>{$nama_perusahaan}</td>
                        <td>{$d['alamat']}</td>
                        <td>
                            <a href='?edit={$d['id_customer']}' class='btn btn-sm btn-warning'>Edit</a>
                            <a href='?hapus={$d['id_customer']}' onclick='return confirm(\"Yakin hapus customer ini?\")' class='btn btn-sm btn-danger'>Hapus</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Belum ada data customer</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <hr>
    <h4><?php echo $editData ? "Ubah" : "Tambah"; ?> Customer</h4>
    <form method="post">
        <input type="hidden" name="id_customer" value="<?php echo $editData['id_customer'] ?? ''; ?>">
        <div class="mb-3">
            <label>Nama Customer</label>
            <input type="text" name="nama_customer" class="form-control" required value="<?php echo $editData['nama_customer'] ?? ''; ?>">
        </div>
        <div class="mb-3">
            <label>Perusahaan</label>
            <select name="id_perusahaan" class="form-select" required>
                <option value="">-- Pilih Perusahaan --</option>
                <?php
                $perusahaanData->data_seek(0);
                while($p = $perusahaanData->fetch_assoc()) {
                    $selected = ($editData && $editData['id_perusahaan'] == $p['id_perusahaan']) ? 'selected' : '';
                    echo "<option value='{$p['id_perusahaan']}' $selected>{$p['nama_perusahaan']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Alamat</label>
            <input type="text" name="alamat" class="form-control" required value="<?php echo $editData['alamat'] ?? ''; ?>">
        </div>
        <button type="submit" name="simpan" class="btn btn-primary"><?php echo $editData ? "Update" : "Tambah"; ?> Customer</button>
        <?php if($editData): ?>
            <a href="customer.php" class="btn btn-secondary">Batal</a>
        <?php endif; ?>
    </form>

</div>

<footer>
    © 2025 Program Faktur Apotek | Dibuat oleh Zax Desain 💙
</footer>
</div>

</body>
</html>
