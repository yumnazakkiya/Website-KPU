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


/* TAMBAH RIWAYAT GOLONGAN */

   if(isset($_POST['tambah'])){

    $id_gol = $_POST['id_gol'];
    $tmt_golongan    = $_POST['tmt_golongan'];

    if(!empty($id_gol) && !empty($tmt_golongan)){

        $cek = mysqli_query($conn,"
        SELECT * FROM riwayat_golongan 
        WHERE nip='$nip'
        AND id_gol='$id_gol'
        AND tmt_golongan='$tmt_golongan'
        ");

        if(mysqli_num_rows($cek)==0){

            mysqli_query($conn,"
            INSERT INTO riwayat_golongan
            (
            nip,
            id_gol,
            tmt_golongan
            )
            VALUES
            (
            '$nip',
            '$id_gol',
            '$tmt_golongan'
            )
            ");

            header("Location: Edit_Riwayat_Golongan_User.php?status=berhasil_tambah");
            exit;
        }
    }
}
/* UBAH RIWAYAT GOLONGAN */

   if(isset($_POST['ubah'])){

    $id     = $_POST['id_riwayat_gol'];
    $id_gol = $_POST['id_gol'];
    $tmt_golongan    = $_POST['tmt_golongan'];

    if(!empty($id) && !empty($id_gol) && !empty($tmt_golongan)){

        mysqli_query($conn,"
        UPDATE riwayat_golongan
        SET
        id_gol='$id_gol',
        tmt_golongan='$tmt_golongan'
        WHERE id_riwayat_gol='$id'
        ");

        header("Location: Edit_Riwayat_Golongan_User.php?status=berhasil_ubah");
        exit;
    }
}
if(isset($_POST['hapus'])){

  $id = $_POST['id_riwayat_gol'];
  
  mysqli_query($conn,"
  DELETE FROM riwayat_golongan
  WHERE id_riwayat_gol='$id'
  ");

  header("Location: Edit_Riwayat_Golongan_User.php?status=berhasil_hapus");
  exit;
  
  }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data – Riwayat Golongan</title>
    <link rel="stylesheet" href="../assets/style_edit.css">
    <link rel="stylesheet" href="../assets/style_tab.css">
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
        <a href="Edit_Riwayat_Golongan_User.php" class="item-submenu aktif">Riwayat Golongan</a>
        <a href="Edit_Riwayat_Jabatan_User.php" class="item-submenu">Riwayat Jabatan</a>
        <a href="Edit_Riwayat_Pendidikan_User.php" class="item-submenu">Riwayat Pendidikan</a>
        <a href="Edit_Riwayat_Diklat_User.php" class="item-submenu">Riwayat Diklat</a>
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
    <h2>Riwayat Golongan</h2>
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
      <div class="form-edit">
        <form method="POST" id="formUpload">

        <input type="hidden" name="id_riwayat_gol" id="id_riwayat_gol">
        <div class="baris-form" style="grid-template-columns:120px 500px 120px">
        <label>Golongan Pangkat</label>

        <select name="id_gol" style="height:30px; border:1px solid #888;">

        <option value="">Pilih Golongan</option>

        <?php

        $qGol = mysqli_query($conn,"SELECT * FROM master_golongan ORDER BY kode_gol");

        while($g = mysqli_fetch_assoc($qGol)){

        echo "<option value='$g[id_gol]'>$g[kode_gol] - $g[nama_pangkat]</option>";

        }

        ?>

        </select>

        <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">
        TAMBAH
        </button>
        </div>


        <div class="baris-form" style="grid-template-columns:120px 500px 120px">

        <label>TMT</label>

        <input type="date" name="tmt_golongan">

        <div class="aksi-vertikal">
        <button type="button" onclick="klikUbahBeda('id_riwayat_gol')" class="tombol-ubah btn-kecil">
        UBAH
        </button>
        <button type="button" onclick="klikHapus('id_riwayat_gol')" class="tombol-hapus btn-kecil">
          HAPUS
          </button>
        </div>

        </div>

        </form>


        <table class="tabel-riwayat" border="1" cellpadding="5">

        <thead>
        <tr>
        <th>Golongan Pangkat</th>
        <th>TMT</th>
        </tr>
        </thead>

        <tbody>
        <?php
          $data = mysqli_query($conn,"
          SELECT rg.*, mg.kode_gol, mg.nama_pangkat
          FROM riwayat_golongan rg
          JOIN master_golongan mg ON rg.id_gol = mg.id_gol
          WHERE rg.nip='$nip'
          ORDER BY rg.tmt_golongan DESC
          ");

          while($row = mysqli_fetch_assoc($data)){

            echo "<tr onclick=\"pilihData('".$row['id_riwayat_gol']."','".$row['id_gol']."','".$row['tmt_golongan']."')\">
            <td>".$row['kode_gol']." - ".$row['nama_pangkat']."</td>
            <td>".date('d-m-Y', strtotime($row['tmt_golongan']))."</td>
            </tr>";
            
            }

        ?>
        </tbody>
        </table>

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
function pilihData(id,id_gol,tmt_golongan){

    document.getElementById("id_riwayat_gol").value = id;
    document.querySelector("select[name='id_gol']").value = id_gol;
    document.querySelector("input[name='tmt_golongan']").value = tmt_golongan;

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