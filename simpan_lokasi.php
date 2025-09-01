<?php
session_start();
include "koneksi.php";

$id_deb = $_POST['id_deb'] ?? '';
$latitude = $_POST['latitude'] ?? '';
$longitude = $_POST['longitude'] ?? '';

if (empty($id_deb) || empty($latitude) || empty($longitude)) {
    echo "<script>alert('Data tidak lengkap.'); window.history.back();</script>";
    exit;
}

if (!is_numeric($latitude) || !is_numeric($longitude)) {
    echo "<script>alert('Latitude dan Longitude harus berupa angka.'); window.history.back();</script>";
    exit;
}

$stmt = $mysqli->prepare("UPDATE debitur_npl SET latitude = ?, longitude = ? WHERE id_deb = ?");
$stmt->bind_param("ddi", $latitude, $longitude, $id_deb);

if ($stmt->execute()) {
    echo "<script>alert('Lokasi berhasil disimpan.'); window.history.back();</script>";
} else {
    echo "<script>alert('Gagal menyimpan lokasi.'); window.history.back();</script>";
}

$stmt->close();
$mysqli->close();
?>
