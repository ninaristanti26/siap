<?php
session_start();
include("koneksi.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['Submit']) && $_POST['Submit'] === "Submit") {
    $kode_cabang           = $_POST['kode_cabang'];
    $id_ao                 = $_POST['id_ao'];
    $tgl_data              = $_POST['tgl_data'];
    $norek                 = $_POST['norek'];
    $kolek                 = $_POST['kolek'];
    $nama_debitur          = $_POST['nama_debitur'];
    $alamat_npl            = $_POST['alamat_npl'];

    function clean_number($number) {
        return floatval(str_replace('.', '', $number));
    }

    $plafon_npl            = clean_number($_POST['plafon_npl']);
    $bd_npl                = clean_number($_POST['bd_npl']);
    $ckpn                  = clean_number($_POST['ckpn']);
    $bayar_pokok           = clean_number($_POST['bayar_pokok']);
    $bayar_bunga           = clean_number($_POST['bayar_bunga']);

    $penyebab_masalah      = $_POST['penyebab_masalah'];
    $strategi_penyelesaian = $_POST['strategi_penyelesaian'];
    $alasan_strategi       = $_POST['alasan_strategi'];
    $satu                  = intval($_POST['satu']);
    $dua                   = intval($_POST['dua']);
    $tiga                  = intval($_POST['tiga']);
    $empat                 = intval($_POST['empat']);
    $lima                  = intval($_POST['lima']);

    // ✅ Validasi default 'masih kosong'
    $file_dokumentasi = 'masih kosong';
    $latitude         = '0.0';
    $longitude        = '0.0';

    // Query lengkap
    $query = "INSERT INTO debitur_npl (
        kode_cabang,
        id_ao,
        tgl_data,
        norek,
        kolek,
        nama_debitur,
        alamat_npl,
        plafon_npl,
        bd_npl,
        ckpn,
        penyebab_masalah,
        strategi_penyelesaian,
        alasan_strategi,
        satu,
        dua,
        tiga,
        empat,
        lima,
        bayar_pokok,
        bayar_bunga,
        file_dokumentasi,
        latitude, 
        longitude
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($query);

    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Bind parameter (s = string, d = double, i = integer)
    $stmt->bind_param(
        "sssissssddssssiiiddssss",
        $kode_cabang,
        $id_ao,
        $tgl_data,
        $norek,
        $kolek,
        $nama_debitur,
        $alamat_npl,
        $plafon_npl,
        $bd_npl,
        $ckpn,
        $penyebab_masalah,
        $strategi_penyelesaian,
        $alasan_strategi,
        $satu,
        $dua,
        $tiga,
        $empat,
        $lima,
        $bayar_pokok,
        $bayar_bunga,
        $file_dokumentasi,
        $latitude,
        $longitude
    );

    if ($stmt->execute()) {
        $encoded_id_ao = isset($_SESSION['id_ao']) ? urlencode($_SESSION['id_ao']) : '';
        header("Location: debitur?id_ao=" . $encoded_id_ao);
        exit;
    } else {
        echo "Tambah Data Gagal: " . $stmt->error;
    }

    $stmt->close();
}
?>