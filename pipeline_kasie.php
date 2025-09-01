<?php
session_start();
include "koneksi.php";
include "atas_kasie.php";

// Simpan ID AO dan kode_cabang dari GET jika tersedia
if (isset($_GET['id_ao'])) {
    $_SESSION['id_ao'] = $_GET['id_ao'];

    // Ambil kode_cabang dari AO
    $stmt = $mysqli->prepare("SELECT kode_cabang FROM ao WHERE id_ao = ?");
    $stmt->bind_param("s", $_SESSION['id_ao']);
    $stmt->execute();
    $stmt->bind_result($kode_cabang_db);
    if ($stmt->fetch()) {
        $_SESSION['kode_cabang'] = $kode_cabang_db;
    }
    $stmt->close();
}

$id_ao = $_SESSION['id_ao'] ?? '';
$kode_cabang = $_SESSION['kode_cabang'] ?? '';
$selectedBulan = $_GET['bulan'] ?? ''; // Tambahkan ini untuk menangkap bulan dari GET

function renderStatusIcon($status) {
    return $status == '1'
        ? "<i class='far fa-thumbs-up' style='font-size:15px;color:green'></i>"
        : "<i class='far fa-thumbs-down' style='font-size:15px;color:red'></i>";
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<link href="datatables-responsive/dataTables.responsive.css" rel="stylesheet">

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row mb-4">
      <div class="col-12 col-xl-8">
        <h3 class="font-weight-bold">Welcome <?= htmlspecialchars($kode_cabang) ?></h3>
      </div>
    </div>

    <?php
    include("getCode/get_pipe.php");
    if (!empty($options)) {
        foreach ($options as $data):
    ?>
    <div class="table-responsive mb-3">
      <table class="table">
        <thead class="text-primary">
          <tr><th width="150">Kantor Cabang</th><th width="30">:</th><td><?= htmlspecialchars($data['nama_cabang']) ?></td></tr>
          <tr><th>Nama AO</th><th>:</th><td><?= htmlspecialchars($data['nama_ao']) ?></td></tr>
          <tr><th>Jabatan</th><th>:</th><td><?= htmlspecialchars($data['jabatan']) ?></td></tr>
        </thead>
      </table>
    </div>
    <?php endforeach; } else { echo "<p class='text-muted'>Belum Ada Data</p>"; } ?>

    <hr>

    <?php include "pilih_data_kasie.php"; ?>

    <?php if (!empty($selectedBulan)): ?>
<!-- Tabel Debitur -->
<div class="table-responsive">
  <table class="table table-hover table-bordered" id="dataTables-example" width="108%">
    <thead class="thead-light table-primary">
      <tr>
        <th class="text-center">No.</th>
        <th class="text-center">ID Marketing</th>
        <th class="text-center">No. Rekening</th>
        <th class="text-center">Nama Debitur</th>
        <th class="text-center">Alamat</th>
        <th class="text-center">Plafon</th>
        <th class="text-center">Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php
    // Format bulan ke YYYY-MM (misal 2025-08)
    $query = "SELECT dn.norek,
                    MAX(dn.nama_debitur) AS nama_debitur,
                    MAX(dn.alamat_npl) AS alamat_npl,
                    MAX(dn.plafon_npl) AS plafon_npl,
                    MAX(dn.id_deb) AS id_deb,
                    MAX(ao.nama_ao) AS nama_ao,
                    MAX(cabang.nama_cabang) AS nama_cabang,
                    MAX(dn.tgl_data) AS tgl_data
              FROM debitur_npl dn
              JOIN ao ON dn.id_ao = ao.id_ao
              JOIN cabang ON ao.kode_cabang = cabang.kode_cabang
              WHERE cabang.kode_cabang = ?
              AND DATE_FORMAT(dn.tgl_data, '%Y-%m') = ?
              GROUP BY dn.norek
              ORDER BY dn.norek ASC
              LIMIT 0, 25";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $kode_cabang, $selectedBulan);
    $stmt->execute();
    $result = $stmt->get_result();
    $no = 1;

    while ($data = $result->fetch_assoc()):
        $norek        = htmlspecialchars($data['norek']);
        $nama_debitur = htmlspecialchars($data['nama_debitur']);
        $alamat_npl   = htmlspecialchars($data['alamat_npl']);
        $plafon_npl   = number_format($data['plafon_npl'], 0, ',', '.');
        $tgl_data     = htmlspecialchars($data['tgl_data']);
    ?>
    <tr>
      <td class="text-center"><?= $no++ ?></td>
      <td class="text-center"><?= $norek ?></td>
      <td><?= $nama_debitur ?></td>
      <td><?= $alamat_npl ?></td>
      <td class="text-right"><?= $plafon_npl ?></td>
      <td class="text-center">
        <a href="history_pipeline_kasie.php?norek=<?= urlencode($norek) ?>&bulan=<?= urlencode(substr($tgl_data, 0, 7)) ?>" class="btn btn-sm btn-primary">
          Selengkapnya
        </a>
      </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
  <p class="text-muted">Silakan pilih bulan terlebih dahulu untuk menampilkan data.</p>
<?php endif; ?>

    <?php include "add_debitur.php"; ?>
  </div>
</div>

<?php include("footer.php"); ?>

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