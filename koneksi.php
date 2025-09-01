<?php 
 
$mysqli = mysqli_connect("localhost","bprsukab_pipeline","putrisaljudan7kurcaci","bprsukab_pipeline");
 
// Check connection
if (mysqli_connect_errno()){
	echo "Koneksi database gagal : " . mysqli_connect_error();
}
 
?>