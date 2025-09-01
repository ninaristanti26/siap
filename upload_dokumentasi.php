<?php
session_start();
include "koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_pdf'])) {
    $id_deb = $_POST['id_deb'];
    $file   = $_FILES['file_pdf'];

    $allowedExt = ['pdf'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (in_array($fileExt, $allowedExt) && $file['size'] <= 2 * 1024 * 1024) { // max 2MB
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $filename = "dok_{$id_deb}_" . time() . ".pdf";
        $targetFile = $targetDir . basename($filename);

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Simpan informasi file ke database (opsional)
            $stmt = $mysqli->prepare("UPDATE debitur_npl SET file_dokumentasi = ? WHERE id_deb = ?");
            $stmt->bind_param("ss", $filename, $id_deb);
            $stmt->execute();

            echo "<script>alert('Upload berhasil'); window.location.href='pipeline.php';</script>";
        } else {
            echo "Upload gagal.";
        }
    } else {
        echo "File tidak valid atau terlalu besar (maks 2MB, PDF saja).";
    }
}
?>
