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

$today = date("Y-m-d");
$badge = [
    'hadir' => 'success', 
    'sakit' => 'warning', 
    'izin' => 'primary', 
    'tanpa keterangan' => 'danger', 
];

$sql_stat = "SELECT 
            COUNT(CASE WHEN k.status = 'aktif' THEN 1 END) as total_kar,
            COUNT(CASE WHEN a.keterangan = 'hadir' THEN 1 END) as hadir,
            COUNT(CASE WHEN a.keterangan = 'izin' THEN 1 END) as izin,
            COUNT(CASE WHEN a.keterangan = 'sakit' THEN 1 END) as sakit,
            COUNT(CASE WHEN a.keterangan = 'tanpa keterangan' THEN 1 END) as alpha,
            COUNT(a.nik) as sudah_absen
        FROM karyawan k
        LEFT JOIN absensi a ON k.nik = a.nik AND a.tanggal = ?
        WHERE k.status = 'aktif'";

$stmt_stat = $db->prepare($sql_stat);
$stmt_stat->bind_param("s", $today);
$stmt_stat->execute();
$stat = $stmt_stat->get_result()->fetch_assoc();

$total_kar   = $stat['total_kar'] ?? 0;
$hadir       = $stat['hadir'] ?? 0;
$izin        = $stat['izin'] ?? 0;
$sakit       = $stat['sakit'] ?? 0;
$alpha       = $stat['alpha'] ?? 0;
$sudah_absen = $stat['sudah_absen'] ?? 0;

$belum_absen = $total_kar - $sudah_absen;
$belum_absen = ($belum_absen < 0) ? 0 : $belum_absen;

$stmt_log = $db->prepare("SELECT a.*, k.nama FROM absensi a 
                         JOIN karyawan k ON a.nik=k.nik 
                         WHERE a.tanggal=? 
                         ORDER BY a.jam_masuk DESC");
$stmt_log->bind_param("s", $today);
$stmt_log->execute();
$res_log = $stmt_log->get_result();
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Dashboard <small class="text-muted" style="font-size: 15px;"><?= date("d F Y") ?></small></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <?php 
                $boxes = [
                    ['bg-info', $total_kar, 'Karyawan Aktif', 'fa-users'],
                    ['bg-success', $hadir, 'Hadir', 'fa-check-circle'],
                    ['bg-warning', $sakit, 'Sakit', 'fa-medkit'],
                    ['bg-primary', $izin, 'Izin', 'fa-hand-paper'],
                    ['bg-danger', $alpha, 'Alpha', 'fa-user-times']
                ];
                foreach($boxes as $b):
                ?>
                <div class="col-lg col-md-4 col-6 mb-3">
                    <div class="small-box <?= $b[0] ?> shadow-none">
                        <div class="inner">
                            <h3><?= $b[1] ?></h3>
                            <p><?= $b[2] ?></p>
                        </div>
                        <div class="icon"><i class="fas <?= $b[3] ?>"></i></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-tosca shadow-none">
                        <div class="card-header">
                            <h3 class="card-title">Log Absensi Terbaru</h3>
                        </div>
                        <div class="card-body table-responsive p-3">
                            <table id="tabel" class="table table-bordered table-hover">
                                <thead>
                                    <tr class="text-center">
                                        <th width="50">No</th> 
                                        <th width="100">NIK</th> 
                                        <th width="250">Nama</th>
                                        <th width="70">Masuk</th>
                                        <th width="70">Keluar</th> 
                                        <th width="100">Status</th>
                                        <th width="50">Lokasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; while($row = $res_log->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td> 
                                        <td class="text-center"><?= htmlspecialchars($row['nik']) ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['jam_masuk'] ?: '-') ?></td>
                                        <td class="text-center"><?= htmlspecialchars($row['jam_keluar'] ?: '-') ?></td> 
                                        <td class="text-center">
                                            <span class="badge badge-<?= $badge[$row['keterangan']] ?? 'secondary' ?>">
                                                <?= htmlspecialchars(strtoupper($row['keterangan'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row['keterangan'] == 'hadir'): ?>
                                            <button class="btn btn-xs btn-outline-info btn-map" data-lokasi="<?= htmlspecialchars($row['lokasi']) ?>">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-tosca shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">Progres Absensi (%)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="progressChart" style="min-height: 120px; height: 120px; max-height: 120px; max-width: 100%;"></canvas>
                        </div>
                    </div>

                    <div class="card card-tosca shadow-none">
                        <div class="card-header">
                            <h3 class="card-title">Grafik Kehadiran</h3>
                        </div>
                        <div class="card-body">
                            <div style="height: 200px;">
                                <canvas id="absensiDonutChart"></canvas>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
$(function () {
    Chart.register(ChartDataLabels);
    var ctx = document.getElementById('progressChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jumlah'],
            datasets: [
                {
                    label: 'Sudah Absen',
                    data: [<?= $sudah_absen ?>],
                    backgroundColor: '#00969A',
                    barThickness: 35
                },
                {
                    label: 'Belum Absen',
                    data: [<?= $belum_absen ?>],
                    backgroundColor: '#dee2e6',
                    barThickness: 35
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        boxWidth: 15,
                        padding: 20,
                        generateLabels: function(chart) {
                            const data = chart.data;
                            const totalKaryawan = <?= $total_kar ?>; 
                            return [
                                {
                                    text: Math.round((data.datasets[0].data[0] / totalKaryawan) * 100) + '% Sudah Absen',
                                    fillStyle: '#00969A',
                                    strokeStyle: '#00969A',
                                    lineWidth: 0
                                },
                                {
                                    text: Math.round((data.datasets[1].data[0] / totalKaryawan) * 100) + '% Belum Absen',
                                    fillStyle: '#dee2e6',
                                    strokeStyle: '#dee2e6',
                                    lineWidth: 0
                                }
                            ];
                        }
                    }
                },
                datalabels: {
                    color: (context) => context.datasetIndex === 0 ? 'white' : '#495057',
                    font: { weight: 'bold', size: 12 },
                    formatter: (value) => value > 0 ? value : ''
                }
            },
            scales: {
                x: { stacked: true, display: false },
                y: { stacked: true, grid: { display: false } }
            }
        }
    });
});
</script>

<script>
$(document).ready(function () {
    const ctx = document.getElementById('absensiDonutChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpha'],
            datasets: [{
                data: [<?= $hadir ?>, <?= $sakit ?>, <?= $izin ?>,  <?= $alpha ?>],
                backgroundColor: ['#28a745', '#ffc107',  '#077fffff',  '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    display: true,
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 12
                    },
                    formatter: function(value, context) {
                        const totalKaryawan = <?= $total_kar ?>; 
                        let percentage = ((value / totalKaryawan) * 100).toFixed(0);
                        if (value > 0) {
                            return percentage + '%';
                        } else {
                            return null;
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'left',
                    align: 'center',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: { size: 14 },
                        generateLabels: function(chart) {
                            const data = chart.data;
                            return data.labels.map((label, i) => {
                                const value = data.datasets[0].data[i];
                                const backgroundColor = data.datasets[0].backgroundColor[i];
                                return {
                                    text: label.padEnd(15, ' ') + ' ' + value,
                                    fillStyle: backgroundColor,
                                    strokeStyle: backgroundColor,
                                    lineWidth: 0,
                                    index: i
                                };
                            });
                        }
                    }
                }
            },
            cutout: '50%'
        }
    });
});
</script>