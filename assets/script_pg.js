document.addEventListener("DOMContentLoaded", function () {

    // SIDEBAR BUKA / TUTUP 
    const tombolMenu = document.getElementById("tombolMenu");
    
    // Cari sidebar yang ada di halaman ini
    const sidebar =
        document.getElementById("sidebar") ||
        document.querySelector(".sidebar-edit");
    
    if (tombolMenu && sidebar) {
        tombolMenu.addEventListener("click", () => {
            sidebar.classList.toggle("tertutup");
    
            tombolMenu.textContent = sidebar.classList.contains("tertutup")
                ? "<"
                : "✕";
        });
    }
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
    
    // DROPDOWN USER PROFILE
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
    //LOGOUT
    function openLogoutModal() {
        document.getElementById("modalLogout").style.display = "flex";
    }
    
    function closeLogoutModal() {
        document.getElementById("modalLogout").style.display = "none";
    }
    
    // IDENTITAS
    function previewImage(event) {
        const file = event.target.files[0];
    
        if (file) {
            const maxSize = 2 * 1024 * 1024;
    
            if (file.size > maxSize) {
                showErrorModal("Ukuran foto maksimal 2MB!");
                event.target.value = "";
                return; 
            }
    
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById('preview').src = reader.result;
            }
            reader.readAsDataURL(file);
        }
    }
    
    function showErrorModal(pesan) {
        document.getElementById("errorText").innerText = pesan;
        document.getElementById("modalError").style.display = "flex";
    }
    
    function closeErrorModal() {
        document.getElementById("modalError").style.display = "none";
    }

    //UBAH DAN HAPUS
    function openModalAksi(judul, pesan, tipe, aksiOK = null) {
        document.getElementById("modalAksi").style.display = "flex";
        document.getElementById("judulAksi").innerText = judul;
        document.getElementById("isiAksi").innerText = pesan;
    
        let btnOK = document.getElementById("btnOKAksi");
        let btnBatal = document.getElementById("btnBatalAksi");
    
        // 🔥 INI YANG PENTING BANGET
        btnOK.onclick = null;
        btnBatal.onclick = null;
    
        btnOK.className = "";
        btnOK.classList.add("tombol-tambah");
    
        btnBatal.style.display = "none";
    
        // default (info)
        btnOK.onclick = closeModalAksi;
    
        if (tipe === "confirm") {
            btnBatal.style.display = "inline-block";
    
            btnOK.className = "";
            btnOK.classList.add("tombol-hapus");
    
            btnOK.onclick = function () {
                if (aksiOK) aksiOK();
                closeModalAksi();
            };
    
            btnBatal.onclick = closeModalAksi;
        }
    }
    window.closeModalAksi = function () {
        document.getElementById("modalAksi").style.display = "none";
    };
// ================= UBAH =================
function klikUbah() {
    let kosong = false;

    let nip = document.querySelector("input[name='nip']").value.trim();

    // VALIDASI NIP
    if (nip.length > 18) {
        openModalAksi("Peringatan", "NIP tidak boleh lebih dari 18 digit!", "info");
        return;
    }

    if (nip.length < 18) {
        openModalAksi("Peringatan", "NIP tidak boleh kurang dari 18 digit!", "info");
        return;
    }

    if (!/^\d+$/.test(nip)) {
        openModalAksi("Peringatan", "NIP harus berupa angka!", "info");
        return;
    }

    let telp = document.querySelector("input[name='no_telp']").value.trim();

    let telpAngka = telp.replace(/\D/g, '');

    if (telpAngka.length === 0) {
        openModalAksi("Peringatan", "Nomor telepon tidak boleh kosong!", "info");
        return;
    }

    if (telpAngka.length > 13) {
        openModalAksi("Peringatan", "Nomor telepon terlalu panjang!", "info");
        return;
    }

    if (telpAngka.length < 10) {
        openModalAksi("Peringatan", "Nomor telepon terlalu pendek!", "info");
        return;
    }

    if (!telpAngka.startsWith("08")) {
        openModalAksi("Peringatan", "Nomor telepon harus diawali 08!", "info");
        return;
    }

    document.querySelectorAll("#formUpload input, #formUpload textarea, #formUpload select").forEach(el => {
        if (el.type !== "file" && el.value.trim() === "") {
            kosong = true;
        }
    });

    if (kosong) {
        openModalAksi("Peringatan", "Lengkapi data terlebih dahulu!", "info");
    } else {
        openModalAksi(
            "Konfirmasi",
            "Apakah Anda ingin mengubah data?",
            "confirm",
            function () {
                let form = document.getElementById("formUpload");

                let input = document.createElement("input");
                input.type = "hidden";
                input.name = "ubah";
                input.value = "1";

                form.appendChild(input);
                document.querySelector("input[name='no_telp']").value = telpAngka;
                form.submit();
            }
        );
    }
}

function klikUbahBeda(idField) {

    let id = document.getElementById(idField).value;

    if (!id) {
        openModalAksi("Peringatan", "Pilih data dari tabel terlebih dahulu!", "info");
        return;
    }

    let kosong = false;

    document.querySelectorAll("#formUpload input, #formUpload textarea, #formUpload select").forEach(el => {
        if (el.type !== "file" && el.value.trim() === "") {
            kosong = true;
        }
    });

    if (kosong) {
        openModalAksi("Peringatan", "Lengkapi data terlebih dahulu!", "info");
        return;
    }

    openModalAksi(
        "Konfirmasi",
        "Apakah Anda ingin mengubah data?",
        "confirm",
        function () {
            let form = document.getElementById("formUpload");

            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "ubah";
            input.value = "1";

            form.appendChild(input);
            form.submit();
        }
    );
}
// ================= TAMBAH =================

function klikTambah() {

    let kosong = false;

    // ambil semua field dalam form
    document.querySelectorAll("#formUpload input, #formUpload select, #formUpload textarea")
    .forEach(el => {

        // skip hidden & file
        if (el.type === "hidden" || el.type === "file") return;

        // skip yang memang tidak wajib (kalau ada)
        if (el.hasAttribute("data-optional")) return;

        if (el.value.trim() === "") {
            kosong = true;
        }
    });

    if (kosong) {
        openModalAksi("Peringatan", "Lengkapi data terlebih dahulu!", "info");
        return;
    }

    openModalAksi(
        "Konfirmasi",
        "Apakah Anda ingin menambahkan data?",
        "confirm",
        function () {
            let form = document.getElementById("formUpload");

            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "tambah";
            input.value = "1";

            form.appendChild(input);
            form.submit();
        }
    );
}


// hapus
function klikHapus(idField) {
    let id = document.getElementById(idField).value;

    if (!id) {
        openModalAksi("Peringatan", "Pilih data dari tabel terlebih dahulu!", "info");
        return;
    }

    openModalAksi(
        "Konfirmasi",
        "Apakah Anda ingin menghapus data?",
        "confirm",
        function () {
            let form = document.getElementById("formUpload");

            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "hapus";
            input.value = "1";

            form.appendChild(input);
            form.submit();
        }
    );
}
//no telepon
function formatTelp(input) {

    let angka = input.value.replace(/\D/g, '');

    let format = angka;

    if (angka.length > 4 && angka.length <= 8) {
        format = angka.substring(0, 4) + '-' + angka.substring(4);
    } 
    else if (angka.length > 8) {
        format = angka.substring(0, 4) + '-' + angka.substring(4, 8) + '-' + angka.substring(8);
    }

    input.value = format;
}

window.addEventListener("DOMContentLoaded", function() {
    let input = document.querySelector("input[name='no_telp']");
    if (input && input.value) {
        formatTelp(input);
    }
});
