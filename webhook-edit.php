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

$file_wa = 'app/webhook_whatsapp.php';
$file_tg = 'app/webhook_telegram.php';

if (isset($_POST['save_setting'])) {
    $content_wa = $_POST['content_wa'];
    $content_tg = $_POST['content_tg'];
    $input_pass = $_POST['confirm_password'];
    $password_db = $pass_admin; 

    $is_valid = (password_verify($input_pass, $password_db) || $input_pass === $password_db);

    if ($is_valid) {
        $save_wa = file_put_contents($file_wa, $content_wa);
        $save_tg = file_put_contents($file_tg, $content_tg);

        if ($save_wa !== false && $save_tg !== false) {
            $swal_script = "Swal.fire({icon: 'success', title: 'Berhasil!', text: 'Kedua file webhook berhasil diperbarui.', showConfirmButton: false, timer: 2000});";
        } else {
            $swal_script = "Swal.fire('Gagal!', 'Terjadi kesalahan saat menulis file.', 'error');";
        }
    } else {
        $swal_script = "Swal.fire('Akses Ditolak!', 'Password konfirmasi salah.', 'error');";
    }
}

$curr_wa = file_exists($file_wa) ? file_get_contents($file_wa) : "<?php\n\n// Webhook WhatsApp\n\n?>";
$curr_tg = file_exists($file_tg) ? file_get_contents($file_tg) : "<?php\n\n// Webhook Telegram\n\n?>";
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>

<style>
    .ace-fullscreen {
        position: fixed !important;
        top: 0; right: 0; bottom: 0; left: 0;
        z-index: 9999;
        height: 100% !important;
        width: 100% !important;
    }
    .btn-fs {
        position: absolute;
        right: 10px;
        top: 10px;
        z-index: 10;
        opacity: 0.7;
    }
    .btn-fs:hover { opacity: 1; }
</style>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1 class="m-0 text-dark"> Webhook Editor</h1>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form method="POST" action="" id="webhook-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="badge badge-success p-2"><i class="fab fa-whatsapp mr-1"></i> WhatsApp Webhook</span>
                            <a href="backup.php?file=wa">
                                <span class="btn btn-xs btn-outline-secondary shadow-sm"> <i class="fas fa-download mr-1"></i> Backup Webhook Whatsapp </span>
                            </a>
                        </div>
                        <div class="card card-outline card-success shadow-sm position-relative"> 
                            <button type="button" class="btn btn-sm btn-dark btn-fs" onclick="toggleFullScreen('editor_wa')">
                                <i class="fas fa-expand"></i>
                            </button>
                            <div class="card-body p-0">
                                <div id="editor_wa" style="height: 500px; width: 100%;"><?= htmlspecialchars($curr_wa) ?></div>
                                <textarea name="content_wa" id="content_wa" style="display:none;"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <span class="badge badge-info p-2"><i class="fab fa-telegram-plane mr-1"></i> Telegram Webhook</span>
                                <a href="backup.php?file=tel">
                                <span class="btn btn-xs btn-outline-secondary shadow-sm"> <i class="fas fa-download mr-1"></i> Backup Webhook Telegrm </span>
                            </a>
                        </div>
                        <div class="card card-outline card-info shadow-sm position-relative"> 
                            <button type="button" class="btn btn-sm btn-dark btn-fs" onclick="toggleFullScreen('editor_tg')">
                                <i class="fas fa-expand"></i>
                            </button>
                            <div class="card-body p-0">
                                <div id="editor_tg" style="height: 500px; width: 100%;"><?= htmlspecialchars($curr_tg) ?></div>
                                <textarea name="content_tg" id="content_tg" style="display:none;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card card-body shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="info-warning2">
                                 <i class="fas fa-info-circle text-warning mr-1"></i> 
                                    Sebelum melakukan perubahan pada file webhook, silakan backup filenya terlebih dahulu untuk menghindari kesalahan sistem.
                                </div>
                                <div>
                                    <input type="hidden" name="confirm_password" id="confirm_password">
                                    <button type="button" data-toggle="modal" data-target="#modalConfirmSave" class="btn btn-tosca shadow-sm">
                                        <i class="fas fa-save mr-2"></i> Simpan Semua Perubahan
                                    </button>
                                    <button type="submit" name="save_setting" id="submit_hidden" style="display:none;"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<?php 
include "inc/modals.php";
include "inc/footer.php"; 
?>

<script>
    var editorWa, editorTg;

    $(document).ready(function() {
        <?= $swal_script ?>

        editorWa = ace.edit("editor_wa");
        editorWa.setTheme("ace/theme/monokai");
        editorWa.session.setMode("ace/mode/php");

        editorTg = ace.edit("editor_tg");
        editorTg.setTheme("ace/theme/monokai");
        editorTg.session.setMode("ace/mode/php");

        [editorWa, editorTg].forEach(function(ed) {
            ed.setOptions({
                enableBasicAutocompletion: true,
                enableLiveAutocompletion: true,
                showPrintMargin: false,
                fontSize: "15px"
            });
        });

        $('#webhook-form').on('submit', function() {
            $('#content_wa').val(editorWa.getValue());
            $('#content_tg').val(editorTg.getValue());
        });
    });

    function toggleFullScreen(editorId) {
        var editorElem = document.getElementById(editorId);
        editorElem.classList.toggle("ace-fullscreen");
        var editor = (editorId === 'editor_wa') ? editorWa : editorTg;
        editor.resize();
        $(document).on('keydown.fullscreen', function(e) {
            if (e.key === "Escape" && editorElem.classList.contains("ace-fullscreen")) {
                toggleFullScreen(editorId);
                $(document).off('keydown.fullscreen');
            }
        });
    }
</script>