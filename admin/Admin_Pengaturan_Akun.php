<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['nip'])) {
    header("location: ../auth/Login.php");
    exit;
}

$nip = $_SESSION['nip'];

// AMBIL DATA USER
$stmt = $conn->prepare("
    SELECT u.*, p.nama_pegawai, p.no_telp
    FROM user u
    JOIN pegawai p ON u.nip = p.nip
    WHERE u.nip=?
");
$stmt->bind_param("s", $nip);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// PENTING
$id_user = $user['id_user'];

$pesan = "";

// =======================
// UPDATE DATA
// =======================
if (isset($_POST['update_data'])) {

    // NORMALISASI INPUT
    $username = strtolower(trim($_POST['nama']));
    $email    = strtolower(trim($_POST['email']));
    $telepon  = trim($_POST['no_telp']);

    // =======================
    // CEK USERNAME
    // =======================
    $cekUser = $conn->prepare("
        SELECT id_user FROM user 
        WHERE username = ? AND id_user != ?
    ");
    $cekUser->bind_param("si", $username, $id_user);
    $cekUser->execute();
    $resUser = $cekUser->get_result();

    // =======================
    // CEK EMAIL
    // =======================
    $cekEmail = $conn->prepare("
        SELECT id_user FROM user 
        WHERE email = ? AND id_user != ?
    ");
    $cekEmail->bind_param("si", $email, $id_user);
    $cekEmail->execute();
    $resEmail = $cekEmail->get_result();

    if ($resUser->num_rows > 0) {

        $pesan = "Username sudah digunakan!";

    } elseif ($resEmail->num_rows > 0) {

        $pesan = "Email sudah digunakan!";

    } else {

        // =======================
        // UPDATE USER
        // =======================
        $stmt = $conn->prepare("
            UPDATE user 
            SET username = ?, email = ?
            WHERE id_user = ?
        ");
        $stmt->bind_param("ssi", $username, $email, $id_user);
        $stmt->execute();

        // =======================
        // UPDATE PEGAWAI
        // =======================
        $stmt2 = $conn->prepare("
            UPDATE pegawai 
            SET no_telp = ?
            WHERE nip = ?
        ");
        $stmt2->bind_param("ss", $telepon, $nip);
        $stmt2->execute();

        $pesan = "Data berhasil diperbarui";

        // refresh data biar langsung update di tampilan
        $user['username'] = $username;
        $user['email'] = $email;
        $user['no_telp'] = $telepon;
    }
}

// UPDATE PASSWORD
if(!isset($pesan)){
    $pesan = "";
}

if(isset($_POST['update_password'])){

    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $konfirmasi = $_POST['konfirmasi'];

    // CEK PASSWORD LAMA (PAKAI VERIFY)
    if(!password_verify($pass_lama, $user['password'])){
        
        $pesan = "Password lama yang kamu masukkan salah";

    } elseif($pass_baru != $konfirmasi){

        $pesan = "Konfirmasi password tidak sesuai dengan password baru";

    } elseif(strlen($pass_baru) < 8 || 
             !preg_match('/[A-Z]/', $pass_baru) || 
             !preg_match('/[0-9]/', $pass_baru)){

        $pesan = "Password baru harus minimal 8 karakter, mengandung huruf besar dan angka";

    } else {

        // HASH PASSWORD BARU
        $hash_baru = password_hash($pass_baru, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE user SET password=? WHERE nip=?
        ");
        $stmt->bind_param("ss", $hash_baru, $nip);
        $stmt->execute();

        $pesan = "Password berhasil diperbarui";
    }
}
$tab_aktif = 'data';

if(isset($_POST['update_password'])){
    $tab_aktif = 'password';
}

if(isset($_POST['update_data'])){
    $tab_aktif = 'data';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pengaturan Akun</title>
    <link rel="stylesheet" href="../assets/style_edit.css">
    <link rel="stylesheet" href="../assets/style_tab.css">
    <link rel="stylesheet" href="../assets/style.css">

</head>

<body class="role-admin">

    <!-- SIDEBAR -->
    <aside class="sidebar">
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

        <a href="Admin_Pengaturan_Akun.php" class="item-menu aktif">
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
    <main class="konten-akun">
        <h2>Pengaturan Akun</h2>

        <div class="user-profile" id="userProfile">
            <div class="user-info">
                <div class="user-icon">👤</div>
                <div class="user-name">
                    <?= $user['nama_pegawai'] ?>
                </div>
            </div>

            <div class="dropdown-menu" id="dropdownMenu">
                <a href="Identitas_User.php">Beranda</a>
                <a href="#" onclick="openLogoutModal()">Keluar</a>
            </div>
        </div>
        <!-- TAB -->
        <div class="tab-container">
            <div class="tab <?= $tab_aktif == 'data' ? 'aktif' : '' ?>" data-tab="data">Data Pribadi</div>
            <div class="tab <?= $tab_aktif == 'password' ? 'aktif' : '' ?>" data-tab="password">Kata Sandi</div>
        </div>

        <!-- DATA PRIBADI -->
        <div class="tab-content <?= $tab_aktif == 'data' ? 'aktif' : '' ?>" id="data">
            <form method="POST" onsubmit="return validasiTelp()">
                <div class="kelompok-form">
                    <label>Username</label>
                    <input name="nama" value="<?= $user['username'] ?>">
                </div>

                <div class="kelompok-form">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $user['email'] ?>">
                </div>

                <div class="kelompok-form">
                    <label>Telepon</label>
                    <input type="text" name="no_telp" 
                    value="<?= $user['no_telp'] ?>" 
                    placeholder="Contoh: 0812-3456-7890"
                    oninput="formatTelp(this)">
                </div>

                <div class="aksi-form">
                    <button name="update_data" class="tombol-ubah">PERBARUI</button>
                </div>
            </form>
        </div>

        <!-- PASSWORD -->
        <div class="tab-content <?= $tab_aktif == 'password' ? 'aktif' : '' ?>" id="password">
            <form method="POST">
                <div class="kelompok-form password-wrapper">
                    <label>Password Lama</label>
                    <div class="input-password">
                        <input type="password" name="pass_lama" id="pass_lama">
                        <span onclick="togglePassword('pass_lama', this)">👁</span>
                    </div>
                </div>

                <div class="kelompok-form password-wrapper">
                    <label>Password Baru</label>
                    <div class="input-password">
                        <input type="password" name="pass_baru" id="pass_baru">
                        <span onclick="togglePassword('pass_baru', this)">👁</span>
                    </div>
                    <!-- KETERANGAN -->
                        <small id="infoPassword" style="color:gray;">
                            Minimal 8 karakter, harus ada huruf besar dan angka
                        </small>
                </div>

                <div class="kelompok-form password-wrapper">
                    <label>Konfirmasi Password</label>
                    <div class="input-password">
                        <input type="password" name="konfirmasi" id="konfirmasi">
                        <span onclick="togglePassword('konfirmasi', this)">👁</span>
                    </div>
                </div>

                <div class="aksi-form">
                    <button name="update_password" class="tombol-ubah">PERBARUI</button>
                </div>
            </form>
        </div>
        <div id="notifModal" class="modal">
            <div class="modal-content">
                <p id="isiPesan"></p>
                <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                    <button onclick="closeNotif()" class="tombol-tambah">OK</button>
                </div>
            </div>
        </div>

    </main>
    <div id="modalAksi" class="modal">
  <div class="modal-content">
    <h3 id="judulAksi"></h3>
    <p id="isiAksi"></p>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
      <button id="btnBatalAksi" class="tombol-batal" style="display:none;">Batal</button>
      <button id="btnOKAksi"class="tombol-tambah">OK</button>
    </div>
  </div>
</div>

    <?php include '../pegawai/Notifikasi_Logout.php'; ?>
  <script src="../assets/admin-ui.js"></script>
  <script src="../assets/script_pg.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // SIDEBAR TOGGLE
            const tombolMenu = document.getElementById("tombolMenu");
            const sidebar = document.querySelector(".sidebar");

            if (tombolMenu && sidebar) {
                tombolMenu.addEventListener("click", function() {

                    sidebar.classList.toggle("tertutup");

                    tombolMenu.textContent = sidebar.classList.contains("tertutup") ?
                        "<" :
                        "✕";
                });
            }

            // DROPDOWN USER PROFILE
            const userProfile = document.getElementById("userProfile");

            if (userProfile) {

                userProfile.addEventListener("click", function(e) {
                    e.stopPropagation();
                    userProfile.classList.toggle("active");
                });

                document.addEventListener("click", function(e) {
                    if (!userProfile.contains(e.target)) {
                        userProfile.classList.remove("active");
                    }
                });

            }

            // TAB SWITCH
            const tabs = document.querySelectorAll(".tab");
            const contents = document.querySelectorAll(".tab-content");

            if (tabs.length > 0) {

                tabs.forEach(tab => {

                    tab.addEventListener("click", function() {

                        tabs.forEach(t => t.classList.remove("aktif"));
                        contents.forEach(c => c.classList.remove("aktif"));

                        this.classList.add("aktif");

                        const target = this.dataset.tab;
                        document.getElementById(target).classList.add("aktif");

                    });

                });

            }

            // LOGOUT MODAL
            function openLogoutModal() {
                document.getElementById("modalLogout").style.display = "flex";
            }

            function closeLogoutModal() {
                document.getElementById("modalLogout").style.display = "none";
            }

        });
    </script>

    <script>
        function togglePassword(id, el) {
            const input = document.getElementById(id);

            if (input.type === "password") {
                input.type = "text";
                el.style.color = "gray"; 
            } else {
                input.type = "password";
                el.style.color = "black"; 
            }
        }
        function closeNotif() {
            document.getElementById("notifModal").style.display = "none";
        }

        <?php if (!empty($pesan)): ?>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("isiPesan").innerText = "<?= $pesan ?>";
                document.getElementById("notifModal").style.display = "flex";
            });
        <?php endif; ?>
    </script>
    <script>
        function validasiTelp() {

        let telp = document.querySelector("input[name='no_telp']").value.trim();
        let telpAngka = telp.replace(/\D/g, '');

        if (telpAngka.length === 0) {
            openModalAksi("Peringatan", "Nomor telepon tidak boleh kosong!");
            return false;
        }

        if (telpAngka.length < 10) {
            openModalAksi("Peringatan", "Nomor telepon terlalu pendek!");
            return false;
        }

        if (telpAngka.length > 13) {
            openModalAksi("Peringatan", "Nomor telepon terlalu panjang!");
            return false;
        }

        if (!telpAngka.startsWith("08")) {
            openModalAksi("Peringatan", "Nomor telepon harus diawali 08!");
            return false;
        }

        // bersihkan sebelum submit
        document.querySelector("input[name='no_telp']").value = telpAngka;

        return true;
        }
    </script>
    <script>
        function openModalAksi(judul, isi) {
            document.getElementById("modalAksi").style.display = "flex";
            document.getElementById("judulAksi").innerText = judul;
            document.getElementById("isiAksi").innerText = isi;

            document.getElementById("btnOKAksi").onclick = function () {
                document.getElementById("modalAksi").style.display = "none";
            };
        }
    </script>
</body>

</html>