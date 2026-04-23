<?php
session_start();
include '../config/koneksi.php';

header('Content-Type: application/json');

// Validasi input
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$limit  = $limit > 0 ? $limit : 10;
$page   = $page > 0 ? $page : 1;
$offset = ($page - 1) * $limit;

// Parameter pencarian
$searchParam = "%{$search}%";

/* =========================
   HITUNG TOTAL DATA
========================= */
$countSql = "
    SELECT COUNT(DISTINCT p.nip) AS total
    FROM pegawai p
    LEFT JOIN master_divisi md ON p.id_unit_kerja = md.id_unit_kerja
    LEFT JOIN riwayat_jabatan rj ON rj.id_riwayat_jabatan = (
        SELECT id_riwayat_jabatan
        FROM riwayat_jabatan
        WHERE nip = p.nip
        ORDER BY id_riwayat_jabatan DESC
        LIMIT 1
    )
    LEFT JOIN master_jabatan mj ON rj.id_jabatan = mj.id_jabatan
    LEFT JOIN riwayat_golongan rg ON rg.id_riwayat_gol = (
        SELECT id_riwayat_gol
        FROM riwayat_golongan
        WHERE nip = p.nip
        ORDER BY id_riwayat_gol DESC
        LIMIT 1
    )
    LEFT JOIN master_golongan mg ON rg.id_gol = mg.id_gol
    WHERE 
        p.nama_pegawai LIKE ? OR
        p.nip LIKE ? OR
        mj.nama_jabatan LIKE ? OR
        mg.nama_pangkat LIKE ? OR
        md.unit_kerja LIKE ?
";

$stmt = $conn->prepare($countSql);
$stmt->bind_param("sssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
$stmt->execute();
$totalData = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

/* =========================
   AMBIL DATA SESUAI HALAMAN
========================= */
$dataSql = "
    SELECT 
        p.nip,
        p.nama_pegawai,
        p.tipe_karyawan,
        mj.nama_jabatan,
        mg.nama_pangkat,
        md.unit_kerja
    FROM pegawai p
    LEFT JOIN riwayat_jabatan rj ON rj.id_riwayat_jabatan = (
        SELECT id_riwayat_jabatan
        FROM riwayat_jabatan
        WHERE nip = p.nip
        ORDER BY id_riwayat_jabatan DESC
        LIMIT 1
    )
    LEFT JOIN master_jabatan mj ON rj.id_jabatan = mj.id_jabatan
    LEFT JOIN riwayat_golongan rg ON rg.id_riwayat_gol = (
        SELECT id_riwayat_gol
        FROM riwayat_golongan
        WHERE nip = p.nip
        ORDER BY id_riwayat_gol DESC
        LIMIT 1
    )
    LEFT JOIN master_golongan mg ON rg.id_gol = mg.id_gol
    LEFT JOIN master_divisi md ON p.id_unit_kerja = md.id_unit_kerja
    WHERE 
        p.nama_pegawai LIKE ? OR
        p.nip LIKE ? OR
        mj.nama_jabatan LIKE ? OR
        mg.nama_pangkat LIKE ? OR
        md.unit_kerja LIKE ?
    ORDER BY p.nama_pegawai ASC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($dataSql);
$stmt->bind_param(
    "sssssii",
    $searchParam,
    $searchParam,
    $searchParam,
    $searchParam,
    $searchParam,
    $limit,
    $offset
);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
$stmt->close();

/* =========================
   HITUNG TOTAL HALAMAN
========================= */
$totalPage = $totalData > 0 ? ceil($totalData / $limit) : 1;

/* =========================
   KIRIM RESPONSE JSON
========================= */
echo json_encode([
    "data" => $data,
    "totalPage" => $totalPage,
    "totalData" => $totalData
]);
?>