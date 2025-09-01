<?php
ob_start();
session_start();
include "koneksi.php";
include "atas_kasie.php";

// Ambil kode cabang dari session
if (isset($_GET['kode_cabang'])) {
    $_SESSION['kode_cabang'] = $_GET['kode_cabang'];
}
$kode_cabang = $_SESSION['kode_cabang'] ?? '';

// Ambil input tahun
$tahun = isset($_GET['tahun']) ? intval($_GET['tahun']) : '';

$kategori = [
    ['label' => 'Jumlah Debitur dikonfirmasi baik melalui telpon/WA atau kunjungan langsung (1)', 'field' => 'satu', 'url' => 'rekap_one_kasie'],
    ['label' => 'Jumlah Debitur sudah dikunjungi namun alamat tidak sesuai/belum ditemukan tempat domisili yang baru (2)', 'field' => 'dua', 'url' => 'rekap_two_kasie'],
    ['label' => 'Jumlah Debitur sudah terkonfirmasi namun belum ada kemampuan janji untuk membayar (3)', 'field' => 'tiga', 'url' => 'rekap_three_kasie'],
    ['label' => 'Jumlah Debitur sudah terkonfirmasi sudah ada kemampuan janji untuk membayar (4)', 'field' => 'empat', 'url' => 'rekap_four_kasie'],
    ['label' => 'Jumlah Debitur yang sudah realisasi bayar (5)', 'field' => 'lima', 'url' => 'rekap_five_kasie'],
];
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row mb-3">
      <div class="col">
        <h4>Laporan Tahunan Penanganan Kredit Bermasalah Cabang <?= htmlspecialchars($kode_cabang) ?></h4>
      </div>
    </div>

    <div class="container">
      <form method="get" action="">
        <input type="hidden" name="kode_cabang" value="<?= htmlspecialchars($kode_cabang) ?>">
        <div class="form-group text-center">
          <label for="tahun">Pilih Tahun</label>
          <select name="tahun" class="form-control w-50 mx-auto text-center" required>
            <option value="">-- Pilih Tahun --</option>
            <?php
            $query_tahun = "SELECT DISTINCT YEAR(tgl_data) AS tahun FROM debitur_npl ORDER BY tahun DESC";
            $result_tahun = $mysqli->query($query_tahun);
            while ($row = $result_tahun->fetch_assoc()) {
                $selected = ($tahun == $row['tahun']) ? "selected" : "";
                echo "<option value='{$row['tahun']}' $selected>{$row['tahun']}</option>";
            }
            ?>
          </select>
          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <a href="?kode_cabang=<?= htmlspecialchars($kode_cabang) ?>" class="btn btn-secondary">Reset</a>
          </div>
        </div>
      </form>

<?php
if ($kode_cabang && $tahun) {
    $query = "SELECT 
                SUM(dn.bd_npl) AS bd,
                SUM(dn.ckpn) AS ckpn,
                COUNT(dn.id_deb) AS noa
              FROM cabang c
              JOIN ao ON c.kode_cabang = ao.kode_cabang
              JOIN debitur_npl dn ON dn.id_ao = ao.id_ao
              WHERE c.kode_cabang = '$kode_cabang'
                AND YEAR(dn.tgl_data) = '$tahun'";
    $hasil = $mysqli->query($query);
    $data = $hasil->fetch_assoc();

    $bd = $data['bd'] ?? 0;
    $ckpn = $data['ckpn'] ?? 0;
    $noa = $data['noa'] ?? 0;

    $hasil_kategori = [];
    foreach ($kategori as $item) {
        $field = $item['field'];
        $q = "SELECT SUM($field) AS total 
              FROM debitur_npl 
              WHERE kode_cabang = '$kode_cabang'
                AND YEAR(tgl_data) = '$tahun'";
        $r = $mysqli->query($q);
        $hasil_kategori[$field] = $r->fetch_assoc()['total'] ?? 0;
    }

    $query_bayar = "SELECT 
                      SUM(bayar_pokok) AS pokok,
                      SUM(bayar_bunga) AS bunga
                    FROM debitur_npl
                    WHERE kode_cabang = '$kode_cabang'
                      AND YEAR(tgl_data) = '$tahun'";
    $res_bayar = $mysqli->query($query_bayar);
    $bayar = $res_bayar->fetch_assoc();
    $pokok = $bayar['pokok'] ?? 0;
    $bunga = $bayar['bunga'] ?? 0;
    $jml_bayar = $pokok + $bunga;
}
?>

<?php if ($kode_cabang && $tahun): ?>
<div class="table-responsive mt-4">
  <table class="table">
    <tbody>
      <tr>
        <th>Tahun</th>
        <td><?= htmlspecialchars($tahun) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>Kode Cabang</th>
        <td><?= htmlspecialchars($kode_cabang) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>Total Debitur</th>
        <td class="text-end"><?= number_format($noa) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>Baki Debet NPL</th>
        <td class="text-end"><?= number_format($bd) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>CKPN</th>
        <td class="text-end"><?= number_format($ckpn) ?></td>
        <td></td>
      </tr>

      <?php foreach ($kategori as $item): ?>
      <tr>
        <th><?= htmlspecialchars($item['label']) ?></th>
        <td class="text-end"><?= number_format($hasil_kategori[$item['field']] ?? 0) ?></td>
        <td>
          <a class="btn btn-sm btn-primary float-end"
             href="<?= $item['url'] ?>?kode_cabang=<?= $kode_cabang ?>&tahun=<?= $tahun ?>">Lihat Detail</a>
        </td>
      </tr>
      <?php endforeach; ?>

      <tr>
        <th>Jumlah Pembayaran Pokok</th>
        <td class="text-end"><?= number_format($pokok) ?></td>
        <td></td>
      </tr>
      <tr>
        <th>Jumlah Pembayaran Bunga</th>
        <td class="text-end"><?= number_format($bunga) ?></td>
        <td></td>
      </tr>
      <tr class="table-success">
        <th>Total Realisasi Pembayaran</th>
        <td class="text-end"><?= number_format($jml_bayar) ?></td>
        <td></td>
      </tr>
    </tbody>
  </table>
</div>
<?php endif; ?>
</div>

<?php include("footer.php"); ?>
