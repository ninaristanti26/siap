<?php 
include("koneksi.php");
$id = $_GET['kode_cabang'];
    $query ="SELECT * FROM ao, cabang
             WHERE cabang.kode_cabang=ao.kode_cabang
             AND ao.id_jabatan = 1
             AND cabang.kode_cabang='".$id."'";
    $result = $mysqli->query($query);
    if($result->num_rows> 0){
    	$options= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
?>