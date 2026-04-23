<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['nip'])) {
    header("location: ../auth/Login.php");
    exit;
}

// =========================
// AMBIL DATA ADMIN LOGIN
// =========================
$nip_session = $_SESSION['nip'];

$stmtAdmin = $conn->prepare("
    SELECT u.username, u.role, p.nama_pegawai
    FROM user u
    JOIN pegawai p ON u.nip = p.nip
    WHERE u.nip = ?
");
$stmtAdmin->bind_param("s", $nip_session);
$stmtAdmin->execute();
$resultAdmin = $stmtAdmin->get_result();
$admin = $resultAdmin->fetch_assoc() ?? [
    'username' => 'Administrator',
    'nama_pegawai' => 'Administrator'
];

// =========================
// AMBIL NIP PEGAWAI YANG DIEDIT
// =========================
$nip = $_POST['nip'] ?? $_GET['nip'] ?? '';

if (empty($nip)) {
    die("NIP tidak ditemukan");
}

// =========================
// AMBIL DATA PEGAWAI
// =========================
$query = mysqli_query($conn, "SELECT * FROM pegawai WHERE nip='$nip'");
$pegawai = mysqli_fetch_assoc($query);

if (!$pegawai) {
    die("Data pegawai tidak ditemukan");
}


/* TAMBAH */
if (isset($_POST['tambah'])) {

    $nama_keluarga = $_POST['nama'];
    $no_telp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];
    $id_hub_kel = $_POST['id_hub_kel'];

    if (!empty($nama_keluarga) && !empty($id_hub_kel) && !empty($no_telp) && !empty($alamat)) {

        $cek = mysqli_query($conn, "
SELECT * FROM riwayat_keluarga
WHERE nip='$nip'
AND nama='$nama_keluarga'
AND id_hub_kel='$id_hub_kel'
");

        if (mysqli_num_rows($cek) == 0) {

            mysqli_query($conn, "
INSERT INTO riwayat_keluarga
(nama,no_telp,alamat,nip,id_hub_kel)
VALUES
('$nama_keluarga','$no_telp','$alamat','$nip','$id_hub_kel')
");
header("Location: Admin_Edit_Riwayat_Keluarga.php?nip=" . urlencode($nip) . "&status=berhasil_tambah");
exit;
        } 
    } 
}


/* UBAH */
if (isset($_POST['ubah'])) {

    $id = $_POST['id_riwayat_kel'];

    if (empty($id)) {
        die("Pilih data dulu");
    }

    $nama_keluarga = $_POST['nama'];
    $no_telp = $_POST['no_telp'];
    $alamat = $_POST['alamat'];
    $id_hub_kel = $_POST['id_hub_kel'];

    if (!empty($nama_keluarga) && !empty($id_hub_kel)) {

        mysqli_query($conn, "
UPDATE riwayat_keluarga
SET
nama='$nama_keluarga',
no_telp='$no_telp',
alamat='$alamat',
id_hub_kel='$id_hub_kel'
WHERE id_riwayat_kel='$id'
");

header("Location: Admin_Edit_Riwayat_Keluarga.php?nip=" . urlencode($nip) . "&status=berhasil_ubah");
exit;
    } 
}


/* HAPUS */
if (isset($_POST['hapus'])) {

    $id = $_POST['id_riwayat_kel'];

    if (empty($id)) {
        die("Pilih data dulu");
    }

    mysqli_query($conn, "
DELETE FROM riwayat_keluarga
WHERE id_riwayat_kel='$id'
");

header("Location: Admin_Edit_Riwayat_Keluarga.php?nip=" . urlencode($nip) . "&status=berhasil_hapus");
exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data – Riwayat Keluarga</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <link rel="stylesheet" href="../assets/edit_riwayat.css" />

</head>

<body class="role-admin">

    <aside class="sidebar" id="sidebar">

        <div class="logo">
            <span>LOGO</span>
            <button class="tombol-menu" id="tombolMenu">✕</button>
        </div>

        <hr class="garis-menu" />

        <a href="Admin_Profil_Data_Pegawai.php" class="item-menu aktif">
            Profil Data Pegawai
        </a>

        <hr class="garis-menu" />

        <a href="Admin_Tambah_Data.php" class="item-menu">
            Tambah Data Pegawai Baru
        </a>

        <hr class="garis-menu" />

        <a href="Admin_Pengaturan_Akun.php" class="item-menu">
            Pengaturan Akun
        </a>

        <hr class="garis-menu" />

        <div class="item-menu" id="menuDataMaster">
            Data Master
            <span class="panah-menu" id="panahDataMaster">▼</span>
        </div>

        <div class="submenu" id="submenuDataMaster">
            <a href="Admin_DM_Gender.php" class="item-submenu">Jenis Kelamin</a>
            <a href="Admin_DM_Agama.php" class="item-submenu">Agama</a>
            <a href="Admin_DM_StatusPerkawinan.php" class="item-submenu">Status Perkawinan</a>
            <a href="Admin_DM_JenjangPendidikan.php" class="item-submenu">Jenjang Pendidikan</a>
            <a href="Admin_DM_HubunganKeluarga.php" class="item-submenu">Hubungan Keluarga</a>
            <a href="Admin_DM_Golongan.php" class="item-submenu">Golongan</a>
            <a href="Admin_DM_Jabatan.php" class="item-submenu">Jabatan</a>
            <a href="Admin_DM_UnitKerja.php" class="item-submenu">Unit Kerja / Divisi</a>
            <a href="Admin_DM_JenisDiklat.php" class="item-submenu">Jenis Diklat</a>
            <a href="Admin_DM_PredikatSKP.php" class="item-submenu">Predikat SKP</a>
        </div>

        <hr class="garis-menu" />

        <a href="Admin_Manajemen_Akun.php" class="item-menu">
            Manajemen Akun
        </a>

        <hr class="garis-menu">

    </aside>


    <main class="konten">

        <h2>Riwayat Keluarga</h2>
        <!-- dropdown-->
        <div class="user-profile" id="userProfile">
            <div class="user-info">
                <div class="user-icon">👤</div>
                <div class="user-text">
                    <div class="user-name">
                        <?= htmlspecialchars($admin['nama_pegawai']); ?>
                    </div>
                </div>
            </div>

            <div class="dropdown-menu" id="dropdownMenu">
                <a href="Admin_Profil_Data_Pegawai.php">Beranda</a>
                <a href="#" onclick="openLogoutModal()">Keluar</a>
            </div>
        </div>
        <div class="tab-menu">

            <a href="identitas-pegawai.php?nip=<?= $nip ?>" class="tab">Identitas</a>
            <a href="Admin_Edit_Riwayat_Golongan.php?nip=<?= $nip ?>" class="tab">Riwayat Golongan</a>
            <a href="Admin_Edit_Riwayat_Jabatan.php?nip=<?= $nip ?>" class="tab">Riwayat Jabatan</a>
            <a href="Admin_Edit_Riwayat_Pendidikan.php?nip=<?= $nip ?>" class="tab">Riwayat Pendidikan</a>
            <a href="Admin_Edit_Riwayat_Diklat.php?nip=<?= $nip ?>" class="tab">Riwayat Diklat</a>
            <a href="Admin_Edit_Riwayat_Keluarga.php?nip=<?= $nip ?>" class="tab aktif">Riwayat Keluarga</a>
            <a href="Admin_Edit_Riwayat_Kehormatan.php?nip=<?= $nip ?>" class="tab">Riwayat Kehormatan</a>
            <a href="Admin_Edit_Riwayat_SKP.php?nip=<?= $nip ?>" class="tab">Riwayat SKP</a>

        </div>

        <div class="bagian-identitas">

            <div class="form-edit">

            <form method="POST" id="formUpload">
                    <input type="hidden" name="nip" value="<?= $nip ?>">
                    <input type="hidden" name="id_riwayat_kel" id="id_riwayat_kel">

                    <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
                        <label>Nama</label>
                        <input type="text" name="nama">
                        <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">
              TAMBAH
            </button>
                    </div>

                    <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
                         <label>No Telepon</label>
                        <input type="text" name="no_telp" 
                        placeholder="Contoh: 08XXXXX"
                        oninput="formatTelp(this)">                        <button type="button" onclick="klikUbahBeda('id_riwayat_kel')" class="tombol-ubah btn-kecil">
              UBAH
            </button>
                    </div>

                    <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
                        <label>Alamat</label>
                        <input type="text" name="alamat">
                        <button type="button" onclick="klikHapus('id_riwayat_kel')" class="tombol-hapus btn-kecil">
              HAPUS
            </button>
                    </div>

                    <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
                        <label>Keterangan</label>

                        <select name="id_hub_kel" style="height:30px; border:1px solid #888;">

                            <option value="">Pilih Keterangan</option>

                            <?php
                            $qHub = mysqli_query($conn, "SELECT * FROM master_hub_kel");

                            while ($h = mysqli_fetch_assoc($qHub)) {
                                echo "<option value='" . $h['id_hub_kel'] . "'>" . $h['hub_kel'] . "</option>";
                            }
                            ?>

                        </select>

                    </div>

                    <table class="tabel-riwayat">

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
                            $data = mysqli_query($conn, "
SELECT rk.*, mh.hub_kel
FROM riwayat_keluarga rk
JOIN master_hub_kel mh ON rk.id_hub_kel = mh.id_hub_kel
WHERE rk.nip='$nip'
");

                            while ($row = mysqli_fetch_assoc($data)) {
                                echo "<tr onclick=\"pilihData('" . $row['id_riwayat_kel'] . "','" . $row['nama'] . "','" . $row['no_telp'] . "','" . $row['alamat'] . "','" . $row['id_hub_kel'] . "')\">

<td>" . $row['nama'] . "</td>
<td>" . $row['no_telp'] . "</td>
<td>" . $row['alamat'] . "</td>
<td>" . $row['hub_kel'] . "</td>

</tr>";
                            }

                            $qJumlah = mysqli_query($conn, "
SELECT COUNT(*) as total
FROM riwayat_keluarga
WHERE nip='$nip'
");

                            $j = mysqli_fetch_assoc($qJumlah);
                            ?>

                            <tr>
                                <td colspan="2"><b>Jumlah Anggota Keluarga : <?php echo $j['total']; ?></b></td>
                            </tr>

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
    <?php include '../pegawai/Notifikasi_Logout.php'; ?>

    <script>
        function pilihData(id, nama_keluarga, no_telp, alamat, id_hub_kel) {

            document.getElementById("id_riwayat_kel").value = id;
            document.querySelector("input[name='nama']").value = nama_keluarga;
            document.querySelector("input[name='no_telp']").value = no_telp;
            document.querySelector("input[name='alamat']").value = alamat;
            document.querySelector("select[name='id_hub_kel']").value = id_hub_kel;

        }
    </script>

<script src="../assets/script_pg.js"></script>

<script src="../assets/core-ui.js"></script>
<script src="../assets/datamaster.js"></script>
<script src="../assets/admin-ui.js"></script>
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