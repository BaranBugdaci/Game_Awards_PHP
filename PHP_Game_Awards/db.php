<?php
// db.php
$serverName = ".\\SQLEXPRESS02"; 
$connectionInfo = array(
    "Database" => "TGA_Portal",
    "UID" => "tga_user",      // Oluşturduğumuz kullanıcı
    "PWD" => "Baran1234",     // Belirlediğin şifre
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die("Bağlantı hatası: " . print_r(sqlsrv_errors(), true));
}
?>