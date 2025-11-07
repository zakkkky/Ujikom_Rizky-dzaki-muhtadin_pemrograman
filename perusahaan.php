  <?php
  session_start();
  include '../koneksi.php';
  if (!isset($_SESSION['username'])) {
      header("Location: ../login.php");
      exit;
  }

  // Hapus data perusahaan
  if (isset($_GET['hapus'])) {
      $id = $_GET['hapus'];
      $koneksi->query("DELETE FROM perusahaan WHERE id_perusahaan='$id'");
      header("Location: perusahaan.php");
      exit;
  }

  // Tambah / Ubah perusahaan
  $alert = '';
  if (isset($_POST['simpan'])) {
      $id = $_POST['id_perusahaan'];
      $nama = $_POST['nama_perusahaan'];
      $alamat = $_POST['alamat'];
      $no_telp = $_POST['no_telp'];
      $fax = $_POST['fax'];

      if ($id == '') {
          $result = $koneksi->query("INSERT INTO perusahaan (nama_perusahaan, alamat, no_telp, fax) VALUES ('$nama','$alamat','$no_telp','$fax')");
          if (!$result) $alert = "Error insert: ".$koneksi->error;
          else { header("Location: perusahaan.php"); exit; }
      } else {
          $result = $koneksi->query("UPDATE perusahaan SET nama_perusahaan='$nama', alamat='$alamat', no_telp='$no_telp', fax='$fax' WHERE id_perusahaan='$id'");
          if (!$result) $alert = "Error update: ".$koneksi->error;
          else { header("Location: perusahaan.php"); exit; }
      }
  }

  // Ambil data untuk edit
  $editData = null;
  if (isset($_GET['edit'])) {
      $id = $_GET['edit'];
      $editData = $koneksi->query("SELECT * FROM perusahaan WHERE id_perusahaan='$id'")->fetch_assoc();
  }
  ?>
  <!DOCTYPE html>
  <html lang="id">
  <head>
  <meta charset="UTF-8">
  <title>Data Perusahaan | Program Faktur Apotek</title>
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
      <a href="perusahaan.php" style="background:#0b5ed7;">🏢 Perusahaan</a>
      <a href="customer.php">👥 Customer</a>
      <a href="produk.php">💊 Produk</a>
      <hr style="width:90%;border-color:white">
      <a href="../transaksi/faktur.php">📄 Faktur</a>
      <a href="../transaksi/detail_faktur.php">📋 Detail Faktur</a>
      <hr style="width:90%;border-color:white">
      <a href="../logout.php" class="text-warning">🚪 Logout</a>
  </div>

  <div class="main">
  <header>
      <h2>Data Perusahaan</h2>
      <span>👤 <?php echo $_SESSION['username']; ?></span>
  </header>

  <div class="content">
      <?php if($alert != ''): ?>
          <div class="alert alert-danger"><?php echo $alert; ?></div>
      <?php endif; ?>

      <div class="d-flex justify-content-between mb-3">
          <h4>Daftar Perusahaan</h4>
          <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPerusahaan">+ Tambah Perusahaan</button>
      </div>

      <table class="table table-bordered table-striped align-middle">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Nama Perusahaan</th>
                  <th>Alamat</th>
                  <th>No. Telp</th>
                  <th>Fax</th>
                  <th>Aksi</th>
              </tr>
          </thead>
          <tbody>
              <?php
              $data = $koneksi->query("SELECT * FROM perusahaan");
              if ($data->num_rows > 0) {
                  while ($d = $data->fetch_assoc()) {
                      echo "
                      <tr>
                          <td>{$d['id_perusahaan']}</td>
                          <td>{$d['nama_perusahaan']}</td>
                          <td>{$d['alamat']}</td>
                          <td>{$d['no_telp']}</td>
                          <td>{$d['fax']}</td>
                          <td>
                              <a href='?edit={$d['id_perusahaan']}' class='btn btn-sm btn-warning'>Edit</a>
                              <a href='?hapus={$d['id_perusahaan']}' onclick='return confirm(\"Yakin hapus perusahaan ini?\")' class='btn btn-sm btn-danger'>Hapus</a>
                          </td>
                      </tr>";
                  }
              } else {
                  echo "<tr><td colspan='6' class='text-center'>Belum ada data perusahaan</td></tr>";
              }
              ?>
          </tbody>
      </table>
  </div>

  <footer>
      © 2025 Program Faktur Apotek | Dibuat oleh Zax Desain 💙
  </footer>
  </div>

  <!-- MODAL TAMBAH/EDIT PERUSAHAAN -->
  <div class="modal fade" id="modalPerusahaan" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="post">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title"><?php echo $editData ? 'Ubah' : 'Tambah'; ?> Perusahaan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <input type="hidden" name="id_perusahaan" value="<?php echo $editData['id_perusahaan'] ?? ''; ?>">
              <div class="mb-3">
                  <label>Nama Perusahaan</label>
                  <input type="text" name="nama_perusahaan" class="form-control" required value="<?php echo $editData['nama_perusahaan'] ?? ''; ?>">
              </div>
              <div class="mb-3">
                  <label>Alamat</label>
                  <input type="text" name="alamat" class="form-control" required value="<?php echo $editData['alamat'] ?? ''; ?>">
              </div>
              <div class="mb-3">
                  <label>No. Telp</label>
                  <input type="text" name="no_telp" class="form-control" value="<?php echo $editData['no_telp'] ?? ''; ?>">
              </div>
              <div class="mb-3">
                  <label>Fax</label>
                  <input type="text" name="fax" class="form-control" value="<?php echo $editData['fax'] ?? ''; ?>">
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
  var modal = new bootstrap.Modal(document.getElementById('modalPerusahaan'));
  modal.show();
  </script>
  <?php endif; ?>
  </body>
  </html>
