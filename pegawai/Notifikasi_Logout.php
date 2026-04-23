<style>
      .user-profile {
        position: absolute;
        top: 20px;
        right: 40px;
        cursor: pointer;
        z-index: 9999;
      }

      .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f2f2f2;
        padding: 10px 15px;
        border-radius: 15px;
      }

      .user-icon {
        font-size: 24px;
      }

      .user-name {
        font-weight: bold;
        font-size: 14px;
      }

/* DROPDOWN */
.dropdown-menu {
  display: none;
  position: absolute;
  top: 60px;
  right: 0;
  background: #f2f2f2;
  border-radius: 15px;
  padding: 15px 20px;
  width: 100%;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}

.dropdown-menu a {
  display: block;
  text-decoration: none;
  color: #2c3e50;
  font-weight: 600;
  margin-bottom: 15px;
}

.dropdown-menu a:last-child {
  margin-bottom: 0;
}

/* TAMPIL SAAT AKTIF */
.user-profile.active .dropdown-menu {
  display: block;
}
.modal {
    display: none; 
    position: fixed;
    z-index: 9999;
    left: 0; 
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);

    justify-content: center;
    align-items: center;
}

.modal-content {
    background: white;
    padding: 17px;
    width: 350px;
    border-radius: 15px;
}

.tombol-batal,
.tombol-keluar{
    font-size:16px;
    width:80px;
    height:40px;
    border-radius:6px;
    display:flex;
    justify-content:center;
    align-items:center;
    text-decoration:none;
    border:none;
    color:white;
    cursor:pointer;
    font-weight: bold;
}

.tombol-batal{
    background-color:#27ae60;
}

.tombol-keluar{
    background-color:#c0392b;
}
</style>


<!-- MODAL LOGOUT -->
<div id="modalLogout" class="modal">
  <div class="modal-content">
    <h3>Konfirmasi Keluar</h3>
    <p>Apakah Anda yakin ingin keluar?</p>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
        <button onclick="closeLogoutModal()" class="tombol-batal">
            Batal
        </button>

        <a href="../auth/Logout.php" class="tombol-keluar">
            Keluar
        </a>
    </div>
  </div>
</div>


<script>
// ===== USER PROFILE DROPDOWN =====
document.addEventListener("DOMContentLoaded", function () {
    const userProfile = document.getElementById("userProfile");

    document.addEventListener("click", function (e) {
        if (!userProfile) return;

        if (userProfile.contains(e.target)) {
            userProfile.classList.toggle("active");
        } else {
            userProfile.classList.remove("active");
        }
    });
});


// ===== MODAL LOGOUT =====
function openLogoutModal() {
    document.getElementById("modalLogout").style.display = "flex";
}

function closeLogoutModal() {
    document.getElementById("modalLogout").style.display = "none";
}


</script>
