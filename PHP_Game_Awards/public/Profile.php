<?php
include_once "../includes/auth_nav.php"; // ob_start + session_start burada yapılıyor

// Giriş kontrolü
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// ── Dizinler ve dosyalar ─────────────────────────────────────────────────────
$usersFile  = "../storage/users/users.json";
$avatarsDir = "avatars/";
if (!file_exists("../storage/users"))      mkdir("../storage/users",      0755, true);
if (!file_exists($avatarsDir))  mkdir($avatarsDir,  0755, true);
if (!file_exists($usersFile)) { file_put_contents($usersFile, json_encode([])); }

// ── Yardımcı: kullanıcıları oku ─────────────────────────────────────────────
function readUsers($file) {
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        return $data ? $data : [];
    }
    return [];
}

// ── Yardımcı: kullanıcıları yaz ─────────────────────────────────────────────
function writeUsers($file, $users) {
    return file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

// ── Yardımcı: kullanıcının tüm yorumlarını topla ────────────────────────────
function getUserComments($username) {
    $all = [];
    $dir = "../storage/comments/";
    if (!file_exists($dir)) return $all;
    foreach (glob($dir."comments_*.txt") as $file) {
        $key = str_replace([$dir."comments_",".txt"], "", $file);
        $f = fopen($file, "r");
        if (!$f) continue;
        while (!feof($f)) {
            $line = trim(fgets($f));
            if ($line === "") continue;
            $p = explode("|", $line, 3);
            if (count($p) === 3 && $p[1] === $username)
                $all[] = ["page"=>strtoupper($key),"tarih"=>$p[0],"yorum"=>$p[2]];
        }
        fclose($f);
    }
    usort($all, fn($a,$b) => strcmp($b['tarih'],$a['tarih']));
    return $all;
}

$currentUser = $_SESSION['username'];
$users       = readUsers($usersFile);

// Kullanıcı kayıtlı değilse oluştur
if (!isset($users[$currentUser])) {
    $users[$currentUser] = ["password"=>"","avatar"=>""];
    writeUsers($usersFile, $users);
}

// ── Mesaj haritaları ─────────────────────────────────────────────────────────
$basariMap = [
    "nick"      => "Kullanıcı adı başarıyla güncellendi!",
    "sifre"     => "Şifre başarıyla güncellendi!",
    "avatar"    => "Profil fotoğrafı güncellendi!",
    "avatarsil" => "Profil fotoğrafı kaldırıldı!",
];
$hataMap = [
    "nick_bos"       => "Kullanıcı adı boş olamaz.",
    "nick_var"       => "Bu kullanıcı adı zaten kullanılıyor.",
    "sifre_bos"      => "Şifre alanları boş olamaz.",
    "sifre_eslesme"  => "Yeni şifreler eşleşmiyor.",
    "sifre_yanlis"   => "Mevcut şifre yanlış.",
    "avatar_tip"     => "Sadece JPG, PNG veya GIF yükleyebilirsiniz.",
    "avatar_hata"    => "Fotoğraf yüklenirken hata oluştu.",
];
$basari = isset($_GET['ok'])   ? ($basariMap[$_GET['ok']]   ?? "") : "";
$hata   = isset($_GET['hata']) ? ($hataMap[$_GET['hata']]   ?? "Bir hata oluştu.") : "";

// ════════════════════════════════════════════════════════════════════════════
// POST İŞLEMLERİ (tümü PRG — redirect ile biter)
// ════════════════════════════════════════════════════════════════════════════

// 1. Nick Değiştir ───────────────────────────────────────────────────────────
if (isset($_POST['nick_degistir'])) {
    $yeni = trim(htmlspecialchars($_POST['yeni_nick'] ?? ''));
    if (empty($yeni))                              { header("Location: Profile.php?hata=nick_bos"); exit(); }
    if ($yeni !== $currentUser && isset($users[$yeni])) { header("Location: Profile.php?hata=nick_var"); exit(); }

    // Kullanıcı kaydını taşı
    $data = $users[$currentUser];
    unset($users[$currentUser]);
    $users[$yeni] = $data;
    writeUsers($usersFile, $users);

    // Yorumlardaki eski adı güncelle (fopen/fwrite/fclose)
    foreach (glob("../storage/comments/comments_*.txt") as $cf) {
        $lines = []; $changed = false;
        $f = fopen($cf, "r");
        if ($f) { while (!feof($f)) { $lines[] = fgets($f); } fclose($f); }
        $fw = fopen($cf, "w");
        if ($fw) {
            foreach ($lines as $l) {
                $p = explode("|", trim($l), 3);
                if (count($p) === 3 && $p[1] === $currentUser) {
                    $l = $p[0]."|".$yeni."|".$p[2]."\n"; $changed = true;
                }
                fwrite($fw, $l);
            }
            fclose($fw);
        }
    }
    $_SESSION['username'] = $yeni;
    header("Location: Profile.php?ok=nick"); exit();
}

// 2. Şifre Değiştir ──────────────────────────────────────────────────────────
if (isset($_POST['sifre_degistir'])) {
    $mevcut = $_POST['mevcut_sifre'] ?? '';
    $yeni   = $_POST['yeni_sifre']   ?? '';
    $tekrar = $_POST['tekrar_sifre'] ?? '';

    if (empty($yeni) || empty($tekrar))                              { header("Location: Profile.php?hata=sifre_bos");      exit(); }
    if ($yeni !== $tekrar)                                           { header("Location: Profile.php?hata=sifre_eslesme"); exit(); }
    $kaydedilen = $users[$currentUser]['password'] ?? '';
    if (!empty($kaydedilen) && !password_verify($mevcut, $kaydedilen)) { header("Location: Profile.php?hata=sifre_yanlis"); exit(); }

    $users[$currentUser]['password'] = password_hash($yeni, PASSWORD_BCRYPT);
    writeUsers($usersFile, $users);
    header("Location: Profile.php?ok=sifre"); exit();
}


// 3. Avatar Yükle ────────────────────────────────────────────────────────────
if (isset($_POST['avatar_yukle']) && !empty($_FILES['avatar']['tmp_name'])) {
    $file   = $_FILES['avatar'];
    $izinli = ['image/jpeg','image/png','image/gif'];
    if (!in_array($file['type'], $izinli)) { header("Location: Profile.php?hata=avatar_tip"); exit(); }

    $ext    = pathinfo($file['name'], PATHINFO_EXTENSION);
    $ad     = "avatar_".preg_replace('/[^a-z0-9]/','',strtolower($currentUser)).".".$ext;
    $hedef  = $avatarsDir.$ad;

    // Eski avatarı sil
    $eski = $users[$currentUser]['avatar'];
    if ($eski && file_exists($eski)) unlink($eski);

    // fopen / fwrite / fclose ile kaydet
    $veri = file_get_contents($file['tmp_name']);
    $f = fopen($hedef, "wb");
    if ($f) {
        fwrite($f, $veri);
        fclose($f);
        $users[$currentUser]['avatar'] = $hedef;
        writeUsers($usersFile, $users);
        header("Location: Profile.php?ok=avatar"); exit();
    }
    header("Location: Profile.php?hata=avatar_hata"); exit();
}

// 4. Avatar Kaldır ───────────────────────────────────────────────────────────
if (isset($_POST['avatar_sil'])) {
    $eski = $users[$currentUser]['avatar'];
    if ($eski && file_exists($eski)) unlink($eski);
    $users[$currentUser]['avatar'] = "";
    writeUsers($usersFile, $users);
    header("Location: Profile.php?ok=avatarsil"); exit();
}

// ── Render için verileri hazırla ─────────────────────────────────────────────
$users       = readUsers($usersFile);
$currentUser = $_SESSION['username'];
$avatar      = $users[$currentUser]['avatar'] ?? "";
$yorumlar    = getUserComments($currentUser);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profil - TGA Arşivi</title>
    <!-- TGA CSS: projenizde "TGA CSS.css" veya "TGA_CSS.css" hangisi varsa onu kullanın -->
    <link rel="stylesheet" href="TGA_CSS.css">
    <link rel="stylesheet" href="TGA%20CSS.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS değişkenleri — TGA_CSS.css yüklenemezse diye yedek */
        :root {
            --accent: #c5a059;
            --card-bg: #111;
            --text-dim: rgba(255,255,255,0.4);
        }
        body { background-color: #0a0a0a; padding-top: 80px; color: #fff; }

        /* NAV */
        .category-nav { width:100%; padding:20px 0; background:rgba(0,0,0,0.4); backdrop-filter:blur(10px); border-bottom:1px solid rgba(197,160,89,0.1); position:fixed; top:0; left:0; z-index:1001; }
        .nav-container { max-width:1100px; margin:0 auto; display:flex; justify-content:center; gap:30px; }
        .cat-item { color:rgba(197,160,89,0.7); text-decoration:none; font-size:0.95rem; font-weight:800; letter-spacing:2.5px; padding:12px 24px; border-radius:6px; border:1px solid rgba(197,160,89,0.2); transition:all 0.4s cubic-bezier(0.175,0.885,0.32,1.275); opacity:0.9; background:rgba(0,0,0,0.2); }
        .cat-item:hover { color:#fff; border-color:var(--accent); background:rgba(197,160,89,0.15); opacity:1; transform:translateY(-3px) scale(1.05); box-shadow:0 8px 20px rgba(197,160,89,0.2); }
        .cat-item:not(.active) { background:rgba(0,0,0,0.2)!important; color:rgba(197,160,89,0.7)!important; -webkit-text-fill-color:rgba(197,160,89,0.7)!important; border-color:rgba(197,160,89,0.2)!important; }
        .auth-nav { position:absolute; top:50%; right:30px; transform:translateY(-50%); z-index:2000; }

        /* SAYFA */
        .profile-wrapper { max-width:860px; margin:0 auto; padding:40px 20px 80px; }
        .page-title { font-size:2.2rem; font-weight:900; letter-spacing:4px; text-transform:uppercase; background:linear-gradient(to right,#fff,var(--accent)); -webkit-background-clip:text; -webkit-text-fill-color:transparent; margin-bottom:36px; }

        /* ALERT */
        .alert { padding:12px 18px; border-radius:8px; margin-bottom:20px; font-weight:700; font-size:0.9rem; display:flex; align-items:center; gap:10px; }
        .alert.success { background:rgba(197,160,89,0.12); border:1px solid var(--accent); color:var(--accent); }
        .alert.error   { background:rgba(255,77,77,0.12);  border:1px solid #ff4d4d;      color:#ff4d4d; }

        /* KART */
        .profile-card { background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.09); border-radius:16px; padding:28px; margin-bottom:20px; transition:border-color 0.3s; }
        .profile-card:hover { border-color:rgba(197,160,89,0.25); }
        .card-title { font-size:0.9rem; font-weight:900; letter-spacing:2.5px; color:var(--accent); text-transform:uppercase; margin-bottom:22px; display:flex; align-items:center; gap:10px; }

        /* AVATAR */
        .avatar-section { display:flex; align-items:center; gap:28px; flex-wrap:wrap; }
        .avatar-img { width:96px; height:96px; border-radius:50%; object-fit:cover; border:3px solid var(--accent); box-shadow:0 0 20px rgba(197,160,89,0.25); }
        .avatar-placeholder { width:96px; height:96px; border-radius:50%; background:#111; border:3px solid rgba(197,160,89,0.25); display:flex; align-items:center; justify-content:center; font-size:2.4rem; color:rgba(197,160,89,0.4); }
        .avatar-btns { display:flex; flex-direction:column; gap:10px; }
        .btn-upload { display:inline-flex; align-items:center; gap:8px; padding:10px 22px; background:var(--accent); color:#000; border-radius:8px; font-weight:900; font-size:0.84rem; cursor:pointer; transition:0.3s; letter-spacing:1px; border:none; }
        .btn-upload:hover { opacity:0.82; transform:translateY(-2px); }
        .btn-danger { display:inline-flex; align-items:center; gap:8px; background:transparent; color:#ff4d4d; border:1px solid #ff4d4d; padding:10px 22px; border-radius:8px; font-weight:900; font-size:0.84rem; cursor:pointer; transition:0.3s; letter-spacing:1px; }
        .btn-danger:hover { background:rgba(255,77,77,0.12); }

        /* FORM */
        .form-group { display:flex; flex-direction:column; gap:12px; }
        .form-row { display:flex; gap:12px; flex-wrap:wrap; }
        .form-row input { flex:1; min-width:180px; }
        .profile-card input[type="text"],
        .profile-card input[type="password"] { width:100%; padding:12px 16px; background:#000; border:1px solid #2a2a2a; color:#fff; border-radius:8px; font-size:0.9rem; box-sizing:border-box; outline:none; transition:border-color 0.3s; font-family:inherit; }
        .profile-card input:focus { border-color:var(--accent); }
        .btn-primary { display:inline-flex; align-items:center; gap:8px; background:var(--accent); color:#000; border:none; padding:12px 26px; border-radius:8px; font-weight:900; font-size:0.88rem; cursor:pointer; transition:0.3s; letter-spacing:1px; align-self:flex-start; margin-top:4px; }
        .btn-primary:hover { opacity:0.85; transform:translateY(-2px); }

        /* YORUM LİSTESİ */
        .yorum-item { background:rgba(0,0,0,0.3); border:1px solid rgba(255,255,255,0.07); border-radius:10px; padding:16px 18px; margin-bottom:12px; transition:0.3s; }
        .yorum-item:hover { border-color:rgba(197,160,89,0.25); }
        .yorum-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; flex-wrap:wrap; gap:6px; }
        .yorum-page { background:rgba(197,160,89,0.12); border:1px solid rgba(197,160,89,0.2); color:var(--accent); font-size:0.75rem; font-weight:900; letter-spacing:1.5px; padding:3px 10px; border-radius:20px; }
        .yorum-date { color:rgba(255,255,255,0.28); font-size:0.78rem; }
        .yorum-body { color:rgba(255,255,255,0.72); font-size:0.9rem; line-height:1.6; margin:0; }
        .no-comments { color:rgba(255,255,255,0.28); font-style:italic; text-align:center; padding:32px 0; }
        .yorum-count { font-size:0.8rem; color:rgba(255,255,255,0.3); font-weight:400; letter-spacing:1px; margin-left:8px; }
    </style>
</head>
<body>

<?php render_nav(); ?>

<div class="profile-wrapper">
    <h1 class="page-title"><i class="fas fa-user"></i> Profilim</h1>

    <?php if ($basari): ?>
        <div class="alert success"><i class="fas fa-check-circle"></i> <?= $basari ?></div>
    <?php endif; ?>
    <?php if ($hata): ?>
        <div class="alert error"><i class="fas fa-exclamation-circle"></i> <?= $hata ?></div>
    <?php endif; ?>

    <!-- ── AVATAR ──────────────────────────────────────────────────────── -->
    <div class="profile-card">
        <div class="card-title"><i class="fas fa-image"></i> Profil Fotoğrafı</div>
        <div class="avatar-section">

            <?php if ($avatar && file_exists($avatar)): ?>
                <img src="<?= htmlspecialchars($avatar) ?>" alt="Avatar" class="avatar-img">
            <?php else: ?>
                <div class="avatar-placeholder"><i class="fas fa-user"></i></div>
            <?php endif; ?>

            <div class="avatar-btns">
                <!-- Yükleme: fopen/fwrite/fclose ile kaydediliyor -->
                <form method="POST" enctype="multipart/form-data">
                    <label class="btn-upload">
                        <i class="fas fa-upload"></i>
                        <?= $avatar && file_exists($avatar) ? "Değiştir" : "Fotoğraf Yükle" ?>
                        <input type="file" name="avatar" accept="image/*" style="display:none"
                               onchange="this.form.submit()">
                    </label>
                    <input type="hidden" name="avatar_yukle" value="1">
                </form>

                <?php if ($avatar && file_exists($avatar)): ?>
                <form method="POST">
                    <button type="submit" name="avatar_sil" class="btn-danger">
                        <i class="fas fa-trash"></i> Kaldır
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── NİCK DEĞİŞTİR ──────────────────────────────────────────────── -->
    <div class="profile-card">
        <div class="card-title"><i class="fas fa-pen"></i> Kullanıcı Adı Değiştir</div>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="yeni_nick"
                       value="<?= htmlspecialchars($currentUser) ?>"
                       placeholder="Yeni kullanıcı adı" maxlength="30" required>
                <button type="submit" name="nick_degistir" class="btn-primary">
                    <i class="fas fa-check"></i> Güncelle
                </button>
            </div>
        </form>
    </div>

    <!-- ── ŞİFRE DEĞİŞTİR ─────────────────────────────────────────────── -->
    <div class="profile-card">
        <div class="card-title"><i class="fas fa-lock"></i> Şifre Değiştir</div>
        <form method="POST">
            <div class="form-group">
                <input type="password" name="mevcut_sifre" placeholder="Mevcut şifre (ilk değişiklikte boş bırakılabilir)">
                <div class="form-row">
                    <input type="password" name="yeni_sifre"   placeholder="Yeni şifre"         required>
                    <input type="password" name="tekrar_sifre" placeholder="Yeni şifre (tekrar)" required>
                </div>
                <button type="submit" name="sifre_degistir" class="btn-primary">
                    <i class="fas fa-key"></i> Şifreyi Güncelle
                </button>
            </div>
        </form>
    </div>

    <!-- ── YORUMLARIM ─────────────────────────────────────────────────── -->
    <div class="profile-card">
        <div class="card-title">
            <i class="fas fa-comments"></i> Yorumlarım
            <span class="yorum-count">(<?= count($yorumlar) ?> yorum)</span>
        </div>

        <?php if (empty($yorumlar)): ?>
            <p class="no-comments">Henüz hiç yorum yapmadın.</p>
        <?php else: ?>
            <?php foreach ($yorumlar as $y): ?>
            <div class="yorum-item">
                <div class="yorum-meta">
                    <span class="yorum-page"><i class="fas fa-tag"></i> <?= htmlspecialchars($y['page']) ?></span>
                    <span class="yorum-date"><?= htmlspecialchars($y['tarih']) ?></span>
                </div>
                <p class="yorum-body"><?= nl2br(htmlspecialchars($y['yorum'])) ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="index.php" style="color:var(--accent);text-decoration:none;font-weight:700;">
        ← Ana Sayfaya Dön
    </a>
</div>

</body>
</html>
