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

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

date_default_timezone_set("Asia/Jakarta");

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('HTTP/1.0 403 Forbidden');
    exit("Direct access not allowed");
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); 
define('DB_NAME', 'absensi_chatbot');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    die("Koneksi database bermasalah.");
}

$data_sistem = $db->query("SELECT * FROM sistem LIMIT 1")->fetch_assoc();

$WA_STATUS = $data_sistem['wa_status'] ?? 'OFF';
if ($WA_STATUS === 'ON') {
    $WA_TOKEN = $data_sistem['whatsapp_token'] ?? '';
} else {
    $WA_TOKEN = 'BOT_OFF'; 
}

$TG_STATUS = $data_sistem['tg_status'] ?? 'OFF';

if ($TG_STATUS === 'ON') {
    $TELEGRAM_TOKEN = $data_sistem['telegram_token'] ?? '';
} else {
    $TELEGRAM_TOKEN = 'BOT_OFF';
}

$OFFICE_LAT       = $data_sistem['latitude'] ?? -6.290824886810085;
$OFFICE_LNG       = $data_sistem['longitude'] ?? 107.33035614730994;

$RECORD_TIME      = $data_sistem['time'] ?? '12:00:00';
$RECORD           = $data_sistem['autorecord'] ?? 'OFF';


function hitungJarak($lat1, $lon1, $lat2, $lon2) {
    $radius_bumi = 6371000; 
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return round($radius_bumi * $c);
}

$current_page = basename($_SERVER['PHP_SELF'], ".php");
if ($current_page == 'index') { $current_page = 'home'; }

$page_titles = [
    'home'         => 'Dashboard',
    'karyawan'     => 'Data Karyawan',
    'absensi'      => 'Monitoring Absensi',
    'laporan'      => 'Laporan Kehadiran',
    'lokasi'       => 'Lokasi Kantor',
    'chatbot'      => 'Konfigurasi Bot',
    'webhook-edit' => 'Webhook',
    'flow'         => 'Flow System',
    'support'      => 'Support'
];

$display_title = isset($page_titles[$current_page]) ? $page_titles[$current_page] : 'Admin';