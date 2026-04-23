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

/* TAMBAH RIWAYAT SKP */
if(isset($_POST['tambah'])){

    $tahun            = $_POST['tahun'];
    $rerata_nilai     = $_POST['rerata_nilai'];
    $id_predikat_skp  = $_POST['id_predikat_skp'];

    if(!empty($tahun) && !empty($rerata_nilai) && !empty($id_predikat_skp)){

        $cek = mysqli_query($conn,"
        SELECT * FROM riwayat_skp
        WHERE nip='$nip'
        AND tahun='$tahun'
        ");

        if(mysqli_num_rows($cek)==0){

            mysqli_query($conn,"
            INSERT INTO riwayat_skp
            (
            nip,
            tahun,
            rerata_nilai,
            id_predikat_skp
            )
            VALUES
            (
            '$nip',
            '$tahun',
            '$rerata_nilai',
            '$id_predikat_skp'
            )
            ");

            header("Location: Edit_Riwayat_SKP_User.php?status=berhasil_tambah");
            exit;
        }
    }
}

/* UBAH RIWAYAT SKP */
if(isset($_POST['ubah'])){

    $id               = $_POST['id_riwayat_skp'];
    $tahun            = $_POST['tahun'];
    $rerata_nilai     = $_POST['rerata_nilai'];
    $id_predikat_skp  = $_POST['id_predikat_skp'];

    if(!empty($id) && !empty($tahun) && !empty($rerata_nilai) && !empty($id_predikat_skp)){

        mysqli_query($conn,"
        UPDATE riwayat_skp
        SET
        tahun='$tahun',
        rerata_nilai='$rerata_nilai',
        id_predikat_skp='$id_predikat_skp'
        WHERE id_riwayat_skp='$id'
        ");

        header("Location: Edit_Riwayat_SKP_User.php?status=berhasil_ubah");
        exit;
    }
}

/* HAPUS */
if(isset($_POST['hapus'])){

    $id = $_POST['id_riwayat_skp'];

    mysqli_query($conn,"
    DELETE FROM riwayat_skp
    WHERE id_riwayat_skp='$id'
    ");

    header("Location: Edit_Riwayat_SKP_User.php?status=berhasil_hapus");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data – Riwayat SKP</title>
    <link rel="stylesheet" href="../assets/style_edit.css">
    <link rel="stylesheet" href="../assets/style_tab.css">
    <style> 
        .tabel-riwayat{
            width:750px;
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
        <a href="Edit_Riwayat_Kehormatan_User.php" class="item-submenu">Riwayat Kehormatan</a>
        <a href="Edit_Riwayat_SKP_User.php" class="item-submenu aktif">Riwayat SKP</a>
    </div>

    <hr class="garis-menu" />

    <a href="Pengaturan_Akun_User.php" class="item-menu">Pengaturan Akun</a>

      <hr class="garis-menu" />
    </aside>


<!-- KONTEN -->
<main class="konten">
    <h2>Riwayat SKP</h2>
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
        <input type="hidden" name="id_riwayat_skp" id="id_riwayat_skp">

        <!-- BARIS TAHUN -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
        <label>Tahun</label>

        <input type="number" name="tahun">

        <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">
        TAMBAH
        </button>
        </div>


        <!-- BARIS RATA RATA -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
        <label>Rata-Rata</label>

        <input type="number" name="rerata_nilai" step="0.01">

        <button type="button" onclick="klikUbahBeda('id_riwayat_skp')"  class="tombol-ubah btn-kecil">
        UBAH
        </button>
        </div>


        <!-- BARIS PREDIKAT -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
        <label>Predikat</label>

        <select name="id_predikat_skp" style="height:30px; border:1px solid #888;">

        <option value="">-- Pilih Predikat --</option>

        <?php
        $qPredikat = mysqli_query($conn,"SELECT * FROM master_predikat_skp");

        while($p = mysqli_fetch_assoc($qPredikat)){
        echo "<option value='".$p['id_predikat_skp']."'>".$p['predikat_skp']."</option>";
        }
        ?>
        </select>

        <div class="aksi-vertikal">

        <button type="button" onclick="klikHapus('id_riwayat_skp')" class="tombol-hapus btn-kecil">
        HAPUS
        </button>

        </div>

        </div>

        <!-- TABEL -->
        <table class="tabel-riwayat" border="1" cellpadding="5">
        <thead>
        <tr>
        <th>Tahun</th>
        <th>Rata-Rata</th>
        <th>Predikat</th>
        </tr>
        </thead>

        <tbody>

        <?php
        $data = mysqli_query($conn,"
        SELECT rs.*, mp.predikat_skp
        FROM riwayat_skp rs
        JOIN master_predikat_skp mp
        ON rs.id_predikat_skp = mp.id_predikat_skp
        WHERE rs.nip='$nip'
        ORDER BY rs.tahun DESC
        ");

        while($row = mysqli_fetch_assoc($data)){

            echo "<tr onclick=\"pilihData('".$row['id_riwayat_skp']."','".$row['tahun']."','".$row['rerata_nilai']."','".$row['id_predikat_skp']."')\">
            <td>".$row['tahun']."</td>
            <td>".number_format($row['rerata_nilai'], 2)."</td>
            <td>".$row['predikat_skp']."</td>
            </tr>";
        
        }
        ?>

        </tbody>
        </table>

        </form>
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
function pilihData(id,tahun,rerata,id_predikat){

    document.getElementById("id_riwayat_skp").value = id;
    document.querySelector("input[name='tahun']").value = tahun;
    document.querySelector("input[name='rerata_nilai']").value = rerata;
    document.querySelector("select[name='id_predikat_skp']").value = id_predikat;
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