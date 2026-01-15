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

if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('HTTP/1.0 403 Forbidden');
    exit("Direct access not allowed");
}


$is_pengaturan = in_array($current_page, ['lokasi', 'chatbot', 'webhook', 'webhook-edit']);

?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
<a href="home" class="brand-link text-center" style="margin-top: 20px;">
    <span class="logo-sidebar"></span>
    <span class="brand-text font-weight-light"><b>ABSENSI</b> BOT</span>
</a>
  <div class="sidebar">
    <nav class="mt-5">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
        
        <li class="nav-item">
          <a href="home" class="nav-link <?= ($current_page == 'home') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="karyawan" class="nav-link <?= ($current_page == 'karyawan') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Data Karyawan</p>
          </a>
        </li>
        
        <li class="nav-item">
          <a href="absensi" class="nav-link <?= ($current_page == 'absensi') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-clipboard-check"></i>
            <p>Monitoring Absensi</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="laporan" class="nav-link <?= ($current_page == 'laporan') ? 'active' : '' ?>">
            <i class="nav-icon fas fa-file-alt"></i>
            <p>Laporan</p>
          </a>
        </li>

        <li class="nav-item <?= $is_pengaturan ? 'menu-is-opening menu-open' : '' ?>">
            <a href="#" class="nav-link <?= $is_pengaturan ? 'active' : '' ?>">
                <i class="nav-icon fas fa-cog"></i>
                <p>
                    Pengaturan
                    <i class="right fas fa-angle-left"></i>
                </p>
            </a>

            <ul class="nav nav-treeview" style="display: <?= $is_pengaturan ? 'block' : 'none' ?>;">
                <li class="nav-item">
                    <a href="lokasi" class="nav-link <?= ($current_page == 'lokasi') ? 'active' : '' ?>" style="padding-left: 2rem;">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>Lokasi Kantor</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="chatbot" class="nav-link <?= ($current_page == 'chatbot') ? 'active' : '' ?>" style="padding-left: 2rem;">
                        <i class="nav-icon fas fa-robot"></i>
                        <p>Konfigurasi Bot</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="webhook-edit" class="nav-link <?= ($current_page == 'webhook-edit') ? 'active' : '' ?>" style="padding-left: 2rem;">
                        <i class="nav-icon fas fa-link"></i>
                        <p>Webhook</p>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-item">
              <a href="flow" class="nav-link <?= ($current_page == 'flow') ? 'active' : '' ?>">
                 <i class="nav-icon fas fa-project-diagram"></i>
    <p>Flow System</p>
  </a>
</li>

        <li class="nav-item">
            <a href="support" class="nav-link <?= ($current_page == 'support') ? 'active' : '' ?>">
                <i class="nav-icon fas fa-headset"></i>
                <p>Support</p>
            </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>