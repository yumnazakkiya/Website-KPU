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
    LEFT JOIN pegawai p ON u.nip = p.nip
    WHERE u.nip = ?
");
$stmtAdmin->bind_param("s", $nip_session);
$stmtAdmin->execute();
$resultAdmin = $stmtAdmin->get_result();
$data = $resultAdmin->fetch_assoc();

// Fallback jika data tidak ditemukan
if (!$data) {
    $data = [
        'username' => 'Administrator',
        'nama_pegawai' => 'Administrator'
    ];
}

if (isset($_POST['tambah'])) {

    // =========================
    // AMBIL DATA DARI FORM
    // =========================
    $nip_baru = $_POST['nip'] ?? '';
    $nama = $_POST['nama_pegawai'];
    $tempat_lahir = $_POST['tempat_lahir'] ?? '';
    $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $no_telp = $_POST['no_telp'] ?? '';
    $tmt_cpns = $_POST['tmt_cpns'] ?? null;
    $tmt_pns = $_POST['tmt_pns'] ?? null;
    $tipe_karyawan = $_POST['tipe_karyawan'] ?? '';
    $instansi = "KPU Kota Surabaya";

    $id_agama = !empty($_POST['id_agama']) ? (int)$_POST['id_agama'] : null;
    $id_unit_kerja = !empty($_POST['id_unit_kerja']) ? (int)$_POST['id_unit_kerja'] : null;
    $id_gol = !empty($_POST['id_gol']) ? (int)$_POST['id_gol'] : null;
    $id_jenis_kelamin = !empty($_POST['id_jenis_kelamin']) ? (int)$_POST['id_jenis_kelamin'] : null;
    $id_status_perkawinan = !empty($_POST['id_status_perkawinan']) ? (int)$_POST['id_status_perkawinan'] : null;

    // Riwayat
    $tmt_golongan = $_POST['tmt_golongan'] ?? null;
    $id_jabatan = $_POST['id_jabatan'] ?? null;
    $tmt_jabatan = $_POST['tmt_jabatan'] ?? null;
    $tmt_akhir = $_POST['tmt_akhir'] ?? null;

    $id_jenjang_pend = $_POST['id_jenjang_pend'] ?? null;
    $institusi = $_POST['institusi'] ?? '';
    $tahun_lulus = $_POST['tahun_lulus'] ?? null;

    $id_jenis_diklat = $_POST['id_jenis_diklat'] ?? null;
    $nama_diklat = $_POST['nama_diklat'] ?? '';
    $tahun_diklat = $_POST['tahun_diklat'] ?? null;

    $nama_penghargaan = $_POST['nama_penghargaan'] ?? '';
    $tahun_penghargaan = $_POST['tahun_penghargaan'] ?? null;

    $nama_keluarga = $_POST['nama_keluarga'] ?? '';
    $id_hub_kel = $_POST['id_hub_kel'] ?? null;
    $no_telp_keluarga = $_POST['no_telp_keluarga'] ?? '';
    $alamat_keluarga = $_POST['alamat_keluarga'] ?? '';

    $tahun_skp = $_POST['tahun_skp'] ?? null;
    $rerata_nilai = $_POST['rerata_nilai'] ?? null;
    $id_predikat_skp = $_POST['id_predikat_skp'] ?? null;

    // =========================
    // VALIDASI DATA WAJIB
    // =========================
    if (empty($nip_baru) || empty($nama)) {
        echo "<script>alert('Nama dan NIP wajib diisi');</script>";
        exit;
    }

    // =========================
    // CEK DUPLIKASI NIP
    // =========================
    $stmtCek = $conn->prepare("SELECT nip FROM pegawai WHERE nip = ?");
    $stmtCek->bind_param("s", $nip_baru);
    $stmtCek->execute();
    $stmtCek->store_result();

    if ($stmtCek->num_rows > 0) {
        echo "<script>alert('NIP sudah terdaftar');</script>";
        exit;
    }

    // =====================================================
// =========================
// UPLOAD FOTO
// =========================
$fotoPath = "uploads/default.png";

if (!empty($_FILES['foto']['name'])) {

    if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>
            alert('Terjadi kesalahan saat upload file.');
            window.history.back();
        </script>";
        exit;
    }

    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];
    $size = $_FILES['foto']['size'];

    $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

    // VALIDASI EXT
    if (!in_array($ext, ['jpg', 'jpeg'])) {
        echo "<script>
            alert('Foto harus JPG/JPEG');
            window.history.back();
        </script>";
        exit;
    }

    // VALIDASI MIME
    $mime = mime_content_type($tmp);
    if (!in_array($mime, ['image/jpeg', 'image/pjpeg', 'image/jpg'])) {
        echo "<script>
            alert('File harus JPG/JPEG');
            window.history.back();
        </script>";
        exit;
    }

    // VALIDASI SIZE
    if ($size > 2000000) {
        echo "<script>
            alert('Ukuran maksimal 2MB');
            window.history.back();
        </script>";
        exit;
    }

    // Pastikan folder uploads ada
    $uploadDir = __DIR__ . "/../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $namaBaru = uniqid('foto_', true) . "." . $ext;
    $path = $uploadDir . $namaBaru;

    if (move_uploaded_file($tmp, $path)) {
        $fotoPath = "uploads/" . $namaBaru;
    } else {
        echo "<script>
            alert('Upload gagal. Periksa permission folder.');
            window.history.back();
        </script>";
        exit;
    }
}
    // =====================================================

    // =========================
    // MULAI TRANSAKSI
    // =========================
    mysqli_begin_transaction($conn);

    try {
        // =========================
        // INSERT KE TABEL PEGAWAI
        // =========================
        $stmtPegawai = $conn->prepare("
            INSERT INTO pegawai (
                nip, nama_pegawai, tempat_lahir, tanggal_lahir,
                alamat, no_telp, tmt_cpns, tmt_pns,
                tipe_karyawan, instansi,
                id_agama, id_unit_kerja, id_gol,
                id_jenis_kelamin, id_status_perkawinan, foto
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmtPegawai->bind_param(
            "ssssssssssssssss",
            $nip_baru, $nama, $tempat_lahir, $tanggal_lahir,
            $alamat, $no_telp, $tmt_cpns, $tmt_pns,
            $tipe_karyawan, $instansi,
            $id_agama, $id_unit_kerja, $id_gol,
            $id_jenis_kelamin, $id_status_perkawinan, $fotoPath
        );
        $stmtPegawai->execute();

        // =========================
        // RIWAYAT GOLONGAN
        // =========================
        if (!empty($id_gol) && !empty($tmt_golongan)) {
            $stmt = $conn->prepare("
                INSERT INTO riwayat_golongan (nip, id_gol, tmt_golongan)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sis", $nip_baru, $id_gol, $tmt_golongan);
            $stmt->execute();
        }

        // =========================
        // RIWAYAT JABATAN
        // =========================
        if (!empty($id_jabatan) && !empty($tmt_jabatan)) {
            $stmt = $conn->prepare("
                INSERT INTO riwayat_jabatan
                (nip, id_jabatan, id_unit_kerja, tmt_jabatan, tmt_akhir)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("siiss",
                $nip_baru, $id_jabatan, $id_unit_kerja,
                $tmt_jabatan, $tmt_akhir
            );
            $stmt->execute();
        }

        // =========================
        // RIWAYAT PENDIDIKAN
        // =========================
        if (!empty($id_jenjang_pend)) {
            $stmt = $conn->prepare("
                INSERT INTO riwayat_pendidikan
                (nip, id_jenjang_pend, institusi, tahun_lulus)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("sisi",
                $nip_baru, $id_jenjang_pend,
                $institusi, $tahun_lulus
            );
            $stmt->execute();
        }

        // =========================
        // RIWAYAT DIKLAT
        // =========================
        if (!empty($id_jenis_diklat)) {
            $stmt = $conn->prepare("
                INSERT INTO riwayat_diklat
                (nip, id_jenis_diklat, nama_diklat, tahun)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("sisi",
                $nip_baru, $id_jenis_diklat,
                $nama_diklat, $tahun_diklat
            );
            $stmt->execute();
        }

        // =========================
        // RIWAYAT KEHORMATAN
        // =========================
        if (!empty($nama_penghargaan)) {
            $stmt = $conn->prepare("
                INSERT INTO riwayat_kehormatan
                (nip, nama_penghargaan, tahun)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("ssi",
                $nip_baru, $nama_penghargaan,
                $tahun_penghargaan
            );
            $stmt->execute();
        }

        // =========================
        // RIWAYAT KELUARGA
        // =========================
        if (!empty($nama_keluarga)) {
            $stmt = $conn->prepare("
                INSERT INTO riwayat_keluarga
                (nip, nama, id_hub_kel, no_telp, alamat)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssiss",
                $nip_baru, $nama_keluarga,
                $id_hub_kel, $no_telp_keluarga,
                $alamat_keluarga
            );
            $stmt->execute();
        }

        // =========================
        // RIWAYAT SKP
        // =========================
        if (!empty($tahun_skp) && !empty($rerata_nilai)) {
            $stmt = $conn->prepare("
                INSERT INTO riwayat_skp
                (nip, id_predikat_skp, rerata_nilai, tahun)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("sidi",
                $nip_baru, $id_predikat_skp,
                $rerata_nilai, $tahun_skp
            );
            $stmt->execute();
        }

        // =========================
        // COMMIT TRANSAKSI
        // =========================
        mysqli_commit($conn);

        header("Location: Admin_Tambah_Data.php?status=berhasil_tambah");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "Terjadi kesalahan: " . $e->getMessage();
    }
}

// =========================
// DATAMASTER (SAMAKAN DENGAN EDIT)
// =========================
$jk = mysqli_query($conn, "SELECT * FROM master_jenis_kelamin");
$agama = mysqli_query($conn, "SELECT * FROM master_agama");
$status = mysqli_query($conn, "SELECT * FROM master_status_perkawinan");
$unit = mysqli_query($conn, "SELECT * FROM master_divisi");
$golongan = mysqli_query($conn, "SELECT * FROM master_golongan");
$jabatan = mysqli_query($conn, "SELECT * FROM master_jabatan");
$kabupaten = mysqli_query($conn, "SELECT * FROM master_kabupaten ORDER BY nama_kabupaten ASC");
$pend = mysqli_query($conn, "SELECT * FROM master_jenjang_pend");
$diklat = mysqli_query($conn, "SELECT * FROM master_diklat");
$hub = mysqli_query($conn, "SELECT * FROM master_hub_kel");
$predikat = mysqli_query($conn, "SELECT * FROM master_predikat_skp");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manage Jenis Kelamin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/fotoadmin.css">    
    <style>



    </style>
</head>

<body class="role-admin">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <span>LOGO</span>
            <button class="tombol-menu" id="tombolMenu">✕</button>
        </div>

        <hr class="garis-menu" />

        <a href="Admin_Profil_Data_Pegawai.php" class="item-menu">
            Profil Data Pegawai
        </a>

        <hr class="garis-menu" />

        <a href="Admin_Tambah_Data.php" class="item-menu aktif">
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

        <h2>Tambah Data Pegawai</h2>
        <p style="margin-top:-10px; margin-bottom:30px;">Identitas</p>

        <!-- PROFIL USER -->
        <!-- dropdown-->
        <div class="user-profile" id="userProfile">
            <div class="user-info">
                <div class="user-icon">👤</div>
                <div class="user-text">
                    <div class="user-name">
                        <?= htmlspecialchars($data['nama_pegawai']); ?>
                    </div>
                </div>
            </div>

            <div class="dropdown-menu" id="dropdownMenu">
                <a href="Admin_Profil_Data_Pegawai.php">Beranda</a>
                <a href="#" onclick="openLogoutModal()">Keluar</a>
            </div>
        </div>

        <!-- FORM TAMBAH DATA -->
        <form method="POST" enctype="multipart/form-data" id="formUpload">
            <div class="pembungkus-form">

                <!-- FOTO -->
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

                <!-- FORM UTAMA -->
                <div class="form">

                    <div class="baris-form">
                        <label>Nama</label>
                        <input type="text" name="nama_pegawai">
                    </div>

                    <div class="baris-form">
                        <label>NIP</label>
                        <input type="text" name="nip">
                    </div>



                    <div class="baris-form">
                        <label>TMT CPNS</label>
                        <input type="date" name="tmt_cpns">
                    </div>

                    <div class="baris-form">
                        <label>TMT PNS</label>
                        <input type="date" name="tmt_pns">
                    </div>

                    <div class="baris-form">
                        <label>Tempat & Tanggal Lahir</label>

                        <div style="display:flex; gap:10px;">

                            <select name="tempat_lahir">
                                <option value="">-- Pilih Kabupaten --</option>

                                <?php while ($row = mysqli_fetch_assoc($kabupaten)) { ?>
                                    <option value="<?= $row['nama_kabupaten']; ?>">
                                        <?= $row['nama_kabupaten']; ?>
                                    </option>
                                <?php } ?>

                            </select>

                            <input type="date" name="tanggal_lahir" value="">

                        </div>
                    </div>

                    <div class="baris-form">
                        <label>Jenis Kelamin</label>
                        <!-- <select>
                    <option>-- Pilih --</option>
                </select> -->
                        <select name="id_jenis_kelamin">

                            <option value="">-- Pilih Jenis Kelamin --</option>

                            <?php while ($j = mysqli_fetch_assoc($jk)) { ?>
                                <option value="<?= $j['id_jenis_kelamin']; ?>">
                                    <?= $j['jenis_kelamin']; ?>
                                </option>
                            <?php } ?>

                        </select>
                    </div>

                    <div class="baris-form">
                        <label>Agama</label>
                        <!-- <select>
                    <option>-- Pilih --</option>
                </select> -->
                        <select name="id_agama">
                            <option value="">-- Pilih Agama --</option>

                            <?php while ($a = mysqli_fetch_assoc($agama)) { ?>
                                <option value="<?= $a['id_agama']; ?>">
                                    <?= $a['agama']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="baris-form">
                        <label>Status Perkawinan</label>
                        <!-- <select>
                    <option>-- Pilih --</option>
                </select> -->
                        <select name="id_status_perkawinan">

                            <option value="">-- Pilih Status --</option>

                            <?php
                            while ($s = mysqli_fetch_assoc($status)) {
                            ?>

                                <option value="<?= $s['id_status_perkawinan']; ?>">
                                    <?= $s['status_perkawinan']; ?>
                                </option>

                            <?php } ?>

                        </select>
                    </div>

                    <div class="baris-form">
                        <label>Unit Kerja</label>

                        <select name="id_unit_kerja">
                            <option value="">-- Pilih Unit --</option>

                            <?php
                            while ($u = mysqli_fetch_assoc($unit)) {
                            ?>
                                <option value="<?= $u['id_unit_kerja']; ?>">
                                    <?= $u['unit_kerja']; ?>
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
                        <input type="text" name="tipe_karyawan" placeholder="Contoh: PNS, CPNS, PPPK, dll">
                    </div>

                    <div class="baris-form">
                        <label>No Telepon</label>
                        <input type="text" name="no_telp" 
                        placeholder="Contoh: 08XXXXX"
                        oninput="formatTelp(this)">
                    </div>

                    <div class="baris-form">
                        <label>Alamat Rumah</label>
                        <input type="text" name="alamat">
                    </div>

                </div>
            </div>

            <!-- ============================= -->
            <!-- RIWAYAT GOLONGAN -->
            <!-- ============================= -->

            <h3 style="margin-top:60px;">Riwayat Golongan</h3>

            <div class="form">
                <div class="baris-form">
                    <label>Golongan Pangkat</label>
                    <!-- <select>
                <option>-- Pilih --</option>
            </select> -->
                    <select name="id_gol">
                        <option value="">-- Pilih Golongan --</option>

                        <?php while ($g = mysqli_fetch_assoc($golongan)) { ?>
                            <option value="<?= $g['id_gol']; ?>">
                                <?= $g['nama_pangkat']; ?> (<?= $g['kode_gol']; ?>)
                            </option>
                        <?php } ?>

                    </select>
                </div>

                <div class="baris-form">
                    <label>TMT</label>
                    <input type="date" name="tmt_golongan">
                </div>
            </div>

            <!-- RIWAYAT JABATAN -->

            <h3 style="margin-top:40px;">Riwayat Jabatan</h3>

            <div class="form">

                <div class="baris-form">

                    <label>Jabatan</label>

                    <select name="id_jabatan">

                        <option value="">-- Pilih Jabatan --</option>

                        <?php
                        while ($j = mysqli_fetch_assoc($jabatan)) {
                        ?>

                            <option value="<?= $j['id_jabatan']; ?>">
                                <?= $j['nama_jabatan']; ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="baris-form">

                    <label>TMT Awal</label>

                    <input type="date" name="tmt_jabatan">

                </div>

                <div class="baris-form">

                    <label>TMT Akhir</label>

                    <input type="date" name="tmt_akhir">

                </div>

            </div>

            <!-- RIWAYAT PENDIDIKAN -->
            <h3 style="margin-top:40px;">Riwayat Pendidikan</h3>

            <div class="form">

                <div class="baris-form">

                    <label>Jenjang Pendidikan</label>

                    <select name="id_jenjang_pend">

                        <option value="">-- Pilih Jenjang --</option>

                        <?php
                        while ($p = mysqli_fetch_assoc($pend)) {
                        ?>

                            <option value="<?= $p['id_jenjang_pend']; ?>">
                                <?= $p['jenjang_pend']; ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="baris-form">
                    <label>Institusi</label>
                    <input type="text" name="institusi">
                </div>

                <div class="baris-form">

                    <label>TMT</label>

                    <input type="number" name="tahun_lulus">

                </div>

            </div>

            <!-- RIWAYAT DIKLAT -->
            <h3 style="margin-top:40px;">Riwayat Diklat</h3>

            <div class="form">

                <div class="baris-form">
                    <label>Jenis Diklat</label>

                    <select name="id_jenis_diklat">

                        <option value="">-- Pilih Diklat --</option>

                        <?php
                        while ($d = mysqli_fetch_assoc($diklat)) {
                        ?>

                            <option value="<?= $d['id_jenis_diklat']; ?>">
                                <?= $d['jenis_diklat']; ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="baris-form">
                    <label>Nama Diklat</label>
                    <input type="text" name="nama_diklat">
                </div>

                <div class="baris-form">
                    <label>Tahun</label>
                    <input type="number" name="tahun_diklat">
                </div>

            </div>

            <!-- RIWAYAT KELUARGA -->
            <h3 style="margin-top:40px;">Riwayat Keterangan Keluarga</h3>

            <div class="form">

                <div class="baris-form">
                    <label>Nama</label>
                    <input type="text" name="nama_keluarga" data-optional>
                </div>

                <div class="baris-form">
                    <label>Hubungan Keluarga</label>

                    <select name="id_hub_kel" data-optional>

                        <option value="">-- Pilih Hubungan --</option>

                        <?php
                        while ($h = mysqli_fetch_assoc($hub)) {
                        ?>

                            <option value="<?= $h['id_hub_kel']; ?>">
                                <?= $h['hub_kel']; ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

                <div class="baris-form">
                    <label>No. Telepon</label>
                    <input type="text" name="no_telp_keluarga"
                    placeholder="Contoh: 08XXXXX"
                    oninput="formatTelp(this)" data-optional>
                </div>

                <div class="baris-form">
                    <label>Alamat</label>
                    <input type="text" name="alamat_keluarga" data-optional>
                </div>

            </div>

            <!-- RIWAYAT TANDA JASA -->
            <h3 style="margin-top:40px;">Riwayat Tanda Jasa/Kehormatan</h3>

            <div class="form">

                <div class="baris-form">
                    <label>Nama Penghargaan</label>
                    <input type="text" name="nama_penghargaan" placeholder="Masukkan nama penghargaan">
                </div>

                <div class="baris-form">
                    <label>Tahun</label>
                    <input type="number" name="tahun_penghargaan" placeholder="Contoh: 2022">
                </div>

            </div>

            <!-- RIWAYAT SKP -->
            <h3 style="margin-top:40px;">Riwayat SKP</h3>

            <div class="form">

                <div class="baris-form">
                    <label>Tahun</label>
                    <input type="number" name="tahun_skp">
                </div>

                <div class="baris-form">
                    <label>Rata-Rata</label>
                    <input type="number" step="0.01" name="rerata_nilai">
                </div>

                <div class="baris-form">
                    <label>Predikat</label>

                    <select name="id_predikat_skp">

                        <option value="">-- Pilih Predikat --</option>

                        <?php
                        while ($p = mysqli_fetch_assoc($predikat)) {
                        ?>

                            <option value="<?= $p['id_predikat_skp']; ?>">
                                <?= $p['predikat_skp']; ?>
                            </option>

                        <?php } ?>

                    </select>

                </div>

            </div>

            <!-- TOMBOL TAMBAH -->
            <div class="aksi-form" style="margin-top:50px;">
            <button type="button" onclick="klikTambah()" class="tombol-tambah">TAMBAH</button>
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
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('preview').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        // notif berhasil
        const urlParams = new URLSearchParams(window.location.search);
const status = urlParams.get('status');

if (status === 'berhasil_tambah') {
    openModalAksi("Berhasil", "Data berhasil ditambahkan", "info");
}
    </script>
      <script src=" ../assets/core-ui.js"></script>
    <script src=" ../assets/datamaster.js"></script>
    <script src=" ../assets/admin-ui.js"></script>
    <script src="../assets/script_pg.js"></script>



</body>

</html>