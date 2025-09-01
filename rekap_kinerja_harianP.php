<?php
ob_start();
session_start();
include "koneksi.php";
include("atas_pusat.php");

$tanggal_input = $_GET['tgl_data'] ?? '';

function renderStatusIcon($status) {
    return $status == '1'
        ? "<i class='far fa-thumbs-up' style='font-size:15px;color:green'></i>"
        : "<i class='far fa-thumbs-down' style='font-size:15px;color:red'></i>";
}
?>

<!-- Styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<link href="datatables-responsive/dataTables.responsive.css" rel="stylesheet">

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row mb-4">
      <div class="col-12 col-xl-8">
        <h3 class="font-weight-bold">Rekap Kinerja Harian AO</h3>
      </div>
    </div>
    <hr>

    <form method="get" action="">
        <div class="form-group text-center">
            <label for="tanggal">Pilih Tanggal Data</label>
            <input type="date" name="tgl_data" class="form-control w-50 mx-auto text-center" id="tanggal" value="<?= htmlspecialchars($tanggal_input) ?>" required>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="?" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    <?php if (!empty($tanggal_input)): ?>
    <div class="table-responsive">
      <table class="table table-hover table-bordered" id="dataTables-example" width="100%">
        <thead class="thead-light table-primary">
          <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Petugas AO</th>
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
                    ao.nama_ao,
                    ao.kode_cabang,
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
                  FROM ao
                  LEFT JOIN debitur_npl d 
                    ON d.id_ao = ao.id_ao
                    AND DATE(d.tgl_data) = ?
                  WHERE ao.id_jabatan = 1
                  GROUP BY ao.id_ao
                  ORDER BY ao.nama_ao ASC";

        if ($stmt = $mysqli->prepare($query)) {
            $stmt->bind_param("s", $tanggal_input); // hanya 1 parameter: tanggal
            $stmt->execute();
            $result = $stmt->get_result();
            $no = 1;
            while ($row = $result->fetch_assoc()):
                $total_bayar = $row['pokok'] + $row['bunga'];
        ?>
        <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td class="text-center"><?= htmlspecialchars($tanggal_input) ?></td>
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
      <p class="text-muted text-center mt-4">Silakan pilih tanggal untuk menampilkan data rekap.</p>
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

  function toggleFormDebitur() {
      const form = document.getElementById("formAddDebitur");
      if (form) {
        form.style.display = (form.style.display === "none" ? "block" : "none");
      }
  }

  function toggleFormUpload(id_deb) {
      const form = document.getElementById("formUploadDokumentasi" + id_deb);
      if (form) {
        form.style.display = (form.style.display === "none" ? "block" : "none");
      }
  }
</script>

<style>
  .btn-upload {
    font-weight: 500;
    padding: 6px 12px;
  }
</style>
