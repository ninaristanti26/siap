<?php
ob_start();
session_start();
include "koneksi.php";
include("atas_ao.php");

if (isset($_GET['id_ao'])) {
    $_SESSION['id_ao'] = $_GET['id_ao'];
}
$id_ao = isset($_SESSION['id_ao']) ? $_SESSION['id_ao'] : '';
//include("functions.php");
function renderStatusIcon($status) {
    return $status == '1'
        ? "<i class='far fa-thumbs-up' style='font-size:15px;color:green'></i>"
        : "<i class='far fa-thumbs-down' style='font-size:15px;color:red'></i>";
}
?>
      <!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">Welcome <?php echo $_SESSION['id_ao']; ?></h3>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="justify-content-end d-flex"></div>
                </div>
              </div>
            </div>
        </div>
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card tale-bg">
            <div class="card-people mt-auto"> <img src="images/people.png" alt="people">
                <div class="weather-info">
                    <div class="d-flex">
                      <div class="ml-2">
                        <h4 class="location font-weight-normal">Indonesia</h4>
                        <h6 class="font-weight-normal"><i class="icon-calendar mr-2"></i><span id="tanggal-hari-ini">--</span></h6>
                      </div>
                      <div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
$kode_cabang = $nama_ao = '';
$noa = $bd = $ckpn = $pokok = $bunga = $jml_bayar = 0;
$hasil_kategori = [];
$kategori = [];

if ($id_ao) {
    $queri = "SELECT cabang.kode_cabang, ao.nama_ao,
                    SUM(debitur_npl.bd_npl) AS bd,
                    SUM(debitur_npl.ckpn) AS ckpn,
                    COUNT(debitur_npl.id_deb) AS noa
              FROM cabang
              JOIN ao ON cabang.kode_cabang = ao.kode_cabang
              JOIN debitur_npl ON debitur_npl.id_ao = ao.id_ao
              WHERE ao.id_ao = '$id_ao' 
              GROUP BY cabang.kode_cabang, ao.id_ao";

    $hasil = $mysqli->query($queri);
    if ($data = $hasil->fetch_array()) {
        $bd = $data['bd'];
        $noa = $data['noa'];
        $ckpn = $data['ckpn'];
        $nama_ao = $data['nama_ao'];
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
                      WHERE id_ao = '$id_ao' ";
            $res = $mysqli->query($query);
            $row = $res->fetch_assoc();
            $hasil_kategori[$item['field']] = $row['total'] ?? 0;
        }

        $query = "SELECT SUM(bayar_pokok) AS pokok, SUM(bayar_bunga) AS bunga 
                  FROM debitur_npl 
                  WHERE id_ao = '$id_ao'";
        $res = $mysqli->query($query);
        $pembayaran = $res->fetch_assoc();
        $pokok = $pembayaran['pokok'] ?? 0;
        $bunga = $pembayaran['bunga'] ?? 0;
        $jml_bayar = $pokok + $bunga;
    }
}
?>
    <div class="col-md-6 grid-margin transparent">
        <div class="row">
            <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card card-tale">
                    <div class="card-body">
                      <p class="mb-4">Jumlah Debitur dikonfirmasi baik melalui telpon/WA atau kunjungan langsung</p>
                      <p class="fs-30 mb-2"><?php echo $hasil_kategori['satu'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card card-dark-blue">
                    <div class="card-body">
                      <p class="mb-4">Jumlah Debitur sudah dikunjungi namun debitur pindah alamat dan belum ditemukan alamat yang baru</p>
                      <p class="fs-30 mb-2"><?php echo $hasil_kategori['dua'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                <div class="card card-light-blue">
                    <div class="card-body">
                      <p class="mb-4">Jumlah Debitur sudah terkonfirmasi dan sudah ada kemampuan janji untuk membayar</p>
                      <p class="fs-30 mb-2"><?php echo $hasil_kategori['empat'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        <div class="col-md-6 stretch-card transparent">
            <div class="card card-light-danger">
                <div class="card-body">
                    <p class="mb-4">Jumlah Realisasi pembayaran dari debitur</p>
                    <p class="fs-30 mb-2"><?php echo $hasil_kategori['lima'] ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
<div class="row">
<div class="col-md-6 grid-margin stretch-card"> </div>            
</div>
<?php 
include("footer.php");
?>