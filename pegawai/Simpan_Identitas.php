<?php
include '../config/koneksi.php';

/* AMBIL DATA FORM */
$nip  = $_POST['nip'];
$nama = $_POST['nama_pegawai'];

$instansi = "KPU Kota Surabaya";
$tipe_karyawan = $_POST['tipe_karyawan'];

$tempat_lahir  = ucwords($_POST['tempat_lahir']);
$tanggal_lahir = $_POST['tanggal_lahir'];

$jk     = $_POST['id_jenis_kelamin'];
$agama  = $_POST['id_agama'];
$status = $_POST['id_status_perkawinan'];
$unit   = $_POST['id_unit_kerja'];

$telp   = $_POST['no_telp'];
$alamat = $_POST['alamat'];

$tmt_cpns = $_POST['tmt_cpns'];
$tmt_pns  = $_POST['tmt_pns'];


//* UBAH DATA */
if (isset($_POST['ubah'])) {

    $updateFoto = "";

    // ===== CEK FOTO =====
    if (!empty($_FILES['foto']['name'])) {

        $foto = $_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];
        $size = $_FILES['foto']['size'];

        $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

        // format salah
        if ($ext != "jpg" && $ext != "jpeg") {
            header("location: Edit_Identitas_User.php?status=error_format");
            exit;
        }

        // ukuran terlalu besar
        if ($size > 2000000) {
            header("location: Edit_Identitas_User.php?status=error_size");
            exit;
        }

        // upload berhasil
        $namaBaru = time() . "_" . $foto;
        move_uploaded_file($tmp, "../uploads/" . $namaBaru);

        // masukkan ke query
        $updateFoto = ", foto='uploads/$namaBaru'";
    }

    // ===== UPDATE DATA + FOTO SEKALIGUS =====
    mysqli_query($conn,"UPDATE pegawai SET
    nama_pegawai='$nama',
    instansi='$instansi',
    tempat_lahir='$tempat_lahir',
    tanggal_lahir='$tanggal_lahir',
    alamat='$alamat',
    tmt_cpns='$tmt_cpns',
    tmt_pns='$tmt_pns',
    no_telp='$telp',
    id_jenis_kelamin='$jk',
    id_agama='$agama',
    id_status_perkawinan='$status',
    id_unit_kerja='$unit',
    tipe_karyawan='$tipe_karyawan'
    $updateFoto
    WHERE nip='$nip'
    ");

    header("location: Edit_Identitas_User.php?status=berhasil_ubah");
    exit;
}

?>