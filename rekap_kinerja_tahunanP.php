<?php
ob_start();
session_start();
include "koneksi.php";
include("atas_pusat.php");

$tahun_input = $_GET['tahun_data'] ?? '';
$year = $tahun_input;

$tahun_tersedia = [];
$q_tahun = "SELECT DISTINCT YEAR(tgl_data) AS tahun FROM debitur_npl ORDER BY tahun DESC";
$res_tahun = $mysqli->query($q_tahun);
while ($row = $res_tahun->fetch_assoc()) {
    $tahun_tersedia[] = $row['tahun'];
}
?>

<!-- CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<link href="datatables-responsive/dataTables.responsive.css" rel="stylesheet">

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row mb-4">
      <div class="col-12 col-xl-8">
        <h3 class="font-weight-bold">Laporan Kinerja Tahunan AO</h3>
      </div>
    </div>
    <hr>
    <form method="get" action="">
        <div class="form-group text-center">
            <label for="tahun_data">Pilih Tahun</label>
            <select name="tahun_data" class="form-control w-50 mx-auto text-center" required>
                <option value="">-- Pilih Tahun --</option>
                <?php foreach ($tahun_tersedia as $thn): 
                    $selected = ($tahun_input == $thn) ? "selected" : "";
                ?>
                    <option value="<?= $thn ?>" <?= $selected ?>><?= $thn ?></option>
                <?php endforeach; ?>
            </select>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="?" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <?php if (!empty($year)): ?>
    <div class="table-responsive">
      <table class="table table-hover table-bordered" id="dataTables-example" width="100%">
        <thead class="thead-light table-primary">
          <tr>
            <th>No.</th>
            <th>Petugas AO</th>
            <th>Kode Cabang</th>
            <th>Total Debitur</th>
            <th>Total Baki Debet</th>
            <th>Total CKPN</th>
            <th>Konfirmasi (1)</th>
            <th>Domisili Belum Ditemukan (2)</th>
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
                    ao.nama_ao,
                    ao.kode_cabang,
                    COUNT(d.id_deb) AS noa,
                    SUM(d.bd_npl) AS bd,
                    SUM(d.ckpn) AS ckpn,
                    SUM(d.satu) AS satu,
                    SUM(d.dua) AS dua,
                    SUM(d.tiga) AS tiga,
                    SUM(d.empat) AS empat,
                    SUM(d.lima) AS lima,
                    SUM(d.bayar_pokok) AS pokok,
                    SUM(d.bayar_bunga) AS bunga
                  FROM ao
                  LEFT JOIN debitur_npl d 
                    ON d.id_ao = ao.id_ao
                    AND YEAR(d.tgl_data) = ?
                  WHERE ao.id_jabatan = 1
                  GROUP BY ao.id_ao
                  ORDER BY ao.nama_ao ASC";

        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("i", $year);
            $stmt->execute();
            $result = $stmt->get_result();
            $no = 1;
            while ($row = $result->fetch_assoc()):
                $total_bayar = $row['pokok'] + $row['bunga'];
        ?>
        <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td class="text-center"><?= htmlspecialchars($row['nama_ao']) ?></td>
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
      <p class="text-muted text-center mt-4">Silakan pilih tahun untuk menampilkan laporan.</p>
    <?php endif; ?>

    <?php include "add_debitur.php"; ?>
  </div>
</div>

<?php include("footer.php"); ?>

<!-- Scripts -->
<script src="datatables/js/jquery.dataTables.min.js"></script>
<script src="datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="datatables-responsive/dataTables.responsive.js"></script>
<script>
  $(document).ready(function() {
      $('#dataTables-example').DataTable({ responsive: true });
  });
</script>
