<?php

$host = "localhost";
$db   = "profil_kepegawaian";
$user = "root";
$pass = "";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}