<?php
//session_start();
include("koneksi.php");
$id_ao     = $_SESSION['id_ao'] ?? null;
$id_deb_hb = $_GET['id_deb_hb'] ?? null;

if ($id_deb_hb) {
    $stmt = $mysqli->prepare("
        SELECT * 
        FROM ao
        JOIN debitur_hb ON debitur_hb.id_ao = ao.id_ao
        WHERE debitur_hb.id_deb_hb = ?
    ");
    
    if ($stmt) {
        $stmt->bind_param("i", $id_deb_hb);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $options = $result->fetch_all(MYSQLI_ASSOC);
            } else {
                $options = [];
                echo "Tidak ada data untuk ID Debitur ini.";
            }
        } else {
            echo "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $mysqli->error;
    }
} else {
    echo "ID Debitur tidak valid.";
}
$mysqli->close();
?>
