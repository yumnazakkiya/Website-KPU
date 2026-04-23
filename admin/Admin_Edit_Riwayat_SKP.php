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
  $tahun = $_POST['tahun'];
  $rerata_nilai = $_POST['rerata_nilai'];
  $id_predikat_skp = $_POST['id_predikat_skp'];

  if (!empty($tahun) && !empty($rerata_nilai) && !empty($id_predikat_skp)) {
    $cek = mysqli_query($conn, "
SELECT * FROM riwayat_skp
WHERE nip='$nip'
AND tahun='$tahun'
");

    if (mysqli_num_rows($cek) == 0) {

      mysqli_query($conn, "
INSERT INTO riwayat_skp
(nip,tahun,rerata_nilai,id_predikat_skp)
VALUES
('$nip','$tahun','$rerata_nilai','$id_predikat_skp')
");

header("Location: Admin_Edit_Riwayat_SKP.php?nip=" . urlencode($nip) . "&status=berhasil_tambah");
exit;
    } 
  } 
}


/* UBAH */
if (isset($_POST['ubah'])) {

  $id = $_POST['id_riwayat_skp'];

  if (empty($id)) {
    die("Pilih data dulu");
  }

  $tahun = $_POST['tahun'];
  $rerata_nilai = $_POST['rerata_nilai'];
  $id_predikat_skp = $_POST['id_predikat_skp'];

  if (!empty($tahun) && !empty($rerata_nilai) && !empty($id_predikat_skp)) {

    mysqli_query($conn, "
UPDATE riwayat_skp
SET
tahun='$tahun',
rerata_nilai='$rerata_nilai',
id_predikat_skp='$id_predikat_skp'
WHERE id_riwayat_skp='$id'
");

header("Location: Admin_Edit_Riwayat_SKP.php?nip=" . urlencode($nip) . "&status=berhasil_ubah");
exit;
  } 
}


/* HAPUS */
if (isset($_POST['hapus'])) {

  $id = $_POST['id_riwayat_skp'];

  if (empty($id)) {
    die("Pilih data dulu");
  }

  mysqli_query($conn, "
DELETE FROM riwayat_skp
WHERE id_riwayat_skp='$id'
");

header("Location: Admin_Edit_Riwayat_SKP.php?nip=" . urlencode($nip) . "&status=berhasil_hapus");
exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Edit Data – Riwayat SKP</title>
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

    <h2>Riwayat SKP</h2>
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
      <a href="Admin_Edit_Riwayat_Keluarga.php?nip=<?= $nip ?>" class="tab">Riwayat Keluarga</a>
      <a href="Admin_Edit_Riwayat_Kehormatan.php?nip=<?= $nip ?>" class="tab">Riwayat Kehormatan</a>
      <a href="Admin_Edit_Riwayat_SKP.php?nip=<?= $nip ?>" class="tab aktif">Riwayat SKP</a>

    </div>

    <div class="bagian-identitas">

    <form method="POST" id="formUpload">

        <input type="hidden" name="id_riwayat_skp" id="id_riwayat_skp">

        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
          <label>Tahun</label>

          <input type="number" name="tahun">

          <button type="button" onclick="klikTambah()" class="tombol-tambah btn-kecil">
              TAMBAH
            </button>
        </div>

        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
          <label>Rata-Rata</label>

          <input type="number" name="rerata_nilai" step="0.01">

          <button type="button" onclick="klikUbahBeda('id_riwayat_skp')" class="tombol-ubah btn-kecil">
              UBAH
            </button>
        </div>

        <div class="baris-form" style="grid-template-columns:120px 500px 120px;">
          <label>Predikat</label>

          <select name="id_predikat_skp" style="height:30px; border:1px solid #888;">

            <option value="">Pilih Predikat</option>

            <?php
            $qPredikat = mysqli_query($conn, "SELECT * FROM master_predikat_skp");

            while ($p = mysqli_fetch_assoc($qPredikat)) {
              echo "<option value='" . $p['id_predikat_skp'] . "'>" . $p['predikat_skp'] . "</option>";
            }
            ?>

          </select>

          <div class="aksi-vertikal">

          <button type="button" onclick="klikHapus('id_riwayat_skp')" class="tombol-hapus btn-kecil">
              HAPUS
            </button>

          </div>

        </div>

        <table class="tabel-riwayat">

          <thead>
            <tr>
              <th>Tahun</th>
              <th>Rata-Rata</th>
              <th>Predikat</th>
            </tr>
          </thead>

          <tbody>

            <?php
            $data = mysqli_query($conn, "
SELECT rs.*, mp.predikat_skp
FROM riwayat_skp rs
JOIN master_predikat_skp mp
ON rs.id_predikat_skp = mp.id_predikat_skp
WHERE rs.nip='$nip'
ORDER BY rs.tahun DESC
");

            while ($row = mysqli_fetch_assoc($data)) {

              echo "<tr onclick=\"pilihData('" . $row['id_riwayat_skp'] . "','" . $row['tahun'] . "','" . $row['rerata_nilai'] . "','" . $row['id_predikat_skp'] . "')\">

<td>" . $row['tahun'] . "</td>
<td>" . $row['rerata_nilai'] . "</td>
<td>" . $row['predikat_skp'] . "</td>

</tr>";
            }
            ?>

          </tbody>
        </table>

      </form>

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
    function pilihData(id, tahun, rerata, id_predikat) {

      document.getElementById("id_riwayat_skp").value = id;
      document.querySelector("input[name='tahun']").value = tahun;
      document.querySelector("input[name='rerata_nilai']").value = rerata;
      document.querySelector("select[name='id_predikat_skp']").value = id_predikat;

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