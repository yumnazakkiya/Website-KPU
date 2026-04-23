<?php
session_start();
include '../config/koneksi.php';

$id = $_GET['id'];

// Cegah admin nonaktifkan dirinya sendiri
if($_SESSION['nip'] == $id){
    header("Location: Admin_Manajemen_Akun.php?error=self");
    exit;
}

// update status
mysqli_query($conn, "
    UPDATE user 
    SET is_active = 0 
    WHERE id_user = '$id'
");

header("Location: Admin_Manajemen_Akun.php?success=nonaktif");
exit;