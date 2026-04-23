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


/* =========================
   TAMBAH
   ========================= */
if (isset($_POST['tambah'])) {

  $id_jenjang_pend = $_POST['id_jenjang_pend'];
  $institusi = $_POST['institusi'];
  $tahun_lulus = $_POST['tahun_lulus'];

  if (!empty($id_jenjang_pend) && !empty($institusi) && !empty($tahun_lulus)) {

    $cek = mysqli_query($conn, "
SELECT * FROM riwayat_pendidikan
WHERE nip='$nip'
AND id_jenjang_pend='$id_jenjang_pend'
AND institusi='$institusi'
AND tahun_lulus='$tahun_lulus'
");

    if (mysqli_num_rows($cek) == 0) {

      mysqli_query($conn, "
INSERT INTO riwayat_pendidikan
(nip,id_jenjang_pend,institusi,tahun_lulus)
VALUES
('$nip','$id_jenjang_pend','$institusi','$tahun_lulus')
");

header("Location: Admin_Edit_Riwayat_Pendidikan.php?nip=" . urlencode($nip) . "&status=berhasil_tambah");
exit;
    } 
  } 
}


/* =========================
   UBAH
   ========================= */
if (isset($_POST['ubah'])) {

  $id = $_POST['id_riwayat_pend'];

  if (empty($id)) {
    die("Pilih data dulu");
  }

  $id_jenjang_pend = $_POST['id_jenjang_pend'];
  $institusi = $_POST['institusi'];
  $tahun_lulus = $_POST['tahun_lulus'];

  if (!empty($id_jenjang_pend) && !empty($institusi) && !empty($tahun_lulus)) {

    mysqli_query($conn, "
UPDATE riwayat_pendidikan
SET
id_jenjang_pend='$id_jenjang_pend',
institusi='$institusi',
tahun_lulus='$tahun_lulus'
WHERE id_riwayat_pend='$id'
");

header("Location: Admin_Edit_Riwayat_Pendidikan.php?nip=" . urlencode($nip) . "&status=berhasil_ubah");
exit;
  } 
}


/* =========================
   HAPUS
   ========================= */
if (isset($_POST['hapus'])) {

  $id = $_POST['id_riwayat_pend'];

  if (empty($id)) {
    die("Pilih data dulu");
  }

  mysqli_query($conn, "
DELETE FROM riwayat_pendidikan
WHERE id_riwayat_pend='$id'
");

header("Location: Admin_Edit_Riwayat_Pendidikan.php?nip=" . urlencode($nip) . "&status=berhasil_hapus");
exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Edit Data – Riwayat Pendidikan</title>
  <link rel="stylesheet" href="../assets/style.css" />
  <link rel="stylesheet" href="../assets/edit_riwayat.css" />

</head>

<body class="role-admin">

  <!-- SIDEBAR -->
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

  <!-- KONTEN -->
  <main class="konten">
    <h2>Riwayat Pendidikan</h2>

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
      <a href="Admin_Edit_Riwayat_Pendidikan.php?nip=<?= $nip ?>" class="tab aktif">Riwayat Pendidikan</a>
      <a href="Admin_Edit_Riwayat_Diklat.php?nip=<?= $nip ?>" class="tab">Riwayat Diklat</a>
      <a href="Admin_Edit_Riwayat_Keluarga.php?nip=<?= $nip ?>" class="tab">Riwayat Keluarga</a>
      <a href="Admin_Edit_Riwayat_Kehormatan.php?nip=<?= $nip ?>" class="tab">Riwayat Kehormatan</a>
      <a href="Admin_Edit_Riwayat_SKP.php?nip=<?= $nip ?>" class="tab">Riwayat SKP</a>
    </div>

    <div class="bagian-identitas">

      <div class="form-edit">

      <form method="POST" id="formUpload">
          <input type="hidden" name="nip" value="<?= $nip ?>">
          <input type="hidden" name="id_riwayat_pend" id="id_riwayat_pend">

          <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Jenjang Pendidikan</label>

            <select name="id_jenjang_pend" style="height:30px; border:1px solid #888;">

              <option value="">Pilih Jenjang</option>

              <?php
              $qPend = mysqli_query($conn, "SELECT * FROM master_jenjang_pend ORDER BY id_jenjang_pend");

              while ($p = mysqli_fetch_assoc($qPend)) {
                echo "<option value='$p[id_jenjang_pend]'>$p[jenjang_pend]</option>";
              }
              ?>

            </select>

            <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">
              TAMBAH
            </button>

          </div>

          <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Institusi</label>

            <input type="text" name="institusi" placeholder="Nama Sekolah / Universitas">

            <button type="button" onclick="klikUbahBeda('id_riwayat_pend')" class="tombol-ubah btn-kecil">
              UBAH
            </button>

          </div>

          <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
            <label>Tahun Lulus</label>

            <input type="number" name="tahun_lulus" placeholder="YYYY">
            <button type="button" onclick="klikHapus('id_riwayat_pend')" class="tombol-hapus btn-kecil">
              HAPUS
            </button>

          </div>

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
              $dataRiwayat = mysqli_query($conn, "
SELECT rp.*, mj.jenjang_pend
FROM riwayat_pendidikan rp
JOIN master_jenjang_pend mj
ON rp.id_jenjang_pend = mj.id_jenjang_pend
WHERE rp.nip='$nip'
ORDER BY rp.tahun_lulus DESC
");

              while ($row = mysqli_fetch_assoc($dataRiwayat)) {

                echo "<tr onclick=\"pilihData('" . $row['id_riwayat_pend'] . "','" . $row['id_jenjang_pend'] . "','" . $row['institusi'] . "','" . $row['tahun_lulus'] . "')\">

<td>" . $row['jenjang_pend'] . "</td>
<td>" . $row['institusi'] . "</td>
<td>" . $row['tahun_lulus'] . "</td>

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
  <?php include '../pegawai/Notifikasi_Logout.php'; ?>

  <script>
    function pilihData(id, id_jenjang, institusi, tahun) {

      document.getElementById("id_riwayat_pend").value = id;
      document.querySelector("select[name='id_jenjang_pend']").value = id_jenjang;
      document.querySelector("input[name='institusi']").value = institusi;
      document.querySelector("input[name='tahun_lulus']").value = tahun;

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