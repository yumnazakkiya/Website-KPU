<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['nip'])){
    header("location: ../auth/Login.php");
    exit;
}

$nip = $_SESSION['nip'];

$query = mysqli_query($conn,"
SELECT 
p.*,
jk.jenis_kelamin,
a.agama,
s.status_perkawinan,
u.unit_kerja
FROM pegawai p
LEFT JOIN master_jenis_kelamin jk ON p.id_jenis_kelamin = jk.id_jenis_kelamin
LEFT JOIN master_agama a ON p.id_agama = a.id_agama
LEFT JOIN master_status_perkawinan s ON p.id_status_perkawinan = s.id_status_perkawinan
LEFT JOIN master_divisi u ON p.id_unit_kerja = u.id_unit_kerja
WHERE p.nip='$nip'
");

$data = mysqli_fetch_assoc($query);

$riwayat_gol = mysqli_query($conn,"
SELECT g.nama_pangkat, g.kode_gol, r.tmt_golongan
FROM riwayat_golongan r
JOIN master_golongan g ON r.id_gol = g.id_gol
WHERE r.nip='$nip'
ORDER BY r.id_riwayat_gol DESC
LIMIT 1
");
$data_gol = mysqli_fetch_assoc($riwayat_gol);

$riwayat_jabatan = mysqli_query($conn,"
SELECT j.nama_jabatan, j.jenis_jabatan, r.tmt_jabatan
FROM riwayat_jabatan r
JOIN master_jabatan j ON r.id_jabatan = j.id_jabatan
WHERE r.nip='$nip'
ORDER BY r.id_riwayat_jabatan DESC
LIMIT 1
");

$data_jabatan = mysqli_fetch_assoc($riwayat_jabatan);

$riwayat_pendidikan = mysqli_query($conn,"
SELECT jp.jenjang_pend, r.institusi, r.tahun_lulus
FROM riwayat_pendidikan r
JOIN master_jenjang_pend jp ON r.id_jenjang_pend = jp.id_jenjang_pend
WHERE r.nip='$nip'
ORDER BY r.id_riwayat_pend DESC
LIMIT 1
");

$data_pendidikan = mysqli_fetch_assoc($riwayat_pendidikan);
?>
