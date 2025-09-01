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
        SELECT * 
        FROM cabang
        JOIN ao ON cabang.kode_cabang = ao.kode_cabang
        JOIN debitur_npl ON debitur_npl.id_ao = ao.id_ao
        AND debitur_npl.kode_cabang = cabang.kode_cabang
        WHERE debitur_npl.id_deb = ?
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
