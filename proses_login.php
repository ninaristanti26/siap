<?php 
session_start();

include("koneksi.php");
include("functions.php");

$id_ao       = mysqli_real_escape_string($mysqli, $_POST['id_ao']);
$kode_cabang = mysqli_real_escape_string($mysqli, $_POST['kode_cabang']);
$password    = mysqli_real_escape_string($mysqli, $_POST['password']);
$id_jabatan  = mysqli_real_escape_string($mysqli, $_POST['id_jabatan']);

$query = "SELECT * FROM ao 
          JOIN cabang ON ao.kode_cabang = cabang.kode_cabang 
          JOIN jabatan ON ao.id_jabatan = jabatan.id_jabatan 
          WHERE cabang.kode_cabang = ? 
          AND ao.id_ao = ? 
          AND ao.password = ? 
          AND jabatan.id_jabatan = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('ssss', $kode_cabang, $id_ao, $password, $id_jabatan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    $_SESSION['id_ao']       = $user['id_ao'];
    $_SESSION['kode_cabang'] = $user['kode_cabang'];
    $_SESSION['id_jabatan']  = $user['id_jabatan'];
    $_SESSION['status']      = "login";

    $encoded_id_ao       = encrypt($_SESSION['id_ao']);
    $encoded_kode_cabang = encrypt($_SESSION['kode_cabang']);

    switch ($id_jabatan) {
        case 1:
            header("Location: beranda_ao?id_ao=".$_SESSION['id_ao']."&kode_cabang=".$_SESSION['kode_cabang']."");
            break;
        case 2:
            header("Location: beranda_kasie?id_ao=".$_SESSION['id_ao']."&kode_cabang=".$_SESSION['kode_cabang']."");
            break;
        case 3:
            header("Location: beranda_pusat?id_ao=".$_SESSION['id_ao']."&kode_cabang=".$_SESSION['kode_cabang']."");
            break;
        case 4:
            header("Location: beranda_kasie?id_ao=".$_SESSION['id_ao']."&kode_cabang=".$_SESSION['kode_cabang']."");
            break;
        case 5:
            header("Location: beranda_remedial?id_ao=$encoded_id_ao&kode_cabang=$encoded_kode_cabang");
            break;
        default:
            header("Location: index?pesan=gagal");
            break;
    }
} else {
    header("Location: index?pesan=gagal");
}

$stmt->close();
$mysqli->close();
?>