<?php
session_start();
include '../koneksi.php';
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

// Hapus produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM produk WHERE id_produk='$id'");
    header("Location: produk.php");
    exit;
}

// Tambah / Ubah produk
$alert = '';
if (isset($_POST['simpan'])) {
    $id = $_POST['id_produk'];
    $nama = $_POST['nama_produk'];
    $price = $_POST['price'];
    $jenis = $_POST['jenis'];
    $stock = $_POST['stock'];

    if ($id == '') {
        $result = $koneksi->query("INSERT INTO produk (nama_produk, Price, Jenis, stock) VALUES ('$nama','$price','$jenis','$stock')");
        if (!$result) $alert = "Error insert: ".$koneksi->error;
        else { header("Location: produk.php"); exit; }
    } else {
        $result = $koneksi->query("UPDATE produk SET nama_produk='$nama', Price='$price', Jenis='$jenis', stock='$stock' WHERE id_produk='$id'");
        if (!$result) $alert = "Error update: ".$koneksi->error;
        else { header("Location: produk.php"); exit; }
    }
}

// Ambil data untuk edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editData = $koneksi->query("SELECT * FROM produk WHERE id_produk='$id'")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Produk | Program Faktur Apotek</title>
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
    <a href="customer.php">👥 Customer</a>
    <a href="produk.php" style="background:#0b5ed7;">💊 Produk</a>
    <hr style="width:90%;border-color:white">
    <a href="../transaksi/faktur.php">📄 Faktur</a>
    <a href="../transaksi/detail_faktur.php">📋 Detail Faktur</a>
    <hr style="width:90%;border-color:white">
    <a href="../logout.php" class="text-warning">🚪 Logout</a>
</div>

<div class="main">
<header>
    <h2>Data Produk</h2>
    <span>👤 <?php echo $_SESSION['username']; ?></span>
</header>

<div class="content">
    <?php if($alert != ''): ?>
        <div class="alert alert-danger"><?php echo $alert; ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between mb-3">
        <h4>Daftar Produk</h4>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalProduk">+ Tambah Produk</button>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Jenis</th>
                <th>Stock</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $data = $koneksi->query("SELECT * FROM produk");
            if ($data->num_rows > 0) {
                while ($d = $data->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>{$d['id_produk']}</td>
                        <td>{$d['nama_produk']}</td>
                        <td>{$d['Price']}</td>
                        <td>{$d['Jenis']}</td>
                        <td>{$d['stock']}</td>
                        <td>
                            <a href='?edit={$d['id_produk']}' class='btn btn-sm btn-warning'>Edit</a>
                            <a href='?hapus={$d['id_produk']}' onclick='return confirm(\"Yakin hapus produk ini?\")' class='btn btn-sm btn-danger'>Hapus</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>Belum ada data produk</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<footer>
    © 2025 Program Faktur Apotek | Dibuat oleh Zax Desain 💙
</footer>
</div>

<!-- MODAL TAMBAH/EDIT PRODUK -->
<div class="modal fade" id="modalProduk" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title"><?php echo $editData ? 'Ubah' : 'Tambah'; ?> Produk</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id_produk" value="<?php echo $editData['id_produk'] ?? ''; ?>">
            <div class="mb-3">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" class="form-control" required value="<?php echo $editData['nama_produk'] ?? ''; ?>">
            </div>
            <div class="mb-3">
                <label>Harga</label>
                <input type="number" name="price" class="form-control" required value="<?php echo $editData['Price'] ?? ''; ?>">
            </div>
            <div class="mb-3">
                <label>Jenis</label>
                <input type="text" name="jenis" class="form-control" required value="<?php echo $editData['Jenis'] ?? ''; ?>">
            </div>
            <div class="mb-3">
                <label>Stock</label>
                <input type="number" name="stock" class="form-control" required value="<?php echo $editData['stock'] ?? ''; ?>">
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($editData): ?>
<script>
var modal = new bootstrap.Modal(document.getElementById('modalProduk'));
modal.show();
</script>
<?php endif; ?>
</body>
</html>
