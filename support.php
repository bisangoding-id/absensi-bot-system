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

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark">Pusat Dukungan</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-7">
                    <div class="card card-flat p-4">
                        <p class="text-muted">
                            Aplikasi <b>ABSENSI BOT</b> ini menggunakan integrasi <b>WhatsApp Gateway</b> untuk pengiriman notifikasi dan laporan real-time. 
                            Jika bot tidak merespon atau pesan tidak terkirim, harap pastikan hal-hal berikut:
                        </p>
                        <ul class="text-muted">
                            <li class="mb-2"><b>API Key / Token</b> WhatsApp Gateway terisi sesuai dengan data yang valid.</li>
                            <li class="mb-2">Pastikan nomor pengirim sudah <b>Terhubung (Connected)</b> pada dashboard provider WhatsApp Anda.</li>
                            <li class="mb-2">Periksa sisa kuota pesan atau masa aktif layanan API Anda.</li>
                            <li class="mb-2">Server wajib menggunakan <b>SSL (HTTPS)</b> agar Webhook dapat menerima data absensi secara instan.</li>
                        </ul>

                        <div class="alert alert-info">
                            Untuk custom fitur dan implementasi aplikasi <b>ABSENSI BOT</b>, silahkan hubungi kami melalui kontak yang tersedia.
                        </div>

                        <hr>

                        <h5 class="mt-4"><i class="fas fa-copyright"></i> Hak Cipta & Lisensi</h5>
                        <p>Aplikasi ini bersifat <b>Open Source</b> di bawah lisensi <a href="https://id.wikipedia.org/wiki/Lisensi_Publik_Umum_GNU" target="blank"><b>GPL</b></a>.</p>
                        
                        <div class="license-box shadow-none border-0" style="background: rgba(0,0,0,0.02); padding: 15px; border-radius: 8px; border-left: 4px solid #6c757d !important;">
                            <p class="text-muted mb-0" style="line-height: 1.6;">
                                Copyright (c) 2025 <b>Ikmal Maulana</b> | Absensi Bot System<br><br>
                                Permission is granted to use, copy, and modify this software provided that the above copyright notice is included in all copies. 
                                The software is provided "as is" without any warranties.
                            </p>
                        </div>

                        

                        <p class="mt-3 text-muted font-italic">
                            * Dengan menggunakan kode ini, Anda setuju untuk tetap menyertakan atribusi pengembang asli 
                        dan tidak menyalahgunakan hak distribusi untuk keuntungan komersial sepihak tanpa izin.
                        </p>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card card-flat p-4 text-center">
                        <h4 class="mb-4">Hubungi Kami</h4>

                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=bisangoding@gmail.com" target="_blank" class="text-decoration-none">
                            <div class="info-box-flat text-left">
                                <i class="fas fa-envelope" style="color: #118a95; font-size: 30px;"></i>
                                <div>
                                    <strong style="color: #333;">Email</strong><br>
                                    <span class="text-muted">bisangoding@gmail.com</span>
                                </div>
                            </div>
                        </a>

                        <a href="https://youtube.com/@KangIkmal" target="_blank" class="text-decoration-none">
                            <div class="info-box-flat text-left">
                                <i class="fab fa-youtube" style="color: #118a95; font-size: 30px;"></i>
                                <div>
                                    <strong style="color: #333;">Youtube</strong><br>
                                    <span class="text-muted">Kang Ikmal</span>
                                </div>
                            </div>
                        </a>

                        <a href="https://instagram.com/KangIkmal" target="_blank" class="text-decoration-none">
                            <div class="info-box-flat text-left">
                                <i class="fab fa-instagram" style="color: #118a95; font-size: 30px;"></i>
                                <div>
                                    <strong style="color: #333;">Instagram</strong><br>
                                    <span class="text-muted">Kang Ikmal</span>
                                </div>
                            </div>
                        </a>

                        <a href="http://bisangoding.id" target="_blank" class="text-decoration-none">
                            <div class="info-box-flat text-left">
                                <i class="fas fa-globe" style="color: #118a95; font-size: 30px;"></i>
                                <div>
                                    <strong style="color: #333;">Website</strong><br>
                                    <span class="text-muted">http://bisangoding.id</span>
                                </div>
                            </div>
                        </a>
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