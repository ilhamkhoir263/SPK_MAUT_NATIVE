<?php
require_once('includes/init.php');

$user_role = get_role();
if ($user_role == 'admin' || $user_role == 'user') {

$page = "Hasil";
require_once('template/header.php');

// Mendapatkan kata kunci pencarian dan filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// Query untuk mendapatkan semua data tanpa filter pencarian
$query_all = "SELECT * FROM hasil JOIN alternatif ON hasil.id_alternatif=alternatif.id_alternatif ORDER BY hasil.nilai ASC";
$result_all = mysqli_query($koneksi, $query_all);

// Menyimpan data dan peringkat

$data_with_rank = [];
$rank = 1;
while ($data = mysqli_fetch_array($result_all)) {
    $data_with_rank[] = [
        'nama' => $data['nama'],
        'nilai' => $data['nilai'],
        'peringkat' => 0,
        'berkualitas' => $data['nilai'] > 0.30 ? 'berkualitas' : 'tidak_berkualitas'
    ];
}

// Sort array by nilai in descending order
usort($data_with_rank, function($a, $b) {
    return $b['nilai'] <=> $a['nilai'];
});

// Assign ranks after sorting
foreach ($data_with_rank as &$item) {
    $item['peringkat'] = $rank++;
}

unset($item);

// Filter data berdasarkan pencarian dan filter hasil
$filtered_data_with_rank = [];
foreach ($data_with_rank as $item) {
    $match_search = empty($search) || stripos($item['nama'], $search) !== false;
    $match_filter = empty($filter) || $item['berkualitas'] === $filter;
    
    if ($match_search && $match_filter) {
        $filtered_data_with_rank[] = $item;
    }
}

?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-chart-area"></i> Data Hasil Akhir</h1>
    <a href="laporan.php" class="btn btn-primary"> <i class="fa fa-file"></i> Halaman Laporan </a>
</div>

<div class="card shadow mb-4">
    <!-- /.card-header -->
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-dark"><i class="fa fa-table"></i> Hasil Akhir Perankingan</h6>
    </div>

    <div class="card-body">
        <!-- Formulir Pencarian dan Filter -->
        <form method="GET" action="">
            <div class="form-row mb-3">
                <div class="col">
                    <input type="text" class="form-control" name="search" placeholder="Cari nama alternatif" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col">
                    <select class="custom-select" name="filter">
                        <option value="">Semua Hasil</option>
                        <option value="berkualitas" <?= $filter === 'berkualitas' ? 'selected' : '' ?>>Hasil Berkualitas</option>
                        <option value="tidak_berkualitas" <?= $filter === 'tidak_berkualitas' ? 'selected' : '' ?>>Hasil Tidak Berkualitas</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" type="submit">Cari</button>
                </div>
                <div class="col-auto">
                    <?php if ($search || $filter): ?>
                        <a href="hasil.php" class="btn btn-secondary">Reset</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead class="bg-dark text-white">
                    <tr align="center">
                        <th>Nama Alternatif</th>
                        <th>Nilai</th>
                        <th>Hasil persen</th>
                        <th width="15%">Rank</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
    <?php 
        foreach ($filtered_data_with_rank as $data) {
            $peringkat = $data['peringkat'];
            $nilai = $data['nilai'];
            $nama = $data['nama'];
            $berkualitas = $data['berkualitas'];
            $detail_url = "detail.php?nama_alternatif=" . urlencode($nama);
    ?>
    <tr align="center">
        <td align="left">
            <a href="<?= $detail_url ?>">
                <?= htmlspecialchars($nama) ?>
            </a>
        </td>
        <td><?= htmlspecialchars($nilai) ?></td>
        <td><?= number_format($nilai * 100, 2) ?>%</td>
        <td><?= $peringkat; ?></td>
        <td>
            <?php
            // Tampilkan hasil berkualitas
            if ($berkualitas === 'berkualitas') {
                echo '<a href="' . $detail_url . '" style="color: green;">Hasil Berkualitas</a>';
            } else {
                echo '<a href="' . $detail_url . '" style="color: red;">Hasil Tidak Berkualitas</a>';
            }
            ?>
            <?php
// Menentukan berkualitas berdasarkan nilai
$berkualitas_text = $berkualitas === 'berkualitas' ? 'Hasil Berkualitas' : 'Hasil Tidak Berkualitas';

// Narasi berdasarkan peringkat
switch ($peringkat) {
    case 1:
        echo "Hasil keputusan dari perhitungan Multi Attribute Utility Theory (MAUT) dari alternatif {$nama} mendapatkan peringkat pertama dengan hasil {$nilai} poin. Silahkan tekan bagian {$berkualitas_text} untuk keterangan lebih lanjut.";
        break;
    case 2:
        echo "Hasil keputusan dari perhitungan Multi Attribute Utility Theory (MAUT) dari alternatif {$nama} menduduki peringkat kedua dengan hasil {$nilai} poin. Silahkan tekan bagian {$berkualitas_text} untuk keterangan lebih lanjut.";
        break;
    case 3:
        echo "Hasil keputusan dari perhitungan Multi Attribute Utility Theory (MAUT) dari alternatif {$nama} menduduki peringkat ketiga dengan hasil {$nilai} poin. Silahkan tekan bagian {$berkualitas_text} untuk keterangan lebih lanjut.";
        break;
    default:
        echo "Hasil keputusan dari perhitungan Multi Attribute Utility Theory (MAUT) dari alternatif {$nama} menduduki peringkat ke-{$peringkat} dengan hasil {$nilai} poin. Silahkan tekan bagian {$berkualitas_text} untuk keterangan lebih lanjut.";
        break;
}
?>

        </td>
    </tr>
    <?php
        }
    ?>
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
