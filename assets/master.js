//CHECKBOX//
document.addEventListener("DOMContentLoaded", function () {

    const checkAll = document.getElementById("checkAll");

    if (checkAll) {
        checkAll.addEventListener("change", function () {

            const checkboxes = document.querySelectorAll("input[name='id_delete[]']");

            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });

        });
    }

});

// MODAL //
//Modal Tambah
function openModal() {
    document.getElementById("modalTambah").style.display = "block";
}

function closeModal() {
    document.getElementById("modalTambah").style.display = "none";
}

let deleteId = null;

//Modal Hapus
function openDeleteModal(id) {
    deleteId = id;

    // set link hapus
    document.getElementById("btnHapusFinal").href = "?hapus=" + id;

    document.getElementById("modalHapus").style.display = "block";
}

function closeDeleteModal() {
    document.getElementById("modalHapus").style.display = "none";
}

// BULK-DELETE //
// Buka Modal
function openBulkDeleteModal() {

    const checked = document.querySelectorAll("input[name='id_delete[]']:checked");

    if (checked.length < 2) {
    showToast("Pilih minimal 2 data untuk menghapus!", "warning");
    return;
    }

    document.getElementById("modalBulkDelete").style.display = "block";
}

// Tutup Modal
function closeBulkDeleteModal() {
    document.getElementById("modalBulkDelete").style.display = "none";
}

// Submit Form
function submitBulkDelete() {
    document.getElementById("formBulkDelete").submit();
}

//Toast Notif for Bulk-Delete//
function showToast(message, type = "warning") {

    const toast = document.getElementById("toastNotif");

    toast.textContent = message;
    toast.className = "toast show " + type;

    setTimeout(() => {
        toast.className = "toast";
    }, 3000);
}


// Untuk Tutup Modal Jika Klik di Luar Windows //
window.addEventListener("click", function(event) {

    const modals = [
        "modalTambah",
        "modalHapus",
        "modalBulkDelete",
        "modalEdit"
    ];

    modals.forEach(id => {
        const modal = document.getElementById(id);
        if (modal && event.target === modal) {
            modal.style.display = "none";
        }
    });

});

