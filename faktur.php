<?php
session_start();
include '../koneksi.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}


// Hapus faktur
if (isset($_GET['hapus'])) {
    $no_faktur = $_GET['hapus'];
    $koneksi->query("DELETE FROM faktur WHERE no_faktur='$no_faktur'");
    header("Location: faktur.php");
    exit;
}

// Update faktur
if (isset($_POST['update'])) {
    $no_faktur = $_POST['no_faktur'];
    $id_customer = $_POST['id_customer'];
    $id_perusahaan = $_POST['id_perusahaan'];
    $id_produk = $_POST['id_produk'];
    $Qty = $_POST['Qty'];
    $metode_bayar = $_POST['metode_bayar'];
    $ppn = $_POST['ppn'];
    $dp = $_POST['dp'];

    $produk = $koneksi->query("SELECT Price FROM produk WHERE id_produk='$id_produk'")->fetch_assoc();
    $harga_produk = $produk ? $produk['Price'] : 0;
    $subtotal = $harga_produk * $qty;
    $grand_total = $subtotal + ($subtotal * $ppn / 100) - $dp;

    $koneksi->query("UPDATE faktur 
        SET metode_bayar='$metode_bayar', ppn='$ppn', dp='$dp', grand_total='$grand_total',
            id_customer='$id_customer', id_perusahaan='$id_perusahaan'
        WHERE no_faktur='$no_faktur'");
        
    $koneksi->query("UPDATE detail_faktur 
        SET id_produk='$id_produk', Qty='$qty', Price='$harga_produk' 
        WHERE no_faktur='$no_faktur'");
    
    header("Location: faktur.php");
    exit;
}

// Tambah faktur
$alert = '';
if (isset($_POST['simpan'])) {
    $id_produk = $_POST['id_produk'];
    $id_customer = $_POST['id_customer'];
    $id_perusahaan = $_POST['id_perusahaan'];
    $qty = $_POST['qty'];
    $metode_bayar = $_POST['metode_bayar'];
    $ppn = $_POST['ppn'];
    $dp = $_POST['dp'];

    $produk = $koneksi->query("SELECT Price, stock FROM produk WHERE id_produk='$id_produk'")->fetch_assoc();
    $harga_produk = $produk ? $produk['Price'] : 0;
    $stok_sekarang = $produk ? $produk['stock'] : 0;

    if ($stok_sekarang < $qty) {
        $alert = "Stok produk tidak mencukupi! (Sisa stok: $stok_sekarang)";
    } else {
        $subtotal = $harga_produk * $qty;
        $grand_total = $subtotal + ($subtotal * $ppn / 100) - $dp;

        $insertFaktur = $koneksi->query("INSERT INTO faktur (due_date, metode_bayar, ppn, dp, grand_total, user, id_customer, id_perusahaan)
                                         VALUES (CURDATE(), '$metode_bayar', '$ppn', '$dp', '$grand_total', '{$_SESSION['username']}', '$id_customer', '$id_perusahaan')");
        if ($insertFaktur) {
            $no_faktur = $koneksi->insert_id;
            $koneksi->query("INSERT INTO detail_faktur (no_faktur, id_produk, Qty, Price)
                             VALUES ('$no_faktur', '$id_produk', '$qty', '$harga_produk')");

            $koneksi->query("UPDATE produk SET stock = stock - $qty WHERE id_produk='$id_produk'");
            header("Location: faktur.php");
            exit;
        } else {
            $alert = "Error: " . $koneksi->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Faktur | Program Faktur Apotek</title>
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
    <a href="faktur.php" style="background:#0b5ed7;">📄 Faktur</a>
    <a href="detail_faktur.php">📋 Detail Faktur</a>
    <hr style="width:90%;border-color:white">
    <a href="../logout.php" class="text-warning">🚪 Logout</a>
</div>

<div class="main">
<header>
    <h2>Data Faktur</h2>
    <span>👤 <?php echo $_SESSION['username']; ?></span>
</header>

<div class="content">
    <?php if($alert != ''): ?>
        <div class="alert alert-danger"><?php echo $alert; ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between mb-3">
        <h4>Daftar Faktur</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahFakturModal">+ Tambah Faktur</button>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>No Faktur</th>
                <th>Customer</th>
                <th>Perusahaan</th>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga Produk</th>
                <th>Metode Bayar</th>
                <th>PPN (%)</th>
                <th>DP</th>
                <th>Grand Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $data = $koneksi->query("
                SELECT f.no_faktur, f.metode_bayar, f.ppn, f.dp, f.grand_total,
                       p.nama_produk, p.Price, df.Qty, c.nama_customer, pr.nama_perusahaan, p.id_produk, c.id_customer, pr.id_perusahaan
                FROM faktur f
                LEFT JOIN detail_faktur df ON f.no_faktur = df.no_faktur
                LEFT JOIN produk p ON df.id_produk = p.id_produk
                LEFT JOIN customer c ON f.id_customer = c.id_customer
                LEFT JOIN perusahaan pr ON f.id_perusahaan = pr.id_perusahaan
                ORDER BY f.no_faktur DESC
            ");
            if ($data->num_rows > 0) {
                while ($d = $data->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>{$d['no_faktur']}</td>
                        <td>{$d['nama_customer']}</td>
                        <td>{$d['nama_perusahaan']}</td>
                        <td>{$d['nama_produk']}</td>
                        <td>{$d['Qty']}</td>
                        <td>Rp " . number_format($d['Price'], 0, ',', '.') . "</td>
                        <td>{$d['metode_bayar']}</td>
                        <td>{$d['ppn']}</td>
                        <td>Rp " . number_format($d['dp'], 0, ',', '.') . "</td>
                        <td>Rp " . number_format($d['grand_total'], 0, ',', '.') . "</td>
                        <td>
                            <button class='btn btn-sm btn-warning' data-bs-toggle='modal' data-bs-target='#editModal{$d['no_faktur']}'>Edit</button>
                            <a href='?hapus={$d['no_faktur']}' onclick='return confirm(\"Yakin hapus faktur ini?\")' class='btn btn-sm btn-danger'>Hapus</a>
                        </td>
                    </tr>";

                    // Modal Edit Faktur
                    echo "
                    <div class='modal fade' id='editModal{$d['no_faktur']}' tabindex='-1'>
                      <div class='modal-dialog modal-lg'>
                        <div class='modal-content'>
                          <form method='POST'>
                            <div class='modal-header bg-warning'>
                              <h5 class='modal-title'>Edit Faktur #{$d['no_faktur']}</h5>
                              <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                            </div>
                            <div class='modal-body'>
                              <input type='hidden' name='no_faktur' value='{$d['no_faktur']}'>
                              <div class='mb-3'>
                                <label>Customer</label>
                                <select class='form-select' name='id_customer' onchange='updatePerusahaanEdit(this, {$d['no_faktur']})'>";
                                $cust = $koneksi->query("SELECT c.id_customer, c.nama_customer, p.id_perusahaan, p.nama_perusahaan FROM customer c LEFT JOIN perusahaan p ON c.id_perusahaan=p.id_perusahaan");
                                while($c = $cust->fetch_assoc()){
                                    $sel = $c['id_customer']==$d['id_customer'] ? 'selected' : '';
                                    echo "<option value='{$c['id_customer']}' data-idperusahaan='{$c['id_perusahaan']}' data-perusahaan='{$c['nama_perusahaan']}' $sel>{$c['nama_customer']}</option>";
                                }
                    echo "      </select>
                              </div>
                              <div class='mb-3'>
                                <label>Perusahaan</label>
                                <input type='text' class='form-control' id='nama_perusahaan_edit{$d['no_faktur']}' value='{$d['nama_perusahaan']}' readonly>
                                <input type='hidden' name='id_perusahaan' id='id_perusahaan_edit{$d['no_faktur']}' value='{$d['id_perusahaan']}'>
                              </div>
                              <div class='mb-3'>
                                <label>Produk</label>
                                <select class='form-select' name='id_produk' onchange='updateHargaEdit(this, {$d['no_faktur']})'>";
                                $produk = $koneksi->query("SELECT * FROM produk");
                                while($p = $produk->fetch_assoc()){
                                    $selp = $p['id_produk']==$d['id_produk'] ? 'selected' : '';
                                    echo "<option value='{$p['id_produk']}' data-harga='{$p['Price']}' $selp>{$p['nama_produk']}</option>";
                                }
                    echo "      </select>
                              </div>
                              <div class='row'>
                                <div class='col-md-3'>
                                  <label>Jumlah (Qty)</label>
                                  <input type='number' class='form-control' name='qty' id='qty_edit{$d['no_faktur']}' value='{$d['Qty']}' min='1' oninput='hitungTotalEdit({$d['no_faktur']})'>
                                </div>
                                <div class='col-md-3'>
                                  <label>Harga Produk</label>
                                  <input type='text' class='form-control' id='harga_produk_edit{$d['no_faktur']}' value='{$d['Price']}' readonly>
                                </div>
                                <div class='col-md-3'>
                                  <label>PPN (%)</label>
                                  <input type='number' class='form-control' name='ppn' value='{$d['ppn']}' oninput='hitungTotalEdit({$d['no_faktur']})'>
                                </div>
                                <div class='col-md-3'>
                                  <label>DP</label>
                                  <input type='number' class='form-control' name='dp' value='{$d['dp']}' oninput='hitungTotalEdit({$d['no_faktur']})'>
                                </div>
                              </div>
                              <div class='mt-3'>
                                <label>Grand Total</label>
                                <input type='text' class='form-control' id='grand_total_edit{$d['no_faktur']}' value='{$d['grand_total']}' readonly>
                              </div>
                            </div>
                            <div class='modal-footer'>
                              <button type='submit' name='update' class='btn btn-warning'>Simpan Perubahan</button>
                              <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Batal</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>";
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

<?php include 'modal_tambah.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updatePerusahaanEdit(sel, id){
  const nama = sel.options[sel.selectedIndex].getAttribute('data-perusahaan') || '';
  const idp = sel.options[sel.selectedIndex].getAttribute('data-idperusahaan') || '';
  document.getElementById('nama_perusahaan_edit'+id).value = nama;
  document.getElementById('id_perusahaan_edit'+id).value = idp;
}
function updateHargaEdit(sel, id){
  const harga = sel.options[sel.selectedIndex].getAttribute('data-harga') || 0;
  document.getElementById('harga_produk_edit'+id).value = harga;
  hitungTotalEdit(id);
}
function hitungTotalEdit(id){
  const harga = parseFloat(document.getElementById('harga_produk_edit'+id).value)||0;
  const qty = parseFloat(document.getElementById('qty_edit'+id).value)||1;
  const ppn = parseFloat(document.querySelector(`#editModal${id} input[name='ppn']`).value)||0;
  const dp = parseFloat(document.querySelector(`#editModal${id} input[name='dp']`).value)||0;
  const subtotal = harga * qty;
  const total = subtotal + (subtotal * ppn / 100) - dp;
  document.getElementById('grand_total_edit'+id).value = total;
}
</script>
</body>
</html>
