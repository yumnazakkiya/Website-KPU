<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['nip'])){
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

/* =====================
   TAMBAH DATA
===================== */
if (isset($_POST['tambah'])) {

    $nama_jabatan  = trim($_POST['nama_jabatan'] ?? '');
    $jenis_jabatan = trim($_POST['jenis_jabatan'] ?? '');

    if ($nama_jabatan !== "" && $jenis_jabatan !== "") {

        $nama_jabatan  = mysqli_real_escape_string($conn, $nama_jabatan);
        $jenis_jabatan = mysqli_real_escape_string($conn, $jenis_jabatan);

        mysqli_query($conn,
            "INSERT INTO master_jabatan 
             (nama_jabatan, jenis_jabatan)
             VALUES ('$nama_jabatan', '$jenis_jabatan')"
        );

        header("Location: Admin_DM_Jabatan.php");
        exit;
    }
}

/* =====================
   HAPUS DATA
===================== */

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];

    mysqli_query($conn, "DELETE FROM master_jabatan WHERE id_jabatan = $id");

    header("Location: Admin_DM_Jabatan.php");
    exit;
}

/* =====================
   UPDATE DATA
===================== */
if (isset($_POST['update'])) {

    $id = (int) $_POST['id_edit'];
    $nama_jabatan = trim($_POST['nama_jabatan_edit'] ?? '');
    $jenis_jabatan = trim($_POST['jenis_jabatan_edit'] ?? '');

    if ($id > 0 && $nama_jabatan !== '') {

        $nama_jabatan = mysqli_real_escape_string($conn, $nama_jabatan);
        $jenis_jabatan = mysqli_real_escape_string($conn, $jenis_jabatan);

        mysqli_query($conn,
            "UPDATE master_jabatan
            SET nama_jabatan = '$nama_jabatan',
                jenis_jabatan = '$jenis_jabatan'
            WHERE id_jabatan = $id"
        );
    }

    header("Location: Admin_DM_Jabatan.php");
    exit;
}

/* =====================
   PAGINATION AMBIL DATA
===================== */

$limit = 10; // jumlah data per halaman

// Halaman aktif
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// Hitung total data
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM master_jabatan");
$totalData = mysqli_fetch_assoc($totalQuery)['total'];

$totalPages = ceil($totalData / $limit);

/* Ambil data sesuai halaman */
$query = mysqli_query($conn,
    "SELECT id_jabatan, nama_jabatan, jenis_jabatan
     FROM master_jabatan
     ORDER BY id_jabatan
     LIMIT $limit OFFSET $offset"
);

if (!$query) {
    die("Query error: " . mysqli_error($conn));
}

/* =====================
   BULK DELETE
===================== */

if (isset($_POST['id_delete'])) {

    $ids = $_POST['id_delete'];

    if (!empty($ids)) {

        // Amankan jadi integer
        $ids = array_map('intval', $ids);
        $idList = implode(',', $ids);

        mysqli_query($conn,
            "DELETE FROM master_jabatan
             WHERE id_jabatan IN ($idList)"
        );
    }

    header("Location: Admin_DM_Jabatan.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manage Jabatan</title>
    <link rel="stylesheet" href="../assets/data_master.css">
    <link rel="stylesheet" href="../assets/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="role-admin" data-jenis="jabatan">

<!-- TOMBOL KELUAR -->

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">

    <!-- LOGO -->
    <div class="logo">
        <span>LOGO</span>
        <button class="tombol-menu" id="tombolMenu">✕</button>
    </div>

    <hr class="garis-menu">

    <!-- MENU UTAMA -->
    <a href="Admin_Profil_Data_Pegawai.php" class="item-menu">
        Profil Data Pegawai
    </a>

    <hr class="garis-menu">

    <a href="Admin_Tambah_Data.php" class="item-menu">
        Tambah Data Pegawai Baru
    </a>

    <hr class="garis-menu aktif">

    <a href="Admin_Pengaturan_Akun.php" class="item-menu">
        Pengaturan Akun
    </a>

    <hr class="garis-menu">

    <!-- DATA MASTER -->
    <div class="item-menu" id="menuDataMaster">
        <span>Data Master</span>
        <span class="panah-menu" id="panahDataMaster">▼</span>
    </div>

    <hr class="garis-menu">

    <!-- SUBMENU DATA MASTER -->
    <div class="submenu aktif" id="submenuDataMaster">
        <a href="Admin_DM_Gender.php" class="item-submenu">Jenis Kelamin</a>
        <a href="Admin_DM_Agama.php" class="item-submenu">Agama</a>
        <a href="Admin_DM_StatusPerkawinan.php" class="item-submenu">Status Perkawinan</a>
        <a href="Admin_DM_JenjangPendidikan.php" class="item-submenu">Jenjang Pendidikan</a>
        <a href="Admin_DM_HubunganKeluarga.php" class="item-submenu">Hubungan Keluarga</a>
        <a href="Admin_DM_Golongan.php" class="item-submenu">Golongan</a>
        <a href="Admin_DM_Jabatan.php" class="item-submenu aktif">Jabatan</a>
        <a href="Admin_DM_UnitKerja.php" class="item-submenu">Unit Kerja / Divisi</a>
        <a href="Admin_DM_JenisDiklat.php" class="item-submenu">Jenis Diklat</a>
        <a href="Admin_DM_PredikatSKP.php" class="item-submenu">Predikat SKP</a>
        <a href="Admin_DM_KabupatenKota.php" class="item-submenu">Kabupaten/Kota</a>
    </div>


    <a href="Admin_Manajemen_Akun.php" class="item-menu">
      Manajemen Akun
    </a>

    <hr class="garis-menu">
</aside>

<!-- KONTEN -->
<main class="konten">

<h2>Data Master</h2>
<div class="user-profile" id="userProfile">
    <div class="user-info">
        <div class="user-icon">👤</div>
        <div class="user-text">
            <div class="user-name">
                <?= htmlspecialchars($admin['nama_pegawai']); ?>
            </div>
            <!-- Opsional: tampilkan username -->
            <!-- <div class="user-role">
                <?= htmlspecialchars($admin['username']); ?>
            </div> -->
        </div>
    </div>

    <div class="dropdown-menu" id="dropdownMenu">
        <a href="Admin_Profil_Data_Pegawai.php">Beranda</a>
        <a href="#" onclick="openLogoutModal()">Keluar</a>
    </div>
</div>
<!-- BARIS ATAS -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">

    <!-- SHOW ENTRIES -->
    <div>
        Show 
        <select id="entriesSelect" style="height:28px;">
            <option value="5">5</option>
            <option value="10" selected>10</option>
        </select>
        Entries
    </div>
</div>

<!-- HEADER MERAH -->
<div class="header-master">
    <span class="judul-master">Manage Jabatan</span>

    <div class="header-actions">
        <button type="button"
                class="tombol-hapus"
                onclick="openBulkDeleteModal()">
            Hapus Terpilih
        </button>

        <button type="button"
                class="tombol-tambah"
                onclick="openModal()">
            Add New
        </button>
    </div>
</div>

<!-- TABEL -->
<form method="POST" id="formBulkDelete">

<table width="100%" class="tabel-master">
    <thead>
        <tr>
            <th width="5%">
                <input type="checkbox" id="checkAll">
            </th>
            <th>Nama Jabatan</th>
            <th>Jenis Jabatan</th>
            <th width="15%">Aksi</th>
        </tr>
    </thead>

    <tbody>

    <?php 
    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
    ?>

    <tr>
        <td>
            <input type="checkbox" 
                   name="id_delete[]" 
                   value="<?= $row['id_jabatan']; ?>">
        </td>

        <td><?= htmlspecialchars($row['nama_jabatan']); ?></td>

        <td><?= htmlspecialchars($row['jenis_jabatan']); ?></td>

        <td>
            <button type="button"
                    class="tombol-ubah"
                    onclick="openEditModal(<?= $row['id_jabatan']; ?>, 
                    '<?= htmlspecialchars($row['nama_jabatan']); ?>',
                    '<?= htmlspecialchars($row['jenis_jabatan']); ?>'
                    )">
                <i class="fa-solid fa-pen"></i>
            </button>

            <button type="button"
                    class="tombol-hapus"
                    onclick="openDeleteModal(<?= $row['id_jabatan']; ?>)">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>

    <?php 
        }
    } else {
        echo "<tr>
                <td colspan='3' style='text-align:center; padding:20px;'>
                    Belum ada data
                </td>
            </tr>";
    }
    ?>

    </tbody>
</table>

<!-- FOOTER TABLE -->
<?php
$start = $totalData > 0 ? $offset + 1 : 0;
$end = min($offset + $limit, $totalData);
?>

<div class="table-footer">

    <div>
        Showing <?= $start ?>–<?= $end ?> of <?= $totalData ?> entries
    </div>

    <?php if ($totalPages > 1): ?>
        <div id="pagination">

            <!-- Previous -->
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">Previous</a>
            <?php endif; ?>

            <!-- Nomor Halaman -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>"
                   class="<?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <!-- Next -->
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Next</a>
            <?php endif; ?>

        </div>
    <?php endif; ?>

</div>

</main>
<?php include '../pegawai/Notifikasi_Logout.php'; ?>

<!-- MODAL TAMBAH -->
<div id="modalTambah" class="modal">
    <div class="modal-content">

        <h3 style="margin-top:0;">Tambah Jabatan</h3>

        <form method="POST" action="">
            <input type="text" 
                   name="nama_jabatan" 
                   placeholder="Masukkan Nama Jabatan" 
                   required
                   style="width:100%; height:35px; margin-bottom:15px; padding:5px;">

            <input type="text" 
                    name="jenis_jabatan" 
                    placeholder="Masukkan Jenis Jabatan" 
                    required
                    style="width:100%; height:35px; margin-bottom:15px; padding:5px;">

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" onclick="closeModal()" class="tombol-hapus">
                    Batal
                </button>

                <button type="submit" name="tambah" class="tombol-tambah">
                    Simpan
                </button>
            </div>
        </form>

    </div>
</div>

<!-- MODAL HAPUS -->
<div id="modalHapus" class="modal">
    <div class="modal-content">

        <h3>Konfirmasi Hapus</h3>
        <p>Yakin ingin menghapus data ini?</p>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
            <button type="button"
                    onclick="closeDeleteModal()"
                    class="tombol-tambah">
                Batal
            </button>

            <a id="btnHapusFinal"
               href="#"
               class="tombol-hapus"
               style="padding:8px 15px; text-decoration:none;">
                Hapus
            </a>
        </div>
    </div>
</div>

<!-- MODAL BULK-DELETE -->
<div id="modalBulkDelete" class="modal">
    <div class="modal-content">

        <h3>Konfirmasi Hapus</h3>
        <p>Yakin ingin menghapus data yang dipilih?</p>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button type="button"
                    onclick="closeBulkDeleteModal()"
                    class="tombol-tambah">
                Batal
            </button>

            <button type="button"
                    onclick="submitBulkDelete()"
                    class="tombol-hapus">
                Hapus
            </button>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="modalEdit" class="modal">
    <div class="modal-content modal-form">

        <h3>Edit Jabatan</h3>

        <form method="POST">
            <input type="hidden" name="id_edit" id="editId">

            <div class="form-group-vertikal">
                <label>Nama Jabatan</label>
                <input type="text"
                       name="nama_jabatan_edit"
                       id="editNamaJabatan"
                       required>
            </div>

            <div class="form-group-vertikal">
                <label>Jenis Jabatan</label>
                <input type="text"
                       name="jenis_jabatan_edit"
                       id="editJenisJabatan"
                       required>
            </div>

            <div class="modal-actions">
                <button type="button"
                        onclick="closeEditModal()"
                        class="tombol-hapus">
                    Batal
                </button>

                <button type="submit"
                        name="update"
                        class="tombol-tambah">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="toastNotif" class="toast"></div>

<script src="../assets/datamaster.js"></script>
<script src="../assets/master.js"></script>
<script src="../assets/admin-ui.js"></script>
<script src="../assets/core-ui.js"></script>

<script>
//modal edit//
function openEditModal(id, nama, jenis) {
    document.getElementById("editId").value = id;
    document.getElementById("editNamaJabatan").value = nama;
    document.getElementById("editJenisJabatan").value = jenis;
    document.getElementById("modalEdit").style.display = "block";
}

function closeEditModal() {
    document.getElementById("modalEdit").style.display = "none";
}
</script>
</body>
</html>