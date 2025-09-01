<?php 
include("koneksi.php");
    $query ="SELECT * FROM jabatan";
    $result = $mysqli->query($query);
    if($result->num_rows> 0){
    	$options= mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
?>