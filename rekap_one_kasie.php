<?php
ob_start();
session_start();
include "koneksi.php";
include "atas_kasie.php";

// Set id_ao dari GET ke session
if (isset($_GET['kode_cabang'])) {
    $_SESSION['kode_cabang'] = $_GET['kode_cabang'];
}
$kode_cabang = $_SESSION['kode_cabang'] ?? '';
$selectedBulan = $_POST['bulan'] ?? date('m-Y');
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
        <h3 class="font-weight-bold">Debitur yang sudah dikonfirmasi baik melalui telpon/WA atau kunjungan langsung (1)</h3>
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

    <!-- Tabel Debitur -->
    <div class="table-responsive">
      <table class="table table-hover table-bordered" id="dataTables-example" width="100%">
        <thead class="thead-light table-primary">
          <tr>
            <th class="text-center">No.</th>
            <th class="text-center">No. Rekening</th>
            <th class="text-center">Nama Debitur</th>
            <th class="text-center">Alamat</th>
            <th class="text-center">Plafon</th>
            <th class="text-center">Baki Debet NPL</th>
            <th class="text-center">CKPN</th>
            <th class="text-center">Penyebab Masalah</th>
            <th class="text-center">Strategi Penyelesaian</th>
            <th class="text-center">Alasan Strategi</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $query = "SELECT * FROM debitur_npl
                  JOIN ao ON debitur_npl.id_ao = ao.id_ao
                  JOIN cabang ON ao.kode_cabang = cabang.kode_cabang
                  WHERE cabang.kode_cabang = ? 
                  AND DATE_FORMAT(debitur_npl.tgl_data, '%m-%Y') = ?
                  AND debitur_npl.satu = 1
                  GROUP BY debitur_npl.norek
        ";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ss", $kode_cabang, $selectedBulan);
        $stmt->execute();
        $result = $stmt->get_result();
        $no = 1;

        while ($data = $result->fetch_assoc()):
            $norek = htmlspecialchars($data['norek']);
            $nama_debitur = htmlspecialchars($data['nama_debitur']);
            $alamat_npl = htmlspecialchars($data['alamat_npl']);
            $plafon_npl = number_format($data['plafon_npl'], 0, ',', '.');
            $bd_npl = number_format($data['bd_npl'], 0, ',', '.');
            $ckpn = number_format($data['ckpn'], 0, ',', '.');
            $tgl_data = htmlspecialchars($data['tgl_data']);
            $penyebab_masalah = htmlspecialchars($data['penyebab_masalah']);
            $strategi_penyelesaian = htmlspecialchars($data['strategi_penyelesaian']);
            $alasan_strategi = htmlspecialchars($data['alasan_strategi']);
        ?>
        <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td class="text-center"><?= $norek ?></td>
          <td><?= $nama_debitur ?></td>
          <td><?= $alamat_npl ?></td>
          <td class="text-right"><?= $plafon_npl ?></td>
          <td class="text-right"><?= $bd_npl ?></td>
          <td class="text-right"><?= $ckpn ?></td>
          <td class="text-left"><?= $penyebab_masalah ?></td>
          <td class="text-left"><?= $strategi_penyelesaian ?></td>
          <td class="text-left"><?= $alasan_strategi ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
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
