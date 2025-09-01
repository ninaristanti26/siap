<?php
ob_start();
session_start();
include "koneksi.php";
include "atas_ao.php";

if (isset($_GET['id_ao'])) {
    $_SESSION['id_ao'] = $_GET['id_ao'];
}
$id_ao = $_SESSION['id_ao'] ?? '';

function renderStatusIcon($status) {
    return $status == '1'
        ? "<i class='far fa-thumbs-up' style='font-size:15px;color:green'></i>"
        : "<i class='far fa-thumbs-down' style='font-size:15px;color:red'></i>";
}

$norek = $_GET['norek'] ?? '';
$bulan = $_GET['bulan'] ?? ''; 

if (empty($norek) || empty($bulan)) {
    echo "<div class='alert alert-danger'>Data tidak lengkap. Pastikan No Rekening dan Bulan tersedia.</div>";
    exit;
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<link href="datatables-responsive/dataTables.responsive.css" rel="stylesheet">

<div class="main-panel">
  <div class="content-wrapper">
    <h3 class="font-weight-bold">
      History Pipeline untuk No. Rekening: <?= htmlspecialchars($norek) ?> 
      | Bulan: <?= htmlspecialchars($bulan) ?>
    </h3>

   <!-- <div class="table-responsive mb-3">
      <a href="tindak_lanjut_kasie.php?norek=<?= urlencode($norek) ?>" class="btn btn-sm btn-primary">
        Tindak Lanjut Kepala Seksi Pemasaran
      </a>
    </div>-->

    <div class="table-responsive">
      <table class="table table-hover table-bordered" id="dataTables-example" width="100%">
        <thead class="thead-light table-primary">
          <tr class="text-center">
            <th>No.</th>
            <th>Tanggal Data</th>
            <th>No. Rekening</th>
            <th>Kolek</th>
            <th>Nama Debitur</th>
            <th>Alamat</th>
            <th>Plafon</th>
            <th>Baki Debet</th>
            <th>CKPN</th>
            <th>Penyebab Masalah</th>
            <th>Strategi Penyelesaian</th>
            <th>Alasan Strategi</th>
            <th>Telp/WA/Kunjungan</th>
            <th>Belum Ditemukan</th>
            <th>Belum Ada Kemampuan Bayar</th>
            <th>Ada Janji Bayar</th>
            <th>Realisasi Pembayaran</th>
            <th>Bayar Pokok</th>
            <th>Bayar Bunga</th>
            <th>Total Bayar</th>
            <th>Dokumentasi</th>
            <th>Lokasi</th>
            <th>Tambah Lokasi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $stmt = $mysqli->prepare("
              SELECT * FROM debitur_npl 
              JOIN ao ON debitur_npl.id_ao = ao.id_ao
              JOIN cabang ON ao.kode_cabang = cabang.kode_cabang
              WHERE norek = ? AND DATE_FORMAT(tgl_data, '%Y-%m') = ?
              ORDER BY tgl_data DESC
          ");
          $stmt->bind_param("ss", $norek, $bulan);
          $stmt->execute();
          $result = $stmt->get_result();
          $no = 1;

          while ($row = $result->fetch_assoc()):
            $bayar_pokok = $row['bayar_pokok'];
            $bayar_bunga = $row['bayar_bunga'];
            $total_bayar = $bayar_pokok + $bayar_bunga;
          ?>
          <tr>
          <td class="text-center"><?= $no++ ?></td>
          <td class="text-center"><?= htmlspecialchars($row['tgl_data']) ?></td>
          <td class="text-center"><?= htmlspecialchars($row['norek']) ?></td>
          <td class="text-center"><?= htmlspecialchars($row['kolek']) ?></td>
          <td><?= htmlspecialchars($row['nama_debitur']) ?></td>
          <td><?= htmlspecialchars($row['alamat_npl']) ?></td>
          <td class="text-right"><?= number_format($row['plafon_npl'], 0, ',', '.') ?></td>
          <td class="text-right"><?= number_format($row['bd_npl'], 0, ',', '.') ?></td>
          <td class="text-right"><?= number_format($row['ckpn'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['penyebab_masalah']) ?></td>
          <td><?= htmlspecialchars($row['strategi_penyelesaian']) ?></td>
          <td><?= htmlspecialchars($row['alasan_strategi']) ?></td>
          <td class="text-center"><?= renderStatusIcon($row['satu']) ?></td>
          <td class="text-center"><?= renderStatusIcon($row['dua']) ?></td>
          <td class="text-center"><?= renderStatusIcon($row['tiga']) ?></td>
          <td class="text-center"><?= renderStatusIcon($row['empat']) ?></td>
          <td class="text-center"><?= renderStatusIcon($row['lima']) ?></td>
          <td class="text-right"><?= number_format($bayar_pokok, 0, ',', '.') ?></td>
          <td class="text-right"><?= number_format($bayar_bunga, 0, ',', '.') ?></td>
          <td class="text-right"><?= number_format($total_bayar, 0, ',', '.') ?></td>
          <td class="text-center">
            <?php if (!empty($row['file_dokumentasi']) && $row['file_dokumentasi'] !== 'masih kosong'): ?>
              <a href="uploads/<?= htmlspecialchars($row['file_dokumentasi']) ?>" target="_blank" class="btn btn-sm btn-outline-success">Lihat PDF</a>
            <?php else: ?>
              <button type="button" class="btn btn-outline-primary btn-sm btn-upload" onclick="toggleFormUpload('<?= $row['id_deb'] ?>')">+ Upload</button>
              <div id="formUploadDokumentasi<?= $row['id_deb'] ?>" style="display:none" class="mt-2">
                <form action="upload_dokumentasi.php" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="id_deb" value="<?= $row['id_deb'] ?>">
                  <input type="file" name="file_pdf" accept="application/pdf" required>
                  <button type="submit" class="btn btn-success btn-sm mt-1">Simpan</button>
                </form>
              </div>
            <?php endif; ?>
          </td>
          <?php
  $lat = floatval($row['latitude']);
  $lng = floatval($row['longitude']);
  $hasValidCoords = ($lat !== 0.0 && $lng !== 0.0);
?>

<!-- Kolom TOMBOL LIHAT MAP -->
<td class="text-center">
  <?php if ($hasValidCoords): ?>
    <a href="https://www.google.com/maps?q=<?= $lat ?>,<?= $lng ?>" 
       target="_blank" 
       class="btn btn-sm btn-outline-info">
      <i class="fas fa-map-marker-alt"></i> Lihat
    </a>
  <?php else: ?>
    <span class="text-muted">Belum Ada</span>
  <?php endif; ?>
</td>

<!-- Kolom KOORDINAT / TOMBOL + LOKASI -->
<td class="text-center">
  <?php if (!$hasValidCoords): ?>
    <button type="button" class="btn btn-outline-info btn-sm" onclick="getLocation('<?= $row['id_deb'] ?>')">+ Lokasi</button>
    <form action="simpan_lokasi.php" method="post" id="formLokasi<?= $row['id_deb'] ?>" style="display:none" class="mt-2">
      <input type="hidden" name="id_deb" value="<?= $row['id_deb'] ?>">
      <input type="text" name="latitude" id="lat<?= $row['id_deb'] ?>" class="form-control mb-1" placeholder="Latitude" readonly required>
      <input type="text" name="longitude" id="lng<?= $row['id_deb'] ?>" class="form-control mb-1" placeholder="Longitude" readonly required>
      <button type="submit" class="btn btn-success btn-sm">Simpan</button>
    </form>
  <?php else: ?>
    <span class="text-success">🌍 <?= $lat ?>, <?= $lng ?></span>
  <?php endif; ?>
</td>

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

  function toggleFormUpload(id_deb) {
      const form = document.getElementById("formUploadDokumentasi" + id_deb);
      if (form) {
        form.style.display = (form.style.display === "none" ? "block" : "none");
      }
  }
</script>
<script>
function getLocation(id) {
  if (!navigator.geolocation) {
    alert("Geolocation tidak didukung oleh browser ini.");
    return;
  }

  navigator.geolocation.getCurrentPosition(
    function (position) {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;

      document.getElementById("lat" + id).value = lat.toFixed(6);
      document.getElementById("lng" + id).value = lng.toFixed(6);
      document.getElementById("formLokasi" + id).style.display = "block";
    },
    function (error) {
      alert("Gagal mendapatkan lokasi: " + error.message);
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
}
</script>

<style>
  .btn-upload {
    font-weight: 500;
    padding: 6px 12px;
  }
</style>