// SIDEBAR BUKA / TUTUP
const sidebar = document.getElementById("sidebar");
const tombolMenu = document.getElementById("tombolMenu");

tombolMenu.addEventListener("click", () => {
    sidebar.classList.toggle("tertutup");

    if (sidebar.classList.contains("tertutup")) {
        tombolMenu.textContent = "<";
    } else {
        tombolMenu.textContent = "✕";
    }
});


// SUBMENU EDIT DATA 
const menuEditData = document.getElementById("menuEditData");
const submenuEditData = document.getElementById("submenuEditData");
const panahEditData = document.getElementById("panahEditData");

// Semua item menu utama
const semuaMenu = document.querySelectorAll(".item-menu");

menuEditData.addEventListener("click", function (e) {
    e.stopPropagation(); // supaya tidak bentrok

    const isAktif = submenuEditData.classList.contains("aktif");

    // Tutup semua submenu dulu
    submenuEditData.classList.remove("aktif");
    panahEditData.textContent = "▼";

    // Kalau sebelumnya belum aktif, buka lagi
    if (!isAktif) {
        submenuEditData.classList.add("aktif");
        panahEditData.textContent = "▲";
    }
});

// Kalau klik menu lain → otomatis tutup submenu
semuaMenu.forEach(menu => {
    if (menu !== menuEditData) {
        menu.addEventListener("click", function () {
            submenuEditData.classList.remove("aktif");
            panahEditData.textContent = "▼";
        });
    }
});

// TAB SWITCH
const tabs = document.querySelectorAll(".tab");
const contents = document.querySelectorAll(".tab-content");

tabs.forEach(tab => {
    tab.addEventListener("click", function () {

        tabs.forEach(t => t.classList.remove("aktif"));
        contents.forEach(c => c.classList.remove("aktif"));

        this.classList.add("aktif");
        document.getElementById(this.dataset.tab).classList.add("aktif");
    });
});

// TOMBOL LOG OUT 
const tombolKeluar = document.querySelector(".tombol-keluar");

tombolKeluar.addEventListener("click", () => {
    if (confirm("Apakah Anda yakin ingin keluar?")) {
        window.location.href = "Login.html";
    }
});

