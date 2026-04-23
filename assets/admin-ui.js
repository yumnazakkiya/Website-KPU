document.addEventListener("DOMContentLoaded", function () {

    // ===============================
    // DROPDOWN DATA MASTER
    // ===============================

    const menuDataMaster = document.getElementById("menuDataMaster");
    const submenuDataMaster = document.getElementById("submenuDataMaster");
    const panahDataMaster = document.getElementById("panahDataMaster");

    if (menuDataMaster && submenuDataMaster && panahDataMaster) {

        submenuDataMaster.style.display = "none";
        panahDataMaster.textContent = "▶";

        menuDataMaster.addEventListener("click", function () {

            const terbuka = submenuDataMaster.style.display === "block";

            submenuDataMaster.style.display = terbuka ? "none" : "block";
            panahDataMaster.textContent = terbuka ? "▶" : "▼";

        });
    }

    // ===============================
    // CHECK ALL TABLE
    // ===============================

    const checkAll = document.getElementById("checkAll");

    if (checkAll) {
        checkAll.addEventListener("change", function () {

            const checkboxes = document.querySelectorAll(".row-checkbox");

            checkboxes.forEach(function (cb) {
                cb.checked = checkAll.checked;
            });

        });
    }

});