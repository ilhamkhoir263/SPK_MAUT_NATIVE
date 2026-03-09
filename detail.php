<?php
require_once('includes/init.php');

$user_role = get_role();
if ($user_role == 'admin' || $user_role == 'user') {

    $page = "Detail Keputusan";
    require_once('template/header.php');

    $nama_alternatif = isset($_GET['nama_alternatif']) ? $_GET['nama_alternatif'] : '';

    // Query untuk mendapatkan ID alternatif berdasarkan nama
    $q1 = mysqli_query($koneksi, "SELECT id_alternatif FROM alternatif WHERE nama = '" . mysqli_real_escape_string($koneksi, $nama_alternatif) . "'");
    $alternatif = mysqli_fetch_array($q1);

    if (!$alternatif) {
        echo "Alternatif tidak ditemukan.";
        require_once('template/footer.php');
        exit;
    }

    $id_alternatif = $alternatif['id_alternatif'];

    // Query untuk mendapatkan data kriteria
    $kriterias = array();
    $q2 = mysqli_query($koneksi, "SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
    while ($krit = mysqli_fetch_array($q2)) {
        $kriterias[$krit['id_kriteria']] = [
            'id_kriteria' => $krit['id_kriteria'],
            'kode_kriteria' => $krit['kode_kriteria'],
            'nama' => $krit['nama'],
            'bobot' => $krit['bobot'],
            'ada_pilihan' => $krit['ada_pilihan']
        ];
    }

    // Matrix Keputusan (X)
    $matriks_x = array();
    foreach ($kriterias as $kriteria) {
        $id_kriteria = $kriteria['id_kriteria'];

        if ($kriteria['ada_pilihan'] == 1) {
            $q4 = mysqli_query($koneksi, "SELECT sub_kriteria.nilai FROM penilaian JOIN sub_kriteria ON penilaian.nilai=sub_kriteria.id_sub_kriteria WHERE penilaian.id_alternatif='$id_alternatif' AND penilaian.id_kriteria='$id_kriteria'");
            $data = mysqli_fetch_array($q4);
            $nilai = $data['nilai'];
        } else {
            $q4 = mysqli_query($koneksi, "SELECT nilai FROM penilaian WHERE id_alternatif='$id_alternatif' AND id_kriteria='$id_kriteria'");
            $data = mysqli_fetch_array($q4);
            $nilai = $data['nilai'];
        }

        $matriks_x[$id_kriteria] = $nilai;
    }
?>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-fw fa-table"></i> Detail Keputusan - <?= htmlspecialchars($nama_alternatif) ?></h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fa fa-table"></i> Detail Nilai Kriteria untuk <?= htmlspecialchars($nama_alternatif) ?></h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="bg-dark text-white">
                        <tr align="center">
                            <th width="5%">No</th>
                            <th>Nama Kriteria</th>
                            <th>Kode Kriteria</th>
                            <th>Nilai</th>
                            <th>Deskripsi Kriteria</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                            foreach ($kriterias as $kriteria) {
                                $nilai = isset($matriks_x[$kriteria['id_kriteria']]) ? $matriks_x[$kriteria['id_kriteria']] : 'Tidak Ada Data';
                                $deskripsi = '';

                                // Menentukan deskripsi kriteria berdasarkan nilai
                                switch ($nilai) {
                                    case 1: 
                                        $deskripsi = 'Nilai hitungannya rendah, Jadi cocok untuk digunakan'; 
                                        break;
                                    case 2: 
                                        $deskripsi = 'Nilai hitungannya normal, Masih cocok untuk digunakan'; 
                                        break;
                                    case 3: 
                                        $deskripsi = 'Nilai hitungannya cukup tinggi, Bisa diganti agar lebih efektif'; 
                                        break;
                                    case 4: 
                                        $deskripsi = 'Nilai hitungannya tinggi, Lebih baik diganti agar lebih efektif'; 
                                        break;
                                    default: 
                                        $deskripsi = 'Nilai kriteria tidak dikenal'; 
                                }
                        ?>
                        <tr align="center">
                            <td><?= $no; ?></td>
                            <td><?= htmlspecialchars($kriteria['nama']) ?></td>
                            <td><?= htmlspecialchars($kriteria['kode_kriteria']) ?></td>
                            <td><?= htmlspecialchars($nilai) ?></td>
                            <td><?= htmlspecialchars($deskripsi) ?></td>
                        </tr>
                        <?php
                                $no++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tombol Kembali ke Halaman Utama -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <a href="hasil.php" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Kembali ke Halaman Utama</a>
    </div>

<?php
    require_once('template/footer.php');
} else {
    header('Location: login.php');
}
?>
