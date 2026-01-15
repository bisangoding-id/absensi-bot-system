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

$selected_month = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$selected_year  = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni',
    '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];

$tampilan_periode = $nama_bulan[$selected_month] . " " . $selected_year;
$sql_stat = "SELECT 
                COUNT(DISTINCT k.nik) as total_kar,
                SUM(CASE WHEN a.keterangan = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.keterangan = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.keterangan = 'izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.keterangan = 'tanpa keterangan' THEN 1 ELSE 0 END) as ta
             FROM karyawan k
             LEFT JOIN absensi a ON k.nik = a.nik 
                AND MONTH(a.tanggal) = ? 
                AND YEAR(a.tanggal) = ?";

$stmt_s = $db->prepare($sql_stat);
$stmt_s->bind_param("ss", $selected_month, $selected_year);
$stmt_s->execute();
$stat = $stmt_s->get_result()->fetch_assoc();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Rekapitulasi Absensi</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card filter-card shadow-sm">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label><i class="fas fa-calendar-alt mr-1"></i> Bulan</label>
                            <select id="filterBulan" class="form-control filter-dinamis">
                                <?php foreach ($nama_bulan as $m => $nama): ?>
                                    <option value="<?= $m ?>" <?= $selected_month == $m ? 'selected' : '' ?>>
                                        <?= $nama ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label><i class="fas fa-calendar mr-1"></i> Tahun</label>
                            <select id="filterTahun" class="form-control filter-dinamis">
                                <?php for($i = date('Y'); $i >= 2023; $i--): ?>
                                    <option value="<?= $i ?>" <?= $selected_year == $i ? 'selected' : '' ?>>
                                        <?= $i ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-7 text-right">
                            <div id="tempatTombol"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Karyawan</span>
                            <span class="info-box-number"><?= $stat['total_kar'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Hadir</span>
                            <span class="info-box-number"><?= $stat['hadir'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning text-white"><i class="fas fa-medkit"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Sakit</span>
                            <span class="info-box-number"><?= $stat['sakit'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-envelope"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Izin</span>
                            <span class="info-box-number"><?= $stat['izin'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md col-sm-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total TA</span>
                            <span class="info-box-number"><?= $stat['ta'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-tosca shadow-none">
                <div class="card-header">
                    <h3 class="card-title">Rincian Periode: <b><?= $tampilan_periode ?></b></h3>
                </div>
                <div class="card-body">
                    <table id="rekapTable" class="table table-bordered table-hover">
                        <thead>
                            <tr class="text-center">
                                <th width="50" class="text-center">No</th>
                                <th width="100" class="text-center">NIK</th>
                                <th>Nama Karyawan</th>
                                <th class="text-center">Hadir</th>
                                <th class="text-center">Sakit</th>
                                <th class="text-center">Izin</th>
                                <th class="text-center">TA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_table = "SELECT k.nik, k.nama,
                                            SUM(CASE WHEN a.keterangan = 'hadir' THEN 1 ELSE 0 END) as total_hadir,
                                            SUM(CASE WHEN a.keterangan = 'sakit' THEN 1 ELSE 0 END) as total_sakit,
                                            SUM(CASE WHEN a.keterangan = 'izin' THEN 1 ELSE 0 END) as total_izin,
                                            SUM(CASE WHEN a.keterangan = 'tanpa keterangan' THEN 1 ELSE 0 END) as total_ta
                                        FROM karyawan k
                                        LEFT JOIN absensi a ON k.nik = a.nik AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
                                        GROUP BY k.nik, k.nama 
                                        ORDER BY k.nama ASC";
                            
                            $stmt_t = $db->prepare($sql_table);
                            $stmt_t->bind_param("ss", $selected_month, $selected_year);
                            $stmt_t->execute();
                            $res_t = $stmt_t->get_result();
                            
                            $no = 1;
                            while($row = $res_t->fetch_assoc()):
                            ?>
                            <tr class="text-center">
                                <td><?= $no++ ?></td>
                                <td><?= $row['nik'] ?></td>
                                <td class="text-left"><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= $row['total_hadir'] ?></td>
                                <td><?= $row['total_sakit'] ?></td>
                                <td><?= $row['total_izin'] ?></td>
                                <td><?= $row['total_ta'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
$(function () {
    $('.filter-dinamis').on('change', function() {
        var m = $('#filterBulan').val();
        var y = $('#filterTahun').val();
        window.location.href = 'laporan.php?bulan=' + m + '&tahun=' + y;
    });

    var namaFileCustom = 'LAPORAN ABSENSI <?= strtoupper($tampilan_periode) ?>';
    
    var table = $("#rekapTable").DataTable({
        "responsive": true, 
        "autoWidth": false,
        "pageLength": 5,
        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
        "dom": "<'row'<'col-md-6'l><'col-md-6'f>>" + "<'row'<'col-md-12'tr>>" + "<'row'<'col-md-5'i><'col-md-7'p>>",
        "buttons": [
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn-danger btn-sm mx-1 shadow-sm',
                title: '',
                filename: namaFileCustom,
                customize: function (doc) {
                    doc.pageMargins = [30, 30, 30, 30];
                    var totalKar = parseInt('<?= $stat['total_kar'] ?>') || 1;
            
                    doc.content.splice(0, 0, 
                        { text: 'LAPORAN ABSENSI', fontSize: 14, bold: true, alignment: 'center', margin: [0, 0, 0, 5] },
                        { text: 'BULAN <?= strtoupper($tampilan_periode) ?>', fontSize: 12, bold: true, alignment: 'center', margin: [0, 0, 0, 15] },
                        {
                            table: {
                                widths: ['80%', '10%', '10%'],
                                body: [
                                    [{text: 'REKAP', colSpan: 3, alignment: 'center', fillColor: '#f2f2f2', bold: true}, {}, {}],
                                    ['Jumlah Karyawan', {text: '<?= $stat['total_kar'] ?>', alignment: 'center'}, ''],
                                    ['Total Hadir', {text: '<?= $stat['hadir'] ?>', alignment: 'center'}, {text: (('<?= $stat['hadir'] ?>' / totalKar) * 100).toFixed(1) + '%', alignment: 'center'}],
                                    ['Total Sakit', {text: '<?= $stat['sakit'] ?>', alignment: 'center'}, {text: (('<?= $stat['sakit'] ?>' / totalKar) * 100).toFixed(1) + '%', alignment: 'center'}],
                                    ['Total Izin', {text: '<?= $stat['izin'] ?>', alignment: 'center'}, {text: (('<?= $stat['izin'] ?>' / totalKar) * 100).toFixed(1) + '%', alignment: 'center'}],
                                    ['Total Tanpa Keterangan', {text: '<?= $stat['ta'] ?>', alignment: 'center'}, {text: (('<?= $stat['ta'] ?>' / totalKar) * 100).toFixed(1) + '%', alignment: 'center'}]
                                ]
                            },
                            margin: [0, 0, 0, 20],
                            layout: { hLineWidth: function() { return 0.5; }, vLineWidth: function() { return 0.5; } }
                        },
                        { text: 'RINCIAN ABSENSI', fontSize: 11, bold: true, margin: [0, 10, 0, 5] }
                    );

                    if (doc.content[4] && doc.content[4].table) {
                        doc.content[4].layout = {
                            hLineWidth: function() { return 0.5; },
                            vLineWidth: function() { return 0.5; },
                            hLineColor: function() { return '#000000'; },
                            vLineColor: function() { return '#000000'; }
                        };
                        
                        doc.content[4].table.widths = ['5%', '15%', '35%', '11.25%', '11.25%', '11.25%', '11.25%'];
                        var body = doc.content[4].table.body;
                        
                        for (var i = 0; i < body.length; i++) {
                            for (var j = 0; j < body[i].length; j++) {
                                body[i][j].fillColor = '#ffffff';
                                if (j !== 2) body[i][j].alignment = 'center';
                                if (i === 0) { 
                                    body[i][j].fillColor = '#636363'; 
                                    body[i][j].color = '#ffffff'; 
                                    body[i][j].bold = true; 
                                }
                            }
                        }
                    }
                }
            },
            { 
                extend: 'excelHtml5', 
                text: '<i class="fas fa-file-excel"></i> Excel', 
                className: 'btn-success btn-sm mx-1 shadow-sm',
                filename: namaFileCustom,
                title: '', 
                customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    var totalKar = parseInt('<?= $stat['total_kar'] ?>') || 1;

                    function buildRow(index, data, style = "2") {
                        var row = '<row r="' + index + '">';
                        var columns = [data[0], "", "", "", "", data[1] || "", data[2] || ""];
                        for (var i = 0; i < columns.length; i++) {
                            var colLetter = String.fromCharCode(65 + i);
                            if (columns[i] === "" && i > 0 && i < 5) continue; 
                            row += '<c t="inlineStr" r="' + colLetter + index + '" s="' + style + '">';
                            row += '<is><t>' + columns[i] + '</t></is>';
                            row += '</c>';
                        }
                        row += '</row>';
                        return row;
                    }

                    var h1 = buildRow(1, ["LAPORAN ABSENSI"], "51"); 
                    var h2 = buildRow(2, ["BULAN <?= strtoupper($tampilan_periode) ?>"], "51");
                    var e3 = buildRow(3, [""]);
                    var rh = buildRow(4, ["REKAP"], "51");
                    var r1 = buildRow(5, ["Jumlah Karyawan", "<?= $stat['total_kar'] ?>"]);
                    var r2 = buildRow(6, ["Total Hadir", "<?= $stat['hadir'] ?>", (('<?= $stat['hadir'] ?>' / totalKar) * 100).toFixed(1) + '%']);
                    var r3 = buildRow(7, ["Total Sakit", "<?= $stat['sakit'] ?>", (('<?= $stat['sakit'] ?>' / totalKar) * 100).toFixed(1) + '%']);
                    var r4 = buildRow(8, ["Total Izin", "<?= $stat['izin'] ?>", (('<?= $stat['izin'] ?>' / totalKar) * 100).toFixed(1) + '%']);
                    var r5 = buildRow(9, ["Total TA", "<?= $stat['ta'] ?>", (('<?= $stat['ta'] ?>' / totalKar) * 100).toFixed(1) + '%']);
                    var e10 = buildRow(10, [""]);
                    var hr = buildRow(11, ["RINCIAN ABSENSI"], "51");

                    var sheetData = sheet.getElementsByTagName('sheetData')[0];
                    var originalRows = sheetData.getElementsByTagName('row');
                    
                    for (var i = 0; i < originalRows.length; i++) {
                        var oldIdx = parseInt(originalRows[i].getAttribute('r'));
                        var newIdx = oldIdx + 11;
                        originalRows[i].setAttribute('r', newIdx);
                        var cells = originalRows[i].getElementsByTagName('c');
                        for (var j = 0; j < cells.length; j++) {
                            var cellRef = cells[j].getAttribute('r');
                            cells[j].setAttribute('r', cellRef.replace(/[0-9]+/, newIdx));
                        }
                    }

                    sheetData.innerHTML = h1 + h2 + e3 + rh + r1 + r2 + r3 + r4 + r5 + e10 + hr + sheetData.innerHTML;

                    var mergeCells = sheet.getElementsByTagName('mergeCells')[0];
                    if (mergeCells) {
                        while (mergeCells.firstChild) mergeCells.removeChild(mergeCells.firstChild);
                    } else {
                        mergeCells = sheet.createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'mergeCells');
                        sheet.firstChild.appendChild(mergeCells);
                    }
                    
                    var listMerge = ['A1:G1', 'A2:G2', 'A4:G4', 'A11:G11', 'A5:E5', 'A6:E6', 'A7:E7', 'A8:E8', 'A9:E9'];
                    listMerge.forEach(function(ref) {
                        var mCell = sheet.createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'mergeCell');
                        mCell.setAttribute('ref', ref);
                        mergeCells.appendChild(mCell);
                    });
                    mergeCells.setAttribute('count', listMerge.length);

                    $('row[r="1"] c, row[r="2"] c, row[r="4"] c, row[r="11"] c, row[r="12"] c', sheet).attr('s', '51'); 
                }
            }
        ],
        "language": {
            "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            "search": "Cari:",
            "lengthMenu": "Tampilkan _MENU_ entri",
            "paginate": { "next": "»", "previous": "«" }
        }
    });

    table.buttons().container().appendTo('#tempatTombol');
});
</script>