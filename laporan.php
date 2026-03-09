<?php
require_once('includes/init.php');

$user_role = get_role();
if ($user_role == 'admin' || $user_role == 'user') {

    $page = "Laporan";
    require_once('template/header.php');

    // Mendapatkan kata kunci filter
    $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

    // Query untuk mendapatkan semua data tanpa filter pencarian
    $query_all = "SELECT * FROM hasil JOIN alternatif ON hasil.id_alternatif=alternatif.id_alternatif ORDER BY hasil.nilai ASC";
    $result_all = mysqli_query($koneksi, $query_all);

    // Menyimpan data dan peringkat
    $data_with_rank = [];
    $rank = 1;
    $jumlah_berkualitas = 0;
    $jumlah_tidak_berkualitas = 0;

    while ($data = mysqli_fetch_array($result_all)) {
        $efektivitas = $data['nilai'] > 0.30 ? 'berkualitas' : 'tidak_berkualitas';
        if ($efektivitas === 'berkualitas') {
            $jumlah_berkualitas++;
        } else {
            $jumlah_tidak_berkualitas++;
        }

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

        $data_with_rank[] = [
            'nama' => $data['nama'],
            'nilai' => $data['nilai'],
            'efektivitas' => $efektivitas,
            'alasan' => $alasan_keputusan
        ];

        // Sort array by nilai in descending order
        usort($data_with_rank, function($a, $b) {
            return $b['nilai'] <=> $a['nilai'];
        });

    }
    // Assign ranks after sorting
    foreach ($data_with_rank as &$item) {
        $item['peringkat'] = $rank++;
    }

    unset($item);

    // Filter data berdasarkan filter hasil
    $filtered_data_with_rank = [];
    foreach ($data_with_rank as $item) {
        $match_filter = !$filter || $item['efektivitas'] === $filter;
        
        if ($match_filter) {
            $filtered_data_with_rank[] = $item;
        }
    }
?>

    <form method="GET" action="cetak_laporan.php" target="_blank" class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="h3 mb-0 text-gray-800">
            <i class="fas fa-fw fa-file"></i> Laporan Hasil Keputusan<br>KELOMPOK TANI TUNAS BARU
        </h3>
    </div>
    <div class="mt-2">
    <input type="text" name="pimpinan" class="form-control form-control-sm" placeholder="Masukkan Nama Pimpinan" style="width: 250px;" required>
</div>

    <div>
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <button type="submit" class="btn btn-primary mt-2">
            <i class="fa fa-print"></i> Cetak Laporan
        </button>
    </div>
</form>


    <div class="card shadow mb-4">
        <!-- /.card-header -->
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fa fa-table"></i> Laporan Hasil Keputusan</h6>
        </div>

        <div class="card-body">
            <!-- Narasi Jumlah Alternatif -->
            <div class="mb-4">
                <p>Total Alternatif Berkualitas: <?= $jumlah_berkualitas ?></p>
                <p>Total Alternatif Tidak Berkualitas: <?= $jumlah_tidak_berkualitas ?></p>
            </div>
            

            
            <!-- Formulir Filter -->
            <form method="GET" action="">
                <div class="form-row mb-3">
                    <div class="col">
                        <select class="custom-select" name="filter">
                            <option value="">Semua Hasil</option>
                            <option value="berkualitas" <?= $filter === 'berkualitas' ? 'selected' : '' ?>>Hasil Berkualitas</option>
                            <option value="tidak_berkualitas" <?= $filter === 'tidak_berkualitas' ? 'selected' : '' ?>>Hasil Tidak Berkualitas</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                    <div class="col-auto">
                        <?php if ($filter): ?>
                            <a href="laporan.php" class="btn btn-secondary">Reset</a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr align="center">
                            <th>Nama Buah</th>
                            <th>Hasil persen</th>
                            <th width="15%">Rank</th>
                            <th>Keputusan</th>
                            <th>Alasan Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ($filtered_data_with_rank as $data) {
                                $peringkat = $data['peringkat'];
                                $nilai = $data['nilai'];
                                $nama = $data['nama'];
                                $efektivitas = $data['efektivitas'];
                                $alasan = $data['alasan'];
                        ?>
                        <tr align="center">
                            <td align="left"><?= htmlspecialchars($nama) ?></td>
                            <td><?= number_format($nilai * 100, 2) ?>%</td>
                            <td><?= $peringkat; ?></td>
                            <td>
                                <?php
                                // Tampilkan hasil efektivitas
                                if ($efektivitas === 'berkualitas') {
                                    echo '<span style="color: green;">Berkualitas</span>';
                                } else {
                                    echo '<span style="color: red;">Tidak Berkualitas</span>';
                                }
                                ?>
                            </td>
                            <td><?= $alasan ?></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Tombol Kembali ke Halaman Utama -->
            <div class="mt-4">
                <a href="hasil.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali ke Halaman Utama</a>
            </div>
        </div>
    </div>

<?php
    require_once('template/footer.php');
} else {
    header('Location: login.php');
}
?>
