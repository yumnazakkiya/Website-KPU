document.addEventListener("DOMContentLoaded", function () {

    // ===============================
    // SIDEBAR BUKA / TUTUP
    // ===============================

    const sidebar = document.getElementById("sidebar");
    const tombolMenu = document.getElementById("tombolMenu");

    if (sidebar && tombolMenu) {
        tombolMenu.addEventListener("click", function () {
            sidebar.classList.toggle("tertutup");

            tombolMenu.textContent =
                sidebar.classList.contains("tertutup") ? "<" : "✕";
        });
    }

    

    // ===============================
    // LOG OUT
    // ===============================

    // const tombolKeluar = document.querySelector(".tombol-keluar");

    // if (tombolKeluar) {
    //     tombolKeluar.addEventListener("click", function () {
    //         if (confirm("Apakah Anda yakin ingin keluar?")) {
    //             window.location.href = "Login.html";
    //         }
    //     });
    // }

   

    // ===============================
    // DROPDOWN USER PROFILE
    // ===============================
    const userProfile = document.getElementById("userProfile");

    if (userProfile) {

        userProfile.addEventListener("click", function (e) {
            e.stopPropagation(); // supaya tidak langsung tertutup
            userProfile.classList.toggle("active");
        });

        document.addEventListener("click", function (e) {
            if (!userProfile.contains(e.target)) {
                userProfile.classList.remove("active");
            }
        });
    }


});
