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

/* TAMBAH RIWAYAT PENDIDIKAN */
if(isset($_POST['tambah'])){

    $id_jenjang_pend = $_POST['id_jenjang_pend'];
    $institusi       = $_POST['institusi'];
    $tahun_lulus     = $_POST['tahun_lulus'];

    if(!empty($id_jenjang_pend) && !empty($institusi) && !empty($tahun_lulus)){

        $cek = mysqli_query($conn,"
        SELECT * FROM riwayat_pendidikan
        WHERE nip='$nip'
        AND id_jenjang_pend='$id_jenjang_pend'
        AND institusi='$institusi'
        AND tahun_lulus='$tahun_lulus'
        ");

        if(mysqli_num_rows($cek)==0){

            mysqli_query($conn,"
            INSERT INTO riwayat_pendidikan
            (
            nip,
            id_jenjang_pend,
            institusi,
            tahun_lulus
            )
            VALUES
            (
            '$nip',
            '$id_jenjang_pend',
            '$institusi',
            '$tahun_lulus'
            )
            ");

            header("Location: Edit_Riwayat_Pendidikan_User.php?status=berhasil_tambah");
            exit;
        }
    }
}

/* UBAH RIWAYAT PENDIDIKAN */
if(isset($_POST['ubah'])){

    $id              = $_POST['id_riwayat_pend'];
    $id_jenjang_pend = $_POST['id_jenjang_pend'];
    $institusi       = $_POST['institusi'];
    $tahun_lulus     = $_POST['tahun_lulus'];

    if(!empty($id) && !empty($id_jenjang_pend) && !empty($institusi) && !empty($tahun_lulus)){

        mysqli_query($conn,"
        UPDATE riwayat_pendidikan
        SET
        id_jenjang_pend='$id_jenjang_pend',
        institusi='$institusi',
        tahun_lulus='$tahun_lulus'
        WHERE id_riwayat_pend='$id'
        ");

        header("Location: Edit_Riwayat_Pendidikan_User.php?status=berhasil_ubah");
        exit;
    }
}

/* HAPUS */
if(isset($_POST['hapus'])){

    $id = $_POST['id_riwayat_pend'];

    mysqli_query($conn,"
    DELETE FROM riwayat_pendidikan
    WHERE id_riwayat_pend='$id'
    ");

    header("Location: Edit_Riwayat_Pendidikan_User.php?status=berhasil_hapus");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data – Riwayat Pendidikan</title>
    <link rel="stylesheet" href="../assets/style_edit.css">
    <link rel="stylesheet" href="../assets/style_tab.css">
    <style>
        .tabel-riwayat{
            width:750px;
            margin-top:30px;
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
        <a href="Edit_Riwayat_Pendidikan_User.php" class="item-submenu aktif">Riwayat Pendidikan</a>
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
    <h2>Riwayat Pendidikan</h2>
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
        <input type="hidden" name="id_riwayat_pend" id="id_riwayat_pend">

        <!-- BARIS JENJANG -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Jenjang Pendidikan</label>

            <select name="id_jenjang_pend" style="height:30px; border:1px solid #888;">
                <option value="">-- Pilih Jenjang --</option>

                <?php
                $qPend = mysqli_query($conn,"SELECT * FROM master_jenjang_pend ORDER BY id_jenjang_pend");

                while($p = mysqli_fetch_assoc($qPend)){
                echo "<option value='$p[id_jenjang_pend]'>$p[jenjang_pend]</option>";
                }
                ?>
            </select>

            <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">
                TAMBAH
            </button>
        </div>


        <!-- BARIS INSTITUSI -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Institusi</label>

            <input type="text" name="institusi" placeholder="Nama Sekolah / Universitas">

            <button type="button" onclick="klikUbahBeda('id_riwayat_pend')" class="tombol-ubah btn-kecil">
                UBAH
            </button>
        </div>


        <!-- BARIS TAHUN LULUS -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Tahun Lulus</label>

            <input type="number" name="tahun_lulus" placeholder="YYYY">

            <button type="button" onclick="klikHapus('id_riwayat_pend')" class="tombol-hapus btn-kecil">
                HAPUS
            </button>
        </div>
    
           <!-- TABEL -->
          <table class="tabel-riwayat" border="1" cellpadding="5">

          <thead>
          <tr>
          <th>Jenjang Pendidikan</th>
          <th>Institusi</th>
          <th>Tahun Lulus</th>
          </tr>
          </thead>

          <tbody>

          <?php
          $data = mysqli_query($conn,"
          SELECT rp.*, mj.jenjang_pend
          FROM riwayat_pendidikan rp
          JOIN master_jenjang_pend mj
          ON rp.id_jenjang_pend = mj.id_jenjang_pend
          WHERE rp.nip='$nip'
          ORDER BY rp.tahun_lulus DESC
          ");

          while($row = mysqli_fetch_assoc($data)){

          echo "<tr onclick=\"pilihData('".$row['id_riwayat_pend']."','".$row['id_jenjang_pend']."','".$row['institusi']."','".$row['tahun_lulus']."')\">

          <td>".$row['jenjang_pend']."</td>
          <td>".$row['institusi']."</td>
          <td>".$row['tahun_lulus']."</td>

          </tr>";

          }
          ?>

          </tbody>
          </table>
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
function pilihData(id,id_jenjang,institusi,tahun){

    document.getElementById("id_riwayat_pend").value = id;
    document.querySelector("select[name='id_jenjang_pend']").value = id_jenjang;
    document.querySelector("input[name='institusi']").value = institusi;
    document.querySelector("input[name='tahun_lulus']").value = tahun;

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
    if (status) {
    window.history.replaceState({}, document.title, window.location.pathname);
}
});
</script>
</body>
</html>