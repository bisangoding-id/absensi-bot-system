<?php
/**
 * Project     : Absensi Bot
 * Description : Sistem Management Absensi Berbasis Chatbot
 * Author      : Ikmal Maulana 
 * URL         : https://www.bisangoding.id/
 * Copyright   : © 2026 All Rights Reserved.
 * License     : Open Source GNU General Public License (GPL)
 *
 * Perangkat lunak ini bersifat Open Source, namun dilarang 
 * menyalahgunakan hak distribusi untuk keuntungan komersil sepihak tanpa izin.
 * Dengan menggunakan kode ini, Anda setuju untuk tetap menyertakan atribusi 
 * pengembang asli. 
 */

require_once "inc/header.php"; 
require_once "inc/sidebar.php";

if (isset($_POST['update_absensi'])) {
    $nik      = $_POST['nik'];
    $tgl      = $_POST['tanggal'];
    $masuk    = $_POST['jam_masuk'] ?: null;
    $keluar   = $_POST['jam_keluar'] ?: null;
    $ket      = $_POST['keterangan'];

    $user_lat = $_POST['user_lat'] ?? null;
    $user_lng = $_POST['user_lng'] ?? null;

    if ($ket == "hadir") {
        $lokasi = $user_lat . "," . $user_lng;
        $distance = hitungJarak($user_lat, $user_lng, $OFFICE_LAT, $OFFICE_LNG);
    } else {
        $lokasi = "-";
        $distance = "0";
    }

    $check = $db->prepare("SELECT nik FROM absensi WHERE nik = ? AND tanggal = ?");
    $check->bind_param("ss", $nik, $tgl);
    $check->execute();
    $exists = $check->get_result()->num_rows > 0;

    if ($exists) {
        $sql = "UPDATE absensi SET jam_masuk = ?, jam_keluar = ?, keterangan = ?, lokasi = ?, jarak = ? WHERE nik = ? AND tanggal = ?";
    } else {
        $sql = "INSERT INTO absensi (jam_masuk, jam_keluar, keterangan, lokasi, jarak, nik, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?)";
    }

    $stmt = $db->prepare($sql);
    $stmt->bind_param("sssssss", $masuk, $keluar, $ket, $lokasi, $distance, $nik, $tgl);
    
    if ($stmt->execute()) {
        echo "<script>window.location.href='absensi?tanggal=$tgl&status=update';</script>";
        exit;
    }
}

if (isset($_GET['hapus'])) {
    $nik_hapus = $_GET['hapus'];
    $tgl_hapus = $_GET['tanggal'];

    $stmt = $db->prepare("DELETE FROM absensi WHERE nik = ? AND tanggal = ?");
    $stmt->bind_param("ss", $nik_hapus, $tgl_hapus);

    if ($stmt->execute()) {
        echo "<script>window.location.href='absensi?tanggal=$tgl_hapus&status=reset';</script>";
        exit;
    }
}

if (isset($_POST['generate_tk'])) {
    $tgl_gen = $_POST['target_tanggal'];
    $res_karyawan = $db->query("SELECT nik FROM karyawan WHERE status = 'aktif'");
    $count = 0;

    while ($karyawan = $res_karyawan->fetch_assoc()) {
        $nik_k = $karyawan['nik'];
        $cek = $db->prepare("SELECT nik FROM absensi WHERE nik = ? AND tanggal = ?");
        $cek->bind_param("ss", $nik_k, $tgl_gen);
        $cek->execute();
        
        if ($cek->get_result()->num_rows == 0) {
            $ins = $db->prepare("INSERT INTO absensi (nik, tanggal, jam_masuk, jam_keluar, keterangan, lokasi, jarak) 
            VALUES (?, ?, '00:00:00', '00:00:00', 'tanpa keterangan', '-', '0')");
            $ins->bind_param("ss", $nik_k, $tgl_gen);
            $ins->execute();
            $count++;
        }
    }

    if ($count > 0) {
        echo "<script>
            setTimeout(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: '$count karyawan berhasil ditandai TK.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href='absensi?tanggal=$tgl_gen';
                });
            }, 100);
        </script>";
    }
}

$selected_date = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$filter_ket    = isset($_GET['filter_ket']) ? $_GET['filter_ket'] : '';

$badge = [
    'hadir' => 'success', 
    'sakit' => 'warning', 
    'izin' => 'primary', 
    'tanpa keterangan' => 'danger', 
];

$sql_log = "SELECT k.nik, k.nama, a.jam_masuk, a.jam_keluar, a.keterangan, a.lokasi, a.jarak
            FROM karyawan k
            LEFT JOIN absensi a ON k.nik = a.nik AND a.tanggal = ?
            WHERE k.status = 'aktif'";

if ($filter_ket === 'belum') {
    $sql_log .= " AND a.keterangan IS NULL";
} elseif ($filter_ket !== '') {
    $sql_log .= " AND a.keterangan = '$filter_ket'";
}
$sql_log .= " ORDER BY (a.jam_masuk IS NULL), a.jam_masuk DESC";

$stmt_l = $db->prepare($sql_log);
$stmt_l->bind_param("s", $selected_date);
$stmt_l->execute();
$res_log = $stmt_l->get_result();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Monitoring Absensi</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <form method="GET" action="absensi">
                                <label>Pilih Tanggal</label>
                                <input type="date" name="tanggal" class="form-control" value="<?= $selected_date ?>" onchange="this.form.submit()">
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="absensi">
                                <input type="hidden" name="tanggal" value="<?= $selected_date ?>">
                                <label>Status Absensi</label>
                                <select name="filter_ket" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Semua Status --</option>
                                    <option value="hadir" <?= $filter_ket == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                    <option value="izin" <?= $filter_ket == 'izin' ? 'selected' : '' ?>>Izin</option>
                                    <option value="sakit" <?= $filter_ket == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                    <option value="tanpa keterangan" <?= $filter_ket == 'tanpa keterangan' ? 'selected' : '' ?>>Alpha</option>
                                    <option value="belum" <?= $filter_ket == 'belum' ? 'selected' : '' ?>>Belum Absen</option>
                                </select>
                            </form>
                        </div>
                        <?php if($filter_ket == 'belum' || $selected_date != date('Y-m-d')){ ?>
                        <div class="col-md-6 text-right">
                            <button type="button" class="btn btn-danger shadow-sm" onclick="konfirmasiGenerateTK()">
                                <i class="fas fa-user-times"></i> Generate Status "TK"
                            </button>
                            <form id="formGenerateTK" method="POST" action="" style="display:none;">
                                <input type="hidden" name="target_tanggal" value="<?= $selected_date ?>">
                                <input type="hidden" name="generate_tk" value="1">
                            </form>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="card card-tosca shadow-none">
                <div class="card-body">
                    <table id="tabel" class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th width="50">No</th>
                                <th width="120">NIK</th>
                                <th>Nama</th>
                                <th width="100">Masuk</th>
                                <th width="100">Keluar</th>
                                <th width="150">Status</th>
                                <th width="80">Lokasi</th>
                                <th width="120">Jarak</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            while($row = $res_log->fetch_assoc()):
                                $jarak = $row['jarak'] ?? 0;
                                $keterangan = $row['keterangan'];
                                $tampil_jarak = (empty($keterangan)) ? "-" : ($jarak >= 1000 ? round($jarak / 1000, 2) . " km" : round($jarak) . " m");
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= $row['nik'] ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="text-center"><?= $row['jam_masuk'] ?: '-' ?></td>
                                <td class="text-center"><?= $row['jam_keluar'] ?: '-' ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $badge[strtolower($row['keterangan'])] ?? 'secondary' ?>">
                                        <?= strtoupper($row['keterangan'] ?: 'Belum Absen') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if($row['lokasi'] && $row['lokasi'] != "-"): ?>
                                        <button class="btn btn-xs btn-outline-info btn-map" data-lokasi="<?= htmlspecialchars($row['lokasi']) ?>">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </button>
                                    <?php else: ?> - <?php endif; ?>
                                </td>
                                <td class="text-center"><?= $tampil_jarak; ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info btn-edit-absen shadow-sm" 
                                            data-nik="<?= $row['nik'] ?>"
                                            data-nama="<?= htmlspecialchars($row['nama']) ?>"
                                            data-masuk="<?= $row['jam_masuk'] ?>"
                                            data-keluar="<?= $row['jam_keluar'] ?>"
                                            data-ket="<?= $row['keterangan'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger shadow-sm" onclick="konfirmasiReset('<?= $row['nik'] ?>', '<?= $selected_date ?>')">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modalEditAbsen" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-none border-0">
            <form method="POST">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Absensi <b id="display_nama"></b></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="nik" id="edit_nik">
                    <input type="hidden" name="tanggal" value="<?= $selected_date ?>">
                    <input type="hidden" name="user_lat" id="user_lat">
                    <input type="hidden" name="user_lng" id="user_lng">
                    
                    <div class="form-group">
                        <label>Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="edit_masuk" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Jam Keluar</label>
                        <input type="time" name="jam_keluar" id="edit_keluar" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <select name="keterangan" id="edit_ket" class="form-control" required>
                            <option value="hadir">HADIR</option>
                            <option value="sakit">SAKIT</option>
                            <option value="izin">IZIN</option>
                            <option value="tanpa keterangan">TANPA KETERANGAN</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="update_absensi" class="btn btn-info px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
include "inc/modals.php"; 
include "inc/footer.php"; 
?>

<script>
$(document).ready(function() {
    if ( ! $.fn.DataTable.isDataTable( '#tabel' ) ) {
        $('#tabel').DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    }

    $(document).on('click', '.btn-edit-absen', function() {
        const nik = $(this).data('nik');
        const nama = $(this).data('nama');
        const masuk = $(this).data('masuk');
        const keluar = $(this).data('keluar');
        const ket = $(this).data('ket');

        $('#edit_nik').val(nik);
        $('#display_nama').text(nama);
        $('#edit_keluar').val(keluar && keluar !== '-' ? keluar : '');
        $('#edit_ket').val(ket || 'hadir');

        if (!masuk || masuk === '-' || masuk === '00:00:00') {
            const sekarang = new Date();
            const jam = String(sekarang.getHours()).padStart(2, '0');
            const menit = String(sekarang.getMinutes()).padStart(2, '0');
            $('#edit_masuk').val(`${jam}:${menit}`);
        } else {
            $('#edit_masuk').val(masuk);
        }

        $('#modalEditAbsen').modal('show');
    });
});

function konfirmasiGenerateTK() {
    let jumlahBelumAbsen = 0;
    let table = $('#tabel').DataTable();

    table.rows().every(function() {
        let node = this.node();
        if (node && node.innerHTML.includes('BELUM ABSEN')) {
            jumlahBelumAbsen++;
        }
    });

    if (jumlahBelumAbsen === 0) {
        Swal.fire('Info', 'Semua karyawan sudah memiliki status absensi.', 'info');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi',
        html: `Ada ${jumlahBelumAbsen} karyawan, <br> akan digenerate status kehadirannya jadi TK.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Pilih Generate',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formGenerateTK').submit();
        }
    });
}

function konfirmasiReset(nik, tgl) {
    Swal.fire({
        title: 'Hapus Data?',
        text: "Data absensi karyawan ini akan dihapus/direset.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `absensi?hapus=${nik}&tanggal=${tgl}`;
        }
    });
}

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
        if(document.getElementById('user_lat')) {
            document.getElementById('user_lat').value = position.coords.latitude;
            document.getElementById('user_lng').value = position.coords.longitude;
        }
    });
}
</script>