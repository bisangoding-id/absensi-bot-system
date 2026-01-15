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
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/flow-chart.css">

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Flow System</h1>
        </div>
    </section>


<div class="box">
<div class="flow-container">  
  <div class="start-zone">
    <div class="cerd primary">
      <i class="fas fa-mobile-screen"></i>
      <b>MULAI</b>
      <span>User kirim pesan</span>
    </div>
    
    <svg class="connector-svg">
      <path d="M550 0 V 30" />
      <path d="M135 30 H 965" />
      <path d="M135 30 V 60" />
      <path d="M410 30 V 60" />
      <path d="M685 30 V 60" />
      <path d="M965 30 V 60" />
    </svg>
  </div>

  <div class="row">
    <div class="col">
      <div class="section-label">Absensi Hadir</div>
      <div class="node-wrapper">
        <div class="cerd primary"><i class="fa-solid fa-user-check"></i><b>Ketik : Hadir</b></div>
        <div class="v-line"></div>
        <div class="cerd"><i class="fa-solid fa-location-dot"></i><b>Proses</b><span>Bot minta lokasi</span></div>
        <div class="v-line"></div>
        <div class="cerd"><i class="fa-solid fa-cloud-arrow-up"></i><b>Data Tersimpan</b><span>(Jam masuk, lokasi, status)</span></div>
      </div>
    </div>

    <div class="col">
      <div class="section-label">Izin/Sakit</div>
      <div class="node-wrapper">
        <div class="cerd primary"><i class="fa-solid fa-notes-medical"></i><b>Ketik : Izin/Sakit</b></div>
        <div class="v-line"></div>
        <div class="cerd"><i class="fa-solid fa-cloud-arrow-up"></i><b>Data Tersimpan</b><span>(Status)</span></div>
      </div>
    </div>

    <div class="col">
      <div class="section-label">Selesai Kerja</div>
      <div class="node-wrapper">
        <div class="cerd primary"><i class="fa-solid fa-door-open"></i><b>Ketik : Pulang</b></div>
        <div class="v-line"></div>
        <div class="cerd"><i class="fa-solid fa-magnifying-glass"></i><b>Cek Status</b><span>Status : Hadir</span></div>
        <div class="v-line"></div>
        <div class="cerd"><i class="fa-solid fa-clock-rotate-left"></i><b>Update</b><span>(Jam Keluar)</span></div>
      </div>
    </div>

    <div class="col">
      <div class="section-label">Koreksi Data</div>
      <div class="node-wrapper">
        <div class="cerd primary"><i class="fa-solid fa-arrows-rotate"></i><b> Ketik : Reset</b></div>
        <div class="v-line"></div>
        <div class="cerd"><i class="fa-solid fa-trash-can"></i><b>Hapus Data Absen</b><span>Sesuai tanggal kirim</span></div>
      </div>
    </div>
  </div>

  <div class="footer-flow">
    <div class="col" style="width:auto;">
      <div class="section-label">Validasi Data Ganda</div>
      <div class="node-wrapper">
        <div class="cerd primary"><i class="fa-solid fa-user-shield"></i><b>Absen ulang</b><span>(Data absen sudah ada)</span></div>
        <div class="v-line"></div>
        <div class="cerd"><i class="fa-solid fa-circle-info"></i><b>Info Bot</b><span>Status absen sudah ada <br>(Diberi opsi reset)</span></div>
      </div>
    </div>

    <div class="col" style="width:auto;">
      <div class="section-label">Bantuan Sistem</div>
      <div class="node-wrapper">
        <div class="cerd primary"><i class="fa-solid fa-keyboard"></i><b>Ketik Sembarang</b><span>Keyword salah</span></div>
        <div class="v-line"></div>
        <div class="cerd"><i class="fa-solid fa-circle-info"></i><b>Info Bot</b><span> Diarahkan memilih <br>keyword yang benar</span></div>
      </div>
    </div>
  </div>

</div>



<?php 
include "inc/modals.php"; 
include "inc/footer.php"; 
?>