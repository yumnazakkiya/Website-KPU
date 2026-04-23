<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['nip'])) {
  header("location: ../auth/Login.php");
  exit;
}

$nip = $_SESSION['nip'];

$stmt = $conn->prepare("
    SELECT u.*, p.nama_pegawai
    FROM user u
    JOIN pegawai p ON u.nip = p.nip
    WHERE u.nip=?
");
$stmt->bind_param("s", $nip);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$offset = ($page - 1) * $limit;

?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <title>Data Pegawai</title>
  <link rel="stylesheet" href="../assets/style.css" />
  <link rel="stylesheet" href="../assets/profil_data.css" />
  <style>

  </style>
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

    <div class="submenu aktif" id="submenuDataMaster">
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
      <a href="Admin_DM_KabupatenKota.php" class="item-submenu">Kabupaten/Kota</a>

    </div>

    <hr class="garis-menu" />
    <a href="Admin_Manajemen_Akun.php" class="item-menu">
      Manajemen Akun
    </a>

    <hr class="garis-menu">
  </aside>
  <!-- KONTEN UTAMA -->
  <main class="konten">
    <h2>Data Pegawai</h2>
    <!-- dropdown-->
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
      <a href="#" onclick="event.preventDefault(); openLogoutModal();">Keluar</a>
      </div>
    </div>
    <!-- dropdown akhir-->
    <!-- KONTROL TABEL -->
    <section class="kontrol">
      <div>
        Tampilkan
        <select id="jumlahData">
          <option value="10" <?= ($limit == 10 ? 'selected' : '') ?>>10</option>
          <option value="25" <?= ($limit == 25 ? 'selected' : '') ?>>25</option>
          <option value="50" <?= ($limit == 50 ? 'selected' : '') ?>>50</option>
        </select>
        Entri
      </div>

      <div>
        Cari
        <input type="text" id="pencarian" placeholder="Cari data..." value="<?= $search ?>" />
      </div>
    </section>

    <!-- TABEL -->
    <table class="tabel">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Jabatan</th>
          <th>Golongan</th>
          <th>NIP</th>
          <th>Tipe Karyawan</th>
          <th>Divisi/Unit</th>
          <th>Aksi</th>
        </tr>
      </thead>

      <tbody id="dataTabel"></tbody>
    </table>
    <div class="table-footer">
      <div id="infoData"></div>
      <div id="pagination"></div>
    </div>
  </main>
  <!-- dropdown-->
  <?php include '../pegawai/Notifikasi_Logout.php'; ?>

  <script src="../assets/core-ui.js"></script>
  <script src="../assets/datamaster.js"></script>
  <script src="../assets/admin-ui.js"></script>
  <script src="../assets/profildata.js"></script>

</body>

</html>