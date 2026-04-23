<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['nip'])){
    header("location: ../auth/Login.php");
    exit;
}

$username = $_SESSION['username'] ?? '';
$nip = $_SESSION['nip'];

$query = mysqli_query($conn,"SELECT * FROM pegawai WHERE nip='$nip'");
$data = mysqli_fetch_assoc($query);

$riwayat_gol = mysqli_query($conn,"
SELECT rg.*, mg.nama_pangkat, mg.kode_gol
FROM riwayat_golongan rg
JOIN master_golongan mg ON rg.id_gol = mg.id_gol
WHERE rg.nip='$nip'
ORDER BY rg.id_riwayat_gol DESC
LIMIT 1
");

$data_gol = mysqli_fetch_assoc($riwayat_gol) ?? [];

$riwayat_jabatan = mysqli_query($conn,"
SELECT rj.*, mj.nama_jabatan, mj.jenis_jabatan
FROM riwayat_jabatan rj
JOIN master_jabatan mj ON rj.id_jabatan = mj.id_jabatan
WHERE rj.nip='$nip'
ORDER BY rj.id_riwayat_jabatan DESC
LIMIT 1
");

$data_jabatan = mysqli_fetch_assoc($riwayat_jabatan) ?? [];

$golongan = mysqli_query($conn,"SELECT * FROM master_golongan");
$jabatan  = mysqli_query($conn,"SELECT * FROM master_jabatan");
$jk       = mysqli_query($conn,"SELECT * FROM master_jenis_kelamin");
$agama    = mysqli_query($conn,"SELECT * FROM master_agama");
$status   = mysqli_query($conn,"SELECT * FROM master_status_perkawinan");
$unit     = mysqli_query($conn,"SELECT * FROM master_divisi");
$kabupaten = mysqli_query($conn,"SELECT * FROM master_kabupaten ORDER BY nama_kabupaten ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data – Identitas</title>
    <link rel="stylesheet" href="../assets/style_edit.css">
    <link rel="stylesheet" href="../assets/style_tab.css">
    <style>
        .klik-redirect {
            cursor: pointer;
            transition: 0.2s;
        }

        .klik-redirect:hover {
            background-color: #f5f5f5;
        }
        .klik-redirect select,
        .klik-redirect input {
            pointer-events: none;
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
        <a href="Edit_Identitas_User.php" class="item-submenu aktif">Identitas</a>
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
    <h2>Identitas</h2>
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
      <form method="POST" action="Simpan_Identitas.php" enctype="multipart/form-data"  id="formUpload">
        <div class="bagian-identitas">

        <!-- FOTO -->
        <div class="kotak-foto">
            <div class="pratinjau-foto">
            <img id="preview" src="../<?= $data['foto'] ?>" style="width:100%; height:100%; object-fit:cover;">
            </div>

            <label class="btn-upload">
                Pilih Gambar
                <input type="file" name="foto" accept="image/jpeg" 
                    onchange="previewImage(event)" hidden>
            </label>
        </div>
        <div class="form-edit-identitas">

        <div class="baris-edit">
        <label>Nama</label>
        <input type="text" name="nama_pegawai" value="<?= $data['nama_pegawai'] ?>">
        </div>

        <div class="baris-edit">
        <label>NIP</label>
        <input type="text" name="nip" value="<?= $data['nip'] ?>">
        </div>

        <!-- Golongan -->
        <div class="baris-edit">
            <label>Pangkat / Gol.Ruang / TMT</label>

            <div class="input-gabung klik-redirect" onclick="window.location='Edit_Riwayat_Golongan_User.php'">

            <select style="pointer-events:none;">

              <?php if(!empty($data_gol)) { ?>

                  <?php while($g = mysqli_fetch_assoc($golongan)){ ?>
                  <option value="<?= $g['id_gol'] ?>"
                  <?php if($data_gol['id_gol'] == $g['id_gol']) echo "selected"; ?>>
                  <?= $g['nama_pangkat'] ?> - <?= $g['kode_gol'] ?>
                  </option>
                  <?php } ?>

              <?php } else { ?>

                  <option value="">-- Pilih Pangkat/Gol.Ruang --</option>

              <?php } ?>

              </select>

            <input type="date" value="<?= $data_gol['tmt_golongan'] ?? '' ?>" style="pointer-events:none;">
        </div>
        </div>

        <!-- Jabatan -->
        <div class="baris-edit">
            <label>Jabatan Terakhir / TMT</label>

            <div class="input-gabung klik-redirect" onclick="window.location='Edit_Riwayat_Jabatan_User.php'">

            <select style="pointer-events:none;">
            <?php if(!empty($data_jabatan)) { ?>

              <?php while($g = mysqli_fetch_assoc($jabatan)){ ?>
              <option value="<?= $g['id_jabatan'] ?>"
              <?php if($data_jabatan['id_jabatan']==$g['id_jabatan']) echo "selected"; ?>>
              <?= $g['nama_jabatan'] ?> - <?= $g['jenis_jabatan'] ?>
              </option>
              <?php } ?>

            <?php } else { ?>

              <option value="">-- Pilih Jabatan --</option>

            <?php } ?>
            </select>

            <input type="date" value="<?= $data_jabatan['tmt_jabatan'] ?? '' ?>" style="pointer-events:none;">
        </div>
        </div>


        <div class="baris-edit">
        <label>TMT CPNS</label>
        <input type="date" name="tmt_cpns" value="<?= $data['tmt_cpns'] ?>">
        </div>

        <div class="baris-edit">
        <label>TMT PNS</label>
        <input type="date" name="tmt_pns" value="<?= $data['tmt_pns'] ?>">
        </div>


        <div class="baris-edit">
        <label>Tempat & Tanggal Lahir</label>

        <div class="input-gabung">

        <select name="tempat_lahir">
        <option value="">-- Pilih Kabupaten --</option>

        <?php while($row = mysqli_fetch_assoc($kabupaten)) { ?>

        <option value="<?= $row['nama_kabupaten']; ?>"
        <?= ($row['nama_kabupaten'] == $data['tempat_lahir']) ? 'selected' : ''; ?>>

        <?= $row['nama_kabupaten']; ?>

        </option>

        <?php } ?>

        </select>

        <input type="date" name="tanggal_lahir" value="<?= $data['tanggal_lahir'] ?>">

        </div>
        </div>

        <div class="baris-edit">
        <label>Jenis Kelamin</label>

        <select name="id_jenis_kelamin">

        <?php while($k = mysqli_fetch_assoc($jk)){ ?>

        <option value="<?= $k['id_jenis_kelamin'] ?>"
        <?php if($data['id_jenis_kelamin']==$k['id_jenis_kelamin']) echo "selected"; ?>>

        <?= $k['jenis_kelamin'] ?>

        </option>

        <?php } ?>

        </select>
        </div>


        <div class="baris-edit">
        <label>Agama</label>

        <select name="id_agama">

        <?php while($a = mysqli_fetch_assoc($agama)){ ?>

        <option value="<?= $a['id_agama'] ?>"
        <?= ($data['id_agama'] == $a['id_agama']) ? 'selected' : '' ?>>

        <?= $a['agama'] ?>

        </option>

        <?php } ?>

        </select>

        </div>


        <div class="baris-edit">
        <label>Status Perkawinan</label>

        <select name="id_status_perkawinan">

        <?php while($s = mysqli_fetch_assoc($status)){ ?>

        <option value="<?= $s['id_status_perkawinan'] ?>"
        <?= ($data['id_status_perkawinan'] == $s['id_status_perkawinan']) ? 'selected' : '' ?>>

        <?= $s['status_perkawinan'] ?>

        </option>

        <?php } ?>

        </select>

        </div>


        <div class="baris-edit">
        <label>Unit Kerja</label>

        <select name="id_unit_kerja">

        <?php while($u = mysqli_fetch_assoc($unit)){ ?>

        <option value="<?= $u['id_unit_kerja'] ?>"
        <?= ($data['id_unit_kerja'] == $u['id_unit_kerja']) ? 'selected' : '' ?>>

        <?= $u['unit_kerja'] ?>

        </option>

        <?php } ?>

        </select>

        </div>
        
        <div class="baris-edit">
        <label>Instansi</label>
        <input type="text" value="KPU Kota Surabaya" readonly>
        <input type="hidden" name="instansi" value="KPU Kota Surabaya">
        </div>

        <div class="baris-edit">
        <label>Tipe Karyawan</label>    
        <input type="text" name="tipe_karyawan"
        value="<?= $data['tipe_karyawan'] ?? '' ?>"
        placeholder="Contoh: PNS, CPNS, PPPK, dll">            
        </div>

        <div class="baris-edit">
          <label>No Telepon</label>
          <input type="text" name="no_telp" 
            value="<?= $data['no_telp'] ?>" 
            placeholder="Contoh: 08XXXXX"
            oninput="formatTelp(this)">
        </div>


        <div class="baris-edit">
        <label>Alamat Rumah</label>
        <textarea name="alamat"><?= $data['alamat'] ?></textarea>
        </div>


        <div class="aksi-edit">
        <button type="button" onclick="klikUbah()" class="tombol-ubah">UBAH</button>
        </div>

        </div>
        </div>

        </form>
</main>
<div id="modalError" class="modal">
  <div class="modal-content">
    <h3>Peringatan</h3>
    <p id="errorText"></p>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
        <button onclick="closeErrorModal()" class="tombol-batal">OK</button>
    </div>
  </div>
</div>

<div id="modalAksi" class="modal">
  <div class="modal-content">
    <h3 id="judulAksi"></h3>
    <p id="isiAksi"></p>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
      <button id="btnBatalAksi" class="tombol-batal" style="display:none;">Batal</button>
      <button id="btnOKAksi">OK</button>
    </div>
  </div>
</div>

<?php include 'Notifikasi_Logout.php'; ?>

<script src="../assets/script_pg.js"></script>

<script>
const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get('status');

if (status === 'berhasil_ubah') {
    openModalAksi("Berhasil", "Data berhasil diubah", "info");
}
</script>

</body>
</html>