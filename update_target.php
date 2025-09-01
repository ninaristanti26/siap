<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_target = $_POST['id_target'];
    $tahun     = $_POST['tahun'];
    $target    = $_POST['target'];

    $query = $mysqli->prepare("UPDATE target_ao SET tahun = ?, target = ? WHERE id_target = ?");
    $query->bind_param("sii", $tahun, $target, $id_target);
    $query->execute();

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
?>
