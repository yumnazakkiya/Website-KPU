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

/* TAMBAH RIWAYAT DIKLAT */
if(isset($_POST['tambah'])){

    $id_jenis_diklat = $_POST['id_jenis_diklat'];
    $nama_diklat     = $_POST['nama_diklat'];
    $tahun           = $_POST['tahun'];

    if(!empty($id_jenis_diklat) && !empty($nama_diklat) && !empty($tahun)){

        $cek = mysqli_query($conn,"
        SELECT * FROM riwayat_diklat
        WHERE nip='$nip'
        AND id_jenis_diklat='$id_jenis_diklat'
        AND nama_diklat='$nama_diklat'
        AND tahun='$tahun'
        ");

        if(mysqli_num_rows($cek)==0){

            mysqli_query($conn,"
            INSERT INTO riwayat_diklat
            (
            nip,
            id_jenis_diklat,
            nama_diklat,
            tahun
            )
            VALUES
            (
            '$nip',
            '$id_jenis_diklat',
            '$nama_diklat',
            '$tahun'
            )
            ");

            header("Location: Edit_Riwayat_Diklat_User.php?status=berhasil_tambah");
            exit;
        }
    }
}


/* UBAH RIWAYAT DIKLAT */
if(isset($_POST['ubah'])){

    $id              = $_POST['id_riwayat_diklat'];
    $id_jenis_diklat = $_POST['id_jenis_diklat'];
    $nama_diklat     = $_POST['nama_diklat'];
    $tahun           = $_POST['tahun'];

    if(!empty($id) && !empty($id_jenis_diklat) && !empty($nama_diklat) && !empty($tahun)){

        mysqli_query($conn,"
        UPDATE riwayat_diklat
        SET
        id_jenis_diklat='$id_jenis_diklat',
        nama_diklat='$nama_diklat',
        tahun='$tahun'
        WHERE id_riwayat_diklat='$id'
        ");

        header("Location: Edit_Riwayat_Diklat_User.php?status=berhasil_ubah");
        exit;
    }
}

/* HAPUS */
if(isset($_POST['hapus'])){

    $id = $_POST['id_riwayat_diklat'];

    mysqli_query($conn,"
    DELETE FROM riwayat_diklat
    WHERE id_riwayat_diklat='$id'
    ");

    header("Location: Edit_Riwayat_Diklat_User.php?status=berhasil_hapus");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data – Riwayat Diklat</title>
    <link rel="stylesheet" href="../assets/style_tab.css">
    <link rel="stylesheet" href="../assets/style_edit.css">
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
        <a href="Edit_Riwayat_Diklat_User.php" class="item-submenu aktif">Riwayat Diklat</a>
        <a href="Edit_Riwayat_Keluarga_User.php" class="item-submenu">Riwayat Keluarga</a>
        <a href="Edit_Riwayat_Kehormatan_User.php" class="item-submenu">Riwayat Kehormatan</a>
        <a href="Edit_Riwayat_SKP_User.php" class="item-submenu">Riwayat SKP</a>
    </div>

    <hr class="garis-menu" />

    <a href="Pengaturan_Akun_User.php" class="item-menu">Pengaturan Akun</a>

      <hr class="garis-menu" />
    </aside>


<!-- KONTEN -->
<main class="konten">
    <h2>Riwayat Diklat</h2>
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
            <div class="form-edit">
            <form method="POST" id="formUpload">

            <input type="hidden" name="id_riwayat_diklat" id="id_riwayat_diklat">

            <!-- BARIS JENIS DIKLAT -->
            <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Jenis Diklat</label>

            <select name="id_jenis_diklat" style="height:30px; border:1px solid #888;">
            <option value="">-- Pilih Jenis Diklat --</option>

            <?php
            $qDiklat = mysqli_query($conn,"SELECT * FROM master_diklat ORDER BY id_jenis_diklat");

            while($d = mysqli_fetch_assoc($qDiklat)){
            echo "<option value='$d[id_jenis_diklat]'>$d[jenis_diklat]</option>";
            }
            ?>

            </select>

            <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">
            TAMBAH
            </button>

            </div>


            <!-- BARIS NAMA DIKLAT -->
            <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Nama Diklat</label>

            <input type="text" name="nama_diklat" placeholder="Nama Diklat">

            <button type="button" onclick="klikUbahBeda('id_riwayat_diklat')" class="tombol-ubah btn-kecil">
            UBAH
            </button>

            </div>

            <!-- BARIS TAHUN -->
            <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
                <label>Tahun</label>

                <input type="number" name="tahun" placeholder="YYYY">

                <div class="aksi-vertikal">
                    <button type="button" onclick="klikHapus('id_riwayat_diklat')" class="tombol-hapus btn-kecil">
                        HAPUS
                    </button>
                </div>
            </div>


            <!-- TABEL -->
            <table class="tabel-riwayat" border="1" cellpadding="5">

            <thead>
            <tr>
            <th>Jenis Diklat</th>
            <th>Nama Diklat</th>
            <th>Tahun</th>
            </tr>
            </thead>

            <tbody>

            <?php
            $data = mysqli_query($conn,"
            SELECT rd.*, md.jenis_diklat
            FROM riwayat_diklat rd
            JOIN master_diklat md
            ON rd.id_jenis_diklat = md.id_jenis_diklat
            WHERE rd.nip='$nip'
            ORDER BY rd.tahun DESC
            ");

            while($row = mysqli_fetch_assoc($data)){

            echo "<tr onclick=\"pilihData('".$row['id_riwayat_diklat']."','".$row['id_jenis_diklat']."','".$row['nama_diklat']."','".$row['tahun']."')\">

            <td>".$row['jenis_diklat']."</td>
            <td>".$row['nama_diklat']."</td>
            <td>".$row['tahun']."</td>

            </tr>";

            }
            ?>

            </tbody>
            </table>
        </div>
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
function pilihData(id,id_jenis,nama,tahun){

document.getElementById("id_riwayat_diklat").value = id;
document.querySelector("select[name='id_jenis_diklat']").value = id_jenis;
document.querySelector("input[name='nama_diklat']").value = nama;
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