<?php 
//session_start();
include("koneksi.php");
//include_once(__DIR__ . "/../functions.php");

// Ambil id_ao dari session
$id_ao = $_SESSION['id_ao'] ?? null;

// Ambil id_deb dari GET dan sanitasi
$id_deb = $_GET['id_deb'] ?? null;

if ($id_deb) {
    // Gunakan prepared statement untuk menghindari SQL Injection
    $stmt = $mysqli->prepare("
            SELECT *, 
                (SELECT SUM(bd_npl) as bd_npl1
                 FROM debitur_npl, rekap 
                 WHERE debitur_npl.id_deb=rekap.id_deb 
                 AND ket='Naik Ke Kolek 2') AS bd_npl1,
                 (SELECT SUM(bayar_pokok) as bayar_pokok1
                 FROM debitur_npl, rekap 
                 WHERE debitur_npl.id_deb=rekap.id_deb 
                 AND ket='Naik Ke Kolek 2') AS bayar_pokok1
            FROM rekap, debitur_npl
            WHERE rekap.id_deb=debitur_npl.id_deb 
            AND debitur_npl.id_deb= ?
    ");
    
    if ($stmt) {
        // Bind parameter
        $stmt->bind_param("i", $id_deb);
        
        // Eksekusi query
        if ($stmt->execute()) {
            // Ambil hasil
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $options = $result->fetch_all(MYSQLI_ASSOC);
            } else {
                $options = []; // Kosongkan array jika tidak ada data
                echo "Tidak ada data untuk ID Debitur ini.";
            }
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        // Tutup statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $mysqli->error;
    }
} else {
    echo "ID Debitur tidak valid.";
}

// Tutup koneksi jika tidak dibutuhkan lagi
$mysqli->close();
?>
