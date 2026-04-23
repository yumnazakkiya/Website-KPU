<?php
include 'Data_Pegawai.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Identitas</title>
  <link rel="stylesheet" href="../assets/style_tab.css">
</head>
<body class="role-user">

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="logo">
    <span>LOGO</span>
    <button class="tombol-menu" id="tombolMenu">✕</button>
  </div>

  <hr class="garis-menu" />

  <div class="item-menu aktif">Profil</div>

  <hr class="garis-menu" />

  <div class="item-menu" id="menuEditData">
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
    <a href="Edit_Riwayat_SKP_User.php" class="item-submenu">Riwayat SKP</a>
  </div>
  <hr class="garis-menu" />

  <a href="Pengaturan_Akun_User.php" class="item-menu">Pengaturan Akun</a>
    <hr class="garis-menu" />
</aside>

<!-- KONTEN -->
<main class="konten">
  <div class="header-atas">
    <h2 class="judul-halaman">Profil</h2>
    <div class="user-profile" id="userProfile">
      <div class="user-info">
        <div class="user-icon">👤</div>
          <div class="user-name">
            <?= $data['nama_pegawai'] ?>
          </div>
      </div>

            <div class="dropdown-menu" id="dropdownMenu">
              <a href="#" onclick="openLogoutModal()">Keluar</a>
            </div>
    </div>
  </div>

    <div class="header-profil">
      <div class="profil-atas">  
        <!-- FOTO -->
        <div class="kotak-foto-profil">
          <img id="preview" src="../<?= $data['foto'] ?>" style="width:100%; height:100%; object-fit:cover;">
        </div>

        <!-- INFO -->
        <div class="info-profil">
          <h2><?= $data['nama_pegawai'] ?></h2>
            <p>
              <?= $data['nama_pegawai'] ?> memegang jabatan sebagai
              <?= $data_jabatan['jenis_jabatan'] ?? '-' ?> di 
              <?= $data['unit_kerja'] ?>.

              Memiliki riwayat jabatan sejak 
              <?= isset($data_jabatan['tmt_jabatan']) ? date('Y', strtotime($data_jabatan['tmt_jabatan'])) : '-' ?> 
              dengan pangkat/golongan terakhir 
              <?= $data_gol['nama_pangkat'] ?? '-' ?> (<?= $data_gol['kode_gol'] ?? '-' ?>).

              Pendidikan terakhir 
              <?= $data_pendidikan['jenjang_pend'] ?? '-' ?> dari 
              <?= $data_pendidikan['institusi'] ?? '-' ?> (<?= $data_pendidikan['tahun_lulus'] ?? '-' ?>).
            </p>
        </div>
            
        <!-- PDF -->
        <a href="#" onclick="cekPDF('<?= $data['nip'] ?>')" class="pdf-box">
          <div class="pdf-icon">PDF</div>
            <span>Lihat PDF</span>
        </a>
    </div>
  </div>

<!-- TAB -->
  <div class="tab-menu">
    <a href="Identitas_User.php" class="tab aktif">Identitas</a>
    <a href="Riwayat_Golongan_User.php" class="tab">Riwayat Golongan</a>
    <a href="Riwayat_Jabatan_User.php" class="tab">Riwayat Jabatan</a>
    <a href="Riwayat_Pendidikan_User.php" class="tab">Riwayat Pendidikan</a>
    <a href="Riwayat_Diklat_User.php" class="tab">Riwayat Diklat</a>
    <a href="Riwayat_Keluarga_User.php" class="tab">Riwayat Keluarga</a>
    <a href="Riwayat_Kehormatan_User.php" class="tab">Riwayat Kehormatan</a>
    <a href="Riwayat_SKP_User.php" class="tab">Riwayat SKP</a>
  </div>

  <!-- FORM -->
  <section class="form-identitas">
    <div class="form">
      <div class="baris-form">
        <label>Nama</label>
        <input value="<?= $data['nama_pegawai'] ?>" readonly>
    </div>

      <div class="baris-form">
        <label>NIP</label>
        <input value="<?= $data['nip'] ?>" readonly>
      </div>

      <div class="baris-form">
        <label>Pangkat/Gol. Ruang/TMT</label>
        <input value="<?= $data_gol['nama_pangkat'] ?? '-' ?> (<?= $data_gol['kode_gol'] ?? '-' ?>) / <?= isset($data_gol['tmt_golongan']) ? date('d-m-Y', strtotime($data_gol['tmt_golongan'])) : '-' ?>" readonly>
      </div>

      <div class="baris-form">
        <label>Jabatan Terakhir / TMT</label>
        <input value="<?= $data_jabatan['nama_jabatan'] ?? '-' ?> - <?= $data_jabatan['jenis_jabatan'] ?? '-' ?> / <?= isset($data_jabatan['tmt_jabatan']) ? date('d-m-Y', strtotime($data_jabatan['tmt_jabatan'])) : '-' ?>" readonly>
      </div>

      <div class="baris-form">
        <label>TMT CPNS</label>
        <input type="date" value="<?= $data['tmt_cpns'] ?>" readonly>
      </div>

      <div class="baris-form">
        <label>TMT PNS</label>
        <input type="date" value="<?= $data['tmt_pns'] ?>" readonly>
      </div>

      <div class="baris-form">
        <label>Tempat & Tanggal Lahir</label>
        <input value="<?= $data['tempat_lahir'] ?>, <?= date('d-m-Y', strtotime($data['tanggal_lahir'])) ?>" readonly>
      </div>

      <div class="baris-form">
        <label>Jenis Kelamin</label>
        <input value="<?= $data['jenis_kelamin'] ?>" readonly>
      </div>

      <div class="baris-form">
        <label>Agama</label>
        <input value="<?= $data['agama'] ?>" readonly>
      </div>

      <div class="baris-form">
        <label>Status Perkawinan</label>
        <input value="<?= $data['status_perkawinan'] ?>" readonly>
      </div>

      <div class="baris-form">
        <label>Unit Kerja</label>
        <input value="<?= $data['unit_kerja'] ?>" readonly>
      </div>

      <div class="baris-form">
        <label>Instansi</label>
        <input type="text" value="KPU Kota Surabaya" readonly>
        <input type="hidden" name="instansi" value="KPU Kota Surabaya">
      </div>

      <div class="baris-form">
        <label>Tipe Karyawan</label>    
        <input value="<?= $data['tipe_karyawan'] ?>" readonly>         
      </div>

      <div class="baris-form">
        <label>No Telepon</label>
        <input value="<?= $data['no_telp'] ?>" readonly>
      </div>

      <div class="baris-form">
        <label>Alamat Rumah</label>
        <textarea readonly><?= $data['alamat'] ?></textarea>
      </div>
    </div>
  </section>
</main>
<div id="modalAksi" class="modal">
  <div class="modal-content">
    <h3 id="judulAksi"></h3>
    <p id="isiAksi"></p>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
      <button id="btnOKAksi" class="tombol-tambah">OK</button>
    </div>
  </div>
</div>

<?php include 'Notifikasi_Logout.php'; ?>

<script src="../assets/script_pg.js"></script>
<script>
function showModal(judul, isi) {
    document.getElementById("judulAksi").innerText = judul;
    document.getElementById("isiAksi").innerText = isi;
    document.getElementById("modalAksi").style.display = "flex";

    document.getElementById("btnOKAksi").onclick = function() {
        document.getElementById("modalAksi").style.display = "none";
    };
}

function cekPDF(nip) {

    // DATA DARI PHP 
    let golongan = <?= isset($data_gol) ? 1 : 0 ?>;
    let jabatan = <?= isset($data_jabatan) ? 1 : 0 ?>;
    let pendidikan = <?= isset($data_pendidikan) ? 1 : 0 ?>;

    if (golongan == 0) {
        showModal("Peringatan", "Riwayat golongan belum diisi!");
        return;
    }

    if (jabatan == 0) {
        showModal("Peringatan", "Riwayat jabatan belum diisi!");
        return;
    }

    if (pendidikan == 0) {
        showModal("Peringatan", "Riwayat pendidikan belum diisi!");
        return;
    }

    // kalau lolos → buka PDF
    window.open("../admin/pdf/generate_pdf.php?nip=" + nip, "_blank");
}
</script>
</body>
</html>
