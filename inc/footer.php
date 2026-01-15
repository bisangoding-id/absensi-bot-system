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
<footer class="main-footer text-center">
© <a href="https://bisangoding.id" target="blank">2025 AbsensiBot | Ikmal Maulana</a>
</footer>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<?php
if (isset($_POST['submit_password'])) {
    $username = $_SESSION['username'] ?? '';
    $pass_lama  = $_POST['pass_lama'] ?? '';
    $pass_baru  = $_POST['pass_baru'] ?? '';
    $konfirmasi = $_POST['konfirmasi_pass'] ?? '';

    if ($username && $pass_lama && $pass_baru === $konfirmasi) {
        $stmt = $db->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row && password_verify($pass_lama, $row['password'])) {
            $hash = password_hash($pass_baru, PASSWORD_DEFAULT);
            $upd = $db->prepare("UPDATE users SET password = ? WHERE username = ?");
            $upd->bind_param("ss", $hash, $username);
            
            if ($upd->execute()) {
                echo "<script>window.location.href='?status=sukses_pass';</script>";
            }
        } else {
            echo "<script>window.location.href='?status=error_pass';</script>";
        }
    } else {
        echo "<script>window.location.href='?status=error_konfirmasi';</script>";;
    }
}
?>

<script>
$(document).ready(function() {
    const params = new URLSearchParams(window.location.search);

    if ($('#tabel').length) {
        $('#tabel').DataTable({
            "responsive": true,
            "autoWidth": false,
            "dom": "<'row'<'col-md-6'l><'col-md-6'f>>" +
                   "<'row'<'col-md-12'tr>>" +
                   "<'row'<'col-md-5'i><'col-md-7'p>>",
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "paginate": { "next": "»", "previous": "«" }
            }
        });
    }

    if (params.has('status')) {
        let msg = '';
        let icon = 'success';
        let title = 'Berhasil';
        
        const st = params.get('status');
        const jumlah = params.get('jumlah');
        const errorMsg = params.get('msg'); 

        const statusMap = {
            'sukses': 'Data berhasil ditambahkan!',
            'update': 'Data berhasil diperbarui!',
            'hapus':  'Data berhasil dihapus!',
            'reset':  'Data berhasil direset!',
            'sukses_tele':  'Webhook Telegram berhasil diaktifkan',
            'sukses_pass': 'Password berhasil diperbaharui!',
            'import_sukses': `Data berhasil diimport (${jumlah} baris)`
        };


        if (statusMap[st]) {
            msg = statusMap[st];
        } else if (st === 'ganda_total') {
            msg = errorMsg || 'Terdeteksi data ganda pada NIK atau No.HP.';
            icon = 'error';
            title = 'Import Dibatalkan!';
        }

       else if (st === 'error_pass') {
          msg = 'Password lama yang Anda masukkan salah!';
          icon = 'error';
          title = 'Gagal Ubah Password';
       } else if (st === 'error_konfirmasi') {
          msg = 'Konfirmasi password baru tidak cocok!';
          icon = 'warning';
          title = 'Input Tidak Valid';
      }

        if (msg) {
            Swal.fire({ 
                icon: icon, 
                title: title, 
                text: msg, 
                timer: (icon === 'success' ? 2000 : 5000), 
                showConfirmButton: (icon !== 'success') 
            });
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    }

    $(document).on('click', '.btn-edit', function () {
        const d = $(this).data();
        $('#editNik').val(d.nik);
        $('#editNama').val(d.nama);
        $('#editHp').val(d.hp);
        $('#modalEdit').modal('show');
    });

    $(document).on('click', '.btn-edit-absen', function() {
        const d = $(this).data();
        $('#edit_nik').val(d.nik);
        $('#display_nama').text(d.nama);
        $('#edit_masuk').val(d.masuk);
        $('#edit_keluar').val(d.keluar);
        $('#edit_ket').val(d.ket || 'hadir');
        $('#modalEditAbsen').modal('show'); 
    });

    $(document).on('click', '.btn-map', function () {
        const lokasi = $(this).data('lokasi');
        const mapUrl = `https://maps.google.com/maps?q=${encodeURIComponent(lokasi)}&output=embed`;
        $('#mapFrame').attr('src', mapUrl);
        $('#modalMap').modal('show');
    });
});

function konfirmasiHapus(nik) {
    Swal.fire({
        title: 'Hapus Data?',
        text: `Karyawan dengan NIK ${nik} akan dihapus selamanya.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'karyawan.php?hapus=' + nik;
        }
    });
}

function konfirmasiReset(nik, tanggal) {
    Swal.fire({
        title: 'Reset Data?',
        html: `Data absensi NIK ${nik}<br>Pada tanggal <b>${tanggal}</b> akan direset.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reset!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `absensi.php?hapus=${nik}&tanggal=${tanggal}`;
        }
    });
}

function submitWithPassword() {
    const pass = $('#input_password_confirm').val();
    if (!pass) {
        Swal.fire('Peringatan', 'Masukkan password login Anda!', 'warning');
        return;
    }
    
    $('#confirm_password').val(pass);

    if (typeof editor !== 'undefined') {
        $('#file_content').val(editor.getValue());
    }

    $('#submit_hidden').click();
}
</script>