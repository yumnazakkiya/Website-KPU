<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['nip'])){
    header("location: ../auth/Login.php");
    exit;
}

$nip = $_SESSION['nip'];

$query = mysqli_query($conn,"SELECT * FROM pegawai WHERE nip='$nip'");
$data = mysqli_fetch_assoc($query);

/* TAMBAH RIWAYAT KEHORMATAN */
if(isset($_POST['tambah'])){

    $nama_penghargaan = $_POST['nama_penghargaan'];
    $tahun            = $_POST['tahun'];

    if(!empty($nama_penghargaan) && !empty($tahun)){

        $cek = mysqli_query($conn,"
        SELECT * FROM riwayat_kehormatan
        WHERE nip='$nip'
        AND nama_penghargaan='$nama_penghargaan'
        AND tahun='$tahun'
        ");

        if(mysqli_num_rows($cek)==0){

            mysqli_query($conn,"
            INSERT INTO riwayat_kehormatan
            (
            nip,
            nama_penghargaan,
            tahun
            )
            VALUES
            (
            '$nip',
            '$nama_penghargaan',
            '$tahun'
            )
            ");

            header("Location: Edit_Riwayat_Kehormatan_User.php?status=berhasil_tambah");
            exit;
        }
    }
}
/* UBAH RIWAYAT KEHORMATAN */
if(isset($_POST['ubah'])){

    $id               = $_POST['id_riwayat_kehormatan'];
    $nama_penghargaan = $_POST['nama_penghargaan'];
    $tahun            = $_POST['tahun'];

    if(!empty($id) && !empty($nama_penghargaan) && !empty($tahun)){

        mysqli_query($conn,"
        UPDATE riwayat_kehormatan
        SET
        nama_penghargaan='$nama_penghargaan',
        tahun='$tahun'
        WHERE id_riwayat_kehormatan='$id'
        ");

        header("Location: Edit_Riwayat_Kehormatan_User.php?status=berhasil_ubah");
        exit;
    }
}
/* HAPUS */
if(isset($_POST['hapus'])){

    $id = $_POST['id_riwayat_kehormatan'];

    mysqli_query($conn,"
    DELETE FROM riwayat_kehormatan
    WHERE id_riwayat_kehormatan='$id'
    ");

    header("Location: Edit_Riwayat_Kehormatan_User.php?status=berhasil_hapus");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data – Riwayat Kehormatan</title>
    <link rel="stylesheet" href="../assets/style_edit.css">
    <link rel="stylesheet" href="../assets/style_tab.css">
    <style> 
    .tabel-riwayat{
        width:780px;
        margin-top:30px;
    }

    .bagian-identitas{
        display:flex;
        justify-content:center;
        margin-top: 60px;
    }
    </style>
</head>

<body class="role-user">

<!-- SIDEBAR -->
<aside class="sidebar-edit">
    <div class="logo">
        <span>LOGO</span>
        <button class="tombol-menu" id="tombolMenu">✕</button>
    </div>
      <hr class="garis-menu" />

      <a href="Identitas_User.php" class="item-menu">Profil</a>

      <hr class="garis-menu" />

      <div class="item-menu aktif" id="menuEditData">
        Edit Data
        <span class="panah-menu" id="panahEditData">▼</span>
    </div>

    <div class="submenu" id="submenuEditData">
        <a href="Edit_Identitas_User.php" class="item-submenu">Identitas</a>
        <a href="Edit_Riwayat_Golongan_User.php" class="item-submenu">Riwayat Golongan</a>
        <a href="Edit_Riwayat_Jabatan_User.php" class="item-submenu">Riwayat Jabatan</a>
        <a href="Edit_Riwayat_Pendidikan_User.php" class="item-submenu">Riwayat Pendidikan</a>
        <a href="Edit_Riwayat_Diklat_User.php" class="item-submenu">Riwayat Diklat</a>
        <a href="Edit_Riwayat_Keluarga_User.php" class="item-submenu">Riwayat Keluarga</a>
        <a href="Edit_Riwayat_Kehormatan_User.php" class="item-submenu aktif">Riwayat Kehormatan</a>
        <a href="Edit_Riwayat_SKP_User.php" class="item-submenu">Riwayat SKP</a>
    </div>

    <hr class="garis-menu" />

    <a href="Pengaturan_Akun_User.php" class="item-menu">Pengaturan Akun</a>

      <hr class="garis-menu" />
    </aside>


<!-- KONTEN -->
<main class="konten">
    <h2>Riwayat Kehormatan</h2>
     <!-- <button class="tombol-keluar">Log Out</button> -->
     <div class="user-profile" id="userProfile">
        <div class="user-info">
            <div class="user-icon">👤</div>
            <div class="user-text">
            <div class="user-name">
            <?= $data['nama_pegawai'] ?>
            </div>
            </div>
        </div>

        <div class="dropdown-menu" id="dropdownMenu">
            <a href="Identitas_User.php">Beranda</a>
            <a href="#" onclick="openLogoutModal()">Keluar</a>
        </div>

        </div>
      <div class="bagian-identitas">
        <!-- FORM -->
        <form method="POST" id="formUpload">
        <input type="hidden" name="id_riwayat_kehormatan" id="id_riwayat_kehormatan">

        <!-- BARIS NAMA PENGHARGAAN -->
        <div class="baris-form" style="grid-template-columns:150px 500px 120px;">
            <label>Nama Penghargaan</label>

            <input type="text" name="nama_penghargaan">

            <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">
                TAMBAH
            </button>
        </div>


        <!-- BARIS TAHUN -->
        <div class="baris-form" style="grid-template-columns:150px 500px 120px;">
            <label>Tahun</label>

            <input type="number" name="tahun" placeholder="YYYY">

            <div class="aksi-vertikal">
            <button type="button" onclick="klikUbahBeda('id_riwayat_kehormatan')" class="tombol-ubah btn-kecil">
                    UBAH
                </button>

                <button type="button" onclick="klikHapus('id_riwayat_kehormatan')" class="tombol-hapus btn-kecil">
                    HAPUS
                </button>
            </div>
        </div>
        <!-- TABEL -->
        <table class="tabel-riwayat" border="1" cellpadding="5">

        <thead>
        <tr>
        <th>Jenis Penghargaan</th>
        <th>Tahun</th>
        </tr>
        </thead>

        <tbody>

        <?php
        $data = mysqli_query($conn,"
        SELECT * FROM riwayat_kehormatan
        WHERE nip='$nip'
        ORDER BY tahun DESC
        ");

        while($row = mysqli_fetch_assoc($data)){

        echo "<tr onclick=\"pilihData('".$row['id_riwayat_kehormatan']."','".$row['nama_penghargaan']."','".$row['tahun']."')\">

        <td>".$row['nama_penghargaan']."</td>
        <td>".$row['tahun']."</td>

        </tr>";

        }
        ?>

        </tbody>
        </table>

        </form>
    
        </div>
    </div>

</main>
<div id="modalAksi" class="modal">
  <div class="modal-content">
    <h3 id="judulAksi"></h3>
    <p id="isiAksi"></p>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
      <button id="btnBatalAksi" class="tombol-batal" style="display:none;">Batal</button>
      <button id="btnOKAksi" class="tombol-hapus">OK</button>
    </div>
  </div>
</div>

<?php include 'Notifikasi_Logout.php'; ?>

<script>
function pilihData(id,nama,tahun){

    document.getElementById("id_riwayat_kehormatan").value = id;
    document.querySelector("input[name='nama_penghargaan']").value = nama;
    document.querySelector("input[name='tahun']").value = tahun;

}
</script>

<script src="../assets/script_pg.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status === 'berhasil_tambah') {
        openModalAksi("Berhasil", "Data berhasil ditambahkan", "info");
    }

    if (status === 'berhasil_ubah') {
        openModalAksi("Berhasil", "Data berhasil diubah", "info");
    }

    if (status === 'berhasil_hapus') {
        openModalAksi("Berhasil", "Data berhasil dihapus", "info");
    }
});
</script>
</body>
</html>