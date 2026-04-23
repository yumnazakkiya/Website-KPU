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
/* TAMBAH RIWAYAT GOLONGAN*/

   if(isset($_POST['tambah'])){

    $id_jabatan = $_POST['id_jabatan'];
    $tmt_jabatan = $_POST['tmt_jabatan'];
    $tmt_akhir = $_POST['tmt_akhir'];
    
    if(!empty($id_jabatan) && !empty($tmt_jabatan) && !empty($tmt_akhir)){

        $cek = mysqli_query($conn,"
        SELECT * FROM riwayat_jabatan
        WHERE nip='$nip'
        AND id_jabatan='$id_jabatan'
        AND tmt_jabatan  ='$tmt_jabatan'
        AND tmt_akhir  ='$tmt_akhir'
        ");

        if(mysqli_num_rows($cek)==0){

            mysqli_query($conn,"
            INSERT INTO riwayat_jabatan
            (
            nip,
            id_jabatan,
            id_unit_kerja,
            tmt_jabatan,
            tmt_akhir  
            )
            VALUES
            (
            '$nip',
            '$id_jabatan',
            '1',
            '$tmt_jabatan',
            '$tmt_akhir'
            )
            ");

            header("Location: Edit_Riwayat_Jabatan_User.php?status=berhasil_tambah");
            exit;
        }
    }
}
/* UBAH RIWAYAT JABATAN */
   if(isset($_POST['ubah'])){
    $id         = $_POST['id_riwayat_jabatan'];
    $id_jabatan = $_POST['id_jabatan'];
    $tmt_jabatan = $_POST['tmt_jabatan'];
    $tmt_akhir = $_POST['tmt_akhir'];
    
    if(!empty($id) && !empty($id_jabatan) && !empty($tmt_jabatan) && !empty($tmt_akhir)){
    
    mysqli_query($conn,"
    UPDATE riwayat_jabatan
    SET
    id_jabatan='$id_jabatan',
    tmt_jabatan='$tmt_jabatan',
    tmt_akhir='$tmt_akhir'
    WHERE id_riwayat_jabatan='$id'
    ");

        header("Location: Edit_Riwayat_Jabatan_User.php?status=berhasil_ubah");
        exit;
    }
}
if(isset($_POST['hapus'])){

  $id = $_POST['id_riwayat_jabatan'];
  
  mysqli_query($conn,"
  DELETE FROM riwayat_jabatan
  WHERE id_riwayat_jabatan='$id'
  ");
  header("Location: Edit_Riwayat_Jabatan_User.php?status=berhasil_hapus");
  exit;
  }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data – Riwayat Jabatan</title>
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
        <a href="Edit_Riwayat_Golongan_User.php" class="item-submenu">Riwayat Golongan</a>
        <a href="Edit_Riwayat_Jabatan_User.php" class="item-submenu aktif">Riwayat Jabatan</a>
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
    <h2>Riwayat Jabatan</h2>
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
        <input type="hidden" name="id_riwayat_jabatan" id="id_riwayat_jabatan">
            <!-- BARIS GOLONGAN -->
            <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
                <label>Nama Jabatan</label>
                <select name="id_jabatan" style="height:30px; border:1px solid #888;">
                <option value="">-- Pilih Jabatan --</option>
                <?php
                $qGol = mysqli_query($conn,"SELECT * FROM master_jabatan ORDER BY jenis_jabatan");

                while($g = mysqli_fetch_assoc($qGol)){
                echo "<option value='$g[id_jabatan]'>$g[jenis_jabatan] - $g[nama_jabatan]</option>";
                }
                ?>
                </select>
                
                <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">
                TAMBAH
                </button>
            </div>
    
            <!-- BARIS TMT -->
            <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
                <label>TMT Awal</label>
                <input type="date" name="tmt_jabatan">

                <div class="aksi-vertikal">
                <button type="button" onclick="klikUbahBeda('id_riwayat_jabatan')" class="tombol-ubah btn-kecil">
                    UBAH
                    </button>
                </div>
            </div>
            <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
                <label>TMT Akhir</label>
                <input type="date" name="tmt_akhir">

                <div class="aksi-vertikal">
                    <button type="button" onclick="klikHapus('id_riwayat_jabatan')" class="tombol-hapus btn-kecil">
                    HAPUS
                    </button>
                </div>
            </div>
            </form>
    
            <!-- TABEL -->
            <table class="tabel-riwayat" border="1" cellpadding="5">

            <thead>
            <tr>
            <th>Nama Jabatan</th>
            <th>TMT Awal</th>
            <th>TMT Akhir</th>
            </tr>
            </thead>

            <tbody>
            <?php
            $data = mysqli_query($conn,"
            SELECT rg.*, mg.jenis_jabatan, mg.nama_jabatan
            FROM riwayat_jabatan rg
            JOIN master_jabatan mg ON rg.id_jabatan = mg.id_jabatan
            WHERE rg.nip='$nip'
            ORDER BY rg.tmt_jabatan 
            ");

            while($row = mysqli_fetch_assoc($data)){

                echo "<tr onclick=\"pilihData('".$row['id_riwayat_jabatan']."','".$row['id_jabatan']."','".$row['tmt_jabatan']."', '".$row['tmt_akhir']."')\">
                <td>".$row['jenis_jabatan']." - ".$row['nama_jabatan']."</td>
                <td>".date('d-m-Y', strtotime($row['tmt_jabatan']))."</td>
                <td>".date('d-m-Y', strtotime($row['tmt_akhir']))."</td>
                </tr>";
                
                }

            ?>
            </tbody>
            </table>

            </div>
    
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
function pilihData(id,id_jabatan,tmt_jabatan,tmt_akhir){

    document.getElementById("id_riwayat_jabatan").value = id;
    document.querySelector("select[name='id_jabatan']").value = id_jabatan;
    document.querySelector("input[name='tmt_jabatan']").value = tmt_jabatan;
    document.querySelector("input[name='tmt_akhir']").value = tmt_akhir;
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