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
    return (string)$status === '1'
        ? "<i class='far fa-thumbs-up' style='font-size:15px;color:green'></i>"
        : "<i class='far fa-thumbs-down' style='font-size:15px;color:red'></i>";
}
?>

<!-- External CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
<link href="datatables-responsive/dataTables.responsive.css" rel="stylesheet">

<div class="main-panel">
  <div class="content-wrapper">
    <!-- Welcome Section -->
    <div class="row mb-4">
      <div class="col-12 col-xl-8">
        <h3 class="font-weight-bold">Welcome <?= htmlspecialchars($id_ao) ?></h3>
      </div>
    </div>

    <!-- Judul -->
    <div class="row mb-3">
      <div class="col">
        <h4>Rekapitulasi Aktivitas Penanganan Kredit Bermasalah</h4>
      </div>
    </div>

    <!-- Filter Form -->
    <div class="container">
      <!-- Filter Form -->
<form method="get" action="">
  <input type="hidden" name="id_ao" value="<?= htmlspecialchars($id_ao) ?>">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="form-group">
        <label for="tanggal">Pilih Tanggal</label>
        <input type="date" name="tgl_data" class="form-control" id="tgl_data" required
          value="<?= htmlspecialchars($_GET['tgl_data'] ?? '') ?>">
      </div>
    </div>
  </div>

  <div class="text-center mb-3">
    <button type="submit" class="btn btn-primary btn-custom">Tampilkan</button>
    <a href="?id_ao=<?= $id_ao ?>" class="btn btn-secondary btn-custom">Reset</a>
  </div>
</form>


      <style>
        .btn-custom:hover {
          background-color: #007bff;
          color: #fff;
          transform: scale(1.05);
        }
        .btn-custom {
          margin-right: 5px;
        }
      </style>

<?php
// Inisialisasi variabel
$kode_cabang = $nama_ao = '';
$noa = $bd = $ckpn = $pokok = $bunga = $jml_bayar = 0;
$hasil_kategori = [];
$kategori = [];

$tanggal = $_GET['tgl_data'] ?? '';
if ($id_ao && $tanggal) {
    $bulanTahun = DateTime::createFromFormat('m-Y', $tanggal);
    $bulan = $bulanTahun ? $bulanTahun->format('m') : '';
    $tahun = $bulanTahun ? $bulanTahun->format('Y') : '';

    $queri = "SELECT cabang.kode_cabang, ao.nama_ao,
                SUM(debitur_npl.bd_npl) AS bd,
                SUM(debitur_npl.ckpn) AS ckpn,
                COUNT(debitur_npl.id_deb) AS noa
          FROM cabang
          JOIN ao ON cabang.kode_cabang = ao.kode_cabang
          JOIN debitur_npl ON debitur_npl.id_ao = ao.id_ao
          WHERE ao.id_ao = '$id_ao' 
          AND DATE(debitur_npl.tgl_data) = '$tanggal'
          GROUP BY cabang.kode_cabang, ao.id_ao";

    $hasil = $mysqli->query($queri);
    if ($data = $hasil->fetch_array()) {
        $bd          = $data['bd'];
        $noa         = $data['noa'];
        $ckpn        = $data['ckpn'];
        $nama_ao     = $data['nama_ao'];
        $kode_cabang = $data['kode_cabang'];

        $kategori = [
            ['label' => 'Jumlah Debitur dikonfirmasi baik melalui telpon/WA atau kunjungan langsung (1)', 'field' => 'satu', 'url' => 'rekap_one'],
            ['label' => 'Jumlah Debitur sudah dikunjungi namun alamat tidak sesuai/belum ditemukan tempat domisili yang baru (2)', 'field' => 'dua', 'url' => 'rekap_two'],
            ['label' => 'Jumlah Debitur sudah terkonfirmasi namun belum ada kemampuan janji untuk membayar (3)', 'field' => 'tiga', 'url' => 'rekap_three'],
            ['label' => 'Jumlah Debitur sudah terkonfirmasi sudah ada kemampuan janji untuk membayar (4)', 'field' => 'empat', 'url' => 'rekap_four'],
            ['label' => 'Jumlah Debitur yang sudah realisasi bayar (5)', 'field' => 'lima', 'url' => 'rekap_five'],
        ];

        foreach ($kategori as $item) {
            $query = "SELECT SUM({$item['field']}) AS total 
                      FROM debitur_npl 
                      WHERE id_ao = '$id_ao' 
                      AND DATE(debitur_npl.tgl_data) = '$tanggal'";
            $res = $mysqli->query($query);
            $row = $res->fetch_assoc();
            $hasil_kategori[$item['field']] = $row['total'] ?? 0;
        }

        $query = "SELECT SUM(bayar_pokok) AS pokok, SUM(bayar_bunga) AS bunga 
                  FROM debitur_npl 
                  WHERE id_ao = '$id_ao' 
                  AND DATE(debitur_npl.tgl_data) = '$tanggal'";
        $res = $mysqli->query($query);
        $pembayaran = $res->fetch_assoc();
        $pokok = $pembayaran['pokok'] ?? 0;
        $bunga = $pembayaran['bunga'] ?? 0;
        $jml_bayar = $pokok + $bunga;
    }
}
?>

<?php if ($tanggal): ?>
<div class="table-responsive mt-4">
  <table class="table">
    <tbody>
      <tr><th>Tanggal</th><td><?= date('d-m-Y', strtotime($tanggal)) ?></td><td></td></tr>
      <tr><th>Kode Cabang</th><td><?= $kode_cabang ?></td><td></td></tr>
      <tr><th>Nama Petugas</th><td><?= $nama_ao ?></td><td></td></tr>
      <tr><th>Total Debitur</th><td align="right"><?= number_format($noa) ?></td><td></td></tr>
      <tr><th>Baki Debet NPL</th><td align="right"><?= number_format($bd) ?></td><td></td></tr>
      <tr><th>CKPN</th><td align="right"><?= number_format($ckpn) ?></td><td></td></tr>

      <?php foreach ($kategori as $item): ?>
      <tr>
        <th><?= $item['label'] ?></th>
        <td align="right"><?= number_format($hasil_kategori[$item['field']] ?? 0) ?></td>
        <td><a class="btn btn-sm btn-primary float-end" href="<?= $item['url'] ?>?id_ao=<?= $id_ao ?>&bulan=<?= $tanggal ?>">Lihat Detail</a></td>
      </tr>
      <?php endforeach; ?>

      <tr><th>Jumlah Pembayaran Pokok</th><td align="right"><?= number_format($pokok) ?></td><td></td></tr>
      <tr><th>Jumlah Pembayaran Bunga</th><td align="right"><?= number_format($bunga) ?></td><td></td></tr>
      <tr class="table-success"><th>Total Realisasi Pembayaran</th><td align="right"><?= number_format($jml_bayar) ?></td><td></td></tr>
    </tbody>
  </table>
</div>
<?php endif; ?>
</div>

<?php include("footer.php"); ?>

<!-- DataTables JS -->
<script src="datatables/js/jquery.dataTables.min.js"></script>
<script src="datatables-plugins/dataTables.bootstrap.min.js"></script>
<script src="datatables-responsive/dataTables.responsive.js"></script>

<script>
$(document).ready(function() {
  $('#dataTables-example').DataTable({ responsive: true });
});
</script>

<style>
  .btn-upload {
    font-weight: 500;
    padding: 6px 12px;
  }
</style>
