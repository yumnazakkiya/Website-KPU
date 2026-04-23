<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include '../../config/koneksi.php';

session_start();

// 🔒 CEK LOGIN DULU
if(!isset($_SESSION['nip'])){
    header("location: ../auth/Login.php");
    exit;
}

// ==========================
// AMBIL NIP
// ==========================
if(isset($_GET['nip']) && !empty($_GET['nip'])){
    $nip = $_GET['nip']; // dari URL (admin / link)
} else {
    $nip = $_SESSION['nip']; // fallback ke session user login
}

// ==========================
// FUNCTION DATE AMAN
// ==========================
function safeDate($date){
    return (!empty($date) && $date != '0000-00-00') 
        ? date('d-m-Y', strtotime($date)) 
        : '-';
}

// FUNCTION UNTUK TTL
function formatTanggalIndo($date){
    if (empty($date) || $date == '0000-00-00') return '-';

    $bulan = [
        1 => 'JANUARI', 'FEBRUARI', 'MARET', 'APRIL',
        'MEI', 'JUNI', 'JULI', 'AGUSTUS',
        'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'DESEMBER'
    ];

    $pecah = explode('-', $date);

    return str_pad($pecah[2], 2, '0', STR_PAD_LEFT) . ' ' 
         . $bulan[(int)$pecah[1]] . ' ' 
         . $pecah[0];
}

// ==========================
// DATA PEGAWAI + JOIN MASTER
// ==========================
$query = mysqli_query($conn, "
SELECT p.*, 
jk.jenis_kelamin,
ag.agama,
sp.status_perkawinan,
uk.unit_kerja
FROM pegawai p
LEFT JOIN master_jenis_kelamin jk ON p.id_jenis_kelamin = jk.id_jenis_kelamin
LEFT JOIN master_agama ag ON p.id_agama = ag.id_agama
LEFT JOIN master_status_perkawinan sp ON p.id_status_perkawinan = sp.id_status_perkawinan
LEFT JOIN master_divisi uk ON p.id_unit_kerja = uk.id_unit_kerja
WHERE p.nip='$nip'
");

// AMBIL DATA
$data = mysqli_fetch_assoc($query);

// ==========================
// FOTO PEGAWAI
// ==========================
// Ambil nama file dari database
$fotoDb = $data['foto'] ?? '';

// Karena di DB kamu sudah "uploads/xxx.jpg"
$fotoPath = __DIR__ . '/../../' . $fotoDb;

// Cek apakah file ada
if(!empty($fotoDb) && file_exists($fotoPath)){

    // Ambil isi file
    $imageData = file_get_contents($fotoPath);

    // Encode ke base64
    $base64 = base64_encode($imageData);

    // Ambil ekstensi (jpg/png)
    $ext = strtolower(pathinfo($fotoPath, PATHINFO_EXTENSION));

    // Tentukan mime type
    if($ext == 'png'){
        $mime = 'image/png';
    } elseif($ext == 'jpg' || $ext == 'jpeg'){
        $mime = 'image/jpeg';
    } else {
        $mime = 'image/jpeg'; // default
    }

    // Final src untuk HTML
    $src = 'data:'.$mime.';base64,'.$base64;

} else {

    // Kalau foto tidak ada → pakai default
    $defaultPath = __DIR__ . '/../../uploads/default.png';

    if(file_exists($defaultPath)){
        $imageData = file_get_contents($defaultPath);
        $base64 = base64_encode($imageData);
        $src = 'data:image/png;base64,'.$base64;
    } else {
        $src = ''; // kosong kalau benar-benar tidak ada
    }
}

// ==========================
// GOLONGAN TERAKHIR
// ==========================
$q_gol = mysqli_query($conn, "
SELECT rg.*, mg.nama_pangkat, mg.kode_gol
FROM riwayat_golongan rg
LEFT JOIN master_golongan mg ON rg.id_gol = mg.id_gol
WHERE rg.nip='$nip'
ORDER BY rg.tmt_golongan DESC
LIMIT 1
");

if(!$q_gol){
    die("Error golongan: " . mysqli_error($conn));
}

$data_gol = mysqli_fetch_assoc($q_gol);

// ==========================
// JABATAN TERAKHIR
// ==========================
$q_jabatan = mysqli_query($conn, "
SELECT rj.*, mj.nama_jabatan, mj.jenis_jabatan
FROM riwayat_jabatan rj
LEFT JOIN master_jabatan mj ON rj.id_jabatan = mj.id_jabatan
WHERE rj.nip='$nip'
ORDER BY rj.tmt_jabatan DESC
LIMIT 1
");

if(!$q_jabatan){
    die("Error jabatan: " . mysqli_error($conn));
}

$data_jabatan = mysqli_fetch_assoc($q_jabatan);

// ==========================
// RIWAYAT
// ==========================
$golongan = mysqli_query($conn, "
SELECT rg.*, mg.kode_gol, mg.nama_pangkat
FROM riwayat_golongan rg
LEFT JOIN master_golongan mg ON rg.id_gol = mg.id_gol
WHERE rg.nip='$nip'
ORDER BY rg.tmt_golongan DESC
");

$jabatan = mysqli_query($conn, "
SELECT rj.*, mj.nama_jabatan, mj.jenis_jabatan
FROM riwayat_jabatan rj
LEFT JOIN master_jabatan mj ON rj.id_jabatan = mj.id_jabatan
WHERE rj.nip='$nip'
");

$keluarga = mysqli_query($conn, "
SELECT rk.*, mk.hub_kel
FROM riwayat_keluarga rk
LEFT JOIN master_hub_kel mk ON rk.id_hub_kel = mk.id_hub_kel
WHERE rk.nip='$nip'
");

$pendidikan = mysqli_query($conn, "
SELECT rp.*, mj.jenjang_pend
FROM riwayat_pendidikan rp
LEFT JOIN master_jenjang_pend mj 
ON rp.id_jenjang_pend = mj.id_jenjang_pend
WHERE rp.nip='$nip'
ORDER BY rp.tahun_lulus DESC
");

$diklat = mysqli_query($conn, "
SELECT * FROM riwayat_diklat WHERE nip='$nip'
");

$penghargaan = mysqli_query($conn, "
SELECT * FROM riwayat_kehormatan WHERE nip='$nip'
");

$skp = mysqli_query($conn, "
SELECT rs.*, mp.predikat_skp
FROM riwayat_skp rs
LEFT JOIN master_predikat_skp mp 
ON rs.id_predikat_skp = mp.id_predikat_skp
WHERE rs.nip='$nip'
ORDER BY rs.tahun DESC
");

// ==========================
// HTML
// ==========================
$html = '
<style>
body { 
    font-family: Arial, sans-serif; 
    font-size: 13px; 
}

.title { 
    text-align: center; 
    font-weight: bold; 
    font-size: 18px;
    margin-bottom: 15px;
}

.foto-box {
    width: 100%;
    height: 160px; /* tinggi fix biar gak kepanjangan */
    display: flex;
    align-items: center;
    justify-content: center;
}

.foto {
    width: 100px;
    height: 120px;
    object-fit: cover; /* penting banget */
    border: 1px solid #000;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 10px;
}

td, th {
    border: 1px solid black;
    padding: 6px;
}

.section-title {
    font-weight: bold;
    background: #eaeaea;
    text-align: left;
}

.no-border td {
    border: none;
    padding: 3px;
}

.label {
    width: 40%;
    font-weight: bold;
}

.value {
    width: 60%;
}

.row {
    font-size: 12px;
    margin-bottom: 4px;
}

.col-gol {
    display: inline-block;
    width: 60px;
}

.col-pangkat {
    display: inline-block;
    width: 250px;
}

.col-tmt {
    display: inline-block;
    width: 120px;
}

.table-outer {
    width: 100%;
    border: 1px solid black;
    border-collapse: collapse;
}

.table-outer td {
    border: none; /* HAPUS GARIS DALAM */
    padding: 4px;
}

.table-outer tr:nth-child(2) td {
    border-bottom: 1px solid black;
}

.section-header {
    font-weight: bold;
    background: #b3b0b0;
    padding: 6px;
    border: 1px solid black;
}

.header-row {
    font-weight: bold;
}

.wrapper-table {
    width: 100%;
    border-collapse: collapse;
}

.wrapper-table td {
    border: none;
}

</style>

<div class="title">PROFIL PEGAWAI NEGERI SIPIL</div>

<table>
<tr>
    <td colspan="2" class="section-header">
        IDENTITAS PEGAWAI
    </td>
</tr>

<tr>
<td width="35%" style="text-align:center; vertical-align:top; padding-top:40px;">
    
    <img src="'.$src.'" style="
        width:210px;
        height:240px;
        border:1px solid black;
        display:block;
        margin:0 auto;
    ">
    
    <div style="margin-top:8px; font-size:11px; font-weight: bold;">
        FOTO PEGAWAI
    </div>

</td>

<td width="65%">
<table class="no-border">
<tr><td class="label">NAMA</td><td>: '.strtoupper($data['nama_pegawai'] ?? '-').'</td></tr>
<tr><td class="label">NIP</td><td>: '.strtoupper($data['nip'] ?? '-').'</td></tr>
<tr><td class="label">PANGKAT / GOL / TMT</td><td>: '.strtoupper($data_gol['nama_pangkat'] ?? '-').' / ('.strtoupper($data_gol['kode_gol'] ?? '-').') / '. formatTanggalIndo($data_gol['tmt_golongan']).'</td></tr>
<tr><td class="label">JABATAN TERAKHIR / TMT</td><td>: '.strtoupper($data_jabatan['jenis_jabatan'] ?? '-').' / '. formatTanggalIndo($data_jabatan['tmt_jabatan']).'</td></tr>
<tr><td class="label">TIPE KARYAWAN</td><td>: '.strtoupper($data['tipe_karyawan'] ?? '-').'</td></tr>
<tr><td class="label">TMT CPNS / TMT PNS</td><td>: '. formatTanggalIndo($data['tmt_cpns']).' / '. formatTanggalIndo($data['tmt_pns']).'</td></tr>
<tr><td class="label">TTL</td><td>: '.strtoupper($data['tempat_lahir'] ?? '-').', '. formatTanggalIndo($data['tanggal_lahir']).'</td></tr>
<tr><td class="label">JENIS KELAMIN</td><td>: '.strtoupper($data['jenis_kelamin'] ?? '-').'</td></tr>
<tr><td class="label">AGAMA</td><td>: '.strtoupper($data['agama'] ?? '-').'</td></tr>
<tr><td class="label">STATUS PERKAWINAN</td><td>: '.strtoupper($data['status_perkawinan'] ?? '-').'</td></tr>
<tr><td class="label">UNIT KERJA</td><td>: '.strtoupper($data['unit_kerja'] ?? '-').'</td></tr>
<tr><td class="label">INSTANSI</td><td>: KPU KOTA SURABAYA</td></tr>
<tr><td class="label">ALAMAT</td><td>: '.strtoupper($data['alamat'] ?? '-').'</td></tr>
</table>

</td>
</tr>
</table>
';

// ==========================
// BARIS 1 - KIRI
// ==========================
$html .= '
<table class="wrapper-table">
<tr>

<!-- KIRI -->
<td width="50%" valign="top">
';

// ==========================
// RIWAYAT GOLONGAN
// ==========================
$html .= '
<table class="table-outer">
<tr>
    <td colspan="3" class="section-header">RIWAYAT GOLONGAN</td>
</tr>

<tr style="font-weight:bold;">
    <td width="20%">Golongan</td>
    <td width="50%", style="padding-left:30px;">Pangkat</td>
    <td width="30%">TMT</td>
</tr>';

while($g = mysqli_fetch_assoc($golongan)){
    $html .= '
    <tr>
        <td>'.$g['kode_gol'].'</td>
        <td style="padding-left:30px;">'.$g['nama_pangkat'].'</td>
        <td>'.safeDate($g['tmt_golongan']).'</td>
    </tr>';
}

// TUTUP TABLE GOLONGAN
$html .= '</table>';


// ==========================
// BARIS 1 - KANAN
// ==========================
$html .= '
</td>

<!-- KANAN -->
<td width="50%" valign="top">
';

// ==========================
// RIWAYAT JABATAN
// ==========================
$html .= '
<table class="table-outer">
<tr>
    <td colspan="3" class="section-header">RIWAYAT JABATAN</td>
</tr>

<tr style="font-weight:bold;">
    <td>Nama Jabatan</td>
    <td>Jenis Jabatan</td>
    <td>TMT</td>
</tr>';

while($j = mysqli_fetch_assoc($jabatan)){
    $html .= '
    <tr>
        <td>'.$j['nama_jabatan'].'</td>
        <td>'.$j['jenis_jabatan'].'</td>
        <td>'.safeDate($j['tmt_jabatan']).' '.(!empty($j['tmt_akhir']) ? ' s/d '.safeDate($j['tmt_akhir']) : '').'</td>
    </tr>';
}

// TUTUP TABLE JABATAN
$html .= '</table>';

// TUTUP WRAPPER BARIS 1
$html .= '
</td>
</tr>
</table>
';

// BORDER
$html .= '<hr style="border:1px solid black; margin:10px 0;">';


// ==========================
// BARIS 2 - KIRI
// ==========================
$html .= '
<table class="wrapper-table">
<tr>

<!-- KIRI -->
<td width="50%" valign="top">
';

// ==========================
// RIWAYAT PENDIDIKAN
// ==========================
$html .= '
<table class="table-outer">
<tr>
    <td colspan="3" class="section-header">RIWAYAT PENDIDIKAN</td>
</tr>

<tr style="font-weight:bold;">
    <td>Nama Pendidikan</td>
    <td>Strata</td>
    <td>Tahun</td>
</tr>';

if($pendidikan && mysqli_num_rows($pendidikan) > 0){
    while($p = mysqli_fetch_assoc($pendidikan)){
        $html .= '
        <tr>
            <td>'.$p['institusi'].'</td>
            <td>'.$p['jenjang_pend'].'</td>
            <td>'.$p['tahun_lulus'].'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="3">Tidak ada pendidikan</td></tr>';
}

// TUTUP TABLE PENDIDIKAN
$html .= '</table>';

// ==========================
// BARIS 2 - KANAN
// ==========================
$html .= '
</td>

<!-- KANAN -->
<td width="50%" valign="top">
';

// ==========================
// RIWAYAT DIKLAT
// ==========================
$html .= '
<table class="table-outer">
<tr>
    <td colspan="2" class="section-header">RIWAYAT DIKLAT</td>
</tr>

<tr style="font-weight:bold;">
    <td>Nama Diklat</td>
    <td>Tahun</td>
</tr>';

if($diklat && mysqli_num_rows($diklat) > 0){
    while($d = mysqli_fetch_assoc($diklat)){
        $html .= '
        <tr>
            <td>'.$d['nama_diklat'].'</td>
            <td>'.$d['tahun'].'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="1">Tidak ada diklat</td></tr>';
}

// TUTUP TABLE DIKLAT
$html .= '</table>';

// TUTUP WRAPPER BARIS 2
$html .= '
</td>
</tr>
</table>
';

// BORDER //
$html .= '<hr style="border:1px solid black; margin:10px 0;">';

// ==========================
// BARIS 3 - KIRI
// ==========================
$html .= '
<table class="wrapper-table">
<tr>

<!-- KIRI -->
<td width="50%" valign="top">';

// ==========================
// RIWAYAT KELUARGA
// ==========================
$html .= '
<table class="table-outer">
<tr>
    <td colspan="3" class="section-header">RIWAYAT KELUARGA</td>
</tr>

<tr style="font-weight:bold;">
    <td>Nama</td>
    <td>Hubungan</td>
    <td>No. Telepon</td>
</tr>';

if($keluarga && mysqli_num_rows($keluarga) > 0){
    while($k = mysqli_fetch_assoc($keluarga)){
        $html .= '
        <tr>
            <td>'.$k['nama'].'</td>
            <td>'.$k['hub_kel'].'</td>
            <td>'.$k['no_telp'].'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="3">Tidak ada data keluarga</td></tr>';
}

// TUTUP TABLE KELUARGA
$html .= '</table>';

// ==========================
// BARIS 3 - KANAN
// ==========================
$html .= '
</td>

<!-- KANAN -->
<td width="50%" valign="top">';

// ==========================
// RIWAYAT PENGHARGAAN
// ==========================
$html .= '
<table class="table-outer">
<tr>
    <td colspan="2" class="section-header">RIWAYAT KEHORMATAN</td>
</tr>

<tr style="font-weight:bold;">
    <td>Nama Penghargaan</td>
    <td>Tahun</td>
</tr>';

if($penghargaan && mysqli_num_rows($penghargaan) > 0){
    while($ph = mysqli_fetch_assoc($penghargaan)){
        $html .= '
        <tr>
            <td>'.$ph['nama_penghargaan'].'</td>
            <td>'.$ph['tahun'].'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="2">Tidak ada penghargaan</td></tr>';
}

// TUTUP TABLE PENGHARGAAN
$html .= '</table>';

// ==========================
// TUTUP WRAPPER BARIS 3
// ==========================
$html .= '
</td>
</tr>
</table>
';

// BORDER
$html .= '<hr style="border:1px solid black; margin:10px 0;">';

// ==========================
// SKP - KANAN
// ==========================
// ==========================
// BARIS 4 - WRAPPER
// ==========================
$html .= '
<table class="wrapper-table">
<tr>

<!-- KIRI (SKP) -->
<td width="50%" valign="top">
';

// ==========================
// RIWAYAT SKP
// ==========================
$html .= '
<table class="table-outer">
<tr>
    <td colspan="3" class="section-header">RIWAYAT SKP</td>
</tr>

<tr style="font-weight:bold;">
    <td>Tahun</td>
    <td>Rerata Nilai</td>
    <td>Predikat</td>
</tr>';

if($skp && mysqli_num_rows($skp) > 0){
    while($s = mysqli_fetch_assoc($skp)){
        $html .= '
        <tr>
            <td>'.$s['tahun'].'</td>
            <td>'.number_format($s['rerata_nilai'], 2).'</td>
            <td>'.$s['predikat_skp'].'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="3">Tidak ada data SKP</td></tr>';
}

$html .= '</table>';

// ==========================
// KANAN (kosong)
// ==========================
$html .= '
</td>

<td width="50%" valign="top">
</td>

</tr>
</table>
';

// ==========================
// PDF
// ==========================

ob_end_clean();

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

// penamaan file
$nama = strtoupper($data['nama_pegawai'] ?? 'pegawai');
$nip  = $data['nip'] ?? '';
$nama = str_replace(' ', '_', $nama);
$filename = "Profil_{$nama}_{$nip}.pdf";
$dompdf->stream($filename, ["Attachment"=>false]);
?>