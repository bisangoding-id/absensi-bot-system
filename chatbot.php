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
 * * Dengan menggunakan kode ini, Anda setuju untuk tetap menyertakan atribusi 
 * pengembang asli. 
 */

require_once "inc/header.php"; 
require_once "inc/sidebar.php";

$swal_script = "";

if (isset($_POST['update_bot'])) {
    $wa_token   = $_POST['whatsapp_token'];
    $tg_token   = $_POST['telegram_token'];
    $wa_status  = $_POST['wa_status']; 
    $tg_status  = $_POST['tg_status']; 
    $id         = $_POST['id'];
    $url        = trim($_POST['webhook_url']); 
    $input_pass = $_POST['confirm_password']; 

    $is_valid = false;
    if (password_verify($input_pass, $pass_admin) || $input_pass === $pass_admin) {
        $is_valid = true;
    }

    if ($is_valid) {

        if (!empty($tg_token) && $tg_status === 'ON') {
            $api_url = "https://api.telegram.org/bot$tg_token/setWebhook?url=" . urlencode($url);
            @file_get_contents($api_url); 
        }

        $stmt = $db->prepare("UPDATE sistem SET webhook_url = ?, whatsapp_token = ?, telegram_token = ?, wa_status = ?, tg_status = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $url, $wa_token, $tg_token, $wa_status, $tg_status, $id);
        
        if ($stmt->execute()) {
            $swal_script = "Swal.fire({icon: 'success', title: 'Berhasil!', text: 'Konfigurasi Chatbot diperbarui.', showConfirmButton: false, timer: 2000}).then(() => { window.location.href='chatbot'; });";
        } else {
            $swal_script = "Swal.fire('Gagal!', 'Terjadi kesalahan saat memperbarui database.', 'error');";
        }
        $stmt->close();
    } else {
        $swal_script = "Swal.fire('Akses Ditolak!', 'Password konfirmasi salah.', 'error');";
    }
}

$query = $db->query("SELECT * FROM sistem LIMIT 1");
$bot = $query->fetch_assoc();
?>

<style>
    .flex-input-container { display: flex; align-items: center; gap: 15px; }
    .flex-grow-1 { flex-grow: 1; }
    .slider-inline-wrapper { display: flex; align-items: center; gap: 8px; min-width: 80px; }
    .status-text { font-weight: bold; font-size: 14px; }
    
    .switch { position: relative; display: inline-block; width: 40px; height: 20px; margin-bottom: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 20px; }
    .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #28a745; }
    input:checked + .slider:before { transform: translateX(20px); }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Konfigurasi Chatbot</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary card-outline shadow">
                        <div class="card-header">
                            <h3 class="card-title">Pengaturan API & Sistem</h3>
                        </div>
                        <form method="POST" action="" id="formChatbot">
                            <div class="card-body">
                    
                                <input type="hidden" name="id" value="<?= $bot['id'] ?>">
                                
                                <div class="form-group">
                                    <label for="token_wa">Token WhatsApp API</label>
                                    <div class="flex-input-container">
                                        <div class="input-group flex-grow-1">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-key"></i></span></div>
                                            <input type="password" name="whatsapp_token" id="token_wa" class="form-control" value="<?= $bot['whatsapp_token'] ?>" <?= ($bot['wa_status'] == 'OFF') ? 'disabled' : '' ?> required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="token_wa" <?= ($bot['wa_status'] == 'OFF') ? 'disabled' : '' ?>><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="slider-inline-wrapper">
                                            <label class="switch">
                                                <input type="checkbox" class="status-toggle-token" data-input="token_wa" data-target="wa_status_val" data-label="wa-label" <?= ($bot['wa_status'] == 'ON') ? 'checked' : '' ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <span id="wa-label" class="status-text <?= ($bot['wa_status'] == 'ON') ? 'text-success' : 'text-danger' ?>"><?= $bot['wa_status'] ?></span>
                                            <input type="hidden" name="wa_status" id="wa_status_val" value="<?= $bot['wa_status'] ?>">
                                        </div>
                                    </div>
                                    <small class="text-muted">Token didapatkan dari WhatsApp API Provider.</small>
                                </div>

                                <div class="form-group">
                                    <label for="token_tg">Token Telegram API</label>
                                    <div class="flex-input-container">
                                        <div class="input-group flex-grow-1">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-key"></i></span></div>
                                            <input type="password" name="telegram_token" id="token_tg" class="form-control" value="<?= $bot['telegram_token'] ?>" <?= ($bot['tg_status'] == 'OFF') ? 'disabled' : '' ?> required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="token_tg" <?= ($bot['tg_status'] == 'OFF') ? 'disabled' : '' ?>><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                        <div class="slider-inline-wrapper">
                                            <label class="switch">
                                                <input type="checkbox" class="status-toggle-token" data-input="token_tg" data-target="tg_status_val" data-label="tg-label" <?= ($bot['tg_status'] == 'ON') ? 'checked' : '' ?>>
                                                <span class="slider round"></span>
                                            </label>
                                            <span id="tg-label" class="status-text <?= ($bot['tg_status'] == 'ON') ? 'text-success' : 'text-danger' ?>"><?= $bot['tg_status'] ?></span>
                                            <input type="hidden" name="tg_status" id="tg_status_val" value="<?= $bot['tg_status'] ?>">
                                        </div>                                        
                                    </div>
                                <small class="text-muted">Token didapatkan dari @BotFather.</small>

                                </div>
                                <div class="form-group mt-3" style="max-width: 730px;">
                                    <label for="url_webhook">URL Webhook Telegram</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                                        </div>
                                        <input type="url" name="webhook_url" id="url_webhook" class="form-control" placeholder="https://domainanda.com" value="<?= $bot['webhook_url'] ?? '' ?>">
                                    </div>
                                    <small class="text-muted">Masukkan URL (dengan https).</small>
                                </div>                                    
                                
                            </div>

                            <div class="card-footer text-right">
                                <input type="hidden" name="confirm_password" id="confirm_password">
                                <button type="submit" name="update_bot" id="submit_hidden" style="display:none;"></button>
                                <button type="button" data-toggle="modal" data-target="#modalConfirmSave" class="btn btn-info px-4 shadow-sm">
                                    <i class="fas fa-save mr-1"></i> Simpan Konfigurasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-info-circle"></i> Panduan Pengaturan</h3>
                        </div>
                        <div class="card-body">
                            <h5>Cara Mengatur Bot:</h5>
                            <ol>
                                <li>Gunakan ikon <i class="fas fa-eye"></i> untuk melihat token yang tersembunyi.</li>
                                <li>Isi <b>Token WhatsApp</b> & <b>Telegram</b> dengan benar dan valid.</li>
                                <li>Fitur <b> Waktu Record Otomatis </b> hanya berlaku untuk versi berbayar. Fitur ini memungkinan perekaman otomatis
                                    untuk karyawan yang telat absen atau tidak absen sama sekali.
                                </li>
                                <li>Klik simpan dan masukkan password admin untuk verifikasi keamanan.</li>
                            </ol>
                            <div class="alert alert-warning shadow-sm border-0">
                                <i class="fas fa-exclamation-triangle mr-1"></i> <b>Penting:</b> Jangan bagikan Token API Anda kepada siapapun.
                            </div>
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

<script>
$(document).ready(function() {
    <?= $swal_script ?>

    $('.toggle-password').on('click', function() {
        const input = $('#' + $(this).data('target'));
        const icon = $(this).find('i');
        if (input.attr('type') === "password") {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('.status-toggle-token').on('change', function() {
        const isChecked = this.checked;
        const status = isChecked ? "ON" : "OFF";
        const inputId = $(this).data('input');
        const hiddenInputId = $(this).data('target');
        const labelId = $(this).data('label');
        
        $('#' + hiddenInputId).val(status);
        $('#' + labelId).text(status).toggleClass('text-success', isChecked).toggleClass('text-danger', !isChecked);
        
        const inputElement = $('#' + inputId);
        inputElement.prop('disabled', !isChecked);
        inputElement.closest('.input-group').find('button').prop('disabled', !isChecked);
    });

    $('#formChatbot').on('submit', function() {
        $('#token_wa, #token_tg, #time').prop('disabled', false);
    });
});
</script>