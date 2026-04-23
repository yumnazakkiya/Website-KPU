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

/* TAMBAH RIWAYAT KELUARGA */
if(isset($_POST['tambah'])){

    $nama       = $_POST['nama'];
    $no_telp    = $_POST['no_telp'];
    $alamat     = $_POST['alamat'];
    $id_hub_kel = $_POST['id_hub_kel'];

    if(!empty($nama) && !empty($id_hub_kel)){

        $cek = mysqli_query($conn,"
        SELECT * FROM riwayat_keluarga
        WHERE nip='$nip'
        AND nama='$nama'
        AND id_hub_kel='$id_hub_kel'
        ");

        if(mysqli_num_rows($cek)==0){

            mysqli_query($conn,"
            INSERT INTO riwayat_keluarga
            (
            nama,
            no_telp,
            alamat,
            nip,
            id_hub_kel
            )
            VALUES
            (
            '$nama',
            '$no_telp',
            '$alamat',
            '$nip',
            '$id_hub_kel'
            )
            ");

            header("Location: Edit_Riwayat_Keluarga_User.php?status=berhasil_tambah");
            exit;
        }
    }
}


/* UBAH RIWAYAT KELUARGA */
if(isset($_POST['ubah'])){

    $id         = $_POST['id_riwayat_kel'];
    $nama       = $_POST['nama'];
    $no_telp    = $_POST['no_telp'];
    $alamat     = $_POST['alamat'];
    $id_hub_kel = $_POST['id_hub_kel'];

    if(!empty($id) && !empty($nama)){

        mysqli_query($conn,"
        UPDATE riwayat_keluarga
        SET
        nama='$nama',
        no_telp='$no_telp',
        alamat='$alamat',
        id_hub_kel='$id_hub_kel'
        WHERE id_riwayat_kel='$id'
        ");

        header("Location: Edit_Riwayat_Keluarga_User.php?status=berhasil_ubah");
        exit;
    }
}


/* HAPUS */
if(isset($_POST['hapus'])){

    $id = $_POST['id_riwayat_kel'];

    mysqli_query($conn,"
    DELETE FROM riwayat_keluarga
    WHERE id_riwayat_kel='$id'
    ");

    header("Location: Edit_Riwayat_Keluarga_User.php?status=berhasil_hapus");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data – Riwayat Keluarga</title>
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
        <a href="Edit_Riwayat_Pendidikan_User.php" class="item-submenu">Riwayat Pendidikan</a>
        <a href="Edit_Riwayat_Diklat_User.php" class="item-submenu">Riwayat Diklat</a>
        <a href="Edit_Riwayat_Keluarga_User.php" class="item-submenu aktif">Riwayat Keluarga</a>
        <a href="Edit_Riwayat_Kehormatan_User.php" class="item-submenu">Riwayat Kehormatan</a>
        <a href="Edit_Riwayat_SKP_User.php" class="item-submenu">Riwayat SKP</a>
    </div>

    <hr class="garis-menu" />

    <a href="Pengaturan_Akun_User.php" class="item-menu">Pengaturan Akun</a>

      <hr class="garis-menu" />
    </aside>


<!-- KONTEN -->
<main class="konten">
    <h2>Riwayat Keluarga</h2>
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
        <input type="hidden" name="id_riwayat_kel" id="id_riwayat_kel">

        <!-- Nama -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Nama</label>
            <input type="text" name="nama">
            <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">TAMBAH</button>
        </div>

        <!-- Keterangan -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Keterangan</label>
            <select name="id_hub_kel" style="height:30px; border:1px solid #888;">
                <option value="">-- Pilih Keterangan --</option>
                <?php
                $qHub = mysqli_query($conn,"SELECT * FROM master_hub_kel");
                while($h = mysqli_fetch_assoc($qHub)){
                    echo "<option value='".$h['id_hub_kel']."'>".$h['hub_kel']."</option>";
                }
                ?>
            </select>
            <button type="button" onclick="klikUbahBeda('id_riwayat_kel')" class="tombol-ubah btn-kecil">UBAH</button>
        </div>

        <!-- No Telepon -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>No Telepon</label>
            <input type="text" name="no_telp">
            <button type="button" onclick="klikHapus('id_riwayat_kel')" class="tombol-hapus btn-kecil">HAPUS</button>
        </div>

        <!-- Alamat -->
        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Alamat</label>
            <input type="text" name="alamat">
        </div>

        </form>

        <!-- TABEL -->
        <table class="tabel-riwayat" border="1" cellpadding="5">
        <thead>
        <tr>
        <th>Nama</th>
        <th>No Telepon</th>
        <th>Alamat</th>
        <th>Keterangan</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $data = mysqli_query($conn,"
        SELECT rk.*, mh.hub_kel
        FROM riwayat_keluarga rk
        JOIN master_hub_kel mh ON rk.id_hub_kel = mh.id_hub_kel
        WHERE rk.nip='$nip'
        ");

        while($row = mysqli_fetch_assoc($data)){
            echo "<tr onclick=\"pilihData('".$row['id_riwayat_kel']."','".$row['nama']."','".$row['no_telp']."','".$row['alamat']."','".$row['id_hub_kel']."')\">
            
            <td>".$row['nama']."</td>
            <td>".$row['no_telp']."</td>
            <td>".$row['alamat']."</td>
            <td>".$row['hub_kel']."</td>
            
            </tr>";
            }
        ?>

        <?php
        $qJumlah = mysqli_query($conn,"
        SELECT COUNT(*) as total
        FROM riwayat_keluarga
        WHERE nip='$nip'
        ");

        $j = mysqli_fetch_assoc($qJumlah);
        ?>

        <tr>
        <td colspan="4"><b>Jumlah Anggota Keluarga : <?php echo $j['total']; ?></b></td>
        </tr>

        </tbody>
        </table>

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
function pilihData(id,nama,no_telp,alamat,id_hub_kel){

    document.getElementById("id_riwayat_kel").value = id;
    document.querySelector("input[name='nama']").value = nama;
    document.querySelector("input[name='no_telp']").value = no_telp;
    document.querySelector("input[name='alamat']").value = alamat;
    document.querySelector("select[name='id_hub_kel']").value = id_hub_kel;

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