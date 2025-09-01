<?php
session_start();
include "koneksi.php";

// Validasi input
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Siapkan dan jalankan query
    $query = $mysqli->prepare("DELETE FROM target_ao WHERE id_target = ?");
    $query->bind_param("i", $id);

    if ($query->execute()) {
        // Redirect dengan kode_cabang setelah sukses hapus
        header("Location: target_ao.php?kode_cabang=" . urlencode($_SESSION['kode_cabang']));
        exit;
    } else {
        echo "Gagal menghapus data.";
    }
} else {
    echo "ID tidak valid.";
}
?>
