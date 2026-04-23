
document.getElementById("jumlahData").addEventListener("change", () => {
    page = 1;
    loadData();
});

document.getElementById("pencarian").addEventListener("keypress", function(e) {
    if (e.key === "Enter") {
        let search = this.value;
        let limit = document.getElementById("jumlahData").value;

        window.location.href = "?limit=" + limit + "&search=" + search;
    }
});

// Ambil parameter dari URL
const urlParams = new URLSearchParams(window.location.search);
let page = parseInt(urlParams.get("page")) || 1;

function loadData() {
    let limit = parseInt(document.getElementById("jumlahData").value);
    let search = document.getElementById("pencarian").value;

    fetch(`../admin/get_data_pegawai.php?limit=${limit}&page=${page}&search=${search}`)
        .then(res => res.json())
        .then(res => {
            let html = "";
            let no = (page - 1) * limit + 1; // Nomor urut sesuai halaman

            res.data.forEach(item => {
                html += `
                <tr>
                    <td>${no++}</td>
                    <td>${item.nama_pegawai}</td>
                    <td>${item.nama_jabatan ?? '-'}</td>
                    <td>${item.nama_pangkat ?? '-'}</td>
                    <td>${item.nip}</td>
                    <td>${item.tipe_karyawan}</td>
                    <td>${item.unit_kerja ?? '-'}</td>
                    <td>
                    <a href="../admin/pdf/generate_pdf.php?nip=${item.nip}" target="_blank" style="text-decoration: none;">👁</a>
                    <a href="identitas-pegawai.php?nip=${item.nip}" style="text-decoration: none;">✏️</a>
                    </td>
                </tr>`;
            });

            document.getElementById("dataTabel").innerHTML = html;
            buatPagination(res.totalPage);
            updateInfo(res.totalData);
        });
}
// EVENT
document.getElementById("jumlahData").addEventListener("change", () => {
    page = 1;
    loadData();
});

document.getElementById("pencarian").addEventListener("keyup", () => {
    page = 1;
    loadData();
});

// LOAD AWAL
loadData();


function buatPagination(totalPage) {
    let html = "";

    let maxPage = 5; // maksimal tombol angka tampil
    let start = Math.max(1, page - 2);
    let end = Math.min(totalPage, start + maxPage - 1);

    // PREV
    html += `<button ${page == 1 ? 'disabled' : ''} onclick="prevPage()">Previous</button>`;

    // ANGKA
    for (let i = start; i <= end; i++) {
        html += `
        <button 
            onclick="goPage(${i})" 
            class="${page == i ? 'active-page' : ''}">
            ${i}
        </button>`;
    }

    // NEXT
    html += `<button ${page == totalPage ? 'disabled' : ''} onclick="nextPage(${totalPage})">Next</button>`;

    document.getElementById("pagination").innerHTML = html;
}

function goPage(p) {
    page = p;
    loadData();
}

function prevPage() {
    if (page > 1) {
        page--;
        loadData();
    }
}

function nextPage(totalPage) {
    if (page < totalPage) {
        page++;
        loadData();
    }
}

// function updateInfo(totalData) {
//     let limit = document.getElementById("jumlahData").value;
//     let start = (page - 1) * limit + 1;
//     let end = Math.min(page * limit, totalData);

//     document.getElementById("infoData").innerHTML =
//         `Showing ${start}–${end} of ${totalData} entries`;
// }

function updateInfo(totalData) {
    let limit = parseInt(document.getElementById("jumlahData").value);
    let start = (page - 1) * limit + 1;
    let end = Math.min(page * limit, totalData);

    // Jika tidak ada data
    if (totalData === 0) {
        start = 0;
        end = 0;
    }

    document.getElementById("infoData").innerHTML =
        `Showing ${start}–${end} of ${totalData} entries`;
}
