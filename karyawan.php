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

if (isset($_POST['tambah'])) {
    $nik   = trim($_POST['nik']);
    $nama  = trim($_POST['nama']);
    $no_hp = trim($_POST['no_hp']);

    $check = $db->query("SELECT nik FROM karyawan WHERE nik = '$nik' OR no_hp = '$no_hp' LIMIT 1");
    if ($check->num_rows > 0) {
        echo "<script>window.location.href='?status=ganda_total&msg=" . urlencode("NIK atau Nomor HP sudah terdaftar!") . "';</script>";
        exit;
    }

    $stmt = $db->prepare("INSERT INTO karyawan (nik, nama, no_hp, status) VALUES (?, ?, ?, 'aktif')");
    $stmt->bind_param("sss", $nik, $nama, $no_hp);
    
    if ($stmt->execute()) {
        echo "<script>window.location.href='?status=sukses';</script>";
        exit;
    }
}

if (isset($_POST['update'])) {
    $nik_lama = $_POST['nik_lama'];
    $nama     = trim($_POST['nama']);
    $no_hp    = trim($_POST['no_hp']);

    $check = $db->query("SELECT nik FROM karyawan WHERE no_hp = '$no_hp' AND nik != '$nik_lama' LIMIT 1");
    if ($check->num_rows > 0) {
        echo "<script>window.location.href='?status=ganda_total&msg=" . urlencode("Nomor HP tersebut sudah digunakan karyawan lain!") . "';</script>";
        exit;
    }

    $stmt = $db->prepare("UPDATE karyawan SET nama=?, no_hp=? WHERE nik=?");
    $stmt->bind_param("sss", $nama, $no_hp, $nik_lama);
    
    if ($stmt->execute()) {
        echo "<script>window.location.href='?status=update';</script>";
        exit;
    }
}

if (isset($_GET['hapus'])) {
    $nik = $_GET['hapus'];
    $stmt = $db->prepare("DELETE FROM karyawan WHERE nik=?");
    $stmt->bind_param("s", $nik);
    
    if ($stmt->execute()) {
        echo "<script>window.location.href='?status=hapus';</script>";
        exit;
    }
}

if (isset($_POST['import_data'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    
    if (($handle = fopen($file, "r")) !== FALSE) {
        fgetcsv($handle, 1000, ";", "\"", "\\"); 
        
        $data_import = [];
        $pesan_error = "";
        $baris = 2; 

        while (($data = fgetcsv($handle, 1000, ";", "\"", "\\")) !== FALSE) {
            if (!isset($data[1]) || trim($data[1]) === "") {
                continue;
            }

            $nik   = trim($data[1]);
            $nama  = isset($data[2]) ? trim($data[2]) : '';
            $no_hp = isset($data[3]) ? trim($data[3]) : '';

            if (!empty($nik)) {
                $check = $db->query("SELECT nik, no_hp FROM karyawan WHERE nik = '$nik' OR no_hp = '$no_hp' LIMIT 1");
                if ($check->num_rows > 0) {
                    $row = $check->fetch_assoc();
                    $identitas = ($row['nik'] == $nik) ? "NIK $nik" : "No HP $no_hp";
                    $pesan_error = "Baris $baris: Data ganda di sistem ($identitas).";
                    break; 
                }
                
                foreach ($data_import as $temp) {
                    if ($temp['nik'] == $nik || $temp['no_hp'] == $no_hp) {
                        $pesan_error = "Baris $baris: NIK/No HP ganda di dalam file ($nik).";
                        break 2;
                    }
                }
                $data_import[] = ['nik' => $nik, 'nama' => $nama, 'no_hp' => $no_hp];
            }
            $baris++;
        }
        fclose($handle);

        if (empty($pesan_error) && !empty($data_import)) {
            $db->begin_transaction();
            try {
                $stmt = $db->prepare("INSERT INTO karyawan (nik, nama, no_hp, status) VALUES (?, ?, ?, 'aktif')");
                foreach ($data_import as $item) {
                    $stmt->bind_param("sss", $item['nik'], $item['nama'], $item['no_hp']);
                    $stmt->execute();
                }
                $db->commit();
                echo "<script>window.location.href='karyawan?status=import_sukses&jumlah=" . count($data_import) . "';</script>";
            } catch (Exception $e) {
                $db->rollback();
                echo "<script>window.location.href='karyawan?status=error&msg=Gagal menyimpan ke database';</script>";
            }
        } else if (!empty($pesan_error)) {
            echo "<script>window.location.href='karyawan?status=ganda_total&msg=" . urlencode($pesan_error) . "';</script>";
        } else {
            echo "<script>window.location.href='karyawan?status=error&msg=File CSV kosong atau tidak valid';</script>";
        }
        exit;
    }
}
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Karyawan</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-tosca shadow-sm" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-user-plus mr-1"></i> Tambah Karyawan
                    </button>
                    <button class="btn btn-success shadow-sm" data-toggle="modal" data-target="#modalImport">
                        <i class="fas fa-file-import mr-1"></i> Import CSV
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-tosca shadow-none">
                <div class="card-body">
                    <table id="tabel" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="120" class="text-center">NIK</th>
                                <th>Nama Karyawan</th>
                                <th width="180" class="text-center">No. WhatsApp</th>
                                <th width="180" class="text-center">ID Telegram</th>
                                <th width="100" class="text-center">Status</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res = $db->query("SELECT * FROM karyawan ORDER BY nama ASC");
                            $no = 1;
                            while($row = $res->fetch_assoc()):
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= $row['nik'] ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['no_hp'] ?? '') ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['telegram_id'] ?? '') ?></td>
                                <td class="text-center">
                                    <span class="badge badge-<?= $badge[strtolower($row['status'])] ?? 'secondary' ?>">
                                        <?= strtoupper($row['status'] ?: '') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info btn-edit shadow-sm" 
                                            data-nik="<?= $row['nik'] ?>" 
                                            data-nama="<?= htmlspecialchars($row['nama']) ?>" 
                                            data-hp="<?= $row['no_hp'] ?>" 
                                            title="Edit">
                                        <i class="fas fa-edit"></i> 
                                    </button>
                                    <button class="btn btn-sm btn-danger shadow-sm" 
                                            onclick="konfirmasiHapus('<?= $row['nik'] ?>')" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
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

<?php 
include "inc/modals.php"; 
include "inc/footer.php"; 
?>