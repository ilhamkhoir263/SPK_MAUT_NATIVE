<?php
require_once('includes/init.php');

$user_role = get_role();
if($user_role == 'admin') {

$page = "Perhitungan";
require_once('template/header.php');

mysqli_query($koneksi,"TRUNCATE TABLE hasil;");

$kriterias = array();
$q1 = mysqli_query($koneksi,"SELECT * FROM kriteria ORDER BY kode_kriteria ASC");			
while($krit = mysqli_fetch_array($q1)){
	$kriterias[$krit['id_kriteria']]['id_kriteria'] = $krit['id_kriteria'];
	$kriterias[$krit['id_kriteria']]['kode_kriteria'] = $krit['kode_kriteria'];
	$kriterias[$krit['id_kriteria']]['nama'] = $krit['nama'];
	$kriterias[$krit['id_kriteria']]['bobot'] = $krit['bobot'];
	$kriterias[$krit['id_kriteria']]['ada_pilihan'] = $krit['ada_pilihan'];
}

$alternatifs = array();
$q2 = mysqli_query($koneksi,"SELECT * FROM alternatif");			
while($alt = mysqli_fetch_array($q2)){
	$alternatifs[$alt['id_alternatif']]['id_alternatif'] = $alt['id_alternatif'];
	$alternatifs[$alt['id_alternatif']]['nama'] = $alt['nama'];
} 

//Matrix Keputusan (X)
$matriks_x = array();
foreach($kriterias as $kriteria):
	foreach($alternatifs as $alternatif):
		
		$id_alternatif = $alternatif['id_alternatif'];
		$id_kriteria = $kriteria['id_kriteria'];
		
		if($kriteria['ada_pilihan']==1){
			$q4 = mysqli_query($koneksi,"SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria WHERE penilaian.nilai=sub_kriteria.id_sub_kriteria AND penilaian.id_alternatif='$alternatif[id_alternatif]' AND penilaian.id_kriteria='$kriteria[id_kriteria]'");
			$data = mysqli_fetch_array($q4);
			$nilai = $data['nilai'];
		}else{
			$q4 = mysqli_query($koneksi,"SELECT nilai FROM penilaian WHERE id_alternatif='$alternatif[id_alternatif]' AND id_kriteria='$kriteria[id_kriteria]'");
			$data = mysqli_fetch_array($q4);
			$nilai = $data['nilai'];
		}
		
		$matriks_x[$id_kriteria][$id_alternatif] = $nilai;
	endforeach;
endforeach;

//Normalisasi
$nilai_u = array();
foreach($kriterias as $kriteria):
	foreach($alternatifs as $alternatif):
		$id_alternatif = $alternatif['id_alternatif'];
		$id_kriteria = $kriteria['id_kriteria'];
		$nilai = $matriks_x[$id_kriteria][$id_alternatif];
		
		$nilai_max = @(max($matriks_x[$id_kriteria]));
		$nilai_min = @(min($matriks_x[$id_kriteria]));
		//rumus 1
		$u = ($nilai-$nilai_min)/($nilai_max-$nilai_min);		
			
		$nilai_u[$id_kriteria][$id_alternatif] = $u;

	endforeach;
endforeach;

//Perhitungan Nilai
$perhitungan = array();
foreach($alternatifs as $alternatif):
	$nilai_total = 0;
	foreach($kriterias as $kriteria):		
		$id_alternatif = $alternatif['id_alternatif'];
		$id_kriteria = $kriteria['id_kriteria'];
		$bobot = $kriteria['bobot'];
		$u = $nilai_u[$id_kriteria][$id_alternatif];
		$nilai_total += $bobot * $u;
	endforeach;
	$perhitungan[$alternatif['id_alternatif']]['id_alternatif'] = $alternatif['id_alternatif'];
	$perhitungan[$alternatif['id_alternatif']]['nama'] = $alternatif['nama'];
	$perhitungan[$alternatif['id_alternatif']]['nilai'] = $nilai_total;
endforeach;
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-calculator"></i> Data Perhitungan</h1>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-dark"><i class="fa fa-table"></i> Matrix Keputusan (X)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-dark text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th>Nama Alternatif</th>
						<?php foreach ($kriterias as $kriteria): ?>
							<th><?= $kriteria['kode_kriteria'] ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatifs as $alternatif): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $alternatif['nama'] ?></td>
						<?php
						foreach ($kriterias as $kriteria):
							$id_alternatif = $alternatif['id_alternatif'];
							$id_kriteria = $kriteria['id_kriteria'];
							echo '<td>';
							echo $matriks_x[$id_kriteria][$id_alternatif];
							echo '</td>';
						endforeach
						?>
					</tr>
					<?php
						$no++;
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-dark"><i class="fa fa-table"></i> Bobot Kriteria</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-dark text-white">
					<tr align="center">
						<?php foreach ($kriterias as $kriteria): ?>
						<th><?= $kriteria['kode_kriteria'] ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<?php 
						
						foreach ($kriterias as $kriteria): ?>
						<td>
						<?php 
						echo $kriteria['bobot'];
						?>
						</td>
						<?php endforeach ?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-dark"><i class="fa fa-table"></i> Normalisasi Matrix (X)</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-dark text-white">
					<tr align="center">
						<th width="5%" rowspan="2">No</th>
						<th>Nama Alternatif</th>
						<?php foreach ($kriterias as $kriteria): ?>
							<th><?= $kriteria['kode_kriteria'] ?></th>
						<?php endforeach ?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$no=1;
						foreach ($alternatifs as $alternatif): ?>
					<tr align="center">
						<td><?= $no; ?></td>
						<td align="left"><?= $alternatif['nama'] ?></td>
						<?php						
						foreach($kriterias as $kriteria):
							$id_alternatif = $alternatif['id_alternatif'];
							$id_kriteria = $kriteria['id_kriteria'];
							echo '<td>';
							echo number_format($nilai_u[$id_kriteria][$id_alternatif], 3);
							echo '</td>';
						endforeach;
						?>
					</tr>
					<?php
						$no++;
						endforeach
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-dark"><i class="fa fa-table"></i> Perhitungan Nilai</h6>
    </div>

    <div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered" width="100%" cellspacing="0">
				<thead class="bg-dark text-white">
					<tr align="center">
						<th width="5%">No</th>
						<th>Nama Alternatif</th>
						<th width="30%">Total Nilai</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$no=1;
					foreach($perhitungan as $alternatif ): ?>
						<tr align="center">
							<td><?php echo $no; ?></td>
							<td align="left"><?php echo $alternatif['nama']; ?></td>
							<td><?php echo number_format($alternatif['nilai'], 3); ?></td>											
						</tr>
					<?php 
					$no++;
					$nilai_terformat = round($alternatif['nilai'], 3);
					mysqli_query($koneksi,"INSERT INTO hasil (id_hasil, id_alternatif, nilai) VALUES ('', '$alternatif[id_alternatif]', '$nilai_terformat')");

					endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php
require_once('template/footer.php');
}
else {
	header('Location: login.php');
}
?>