<?php
/**
 * login.php
 * ─────────────────────────────────────────────────────────
 * Her sayfada tekrar yazmak yerine sadece şunu ekle:
 *
 * Ana klasör sayfaları:
 *   require "login.php";
 *
 * Alt klasör sayfaları (2025TGA vb.):
 *   require "../login.php";
 *
 * Modal HTML'i sayfada olduğu gibi kalır.
 * ─────────────────────────────────────────────────────────
 */

if (session_status() === PHP_SESSION_NONE) session_start();

$girisHata   = "";
$girisBasari = "";

// Kullanıcı dosyasının yolu — __DIR__ her zaman login.php'nin bulunduğu klasör
$_LOGIN_USERS_FILE = __DIR__ . "/users/users.txt";

// ── Giriş POST ───────────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login_submit'])) {
    $kullanici = trim(htmlspecialchars($_POST['username'] ?? ''));
    $sifre     = $_POST['password'] ?? '';

    if (empty($kullanici) || empty($sifre)) {
        $girisHata = "Kullanıcı adı ve şifre boş olamaz.";
    } else {
        $users = [];
        if (file_exists($_LOGIN_USERS_FILE)) {
            $f = fopen($_LOGIN_USERS_FILE, "r");
            if ($f) {
                while (!feof($f)) {
                    $line = trim(fgets($f));
                    if ($line === "") continue;
                    $p = explode("|", $line, 3);
                    if (count($p) === 3) $users[$p[0]] = $p[1]; // username => hash
                }
                fclose($f);
            }
        }

        if (isset($users[$kullanici]) && password_verify($sifre, $users[$kullanici])) {
            // Giriş başarılı — PRG ile aynı sayfaya yönlendir
            $_SESSION['username'] = $kullanici;
            $redirectUrl = strtok($_SERVER["REQUEST_URI"], '?') . "?giris=ok";
            if (!ob_get_level()) ob_start();
            ob_end_clean();
            header("Location: " . $redirectUrl);
            exit();
        } else {
            $girisHata = "Kullanıcı adı veya şifre yanlış.";
        }
    }
}

// ── Çıkış POST ───────────────────────────────────────────
if (isset($_POST['cikis_yap'])) {
    session_destroy();
    $redirectUrl = strtok($_SERVER["REQUEST_URI"], '?') . "?cikis=ok";
    if (!ob_get_level()) ob_start();
    ob_end_clean();
    header("Location: " . $redirectUrl);
    exit();
}

// ── GET mesajları ─────────────────────────────────────────
if (isset($_GET['giris']) && $_GET['giris'] === 'ok') {
    $girisBasari = "Hoş geldin, " . htmlspecialchars($_SESSION['username'] ?? '') . "!";
}