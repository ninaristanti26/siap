<?php
ob_start();
session_start();
include "koneksi.php";
include "atas_kasie.php";

// Set session kode_cabang jika dikirim lewat GET
if (isset($_GET['kode_cabang'])) {
    $_SESSION['kode_cabang'] = $_GET['kode_cabang'];
}

$kode_cabang = $_SESSION['kode_cabang'] ?? '';
$tanggal = $_GET['tgl_data'] ?? '';

$kode_cabang = mysqli_real_escape_string($mysqli, $kode_cabang);
$tanggal = mysqli_real_escape_string($mysqli, $tanggal);

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
    <div class="row mb-3">
      <div class="col">
        <h4>Rekapitulasi Aktivitas Penanganan Kredit Bermasalah Cabang <?= htmlspecialchars($kode_cabang) ?></h4>
      </div>
    </div>

    <div class="container">
      <form method="get" action="">
        <input type="hidden" name="kode_cabang" value="<?= htmlspecialchars($kode_cabang) ?>">
        <div class="form-group text-center">
          <label for="tgl_data">Pilih Tanggal Data</label>
          <input type="date" name="tgl_data" class="form-control w-50 mx-auto text-center" id="tgl_data" value="<?= htmlspecialchars($tanggal) ?>" required>
          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <a href="?kode_cabang=<?= htmlspecialchars($kode_cabang) ?>" class="btn btn-secondary">Reset</a>
          </div>
        </div>
      </form>

<?php
// Inisialisasi variabel default
$noa = $bd = $ckpn = $pokok = $bunga = $jml_bayar = 0;
$hasil_kategori = [];
$kategori = [];
$nama_ao = '-';

if ($kode_cabang && $tanggal) {
    // Pastikan tanggal dalam format Y-m-d
    $tanggal_valid = date('Y-m-d', strtotime($tanggal));

    $queri = "SELECT c.kode_cabang,
                 SUM(dn.bd_npl) AS bd,
                 SUM(dn.ckpn) AS ckpn,
                 COUNT(dn.id_deb) AS noa
          FROM cabang c
          JOIN ao ON c.kode_cabang = ao.kode_cabang
          JOIN debitur_npl dn ON dn.id_ao = ao.id_ao
          WHERE c.kode_cabang = '$kode_cabang'
            AND DATE(dn.tgl_data) = '$tanggal_valid'
          GROUP BY c.kode_cabang";

    $hasil = $mysqli->query($queri);

    if (!$hasil) {
        echo "<div class='alert alert-danger'>Query Error: " . $mysqli->error . "</div>";
    } elseif ($hasil->num_rows == 0) {
        echo "<div class='alert alert-warning'>Tidak ada data untuk tanggal tersebut.</div>";
    } else {
        $data    = $hasil->fetch_assoc();
        $bd      = $data['bd'] ?? 0;
        $noa     = $data['noa'] ?? 0;
        $ckpn    = $data['ckpn'] ?? 0;
        $nama_ao = $data['nama_ao'] ?? '';

        $kategori = [
            ['label' => 'Jumlah Debitur dikonfirmasi baik melalui telpon/WA atau kunjungan langsung (1)', 'field' => 'satu', 'url' => 'rekap_one_kasie'],
            ['label' => 'Jumlah Debitur sudah dikunjungi namun alamat tidak sesuai/belum ditemukan tempat domisili yang baru (2)', 'field' => 'dua', 'url' => 'rekap_two_kasie'],
            ['label' => 'Jumlah Debitur sudah terkonfirmasi namun belum ada kemampuan janji untuk membayar (3)', 'field' => 'tiga', 'url' => 'rekap_three_kasie'],
            ['label' => 'Jumlah Debitur sudah terkonfirmasi sudah ada kemampuan janji untuk membayar (4)', 'field' => 'empat', 'url' => 'rekap_four_kasie'],
            ['label' => 'Jumlah Debitur yang sudah realisasi bayar (5)', 'field' => 'lima', 'url' => 'rekap_five_kasie'],
        ];

        foreach ($kategori as $item) {
            $field = $item['field'];
            $query = "SELECT SUM($field) AS total 
                      FROM debitur_npl 
                      WHERE kode_cabang = '$kode_cabang' 
                        AND DATE(tgl_data) = '$tanggal_valid'";
            $res = $mysqli->query($query);
            $row = $res->fetch_assoc();
            $hasil_kategori[$field] = $row['total'] ?? 0;
        }

        $query = "SELECT SUM(bayar_pokok) AS pokok, SUM(bayar_bunga) AS bunga
                  FROM debitur_npl
                  WHERE kode_cabang = '$kode_cabang' 
                    AND DATE(tgl_data) = '$tanggal_valid'";
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
      <tr>
        <th>Tanggal</th>
        <td><?= htmlspecialchars(date('d-m-Y', strtotime($tanggal))) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>Kode Cabang</th>
        <td><?= htmlspecialchars($kode_cabang) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>Total Debitur</th>
        <td class="text-right"><?= number_format($noa) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>Baki Debet NPL</th>
        <td class="text-right"><?= number_format($bd) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>CKPN</th>
        <td class="text-right"><?= number_format($ckpn) ?></td>
        <td></td>
      </tr>

      <?php foreach ($kategori as $item): ?>
      <tr>
        <th><?= htmlspecialchars($item['label']) ?></th>
        <td class="text-right"><?= number_format($hasil_kategori[$item['field']] ?? 0) ?></td>
        <td>
          <a class="btn btn-sm btn-primary float-end" href="<?= $item['url'] ?>?kode_cabang=<?= $kode_cabang ?>&tanggal=<?= $tanggal ?>">Lihat Detail</a>
        </td>
      </tr>
<?php endforeach; ?>
      <tr>
        <th>Jumlah Pembayaran Pokok</th>
        <td class="text-right"><?= number_format($pokok) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>Jumlah Pembayaran Bunga</th>
        <td class="text-right"><?= number_format($bunga) ?></td>
        <td></td>
      </tr>
      <tr class="table-success">
        <th>Total Realisasi Pembayaran</th>
        <td class="text-right"><?= number_format($jml_bayar) ?></td>
        <td></td>
      </tr>
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
