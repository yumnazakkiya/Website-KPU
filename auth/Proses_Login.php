<?php
session_start();
include '../config/koneksi.php';

$username = $_POST['username'];
$password = $_POST['password'];

$error_user = 0;
$error_pass = 0;
$error_nonaktif = 0;

// VALIDASI INPUT
if(empty($username) || empty($password)){
    header("Location: Login.php?error=kosong");
    exit;
}

// CEK USERNAME (AMAN - pakai prepared statement)
$stmt = $conn->prepare("SELECT * FROM user WHERE BINARY username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$data_user = $result->fetch_assoc();

// LOGIKA LOGIN
if(!$data_user){
    $error_user = 1;
} elseif(!$data_user['is_active']){
    $error_nonaktif = 1;
} elseif(!password_verify($password, $data_user['password'])){
    $error_pass = 1;
} else {
    // LOGIN BERHASIL
    $_SESSION['username'] = $data_user['username'];
    $_SESSION['role']     = $data_user['role'];
    $_SESSION['nip']      = $data_user['nip'];

    // update last login
    mysqli_query($conn, "
        UPDATE user 
        SET last_login = NOW() 
        WHERE id_user = '".$data_user['id_user']."'
    ");

    // redirect sesuai role
    if($data_user['role']=="admin"){
        header("location:../admin/Admin_Profil_Data_Pegawai.php");
    } else {
        header("location:../pegawai/Identitas_User.php");
    }
    exit;
}

// JIKA ERROR
header("location:Login.php?error_user=$error_user&error_pass=$error_pass&error_nonaktif=$error_nonaktif");
exit;
?>