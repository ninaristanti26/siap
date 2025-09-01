<?php 
//session_start();
include("koneksi.php");

$id_ao = isset($_SESSION['id_ao']) ? $_SESSION['id_ao'] : null;
$kode_cabang = isset($_GET['kode_cabang']) ? $_GET['kode_cabang'] : null;

if ($id_ao) {
    $stmt = $mysqli->prepare("
        SELECT * FROM cabang 
        JOIN ao ON cabang.kode_cabang = ao.kode_cabang 
        JOIN jabatan ON ao.id_jabatan = jabatan.id_jabatan 
        WHERE ao.id_ao = ?
    ");
    
    if ($stmt) {
        $stmt->bind_param("s", $id_ao);
        $stmt->execute();
        $result  = $stmt->get_result() or die($mysqli->error);

        if ($result && $result->num_rows > 0) {
            $options = $result->fetch_all(MYSQLI_ASSOC);
            // Bisa digunakan untuk menampilkan atau proses lebih lanjut
        } else {
            echo "Belum Ada Data";
        }

        $stmt->close();
    } else {
        echo "Query preparation failed: " . $mysqli->error;
    }
} else {
    echo "ID AO tidak tersedia di sesi.";
}
?>