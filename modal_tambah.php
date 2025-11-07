<div class="modal fade" id="tambahFakturModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">Tambah Faktur Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <!-- Pilih Customer -->
          <div class="mb-3">
            <label>Customer</label>
            <select class="form-select" name="id_customer" id="id_customer" onchange="updatePerusahaan()">
              <option value="">-- Pilih Customer --</option>
              <?php
              $cust = $koneksi->query("
                SELECT c.id_customer, c.nama_customer, p.id_perusahaan, p.nama_perusahaan
                FROM customer c
                LEFT JOIN perusahaan p ON c.id_perusahaan = p.id_perusahaan
              ");
              while ($c = $cust->fetch_assoc()) {
                echo "<option value='{$c['id_customer']}' data-idperusahaan='{$c['id_perusahaan']}' data-perusahaan='{$c['nama_perusahaan']}'>
                        {$c['nama_customer']}
                      </option>";
              }
              ?>
            </select>
          </div>

          <!-- Perusahaan otomatis -->
          <div class="mb-3">
            <label>Perusahaan</label>
            <input type="text" class="form-control" id="nama_perusahaan" readonly>
            <input type="hidden" name="id_perusahaan" id="id_perusahaan">
          </div>

          <!-- Pilih Produk -->
          <div class="mb-3">
            <label>Produk</label>
            <select class="form-select" name="id_produk" id="id_produk" onchange="updateHarga()">
              <option value="">-- Pilih Produk --</option>
              <?php
              $produk = $koneksi->query("SELECT * FROM produk");
              while ($p = $produk->fetch_assoc()) {
                echo "<option value='{$p['id_produk']}' data-harga='{$p['Price']}'>{$p['nama_produk']}</option>";
              }
              ?>
            </select>
          </div>

          <!-- Input Qty, Harga, Metode Bayar -->
          <div class="row">
            <div class="col-md-4">
              <label>Qty</label>
              <input type="number" class="form-control" name="qty" id="qty" value="1" min="1" oninput="hitungTotal()">
            </div>
            <div class="col-md-4">
              <label>Harga Produk</label>
              <input type="text" class="form-control" id="harga_produk" readonly>
            </div>
            <div class="col-md-4">
              <label>Metode Bayar</label>
              <select class="form-select" name="metode_bayar">
                <option value="Cash">Cash</option>
                <option value="Transfer">Transfer</option>
                <option value="Kredit">Kredit</option>
              </select>
            </div>
          </div>

          <!-- Input PPN, DP, dan Grand Total -->
          <div class="row mt-3">
            <div class="col-md-4">
              <label>PPN (%)</label>
              <input type="number" class="form-control" name="ppn" id="ppn" value="10" oninput="hitungTotal()">
            </div>
            <div class="col-md-4">
              <label>DP</label>
              <input type="number" class="form-control" name="dp" id="dp" value="0" oninput="hitungTotal()">
            </div>
            <div class="col-md-4">
              <label>Grand Total</label>
              <input type="text" class="form-control" id="grand_total" readonly>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" name="simpan" class="btn btn-primary">💾 Simpan Faktur</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Ketika customer dipilih, otomatis isi nama & id perusahaan
function updatePerusahaan() {
  const sel = document.getElementById('id_customer');
  const namaPerusahaan = sel.options[sel.selectedIndex].getAttribute('data-perusahaan') || '';
  const idPerusahaan = sel.options[sel.selectedIndex].getAttribute('data-idperusahaan') || '';
  document.getElementById('nama_perusahaan').value = namaPerusahaan;
  document.getElementById('id_perusahaan').value = idPerusahaan;
}

// Ketika produk dipilih, tampilkan harga otomatis
function updateHarga() {
  const sel = document.getElementById('id_produk');
  const harga = parseFloat(sel.options[sel.selectedIndex].getAttribute('data-harga')) || 0;
  document.getElementById('harga_produk').value = harga;
  hitungTotal();
}

// Hitung total otomatis
function hitungTotal() {
  const harga = parseFloat(document.getElementById('harga_produk').value) || 0;
  const qty = parseFloat(document.getElementById('qty').value) || 1;
  const ppn = parseFloat(document.getElementById('ppn').value) || 0;
  const dp = parseFloat(document.getElementById('dp').value) || 0;

  const subtotal = harga * qty;
  const total = subtotal + (subtotal * ppn / 100) - dp;
  document.getElementById('grand_total').value = total.toFixed(2);
}
</script>
