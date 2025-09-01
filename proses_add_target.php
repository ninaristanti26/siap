<?php
session_start();
include("koneksi.php");
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

if (isset($_POST['Submit']) && $_POST['Submit'] === "Submit") {
    $id_ao = $_POST['id_ao'];
    $tahun = $_POST['tahun'];
   
    function clean_number($number) {
        return floatval(str_replace('.', '', $number));
    }

    $target = clean_number($_POST['target']);
    
    // Siapkan prepared statement
    $query = "INSERT INTO target_ao (
                                     id_ao,
                                     tahun,
                                     target
                ) VALUES (?, ?, ?)";

    $stmt = $mysqli->prepare($query);

    if ($stmt === false) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Bind parameter (s = string, d = double, i = integer)
    $stmt->bind_param(
        "ssi",
        $id_ao,
        $tahun,
        $target
    );

    // Eksekusi query
    if ($stmt->execute()) {
        $encoded_kode_cabang = isset($_SESSION['kode_cabang']) ? urlencode($_SESSION['kode_cabang']) : '';
        header("Location: target_ao?kode_cabang=" . $encoded_kode_cabang);
        exit;
    } else {
        echo "Tambah Data Gagal: " . $stmt->error;
    }

    $stmt->close();
}
?>