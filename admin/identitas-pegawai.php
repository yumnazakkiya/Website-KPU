<?php
session_start();
include '../config/koneksi.php';

// =========================
// CEK LOGIN
// =========================
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
$nip = $_GET['nip'] ?? '';
if (empty($nip)) {
    die("NIP tidak ditemukan.");
}

// =========================
// RIWAYAT GOLONGAN TERAKHIR
// =========================
$stmtGol = $conn->prepare("
    SELECT * FROM riwayat_golongan
    WHERE nip = ?
    ORDER BY id_riwayat_gol DESC
    LIMIT 1
");
$stmtGol->bind_param("s", $nip);
$stmtGol->execute();
$data_gol = $stmtGol->get_result()->fetch_assoc() ?? [];

// =========================
// RIWAYAT JABATAN TERAKHIR
// =========================
$stmtJabatan = $conn->prepare("
    SELECT * FROM riwayat_jabatan
    WHERE nip = ?
    ORDER BY id_riwayat_jabatan DESC
    LIMIT 1
");
$stmtJabatan->bind_param("s", $nip);
$stmtJabatan->execute();
$data_jabatan = $stmtJabatan->get_result()->fetch_assoc() ?? [];

// =========================
// PROSES UBAH DATA
// =========================
// =========================
// PROSES UBAH DATA
// =========================
if (isset($_POST['ubah'])) {
    $nama = $_POST['nama_pegawai'] ?? '';
    $tempat_lahir  = ucwords($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $jk     = $_POST['id_jenis_kelamin'] ?? '';
    $agama  = $_POST['id_agama'] ?? '';
    $status = $_POST['id_status_perkawinan'] ?? '';
    $unit   = $_POST['id_unit_kerja'] ?? '';
    $tipe_karyawan = $_POST['tipe_karyawan'] ?? '';
    $telp   = $_POST['no_telp'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $tmt_cpns = $_POST['tmt_cpns'] ?? '';
    $tmt_pns  = $_POST['tmt_pns'] ?? '';
    $golongan = $_POST['id_gol'] ?? '';
    $tmt_gol  = $_POST['tmt_golongan'] ?? '';
    $jabatan  = $_POST['id_jabatan'] ?? '';
    $tmt_jab  = $_POST['tmt_jabatan'] ?? '';

    // =========================
    // UPDATE DATA PEGAWAI
    // =========================
    $stmtUpdate = $conn->prepare("
        UPDATE pegawai SET
            nama_pegawai=?,
            no_telp=?,
            alamat=?,
            tmt_cpns=?,
            tmt_pns=?,
            tipe_karyawan=?,
            id_jenis_kelamin=?,
            id_agama=?,
            id_status_perkawinan=?,
            id_unit_kerja=?,
            id_gol=?,
            tempat_lahir=?,
            tanggal_lahir=?
        WHERE nip=?
    ");
    $stmtUpdate->bind_param(
        "ssssssssssssss",
        $nama,
        $telp,
        $alamat,
        $tmt_cpns,
        $tmt_pns,
        $tipe_karyawan,
        $jk,
        $agama,
        $status,
        $unit,
        $golongan,
        $tempat_lahir,
        $tanggal_lahir,
        $nip
    );
    $stmtUpdate->execute();

    // =========================
    // UPLOAD & UPDATE FOTO
    // =========================
    if (!empty($_FILES['foto']['name'])) {

        // Validasi error upload
        if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {

            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg'];

            // Validasi ekstensi
            if (!in_array($ext, $allowedExt)) {
                echo "<script>alert('Foto harus berformat JPG/JPEG');</script>";
                exit;
            }

            // Validasi ukuran (maksimal 2MB)
            if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
                echo "<script>alert('Ukuran foto maksimal 2MB');</script>";
                exit;
            }

            // Pastikan folder uploads tersedia
            $uploadDir = __DIR__ . "/../uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Generate nama file unik
            $namaBaru = uniqid('foto_', true) . "." . $ext;
            $pathFile = $uploadDir . $namaBaru;

            // Pindahkan file ke folder uploads
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $pathFile)) {
                $fotoPath = "uploads/" . $namaBaru;

                // Update kolom foto pada tabel pegawai
                $stmtFoto = $conn->prepare("UPDATE pegawai SET foto=? WHERE nip=?");
                $stmtFoto->bind_param("ss", $fotoPath, $nip);
                $stmtFoto->execute();
            } else {
                echo "<script>alert('Upload foto gagal');</script>";
                exit;
            }
        } else {
            echo "<script>alert('Terjadi kesalahan saat upload file');</script>";
            exit;
        }
    }

    // =========================
    // REDIRECT KE HALAMAN YANG SAMA
    // =========================
    header("Location: identitas-pegawai.php?nip=" . urlencode($nip) . "&status=berhasil_ubah");
    exit;
}



// =========================
// AMBIL DATA PEGAWAI
// =========================
$stmtPegawai = $conn->prepare("
    SELECT 
        p.*,
        jk.jenis_kelamin,
        ag.agama,
        sp.status_perkawinan,
        d.unit_kerja,
        g.nama_pangkat
    FROM pegawai p
    LEFT JOIN master_jenis_kelamin jk ON p.id_jenis_kelamin = jk.id_jenis_kelamin
    LEFT JOIN master_agama ag ON p.id_agama = ag.id_agama
    LEFT JOIN master_status_perkawinan sp ON p.id_status_perkawinan = sp.id_status_perkawinan
    LEFT JOIN master_divisi d ON p.id_unit_kerja = d.id_unit_kerja
    LEFT JOIN master_golongan g ON p.id_gol = g.id_gol
    WHERE p.nip = ?
");
$stmtPegawai->bind_param("s", $nip);
$stmtPegawai->execute();
$pegawai = $stmtPegawai->get_result()->fetch_assoc();

if (!$pegawai) {
    die("Data pegawai tidak ditemukan");
}

// =========================
// DATAMASTER
// =========================
$jk = mysqli_query($conn, "SELECT * FROM master_jenis_kelamin");
$agama = mysqli_query($conn, "SELECT * FROM master_agama");
$status = mysqli_query($conn, "SELECT * FROM master_status_perkawinan");
$unit = mysqli_query($conn, "SELECT * FROM master_divisi");
$golongan = mysqli_query($conn, "SELECT * FROM master_golongan");
$jabatan = mysqli_query($conn, "SELECT * FROM master_jabatan");
$kabupaten = mysqli_query($conn, "SELECT * FROM master_kabupaten ORDER BY nama_kabupaten ASC");
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Identitas Pegawai</title>
    <link rel="stylesheet" href="../assets/style.css" />
    <link rel="stylesheet" href="../assets/fotoadmin.css" />


</head>

<body class="role-admin">
 
    <!-- SIDEBAR MINIMAL -->


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



    <main class="konten">

        <h2>Riwayat Diklat</h2>

        <!-- <button class="tombol-keluar">Log Out</button> -->
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
            <a href="identitas-pegawai.php?nip=<?= $nip ?>" class="tab aktif">Identitas</a>
            <!-- <a href="Admin_Edit_Riwayat_Golongan.php" class="tab">Riwayat Golongan</a> -->
            <a href="Admin_Edit_Riwayat_Golongan.php?nip=<?= $nip ?>" class="tab">Riwayat Golongan</a>
            <a href="Admin_Edit_Riwayat_Jabatan.php?nip=<?= $nip ?>" class="tab">Riwayat Jabatan</a>
            <a href="Admin_Edit_Riwayat_Pendidikan.php?nip=<?= $nip ?>" class="tab">Riwayat Pendidikan</a>
            <a href="Admin_Edit_Riwayat_Diklat.php?nip=<?= $nip ?>" class="tab">Riwayat Diklat</a>
            <a href="Admin_Edit_Riwayat_Keluarga.php?nip=<?= $nip ?>" class="tab">Riwayat Keluarga</a>
            <a href="Admin_Edit_Riwayat_Kehormatan.php?nip=<?= $nip ?>" class="tab">Riwayat Kehormatan</a>
            <a href="Admin_Edit_Riwayat_SKP.php?nip=<?= $nip ?>" class="tab">Riwayat SKP</a>
        </div>

        <form method="POST" enctype="multipart/form-data" id="formUpload">
            <!-- FORM IDENTITAS -->
            <section class="form-identitas">


                <div class="kotak-foto">

                    <div class="pratinjau-foto">
                        <img id="preview" class="foto-preview"
                            src="<?= isset($pegawai['foto']) ? '../' . $pegawai['foto'] : '../uploads/default.png' ?>">
                    </div>

                    <label class="tombol-unggah">
                        Unggah Foto
                        <input type="file" name="foto" accept="image/jpeg"
                            onchange="previewImage(event)" hidden>
                    </label>

                </div>

                <div class="form">
                    <div class="baris-form">
                        <label>Nama</label>
                        <input name="nama_pegawai" value="<?= $pegawai['nama_pegawai']; ?>">
                    </div>
                    <div class="baris-form">
                        <label>NIP</label>
                        <!-- <input name="nip" value="<?= $pegawai['nip']; ?>"> -->
                        <input value="<?= $pegawai['nip']; ?>" readonly>
                        <input type="hidden" name="nip" value="<?= $pegawai['nip']; ?>">
                    </div>

                    <div class="baris-form">
                        <label>Pangkat/Gol. Ruang/TMT</label>
                        <div style="display:flex; gap:10px;">

                            <select name="id_gol">
                                <?php while ($g = mysqli_fetch_assoc($golongan)) { ?>
                                    <option value="<?= $g['id_gol'] ?>"
                                        <?= ($pegawai['id_gol'] == $g['id_gol']) ? 'selected' : '' ?>>
                                        <?= $g['nama_pangkat'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <input type="date" name="tmt_golongan" value="<?= $data_gol['tmt_golongan'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="baris-form">
                        <label>Jabatan Terakhir / TMT</label>

                        <div style="display:flex; gap:10px;">

                            <select name="id_jabatan">

                                <?php while ($g = mysqli_fetch_assoc($jabatan)) { ?>

                                    <option value="<?= $g['id_jabatan'] ?>"
                                        <?php if (isset($data_jabatan['id_jabatan']) && $data_jabatan['id_jabatan'] == $g['id_jabatan']) echo "selected"; ?>>

                                        <?= $g['nama_jabatan'] ?> - <?= $g['jenis_jabatan'] ?>

                                    </option>

                                <?php } ?>

                            </select>

                            <input type="date" name="tmt_jabatan"
                                value="<?= $data_jabatan['tmt_jabatan'] ?? '' ?>">

                        </div>
                    </div>

                    <div class="baris-form">
                        <label>TMT CPNS</label>
                        <input name="tmt_cpns" type="date" value="<?= $pegawai['tmt_cpns']; ?>">
                    </div>

                    <div class="baris-form">
                        <label>TMT PNS</label>
                        <input name="tmt_pns" type="date" value="<?= $pegawai['tmt_pns']; ?>">
                    </div>

                    <div class="baris-form">
                        <label>Tempat & Tanggal Lahir</label>

                        <div style="display:flex; gap:10px;">

                            <select name="tempat_lahir">
                                <option value="">-- Pilih Kabupaten --</option>

                                <?php while ($row = mysqli_fetch_assoc($kabupaten)) { ?>
                                    <option value="<?= $row['nama_kabupaten']; ?>"
                                        <?= ($row['nama_kabupaten'] == $pegawai['tempat_lahir']) ? 'selected' : ''; ?>>
                                        <?= $row['nama_kabupaten']; ?>
                                    </option>
                                <?php } ?>

                            </select>

                            <input type="date" name="tanggal_lahir" value="<?= $pegawai['tanggal_lahir']; ?>">

                        </div>
                    </div>

                    <div class="baris-form">
                        <label>Jenis Kelamin</label>
                        <select name="id_jenis_kelamin">
                            <?php while ($k = mysqli_fetch_assoc($jk)) { ?>
                                <option value="<?= $k['id_jenis_kelamin'] ?>"
                                    <?= ($pegawai['id_jenis_kelamin'] == $k['id_jenis_kelamin']) ? 'selected' : '' ?>>
                                    <?= $k['jenis_kelamin'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="baris-form">
                        <label>Agama</label>
                        <select name="id_agama">
                            <?php while ($a = mysqli_fetch_assoc($agama)) { ?>
                                <option value="<?= $a['id_agama'] ?>"
                                    <?= ($pegawai['id_agama'] == $a['id_agama']) ? 'selected' : '' ?>>
                                    <?= $a['agama'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="baris-form">
                        <label>Status Perkawinan</label>
                        <select name="id_status_perkawinan">
                            <?php while ($s = mysqli_fetch_assoc($status)) { ?>
                                <option value="<?= $s['id_status_perkawinan'] ?>"
                                    <?= ($pegawai['id_status_perkawinan'] == $s['id_status_perkawinan']) ? 'selected' : '' ?>>
                                    <?= $s['status_perkawinan'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="baris-form">

                        <label>Unit Kerja</label>
                        <select name="id_unit_kerja">
                            <?php while ($u = mysqli_fetch_assoc($unit)) { ?>
                                <option value="<?= $u['id_unit_kerja'] ?>"
                                    <?= ($pegawai['id_unit_kerja'] == $u['id_unit_kerja']) ? 'selected' : '' ?>>
                                    <?= $u['unit_kerja'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="baris-form">
                        <label>Instansi</label>
                        <input type="text" value="KPU Kota Surabaya" readonly>
                        <input type="hidden" name="instansi" value="KPU Kota Surabaya">
                    </div>

                    <div class="baris-form">

                        <label>Tipe Karyawan</label>

                        <input type="text" name="tipe_karyawan"
                            value="<?= $pegawai['tipe_karyawan'] ?? '' ?>"
                            placeholder="Contoh: PNS, CPNS, PPPK, dll">
                    </div>

                    <div class="baris-form">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp" 
                            value="<?= $pegawai['no_telp'] ?>" 
                            placeholder="Contoh: 08XXXXX"
                            oninput="formatTelp(this)">
                    </div>

                    <div class="baris-form">
                        <label>Alamat Rumah</label>
                        <input name="alamat" value="<?= $pegawai['alamat'] ?? '' ?>">
                    </div>
                    <!-- TOMBOL -->

                    <div class="aksi-form">
                    <button type="button" onclick="klikUbah()" class="tombol-ubah">UBAH</button>
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

    <?php include '../pegawai/Notifikasi_Logout.php'; ?>
    <script src="../assets/script_pg.js"></script>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('preview').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get('status');

if (status === 'berhasil_ubah') {
    openModalAksi("Berhasil", "Data berhasil diubah", "info");
}        
    </script>

    <script src="../assets/core-ui.js"></script>
    <script src="../assets/datamaster.js"></script>
    <script src="../assets/admin-ui.js"></script>

</body>

</html>