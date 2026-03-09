<?php 
$koneksi = mysqli_connect("localhost","root","","spk_maut");
// $koneksi = mysqli_connect("localhost:3308","root","","spk_maut");
 
// Check connection
if (mysqli_connect_errno()){
	echo "Koneksi database gagal : " . mysqli_connect_error();
}

?>
 