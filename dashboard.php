<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin' || $user_role == 'user') {
$page = "Dashboard";
require_once('template/header.php');

?>

<div class="mb-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-home"></i> Dashboard</h1>
    </div>

	<?php
	if($user_role == 'admin') {
	?>
	
    <!-- Content Row -->
   
    <div class="row">

    <div class="col-xl-12 col-md-12 mb-12">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <h1>Kelompok Tani Tunas Baru</h1><br>
                                <h4>merupakan komunitas petani yang fokus pada budidaya buah naga merah. Dengan semangat kebersamaan dan inovasi, kami menggunakan Sistem Pendukung Keputusan (SPK) untuk membantu memilih buah naga berkualitas tinggi secara lebih cepat dan tepat.</h4>
                                <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3989.571119453012!2d100.6218694745998!3d-0.6378055993559588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMMKwMzgnMTYuMSJTIDEwMMKwMzcnMjguMCJF!5e0!3m2!1sid!2sid!4v1750078579567!5m2!1sid!2sid" width="900" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

		

      
		
		
    </div>
	<?php
	}elseif($user_role == 'user') {
	?>
	<!-- Content Row -->
   
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><a href="index.php" class="text-secondary text-decoration-none">Dashboard</a></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-home fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><a href="hasil.php" class="text-secondary text-decoration-none">Data Hasil Akhir</a></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-area fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><a href="list-profile.php" class="text-secondary text-decoration-none">Data Profile</a></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-area fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div>
	<?php
	}
	?>
</div>

<?php
require_once('template/footer.php');
}else {
	header('Location: login.php');
}
?>
