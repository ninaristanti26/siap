<?php 
 
$mysqli = mysqli_connect("localhost","root","","bprsukab_pipeline");
 
// Check connection
if (mysqli_connect_errno()){
	echo "Koneksi database gagal : " . mysqli_connect_error();
}
 
?>