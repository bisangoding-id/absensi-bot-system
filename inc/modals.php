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
?>
<div class="modal fade" id="modalUbahPassword" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-tosca py-2">
                <h5 class="modal-title text-white"><i class="fas fa-key mr-2"></i> Ubah Password</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body py-4">
                    <input type="password" name="pass_lama" class="form-control mb-3" placeholder="Password Lama" required>
                    <input type="password" name="pass_baru" class="form-control mb-3" placeholder="Password Baru" required>
                    <input type="password" name="konfirmasi_pass" class="form-control" placeholder="Konfirmasi Password Baru" required>
                </div>
                <div class="modal-footer pt-0 border-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                    <button type="submit" name="submit_password" class="btn btn-tosca">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-none border-0">
            <form method="POST">
                <div class="modal-header bg-tosca">
                    <h5 class="modal-title">Tambah Karyawan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body py-4">
                    <div class="form-group">
                        <label>NIK</label>
                        <input name="nik" class="form-control" placeholder="Contoh: 2025001" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input name="nama" class="form-control" placeholder="Nama Karyawan" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor WhatsApp</label>
                        <input name="no_hp" class="form-control" placeholder="628123xxx" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-tosca px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-none border-0">
            <form method="POST">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"> Ubah Data</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body py-4">
                    <input type="hidden" name="nik_lama" id="editNik">
                    <div class="form-group">
                        <label>Nama Karyawan</label>
                        <input name="nama" id="editNama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor WhatsApp</label>
                        <input name="no_hp" id="editHp" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="update" class="btn btn-info px-4">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalMap" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-none border-0">
            <div class="modal-header bg-tosca py-2">
                <h5 class="modal-title text-white">Lokasi Absensi</h5>
                <button type="button" class="close text-white" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body p-0">
                <iframe id="mapFrame" src="" width="100%" height="450" style="border:0;" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditAbsen" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-none border-0">
            <form method="POST">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Absensi <b id="display_nama"></b></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="nik" id="edit_nik">
                    <input type="hidden" name="tanggal" value="<?= $selected_date ?>">
                    <input type="hidden" name="user_lat" id="user_lat">
                    <input type="hidden" name="user_lng" id="user_lng">
                    
                    <div class="form-group">
                        <label>Jam Masuk</label>
                        <input type="time" name="jam_masuk" id="edit_masuk" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Jam Keluar</label>
                        <input type="time" name="jam_keluar" id="edit_keluar" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <select name="keterangan" id="edit_ket" class="form-control" required>
                            <option value="hadir">HADIR</option>
                            <option value="sakit">SAKIT</option>
                            <option value="izin">IZIN</option>
                            <option value="tanpa keterangan">TANPA KETERANGAN</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="update_absensi" class="btn btn-info px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="modalConfirmSave" tabindex="-1" role="dialog" aria-labelledby="modalConfirmSaveLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none;">
            <div class="modal-header bg-info text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <h5 class="modal-title" id="modalConfirmSaveLabel">
                    <i class="fas fa-lock mr-2"></i> Konfirmasi Keamanan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <i class="fas fa-shield-alt fa-3x text-info mb-2"></i>
                    <p class="text-muted">Untuk menyimpan perubahan pada data sistem, silakan masukkan password login Anda.</p>
                </div>
                <div class="form-group">
                    <input type="password" id="input_password_confirm" class="form-control form-control-lg" placeholder="Masukkan password..." 
                    style="border-radius: 8px;">
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
                <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Batal</button>
                <button type="button" onclick="submitWithPassword()" class="btn btn-info px-4 shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> Konfirmasi & Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header bg-info">
                <h5 class="modal-title"><i class="fas fa-file-excel mr-2"></i> Import Data Karyawan</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="info-warning2">
                        <h6><i class="fas fa-info-circle"></i> Petunjuk Format:</h6>
                        <small class="d-block mb-2">
                            Pastikan file berformat <b>.csv</b> dengan urutan kolom: <b>No, NIK, Nama, No. HP</b>.
                        </small>
                        <a href="import/data-karyawan.csv" class="btn btn-xs btn-outline-info shadow-sm" download>
                            <i class="fas fa-download mr-1"></i> Download Format CSV
                        </a>
                    </div>
                <div class="form-group mt-3">
    <label class="font-weight-bold">Pilih File CSV</label>
    <div class="custom-file-container">
        <input type="file" name="file_csv" id="file_csv" class="custom-file-input-hidden" accept=".csv" required>
        <label for="file_csv" class="custom-file-label-v2">
            <span id="file-name-display">Klik untuk memilih file...</span>
            <div class="btn-browse">Cari</div>
        </label>
    </div>
</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="import_data" class="btn btn-info px-4 shadow-sm">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="webhook-telegram" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-none border-0">
            <form method="POST">
                <div class="modal-header bg-info">
                    <h5 class="modal-title"> Set Webhook Telegram</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body py-4">
                    <input type="hidden" name="nik_lama" id="editNik">
                    <div class="form-group">
                        <label>URL</label>
                        <input name="url" class="form-control" >
                    </div>
                    <div class="form-group">
                        <label>Token Telegram</label>
                        <input name="token" class="form-control" value="<?= $bot['telegram_token'] ?>" type="password">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" name="proses" class="btn btn-info px-4">Aktivasi</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
document.getElementById('file_csv').addEventListener('change', function(e) {
    const fileName = e.target.files[0] ? e.target.files[0].name : "Klik untuk memilih file...";
    const label = e.target.nextElementSibling;
    const display = document.getElementById('file-name-display');
    
    display.innerText = fileName;
    
    if (e.target.files[0]) {
        label.classList.add('file-selected');
    } else {
        label.classList.remove('file-selected');
    }
});
</script>