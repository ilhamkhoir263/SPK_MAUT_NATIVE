<?php
require_once('includes/init.php');

$user_role = get_role();
if ($user_role == 'admin' || $user_role == 'user') {
    // Mendapatkan kata kunci filter
    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

    // Query untuk mendapatkan semua data dengan filter
    $query = "SELECT * FROM hasil JOIN alternatif ON hasil.id_alternatif = alternatif.id_alternatif";
    if ($filter) {
        if ($filter === 'berkualitas') {
            $query .= " WHERE hasil.nilai > 0.30";
        } elseif ($filter === 'tidak_berkualitas') {
            $query .= " WHERE hasil.nilai <= 0.30";
        }
    }
    $query .= " ORDER BY hasil.nilai ASC";
    $result = mysqli_query($koneksi, $query);
?>

<html>
    <head>
        <title>Cetak Laporan</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                text-align: center;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: center;
            }
            th {
                background-color: #f4f4f4;
            }
            @media print {
                .no-print {
                    display: none;
                }
            }
        </style>
    </head>
<body>
<p>Jl. Padang Datar, Jorong Batu Galeh, Nagari Sulit Air,</p>
<p>Kec. X Koto Diatas, Kab. Solok, Sumatera Barat</p>
<h4>LAPORAN HASIL KEPUTUSAN KELOMPOK TANI TUNAS BARU</h4>
<p>Tanggal Cetak: <?= date('d-m-Y') ?></p> <!-- Menampilkan tanggal cetak -->

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Buah</th>
            <th>Hasil persen</th>
            <th width="15%">Rank</th>
            <th>Keputusan</th>
            <th>Alasan Keputusan</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            $rank = 1; // Mulai peringkat dari 1
            // Modifikasi query untuk mengurutkan berdasarkan nilai secara descending
            $data_array = array();
            while ($data = mysqli_fetch_array($result)) {
                $data_array[] = $data;
            }
            // Mengurutkan array berdasarkan nilai secara descending
            usort($data_array, function($a, $b) {
                return $b['nilai'] <=> $a['nilai'];
            });
            
            foreach ($data_array as $data) {
                $efektivitas = $data['nilai'] >= 0.30 ? 'Berkualitas' : 'Tidak Berkualitas'; 
                $efektivitas_style = $data['nilai'] >= 0.30 ? 'color: green;' : 'color: red;';
                
                // Mendapatkan nama kriteria dan deskripsi alasan keputusan
                $id_alternatif = $data['id_alternatif'];
                $query_kriteria = "SELECT kriteria.nama, penilaian.nilai FROM penilaian
                                   JOIN kriteria ON penilaian.id_kriteria = kriteria.id_kriteria
                                   WHERE penilaian.id_alternatif = '$id_alternatif'";
                $result_kriteria = mysqli_query($koneksi, $query_kriteria);
                $kriteria_info = [];
                while ($kriteria = mysqli_fetch_array($result_kriteria)) {
                    // Menentukan deskripsi kriteria berdasarkan nilai
                    $deskripsi = '';
                    switch ($kriteria['nilai']) {
                    case 1: 
                    $deskripsi = 'Nilai hitungannya rendah, Jadi tidak berkualitas'; 
                    break;
                    case 2: 
                    $deskripsi = 'Nilai hitungannya cukup rendah, jadi kurang berkualitas'; 
                    break;
                    case 3: 
                    $deskripsi = 'Nilai hitungannya normal, cukup berkualitas'; 
                    break;
                    case 4: 
                    $deskripsi = 'Nilai hitungannya tinggi, berkualitas'; 
                    break;
                     case 5: 
                    $deskripsi = 'Nilai hitungannya sangat tinggi, sangat berkualitas'; 
                    break;
                default: 
                    $deskripsi = 'Nilai kriteria tidak dikenal'; 
                    break;
                    }
                    $kriteria_info[] = "<li><strong>" . htmlspecialchars($kriteria['nama']) . ":</strong> " . htmlspecialchars($deskripsi) . "</li>";
                }
                $alasan_keputusan = "<ul>" . implode("", $kriteria_info) . "</ul>";
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td align="left"><?= htmlspecialchars($data['nama']) ?></td>
            <td><?= number_format($data['nilai'] * 100, 2) ?>%</td>
            <td><?= $rank++; ?></td>
            <td style="<?= $efektivitas_style; ?>"><?= $efektivitas ?></td>
            <td><?= $alasan_keputusan ?></td>
        </tr>
        <?php
            }
        ?>
    </tbody>
</table>
</table>

<!-- Kolom tanda tangan yang akan dicetak -->
<br><br>
<table style="width: 100%; margin-top: 50px; border: none;">
    <tr>
        <td style="border: none;"></td> <!-- kolom kosong kiri -->
        <td style="text-align: right; border: none;">
            <div>
    Sulit Air, <?= date('d-m-Y') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
    <p>Pimpinan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
</div>

             <?php
$pimpinan = isset($_GET['pimpinan']) ? htmlspecialchars($_GET['pimpinan']) : '_________________';
?>

<br><br><br><br>
<u style="display: inline-block; width: 250px; text-align: center;">
   &nbsp;&nbsp;&nbsp;&nbsp; <?= $pimpinan ?>&nbsp;&nbsp;&nbsp;&nbsp;
</u>

        </td>
    </tr>
</table>


<div class="no-print">
    <form method="post" action="laporan.php">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <button type="submit">Kembali</button>
    </form>
    <button onclick="window.print()">Print this page</button>
</div>

</body>
</html>

<?php
} else {
    header('Location: login.php');
}
?>
