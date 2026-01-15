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

require_once "../inc/config.php";

if ($db->connect_error) {
    exit;
}

$data = file_get_contents("php://input");
$input = json_decode($data, true);
if (!$input) exit;

$id_pengirim    = preg_replace('/[^0-9]/', '', $input["sender"] ?? "");
$teks_pesan     = strtolower(trim(strip_tags($input["message"] ?? "")));
$lokasi         = trim($input["location"] ?? "");

function kirimPesan($target, $pesan, $token) {
    $curl = curl_init("https://api.fonnte.com/send");
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST            => true,
        CURLOPT_HTTPHEADER     => ["Authorization: $token"],
        CURLOPT_POSTFIELDS     => [
            "target"  => $target,
            "message" => $pesan
        ]
    ]);
    curl_exec($curl);
    curl_close($curl);
}

function formatTanggalIndo($tanggal) {
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

$tanggal_sekarang = date("Y-m-d");
$jam_sekarang      = date("H:i:s");
$tampilan_tanggal = formatTanggalIndo($tanggal_sekarang);

$query_karyawan = $db->prepare("SELECT nik, nama FROM karyawan WHERE no_hp = ? AND status = 'aktif'");
$query_karyawan->bind_param("s", $id_pengirim);
$query_karyawan->execute();
$data_karyawan = $query_karyawan->get_result()->fetch_assoc();

if (!$data_karyawan) {
    if ($teks_pesan !== "" || $lokasi !== "") {
        kirimPesan($id_pengirim, "Nomor $id_pengirim belum terdaftar atau tidak aktif, silahkan hubungi Admin.", $WA_TOKEN);
    }
    exit;
}

$query_absen = $db->prepare("SELECT * FROM absensi WHERE nik = ? AND tanggal = ?");
$query_absen->bind_param("ss", $data_karyawan['nik'], $tanggal_sekarang);
$query_absen->execute();
$cek_absen = $query_absen->get_result()->fetch_assoc();

$balasan = "";

if ($lokasi !== "") {
    $query_state = $db->prepare("SELECT step FROM state WHERE no_hp = ? AND tanggal = ?");
    $query_state->bind_param("ss", $id_pengirim, $tanggal_sekarang);
    $query_state->execute();
    $res_state = $query_state->get_result()->fetch_assoc();

    if ($res_state && $res_state['step'] == 'minta_lokasi_masuk') {
        $koordinat = explode(",", $lokasi);
        if (count($koordinat) == 2) {
            $lat_user = (float)trim($koordinat[0]);
            $lng_user = (float)trim($koordinat[1]);
            $jarak    = hitungJarak($OFFICE_LAT, $OFFICE_LNG, $lat_user, $lng_user);
            $jarak_teks = ($jarak >= 1000) ? round($jarak / 1000, 2) . " km" : round($jarak) . " m";

            $query_ins = $db->prepare("REPLACE INTO absensi (nik, tanggal, jam_masuk, lokasi, jarak, keterangan) VALUES (?, ?, ?, ?, ?, 'hadir')");
            $query_ins->bind_param("ssssd", $data_karyawan['nik'], $tanggal_sekarang, $jam_sekarang, $lokasi, $jarak);
            $query_ins->execute();

            $query_del_state = $db->prepare("DELETE FROM state WHERE no_hp = ? AND tanggal = ?");
            $query_del_state->bind_param("ss", $id_pengirim, $tanggal_sekarang);
            $query_del_state->execute();

            $balasan = "Terima kasih *{$data_karyawan['nama']}*,\nAbsensi masuk berhasil disimpan.\n\n"
                     . "Status : *HADIR*\n"
                     . "Tanggal : *{$tampilan_tanggal}*\n"
                     . "Jam : *{$jam_sekarang}*\n"
                     . "Jarak : *{$jarak_teks}*";

            kirimPesan($id_pengirim, $balasan, $WA_TOKEN);
        }
    }
    exit;
}

switch ($teks_pesan) {
    case 'reset':
        $query_res1 = $db->prepare("DELETE FROM absensi WHERE nik = ? AND tanggal = ?");
        $query_res1->bind_param("ss", $data_karyawan['nik'], $tanggal_sekarang);
        $query_res1->execute();

        $query_res2 = $db->prepare("DELETE FROM state WHERE no_hp = ? AND tanggal = ?");
        $query_res2->bind_param("ss", $id_pengirim, $tanggal_sekarang);
        $query_res2->execute();
        
        $balasan = "Data absensi Anda untuk hari ini berhasil di *RESET*. \n\n"
                 . "Silahkan lakukan *Absen* kembali.";
        break;

    case 'hadir':
        if ($cek_absen && $cek_absen['keterangan'] !== 'TK') {
            $status_sekarang = strtoupper($cek_absen['keterangan']);
            $balasan = "Mohon maaf *{$data_karyawan['nama']}*,\n"
                     . "Data absensi Anda sudah tersimpan hari ini dengan status: *{$status_sekarang}*.\n\n"
                     . "Ketik *Reset* jika ingin mengubah data absensi.";      
        } else {
            $step_masuk = 'minta_lokasi_masuk';
            $query_st = $db->prepare("REPLACE INTO state (no_hp, tanggal, step) VALUES (?, ?, ?)");
            $query_st->bind_param("sss", $id_pengirim, $tanggal_sekarang, $step_masuk);
            $query_st->execute();
            $balasan = "Silakan kirim *Lokasi* Anda sekarang.";
        }
        break;

    case 'sakit':
    case 'izin':
        if ($cek_absen && $cek_absen['keterangan'] !== 'TK') {
            $status_sekarang = strtoupper($cek_absen['keterangan']);
            $balasan = "Mohon maaf *{$data_karyawan['nama']}*,\n"
                     . "Data absensi Anda sudah tersimpan hari ini dengan status: *{$status_sekarang}*.\n\n"
                     . "Ketik *Reset* jika ingin mengubah data absensi.";  
        } else {
            $query_del_st = $db->prepare("DELETE FROM state WHERE no_hp = ? AND tanggal = ?");
            $query_del_st->bind_param("ss", $id_pengirim, $tanggal_sekarang);
            $query_del_st->execute();
            
            $status_ket = strtoupper($teks_pesan);
            $query_iz = $db->prepare("REPLACE INTO absensi (nik, tanggal, jam_masuk, keterangan) VALUES (?, ?, ?, ?)");
            $query_iz->bind_param("ssss", $data_karyawan['nik'], $tanggal_sekarang, $jam_sekarang,  $teks_pesan);
            $query_iz->execute();

            $balasan = "Terima kasih *{$data_karyawan['nama']}*,\n"
                     . "Absensi Anda berhasil disimpan.\n\n"
                     . "Status : *{$status_ket}*\n"
                     . "Tanggal : *{$tampilan_tanggal}*\n"
                     . "Jam : *{$jam_sekarang}*";
        }
        break;

    case 'pulang':
        if (!$cek_absen || $cek_absen['keterangan'] == 'TK') {
            $balasan = "Mohon maaf *{$data_karyawan['nama']}*,\n"
                     . "Anda belum melakukan absen masuk hari ini.\n\n"
                     . "Silahkan ketik *Hadir* untuk absen masuk, atau *Izin/Sakit* untuk keterangan tidak hadir.";

        } elseif (strtolower($cek_absen['keterangan']) != 'hadir') {
            $status_skrg = strtoupper($cek_absen['keterangan']);
            $balasan = "Mohon maaf *{$data_karyawan['nama']}*,\n"
                     . "status absen Anda hari ini adalah *{$status_skrg}*.\n\n"
                     . "Anda tidak perlu melakukan absen pulang.";

        } elseif ($cek_absen['jam_keluar'] !== NULL && $cek_absen['jam_keluar'] !== '00:00:00') {
            $balasan = "Mohon maaf *{$data_karyawan['nama']}*,\n"
                     . "Anda sudah melakukan absen pulang sebelumnya.";
        } else {
            $query_plg = $db->prepare("UPDATE absensi SET jam_keluar = ? WHERE nik = ? AND tanggal = ?");
            $query_plg->bind_param("sss", $jam_sekarang, $data_karyawan['nik'], $tanggal_sekarang);
            $query_plg->execute();
            
            $balasan = "Terima kasih *{$data_karyawan['nama']}*,\n"
                     . "Absensi pulang Anda berhasil disimpan.\n\n"
                     . "Tanggal : *{$tampilan_tanggal}*\n"
                     . "Jam : *{$jam_sekarang}*";
        }
        break;

    default:
        if ($teks_pesan !== "") {
            $balasan = "Halo *{$data_karyawan['nama']}*,\n"
                     . "Perintah tidak dikenali.\n\n"
                     . "Silahkan gunakan kata kunci berikut:\n"
                     . "• *Hadir* - Absen Masuk\n"
                     . "• *Pulang* - Absen Keluar\n"
                     . "• *Izin* - Keterangan Izin\n"
                     . "• *Sakit* - Keterangan Sakit\n"
                     . "• *Reset* - Reset Status Absen";
        }
        break;
}

if ($balasan != "") {
    kirimPesan($id_pengirim, $balasan, $WA_TOKEN);
}

exit;