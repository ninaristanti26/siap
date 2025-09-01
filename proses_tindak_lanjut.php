<?php
include "koneksi.php";

$norek = $_POST['norek'] ?? '';
$catatan = $_POST['catatan'] ?? '';

if (empty($norek) || empty($catatan)) {
    echo "Data tidak lengkap.";
    exit;
}

// Simpan ke tabel `tindak_lanjut_kasie`, buat jika belum ada
$stmt = $mysqli->prepare("INSERT INTO tindak_lanjut_kasie (norek, catatan, tgl_input) VALUES (?, ?, NOW())");
$stmt->bind_param("ss", $norek, $catatan);

if ($stmt->execute()) {
    echo "Tindak lanjut berhasil disimpan.";
} else {
    echo "Terjadi kesalahan saat menyimpan.";
}
?>
