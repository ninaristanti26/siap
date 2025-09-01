<?php
ob_start();
session_start();
include "koneksi.php";
include("atas_pusat.php");

// Ambil id_ao dari GET dan simpan ke session
if (isset($_GET['id_ao'])) {
    $_SESSION['id_ao'] = $_GET['id_ao'];
}
$id_ao = $_SESSION['id_ao'] ?? '';

// Fungsi ikon status
function renderStatusIcon($status) {
    return $status == '1'
        ? "<i class='far fa-thumbs-up' style='font-size:15px;color:green'></i>"
        : "<i class='far fa-thumbs-down' style='font-size:15px;color:red'></i>";
}
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-md-12 grid-margin">
        <div class="row">
          <div class="col-12 col-xl-8 mb-4 mb-xl-0">
            <h3 class="font-weight-bold">Welcome <?= htmlspecialchars($id_ao ?: 'AO') ?></h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Card AO & Info Tanggal -->
    <div class="row">
      <div class="col-md-6 grid-margin stretch-card">
        <div class="card tale-bg">
          <div class="card-people mt-auto">
            <img src="images/people.png" alt="people">
            <div class="weather-info">
              <div class="d-flex">
                <div class="ml-2">
                  <h4 class="location font-weight-normal">Indonesia</h4>
                  <h6 class="font-weight-normal"><i class="icon-calendar mr-2"></i><?= date('d-m-Y') ?></h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

<?php
// Inisialisasi nilai default
$bd = $ckpn = $noa = $pokok = $bunga = $jml_bayar = 0;
$nama_ao = $kode_cabang = '';
$hasil_kategori = [];

$kategori = [
    'satu'  => 'Jumlah Debitur dikonfirmasi baik melalui telpon/WA atau kunjungan langsung',
    'dua'   => 'Jumlah Debitur sudah dikunjungi namun alamat tidak sesuai/belum ditemukan tempat domisili yang baru',
    'tiga'  => 'Jumlah Debitur sudah terkonfirmasi namun belum ada kemampuan janji untuk membayar',
    'empat' => 'Jumlah Debitur sudah terkonfirmasi sudah ada kemampuan janji untuk membayar',
    'lima'  => 'Jumlah Debitur yang sudah realisasi bayar',
];

if (!empty($id_ao)) {
    // Ambil info AO & kode_cabang
    $queryAO = "
        SELECT ao.nama_ao, cabang.kode_cabang
        FROM ao
        JOIN cabang ON ao.kode_cabang = cabang.kode_cabang
        WHERE ao.id_ao = ?
    ";
    $stmtAO = $mysqli->prepare($queryAO);
    $stmtAO->bind_param("s", $id_ao);
    $stmtAO->execute();
    $resultAO = $stmtAO->get_result();
    if ($row = $resultAO->fetch_assoc()) {
        $nama_ao = $row['nama_ao'];
        $kode_cabang = $row['kode_cabang'];
    }

    if ($kode_cabang) {
        // Ambil total BD, CKPN, dan NOA berdasarkan kode_cabang
        $querySummary = "
            SELECT SUM(bd_npl) AS bd, SUM(ckpn) AS ckpn, COUNT(id_deb) AS noa
            FROM debitur_npl
            WHERE kode_cabang = ?
        ";
        $stmt = $mysqli->prepare($querySummary);
        $stmt->bind_param("s", $kode_cabang);
        $stmt->execute();
        $summary = $stmt->get_result()->fetch_assoc();

        $bd = $summary['bd'] ?? 0;
        $ckpn = $summary['ckpn'] ?? 0;
        $noa = $summary['noa'] ?? 0;

        // Ambil total bayar pokok & bunga
        $queryBayar = "
            SELECT SUM(bayar_pokok) AS pokok, SUM(bayar_bunga) AS bunga
            FROM debitur_npl
            WHERE kode_cabang = ?
        ";
        $stmt = $mysqli->prepare($queryBayar);
        $stmt->bind_param("s", $kode_cabang);
        $stmt->execute();
        $bayar = $stmt->get_result()->fetch_assoc();

        $pokok = $bayar['pokok'] ?? 0;
        $bunga = $bayar['bunga'] ?? 0;
        $jml_bayar = $pokok + $bunga;

        // Hitung kategori 1–5
        foreach ($kategori as $field => $label) {
            $queryKategori = "SELECT SUM($field) AS total FROM debitur_npl WHERE kode_cabang = ?";
            $stmt = $mysqli->prepare($queryKategori);
            $stmt->bind_param("s", $kode_cabang);
            $stmt->execute();
            $hasil = $stmt->get_result()->fetch_assoc();
            $hasil_kategori[$field] = $hasil['total'] ?? 0;
        }
    }
}
?>
      <!-- CARD STATISTIK -->
      <div class="col-md-6 grid-margin transparent">
        <div class="row">
          <?php foreach ($kategori as $key => $label): ?>
          <div class="col-md-6 mb-4 stretch-card transparent">
            <div class="card card-<?php
                switch ($key) {
                    case 'satu': echo 'tale'; break;
                    case 'dua': echo 'dark-blue'; break;
                    case 'tiga': echo 'info'; break;
                    case 'empat': echo 'light-blue'; break;
                    case 'lima': echo 'light-danger'; break;
                    default: echo 'light'; break;
                }
            ?>">
              <div class="card-body">
                <p class="mb-4"><?= $label ?></p>
                <p class="fs-30 mb-2"><?= number_format($hasil_kategori[$key] ?? 0) ?></p>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

<?php include("footer.php"); ?>
