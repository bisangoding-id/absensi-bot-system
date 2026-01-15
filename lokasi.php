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

$swal_script = "";

if (isset($_POST['update_lokasi'])) {
    $lat = $_POST['latitude'];
    $lng = $_POST['longitude'];
    $id  = $_POST['id'];
    $input_pass = $_POST['confirm_password']; 

    $is_valid = false;
    if (password_verify($input_pass, $pass_admin) || $input_pass === $pass_admin) {
        $is_valid = true;
    }

    if ($is_valid) {
        $stmt = $db->prepare("UPDATE sistem SET latitude = ?, longitude = ? WHERE id = ?");
        $stmt->bind_param("ssi", $lat, $lng, $id);
        
        if ($stmt->execute()) {
            $swal_script = "Swal.fire({icon: 'success', title: 'Berhasil!', text: 'Lokasi kantor diperbarui.', showConfirmButton: false, timer: 2000}).then(() => { window.location.href='lokasi'; });";
        } else {
            $swal_script = "Swal.fire('Gagal!', 'Terjadi kesalahan database.', 'error');";
        }
        $stmt->close();
    } else {
        $swal_script = "Swal.fire('Akses Ditolak!', 'Password konfirmasi salah.', 'error');";
    }
}

$query = $db->query("SELECT * FROM sistem LIMIT 1");
$lokasi = $query->fetch_assoc();
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />


<style>
    #map { height: 450px; border-radius: 8px; border: 1px solid #ddd; }
    .leaflet-control-geocoder { border-radius: 4px; box-shadow: 0 1px 5px rgba(0,0,0,0.4); }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Pengaturan Lokasi Kantor</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-outline card-primary shadow">
                        <div class="card-header">
                            <h3 class="card-title">Koordinat</h3>
                        </div>
                        <form method="POST" action="">
                            <div class="card-body">
                                <input type="hidden" name="id" value="<?= $lokasi['id'] ?>">
                                <div class="form-group">
                                    <label>Latitude</label>
                                    <input type="text" name="latitude" id="lat" class="form-control" value="<?= $lokasi['latitude'] ?>" readonly required>
                                </div>
                                <div class="form-group">
                                    <label>Longitude</label>
                                    <input type="text" name="longitude" id="lng" class="form-control" value="<?= $lokasi['longitude'] ?>" readonly required>
                                </div>
                                <div class="info-warning2">
                                    <small><i class="fas fa-info-circle"></i> Gunakan kolom pencarian di peta atau geser marker untuk menyesuaikan lokasi.</small>
                                </div>
                            </div>
                            <div class="card-footer">
                                <input type="hidden" name="confirm_password" id="confirm_password">

                                <button type="submit" name="update_lokasi" id="submit_hidden" style="display:none;"></button>

                                <button type="button" data-toggle="modal" data-target="#modalConfirmSave" class="btn btn-info btn-block shadow-sm">
                                    <i class="fas fa-save mr-1"></i> Simpan Lokasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="card shadow">
                        <div class="card-body p-2">
                            <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php 
include "inc/modals.php";
include "inc/footer.php"; 
?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
    $(document).ready(function() {
        <?= $swal_script ?>
    });

    var initialLat = <?= $lokasi['latitude'] ?>;
    var initialLng = <?= $lokasi['longitude'] ?>;

    var map = L.map('map').setView([initialLat, initialLng], 17);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([initialLat, initialLng], {
        draggable: true
    }).addTo(map);

    function updateInput(lat, lng) {
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
    }

    marker.on('dragend', function (e) {
        var pos = marker.getLatLng();
        updateInput(pos.lat, pos.lng);
    });

    var geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: "Cari nama jalan atau lokasi...",
        errorMessage: "Lokasi tidak ditemukan."
    })
    .on('markgeocode', function(e) {
        var bbox = e.geocode.bbox;
        var center = e.geocode.center;
        
        marker.setLatLng(center);
        map.fitBounds(bbox);
        updateInput(center.lat, center.lng);
    })
    .addTo(map);

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateInput(e.latlng.lat, e.latlng.lng);
    });
</script>