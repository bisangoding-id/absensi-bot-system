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

$data  = file_get_contents("php://input");
$input = json_decode($data, true);
if (!$input) exit;

$id_pengirim = $input["message"]["chat"]["id"] ?? "";
$nama_awal   = $input["message"]["from"]["first_name"] ?? "";
$teks_pesan  = strtolower(trim($input["message"]["text"] ?? ""));
$kontak      = $input["message"]["contact"] ?? null;
$lokasi      = $input["message"]["location"] ?? null;

function kirimPesan($target, $pesan, $token, $reply_markup = null) {
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $postData = ['chat_id' => $target, 'text' => $pesan, 'parse_mode' => 'Markdown'];
    if ($reply_markup) { $postData['reply_markup'] = json_encode($reply_markup); }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function formatTanggalIndo($tanggal) {
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $pecah = explode('-', $tanggal);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

$tanggal_sekarang = date("Y-m-d");
$jam_sekarang      = date("H:i:s");
$tampilan_tanggal = formatTanggalIndo($tanggal_sekarang);

$query_karyawan = $db->prepare("SELECT nik, nama FROM karyawan WHERE telegram_id = ? AND status = 'aktif'");
$query_karyawan->bind_param("s", $id_pengirim);
$query_karyawan->execute();
$data_karyawan = $query_karyawan->get_result()->fetch_assoc();

$balasan = "";
$reply_markup = null;

if (!$data_karyawan) {
    if ($kontak) {
        $phone = str_replace("+", "", $kontak['phone_number']);
        $phone_alt = "0" . substr($phone, 2);
        
        $query_reg = $db->prepare("SELECT nik, nama FROM karyawan WHERE (no_hp = ? OR no_hp = ?) AND status = 'aktif'");
        $query_reg->bind_param("ss", $phone, $phone_alt);
        $query_reg->execute();
        $res_reg = $query_reg->get_result()->fetch_assoc();

        if ($res_reg) {
            $query_cek_id = $db->prepare("SELECT nama FROM karyawan WHERE telegram_id = ?");
            $query_cek_id->bind_param("s", $id_pengirim);
            $query_cek_id->execute();
            $id_terpakai = $query_cek_id->get_result()->fetch_assoc();

            if ($id_terpakai) {
                $balasan = "Mohon maaf,\nAkun Telegram ini sudah tertaut dengan karyawan bernama *{$id_terpakai['nama']}*.\n"
                         . "Satu akun Telegram hanya bisa digunakan untuk satu NIK.\n\n"
                         . "Silahkan hubungi Admin untuk sinkronisasi ulang.";
            
            } else {
                $query_sync = $db->prepare("UPDATE karyawan SET telegram_id = ? WHERE nik = ?");
                $query_sync->bind_param("ss", $id_pengirim, $res_reg['nik']);
                $query_sync->execute();
                
                $balasan = "Selamat datang *{$res_reg['nama']}*,\nAkun Telegram Anda berhasil tersinkronisasi.\n\nSilakan ketik *Hadir/Sakit/Izin* untuk mulai absen.";
                $reply_markup = ['remove_keyboard' => true];
            }
        } else {
            $balasan = "Mohon maaf *{$nama_awal}*, nomor HP *{$phone}* tidak terdaftar di sistem.\n"
                     . "Silakan hubungi Admin untuk registrasi baru.";
        }
    } else {
        $balasan = "Halo *{$nama_awal}*,\n"
                 . "ID Telegram Anda belum terdaftar di sistem absensi.\n\n"
                 . "Klik tombol *📲 Kirim Nomor HP* di bawah untuk sinkronisasi akun.";

        $reply_markup = [
            'keyboard' => [[['text' => "📲 Kirim Nomor HP", 'request_contact' => true]]], 
            'resize_keyboard' => true, 
            'one_time_keyboard' => true
        ];
    }
    kirimPesan($id_pengirim, $balasan, $TELEGRAM_TOKEN, $reply_markup);
    exit; 
}

$query_absen = $db->prepare("SELECT * FROM absensi WHERE nik = ? AND tanggal = ?");
$query_absen->bind_param("ss", $data_karyawan['nik'], $tanggal_sekarang);
$query_absen->execute();
$cek_absen = $query_absen->get_result()->fetch_assoc();
$status_sekarang = strtoupper($cek_absen['keterangan']);

if ($lokasi) {
    $query_state = $db->prepare("SELECT step FROM state WHERE no_hp = ? AND tanggal = ?");
    $query_state->bind_param("ss", $id_pengirim, $tanggal_sekarang);
    $query_state->execute();
    $res_state = $query_state->get_result()->fetch_assoc();

    if ($res_state && $res_state['step'] == 'minta_lokasi_masuk') {
        $lat_user = $lokasi['latitude'];
        $lng_user = $lokasi['longitude'];
        $koordinat = "$lat_user,$lng_user";
        $jarak = hitungJarak($OFFICE_LAT, $OFFICE_LNG, $lat_user, $lng_user);
        $jarak_teks = ($jarak >= 1000) ? round($jarak / 1000, 2) . " km" : round($jarak) . " m";

        $query_ins = $db->prepare("REPLACE INTO absensi (nik, tanggal, jam_masuk, lokasi, jarak, keterangan) VALUES (?, ?, ?, ?, ?, 'hadir')");
        $query_ins->bind_param("ssssd", $data_karyawan['nik'], $tanggal_sekarang, $jam_sekarang, $koordinat, $jarak);
        $query_ins->execute();

        $query_del = $db->prepare("DELETE FROM state WHERE no_hp = ? AND tanggal = ?");
        $query_del->bind_param("ss", $id_pengirim, $tanggal_sekarang);
        $query_del->execute();

        $balasan = "Terima kasih *{$data_karyawan['nama']}*,\n"
                 . "Absensi masuk berhasil disimpan.\n\n"
                 . "Status : *HADIR*\n"
                 . "Tanggal : *{$tampilan_tanggal}*\n"
                 . "Jam : *{$jam_sekarang}*\n"
                 . "Jarak : *{$jarak_teks}*";
        kirimPesan($id_pengirim, $balasan, $TELEGRAM_TOKEN, ['remove_keyboard' => true]);
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
        if ($cek_absen && $cek_absen['keterangan'] != 'TK') {
            $balasan = "Mohon maaf *{$data_karyawan['nama']}*,\n"
                     . "Data absensi Anda sudah tersimpan hari ini dengan status: *{$status_sekarang}* .\n\n"
                     . "Ketik *Reset* jika ingin mengubah data absensi.";
        } else {
            $step = 'minta_lokasi_masuk';
            $query_st = $db->prepare("REPLACE INTO state (no_hp, tanggal, step) VALUES (?, ?, ?)");
            $query_st->bind_param("sss", $id_pengirim, $tanggal_sekarang, $step);
            $query_st->execute();
            $balasan = "Silakan klik tombol di bawah untuk mengirim lokasi Anda.";
            $reply_markup = [
                'keyboard' => [[['text' => "📍 Kirim Lokasi Sekarang", 'request_location' => true]]], 
                'resize_keyboard' => true, 
                'one_time_keyboard' => true
            ];
        }
        break;

    case 'sakit':
    case 'izin':
        if ($cek_absen && $cek_absen['keterangan'] != 'TK') {
            $balasan = "Mohon maaf *{$data_karyawan['nama']}*,\n"
                     . "Data absensi Anda sudah tersimpan hari ini dengan status: *{$status_sekarang}*.\n\n"
                     . "Ketik *Reset* jika ingin mengubah data absensi.";
        } else {
            $status_ket = strtoupper($teks_pesan);
            $query_iz = $db->prepare("REPLACE INTO absensi (nik, tanggal, jam_masuk, keterangan) VALUES (?, ?, ?, ?)");
            $query_iz->bind_param("ssss", $data_karyawan['nik'], $tanggal_sekarang, $jam_sekarang, $teks_pesan);
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
            $balasan = "Mohon maaf *{$data_karyawan['nama']}*,\n"
                     . "Status absen Anda hari ini adalah *{$status_sekarang}*.\n\n"
                     . "Anda tidak perlu melakukan absen pulang.";          

        } elseif ($cek_absen['jam_keluar'] && $cek_absen['jam_keluar'] != '00:00:00') {
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
        $balasan = "Halo *{$data_karyawan['nama']}*,\n"
                 . "Perintah tidak dikenali.\n\n"
                 . "Silakan gunakan kata kunci berikut:\n"
                 . "• *Hadir* - Absen Masuk\n"
                 . "• *Pulang* - Absen Keluar\n"
                 . "• *Izin* - Keterangan izin\n"
                 . "• *Sakit* - Keterangan sakit\n"
                 . "• *Reset* - Reset Status Absen";
        break;
}

if ($balasan != "") {
    kirimPesan($id_pengirim, $balasan, $TELEGRAM_TOKEN, $reply_markup);
}
exit;