<?php
session_start();
include '../config/koneksi.php';

$notif = "";
$reload = false; //

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
$admin = $resultAdmin->fetch_assoc() ?? [
    'username' => 'Administrator',
    'nama_pegawai' => 'Administrator'
];

/* =========================
   QUERY DATA USER + PEGAWAI
========================= */
$query = mysqli_query($conn, "
    SELECT u.*, p.nama_pegawai AS nama
    FROM user u
    LEFT JOIN pegawai p ON u.nip = p.nip
");

/* =========================
   PROSES TAMBAH USER
========================= */
if (isset($_POST['tambah'])) {

    $nip = $_POST['nip'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password_input = $_POST['password'];

    // 🔥 VALIDASI PASSWORD
    if (
        strlen($password_input) < 8 ||
        !preg_match('/[A-Z]/', $password_input) ||
        !preg_match('/[0-9]/', $password_input)
    ) {

        $notif = "Password minimal 8 karakter, harus ada huruf besar & angka";
    } else {

        $password = password_hash($password_input, PASSWORD_DEFAULT);

        // validasi username unik
        $cek = mysqli_query($conn, "SELECT * FROM user WHERE username='$username'");

        if (mysqli_num_rows($cek) > 0) {

            $notif = "Username sudah digunakan";

        } else {

            mysqli_query($conn, "
                INSERT INTO user (nip, username, email, password, role, is_active)
                VALUES ('$nip','$username','$email','$password','$role',1)
            ");

            $notif = "User berhasil ditambahkan";
$reload = true; 
        }
    }
}

/* =========================
   PROSES RESET PASSWORD
========================= */
if (isset($_POST['reset_password'])) {

    $id = $_POST['user_id'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // VALIDASI
    if ($password !== $confirm) {

        $notif = "Konfirmasi password tidak sama!";    
    } elseif (
        strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[0-9]/', $password)
    ) {

        $notif = "Password minimal 8 karakter, harus ada huruf besar & angka";
    } else {

        // HASH PASSWORD
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // UPDATE
        $update = mysqli_query($conn, "
            UPDATE user 
            SET password='$hash' 
            WHERE id_user='$id'
        ");

        if ($update) {
            header("Location: Admin_Manajemen_Akun.php?success=reset");
            exit;
        } else {
            $notif = "Gagal reset password!";
        }
    }
}


// 
if($notif == "" && isset($_GET['success'])){
    if($_GET['success'] === 'reset'){
        $notif = "Password berhasil direset!";
    } elseif($_GET['success'] === 'nonaktif'){
        $notif = "Akun berhasil dinonaktifkan!";
    } elseif($_GET['success'] === 'aktif'){
        $notif = "Akun berhasil diaktifkan!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Manajemen Akun</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/datamaster.css" />
    <link rel="stylesheet" href="../assets/manajemen_akun.css" />
<style>
    .password-wrapper {
    position: relative;
}

.password-wrapper input {
    width: 100%;
    padding-right: 40px;
}

.toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
}


</style>
</head>

<body class="role-admin">

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
        <a href="Admin_Manajemen_Akun.php" class="item-menu aktif">
            Manajemen Akun
        </a>

        <hr class="garis-menu">
    </aside>

    <!-- KONTEN -->
    <main class="konten">

            <div class="dropdown-menu" id="dropdownMenu">
                <a href="Admin_Profil_Data_Pegawai.php">Beranda</a>
                <a href="#" onclick="openLogoutModal()">Keluar</a>
            </div>
        </div>

        <h2>Manajemen Akun</h2>

        <div class="table-top">
            <div>
                Show
                <select>
                    <option>10</option>
                    <option>25</option>
                </select>
                Entries
            </div>

            <button class="tombol-tambah" onclick="openAddModal()">+ Add New</button>
        </div>

        <div class="table-header">
            <span>Manajemen Akun</span>
        </div>

        <table class="table-master">
            <thead>
                <tr>
                    <th><input type="checkbox"></th>
                    <th>Username</th>
                    <th>Nama Pegawai</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Terakhir Login</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($d = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td><input type="checkbox"></td> <!-- WAJIB ADA -->
                        <td><?= $d['username']; ?></td>
                        <td><?= $d['nama']; ?></td>
                        <td><?= $d['email']; ?></td>
                        <td><?= $d['role']; ?></td>

                        <td class="<?= $d['is_active'] ? 'status-aktif' : 'status-nonaktif'; ?>">
                            <?= $d['is_active'] ? 'Aktif' : 'Nonaktif'; ?>
                        </td>

                        <td><?= $d['last_login'] ?? '-'; ?></td>

                        <td>
                            <div class="aksi-btn">

                                <button onclick="openResetModal('<?= $d['id_user']; ?>','<?= $d['username']; ?>','<?= $d['nama']; ?>')" class="btn-reset">⟳</button>

                                <button 
                                    onclick="confirmNonaktif('<?= $d['id_user']; ?>','<?= $d['username']; ?>')" 
                                    class="btn-nonaktif">⛔</button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- =========================
     MODAL ADD USER
========================= -->
        <div id="modalAdd" class="modal">
            <div class="modal-content">

                <h3 class="modal-title">Tambah Akun</h3>

                <form method="POST" class="form-akun">

                    <!-- PEGAWAI -->
                    <div class="form-group">
                        <label>Pilih Pegawai</label>
                        <select name="nip" required>
                            <option value="">-- Pilih Pegawai --</option>
                            <?php
                            $pegawai = mysqli_query($conn, "SELECT * FROM pegawai");
                            while ($p = mysqli_fetch_assoc($pegawai)) {
                                echo "<option value='" . $p['nip'] . "'>" . $p['nip'] . " - " . $p['nama_pegawai'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- USERNAME -->
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Masukkan username" required>
                    </div>

                    <!-- EMAIL -->
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Masukkan email">
                    </div>

                    <!-- ROLE -->
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin">Admin</option>
                            <option value="pegawai">Pegawai</option>
                        </select>
                    </div>

                    <!-- PASSWORD -->
                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="passwordInput" placeholder="Masukkan password" required>
                            <span class="toggle-password" onclick="togglePassword()">👁</span>
                        </div>
                        <small style="color:gray;">
                            Minimal 8 karakter, harus ada huruf besar dan angka
                        </small>
                    </div>

                    <!-- BUTTON -->
                    <div class="form-actions">
                        <button type="button" class="btn-batal" onclick="closeAddModal()">Batal</button>
                        <button type="submit" name="tambah" class="btn-simpan">Simpan</button>
                    </div>

                </form>

            </div>
        </div>

<!-- =========================
     MODAL RESET PASSWORD
========================= -->
        <div id="modalReset" class="modal">
            <div class="modal-content">

                <h3 class="modal-title">Reset Password</h3>

                <!-- INFO USER -->
                <p><strong>Username:</strong> <span id="resetUsername"></span></p>
                <p><strong>Nama:</strong> <span id="resetNama"></span></p>

                <form method="POST" class="form-akun">
                    <input type="hidden" name="user_id" id="resetUserId">

                    <!-- PASSWORD BARU -->
                    <div class="form-group">
                        <label>Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="resetPassword">
                            <span class="toggle-password" onclick="toggleResetPassword('resetPassword', this)">👁</span>
                        </div>
                        <small style="color:gray;">
                            Minimal 8 karakter, harus ada huruf besar dan angka
                        </small>
                    </div>

                    <!-- KONFIRMASI -->
                    <div class="form-group">
                        <label>Konfirmasi Password</label>
                        <div class="password-wrapper">
                            <input type="password" name="confirm_password" id="resetConfirm">
                            <span class="toggle-password" onclick="toggleResetPassword('resetConfirm', this)">👁</span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-batal" onclick="closeResetModal()">Batal</button>
                        <button type="submit" name="reset_password" class="btn-simpan">Simpan</button>
                    </div>
                </form>

            </div>
        </div>

    </main>

    <!-- MODAL NOTIFIKASI -->
<div id="modalNotif" class="modal">
  <div class="modal-content" style="text-align:center;">
    
    <h3 id="judulNotif">Notifikasi</h3>
    <p id="isiNotif">Pesan notifikasi</p>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
      <button onclick="closeNotif()" class="tombol-batal">
        OK
      </button>
    </div>

  </div>
</div>

<!--  -->
<div id="modalNonaktif" class="modal">
  <div class="modal-content">
    <h3>Konfirmasi</h3>
    <p id="textNonaktif"></p>

    <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
      <button onclick="closeNonaktif()" class="tombol-batal">Batal</button>
      <button onclick="lanjutNonaktif()" class="tombol-keluar">Ya</button>
    </div>
  </div>
</div>
<script>
var notifMessage = "<?= $notif ?>";
var reloadAfterNotif = <?= $reload ? 'true' : 'false' ?>;


</script>
    <?php include '../pegawai/Notifikasi_Logout.php'; ?>

    <script>
        function openAddModal() {
            document.getElementById("modalAdd").style.display = "block";
        }

        function closeAddModal() {
            document.getElementById("modalAdd").style.display = "none";
        }
        // password toggle
        function togglePassword() {
            const input = document.getElementById("passwordInput");
            input.type = input.type === "password" ? "text" : "password";
        }

        // password reset
        function openResetModal(id, username, nama){
            document.getElementById("modalReset").style.display = "block";
            document.getElementById("resetUserId").value = id;

            // tampilkan info user
            document.getElementById("resetUsername").innerText = username;
            document.getElementById("resetNama").innerText = nama;
        }

        function closeResetModal(){
            document.getElementById("modalReset").style.display = "none";
        }

        // 👁 toggle password
        function toggleResetPassword(inputId, el){
            const input = document.getElementById(inputId);

            if(input.type === "password"){
                input.type = "text";
                el.textContent = "🙈";
            } else {
                input.type = "password";
                el.textContent = "👁";
            }
        }
        //btn non aktif
        let selectedId = "";

function confirmNonaktif(id, username){
    selectedId = id;
    document.getElementById("textNonaktif").innerText =
        "Apakah Anda akan menonaktifkan akun (" + username + ")?";
    document.getElementById("modalNonaktif").style.display = "flex";
}

function closeNonaktif(){
    document.getElementById("modalNonaktif").style.display = "none";
}

function lanjutNonaktif(){
    window.location.href = "nonaktifkan_akun.php?id=" + selectedId;
}


        // Notif
        function showNotif(pesan, judul = "Notifikasi") {
    document.getElementById("judulNotif").innerText = judul;
    document.getElementById("isiNotif").innerText = pesan;
    document.getElementById("modalNotif").style.display = "flex";
}

function closeNotif() {
    document.getElementById("modalNotif").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {
    if (notifMessage && notifMessage.trim() !== "") {
        showNotif(notifMessage);
    }

    if (reloadAfterNotif) {
        setTimeout(() => location.reload(), 1500);
    }
});
</script>
    <script src="../assets/core-ui.js"></script>
    <script src="../assets/datamaster.js"></script>
    <script src="../assets/admin-ui.js"></script>
</body>

</html>