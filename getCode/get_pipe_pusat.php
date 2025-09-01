<?php 
include("koneksi.php");
$id_ao          =   @$_SESSION['id_ao'];
$kode_cabang    =   @$_GET['kode_cabang'];
//$_SESSION['id_ao']       = @$id_ao;
//$_SESSION['kode_cabang'] = @$kode_cabang;

   $query ="SELECT * FROM cabang, ao, jabatan
            WHERE cabang.kode_cabang=ao.kode_cabang
            AND ao.id_jabatan=jabatan.id_jabatan
            GROUP BY ao.id_ao'";
    $result = $mysqli->query($query);
    if($result->num_rows> 0){
    	$options= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }else{
        "Belum Ada Data";
    } 
?>