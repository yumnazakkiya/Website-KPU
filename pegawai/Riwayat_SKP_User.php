<?php
include 'Data_Pegawai.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat SKP</title>
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
      <a href="Identitas_User.php" class="tab">Identitas</a>
      <a href="Riwayat_Golongan_User.php" class="tab">Riwayat Golongan</a>
      <a href="Riwayat_Jabatan_User.php" class="tab">Riwayat Jabatan</a>
      <a href="Riwayat_Pendidikan_User.php" class="tab">Riwayat Pendidikan</a>
      <a href="Riwayat_Diklat_User.php" class="tab">Riwayat Diklat</a>
      <a href="Riwayat_Keluarga_User.php" class="tab">Riwayat Keluarga</a>
      <a href="Riwayat_Kehormatan_User.php" class="tab">Riwayat Kehormatan</a>
      <a href="Riwayat_SKP_User.php" class="tab aktif">Riwayat SKP</a>
  </div>
    
    
    <div class="pembungkus-form">
      <div class="form">
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
        <td>".$row['rerata_nilai']."</td>
        <td>".$row['predikat_skp']."</td>

        </tr>";

        }
        ?>

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