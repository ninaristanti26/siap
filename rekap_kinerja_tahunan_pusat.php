<?php
ob_start();
session_start();
include "koneksi.php";
include("atas_pusat.php");

$tahun_input = $_GET['tahun_data'] ?? '';
$tahun_label = '';

if (!empty($tahun_input)) {
    $tahun_label = date("Y", strtotime($tahun_input . "-01")); // Juli 2025
}

// Ambil daftar bulan unik dari tgl_data
$tahun_tersedia = [];
$q_tahun = "SELECT DISTINCT DATE_FORMAT(tgl_data, '%Y') AS tahun FROM debitur_npl ORDER BY tahun DESC";
$res_tahun = $mysqli->query($q_tahun);
while ($row = $res_tahun->fetch_assoc()) {
    $tahun_tersedia[] = $row['tahun'];
}
?>

<!-- Styles -->
<link href="datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<link href="datatables-responsive/dataTables.responsive.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<style>
    .dt-buttons {
  margin-bottom: 10px;
}

.dt-buttons .btn, .dt-buttons button {
  background-color: #0d6efd;
  border: none;
  color: white;
  padding: 5px 10px;
  margin-right: 5px;
  border-radius: 4px;
  cursor: pointer;
}

.dt-buttons .btn:hover, .dt-buttons button:hover {
  background-color: #0b5ed7;
}

</style>
<div class="main-panel">
  <div class="content-wrapper">
    <div class="row mb-4">
      <div class="col-12 col-xl-8">
        <h3 class="font-weight-bold">Rekap Kinerja Tahunan Cabang</h3>
      </div>
    </div>
    <hr>

<form method="get" action="">
  <div class="form-group text-center">
    <label for="tahun_data">Pilih Tahun</label>
    <select name="tahun_data" class="form-control w-50 mx-auto text-center" required>
      <option value="">-- Pilih Tahun --</option>
      <?php foreach ($tahun_tersedia as $tahun): 
        $display = date("Y", strtotime($tahun . "-01"));
        $selected = ($tahun_input == $tahun) ? "selected" : "";
      ?>
        <option value="<?= $tahun ?>" <?= $selected ?>><?= $display ?></option>
      <?php endforeach; ?>
    </select>
    <div class="mt-3">
      <button type="submit" class="btn btn-primary">Tampilkan</button>
      <a href="?" class="btn btn-secondary">Reset</a>
    </div>
  </div>
</form>

    <?php if (!empty($tahun_input)): ?>
    <div class="table-responsive">
  <table class="table table-hover table-bordered" id="dataTables-example" width="100%">
    <thead class="thead-light table-primary">
      <tr>
        <th>No.</th>
        <th>Bulan</th>
        <th>Kode Cabang</th>
        <th>Debitur</th>
        <th>Baki Debet</th>
        <th>CKPN</th>
        <th>Konfirmasi (1)</th>
        <th>Domisili Baru Belum Ditemukan (2)</th>
        <th>Belum Janji Bayar (3)</th>
        <th>Sudah Janji Bayar (4)</th>
        <th>Sudah Bayar (5)</th>
        <th>Bayar Pokok</th>
        <th>Bayar Bunga</th>
        <th>Total Realisasi</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $query = "SELECT 
                cabang.nama_cabang,
                cabang.kode_cabang,
                COALESCE(COUNT(d.id_deb), 0) AS noa,
                COALESCE(SUM(d.bd_npl), 0) AS bd,
                COALESCE(SUM(d.ckpn), 0) AS ckpn,
                COALESCE(SUM(d.satu), 0) AS satu,
                COALESCE(SUM(d.dua), 0) AS dua,
                COALESCE(SUM(d.tiga), 0) AS tiga,
                COALESCE(SUM(d.empat), 0) AS empat,
                COALESCE(SUM(d.lima), 0) AS lima,
                COALESCE(SUM(d.bayar_pokok), 0) AS pokok,
                COALESCE(SUM(d.bayar_bunga), 0) AS bunga
              FROM cabang
              LEFT JOIN debitur_npl d 
                ON d.kode_cabang = cabang.kode_cabang
                AND DATE_FORMAT(d.tgl_data, '%Y') = ?
              GROUP BY cabang.kode_cabang
              ORDER BY cabang.kode_cabang ASC";

    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("s", $tahun_input);
        $stmt->execute();
        $result = $stmt->get_result();
        $no = 1;
        while ($row = $result->fetch_assoc()):
            $total_bayar = $row['pokok'] + $row['bunga'];
    ?>
    <tr>
      <td class="text-center"><?= $no++ ?></td>
      <td class="text-center"><?= htmlspecialchars($tahun_label) ?></td>
      <td class="text-center"><?= htmlspecialchars($row['kode_cabang']) ?></td>
      <td class="text-center"><?= number_format($row['noa']) ?></td>
      <td class="text-right"><?= number_format($row['bd'], 0, ',', '.') ?></td>
      <td class="text-right"><?= number_format($row['ckpn'], 0, ',', '.') ?></td>
      <td class="text-right"><?= number_format($row['satu']) ?></td>
      <td class="text-right"><?= number_format($row['dua']) ?></td>
      <td class="text-right"><?= number_format($row['tiga']) ?></td>
      <td class="text-right"><?= number_format($row['empat']) ?></td>
      <td class="text-right"><?= number_format($row['lima']) ?></td>
      <td class="text-right"><?= number_format($row['pokok'], 0, ',', '.') ?></td>
      <td class="text-right"><?= number_format($row['bunga'], 0, ',', '.') ?></td>
      <td class="text-right"><?= number_format($total_bayar, 0, ',', '.') ?></td>
    </tr>
    <?php endwhile; $stmt->close(); } ?>
    </tbody>
  </table>
</div>
<?php else: ?>
  <p class="text-muted text-center mt-4">Silakan pilih bulan untuk menampilkan data rekap.</p>
<?php endif; ?>

    <?php include "add_debitur.php"; ?>
  </div>
</div>

<?php include("footer.php"); ?>

<script src="datatables/js/jquery.dataTables.min.js"></script>
<script src="datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="datatables-responsive/dataTables.responsive.js"></script>

<!-- Buttons extension -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script>
$(document).ready(function () {
    $('#dataTables-example').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'excelHtml5',
            'pdfHtml5',
            'print'
        ],
        responsive: true,
        pageLength: 10,
        lengthChange: false,
        searching: true,
    });
});
</script>
