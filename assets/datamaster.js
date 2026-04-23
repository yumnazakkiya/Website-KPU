document.addEventListener("DOMContentLoaded", function () {

    const jenis = document.body.dataset.jenis;
    const tableBody = document.getElementById("tableBody");
    const tableHead = document.querySelector(".table-master thead");

    if (!jenis || !tableBody) return;

    // ===============================
    // KONFIGURASI SEMUA DATA MASTER
    // ===============================

    const konfigurasi = {

        "status-perkawinan": {
            kolom: ["Status Perkawinan", "Keterangan"],
            field: ["status", "keterangan"]
        },

        "agama": {
            kolom: ["Nama Agama"],
            field: ["nama"]
        },

        "jenis-kelamin": {
            kolom: ["Jenis Kelamin"],
            field: ["nama"]
        },

        "golongan": {
            kolom: ["Golongan", "Nama Pangkat", "Keterangan"],
            field: ["golongan", "pangkat","keterangan"]
        },

        "jabatan": {
            kolom: ["Nama Jabatan", "Jenis Jabatan", "Keterangan", "Status"],
            field: ["nama", "jenis", "keterangan", "status"]
        },

        "hubungan-keluarga": {
            kolom: ["Hubungan Keluarga", "Keterangan"],
            field: ["hubungan-keluarga", "keterangan"]
        },

        "jenis-diklat": {
            kolom: ["Jenis Diklat", "Keterangan"],
            field: ["jenis-diklat", "keterangan"]
        },

        "jenis-penghargaan": {
            kolom: ["Jenis Diklat", "Keterangan"],
            field: ["jenis-penghargaan", "keterangan"]
        },

        "jenjang-pendidikan": {
            kolom: ["Jenjang Pendidikan", "Keterangan"],
            field: ["jenjang-pendidikan", "keterangan"]
        },

        "predikat-skp": {
            kolom: ["Jenjang Pendidikan", "Keterangan"],
            field: ["predikat-skp", "keterangan"]
        },

        "unit-kerja": {
            kolom: ["Unit Kerja", "Keterangan"],
            field: ["unit-kerja", "keterangan"]
        }
    
    };

    const config = konfigurasi[jenis];
    if (!config) return;

    // ===============================
    // DATA SIMULASI
    // ===============================

    let dataMaster = [];

    // ===============================
    // RENDER TABLE
    // ===============================

    function renderTable() {

        tableBody.innerHTML = "";

        if (dataMaster.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${config.field.length + 2}" 
                        style="text-align:center;padding:20px;">
                        Belum ada data
                    </td>
                </tr>
            `;
            return;
        }

        dataMaster.forEach(function (item, index) {

            let row = `
                <tr>
                    <td>
                        <input type="checkbox" 
                               class="row-checkbox"
                               value="${index}">
                    </td>
            `;

            config.field.forEach(function (fieldName) {
                row += `<td>${item[fieldName] ?? ""}</td>
`;
            });

            row += `
                    <td>
                        <button class="tombol-ubah"
                                onclick="editData(${index})">✏</button>
                        <button class="tombol-hapus"
                                onclick="hapusData(${index})">🗑</button>
                    </td>
                </tr>
            `;

            tableBody.innerHTML += row;
        });
    }

    // ===============================
    // TAMBAH DATA
    // ===============================

    window.tambahData = function () {

        let objekBaru = {};

        config.field.forEach(function (fieldName) {
            const value = prompt("Masukkan " + fieldName + ":");
            objekBaru[fieldName] = value ? value.trim() : "";
        });

        dataMaster.push(objekBaru);
        renderTable();
    };

    // ===============================
    // EDIT DATA
    // ===============================

    window.editData = function (index) {

        let objekEdit = {};

        config.field.forEach(function (fieldName) {
            const value = prompt(
                "Edit " + fieldName + ":",
                dataMaster[index][fieldName]
            );

            objekEdit[fieldName] = value ? value.trim() : "";
        });

        dataMaster[index] = objekEdit;
        renderTable();
    };

    // ===============================
    // HAPUS DATA
    // ===============================

    window.hapusData = function (index) {

        if (confirm("Yakin ingin menghapus data ini?")) {
            dataMaster.splice(index, 1);
            renderTable();
        }
    };

    // ===============================
    // HAPUS TERPILIH
    // ===============================

    window.hapusTerpilih = function () {

        const checked = document.querySelectorAll(
            "#tableBody .row-checkbox:checked"
        );

        if (checked.length === 0) {
            alert("Pilih data yang ingin dihapus.");
            return;
        }

        const indexHapus = [];

        checked.forEach(cb =>
            indexHapus.push(parseInt(cb.value))
        );

        dataMaster = dataMaster.filter((_, index) =>
            !indexHapus.includes(index)
        );

        renderTable();
    };

    renderTable();
});
