<?php
require dirname(__FILE__) . "/../includes/auth_nav.php";
$mesaj = ""; 
$hata  = ""; 

// JSON dosya yolu ayarlaması
$usersFile = dirname(__FILE__) . "/../storage/users/users.json";
if (!file_exists(dirname(__FILE__) . "/../storage/users")) mkdir(dirname(__FILE__) . "/../storage/users", 0755, true);
if (!file_exists($usersFile)) { file_put_contents($usersFile, json_encode([])); }

// JSON dosyasından kullanıcıları okuyan fonksiyon
function readUsersJson($file) {
    if (file_exists($file)) {
        $jsonData = file_get_contents($file);
        return json_decode($jsonData, true) ?? [];
    }
    return [];
}

// FORM POST EDİLDİĞİNDE (Formdaki buton ismi name="kayit_et" olduğu için burayı ona göre eşitledik)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['kayit_et'])) {
    // Form elemanlarının 'name' karşılıklarını eşitledik
    $kullanici = trim(htmlspecialchars($_POST['tam_ad'] ?? '')); // Formdaki name="tam_ad" alanını kullanıcı adı kabul ediyoruz
    $email     = trim(htmlspecialchars($_POST['email'] ?? ''));
    $sifre     = $_POST['sifre'] ?? '';
    $tekrar    = $_POST['sifre_tekrar'] ?? '';

    if (empty($kullanici) || empty($sifre) || empty($email)) {
        $hata = "Tüm alanları doldurun.";
    } elseif ($sifre !== $tekrar) {
        $hata = "Şifreler eşleşmiyor.";
    } elseif (strlen($sifre) < 6) {
        $hata = "Şifre en az 6 karakter olmalı.";
    } else {
        $users = readUsersJson($usersFile);
        
        if (isset($users[$kullanici])) {
            $hata = "Bu kullanıcı adı zaten alınmış.";
        } else {
            // Yeni kullanıcıyı düz metin formatında JSON yapısına uygun hazırlıyoruz
            $users[$kullanici] = [
                "password" => password_hash($sifre, PASSWORD_BCRYPT),
                "email" => $email,
                "role" => "user"
            ];
            
            // Verileri JSON dosyasına yazıyoruz
            if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
                $_SESSION['username'] = $kullanici;
                header("Location: /index.php?ok=kayit");
                exit();
            } else {
                $hata = "JSON dosyasına yazılırken bir hata oluştu.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol | TGA Archive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg: #0A0A0C;
            --card-bg: #161618;
            --accent: #C5A059; 
            --text-main: #FFFFFF;
            --text-dim: #888888;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            margin: 0; display: flex; justify-content: center; align-items: center;
            min-height: 100vh; overflow-x: hidden;
        }

        body::before {
            content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0.04; z-index: -1; pointer-events: none;
            background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="n"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="0.65"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23n)"/%3E%3C/svg%3E');
        }

        .register-container {
            background: var(--card-bg);
            padding: 50px;
            border-radius: 30px;
            border: 1px solid rgba(197, 160, 89, 0.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.6);
            transition: 0.3s;
        }

        .register-container:hover { border-color: var(--accent); }

        h1 {
            font-size: 2.5rem; letter-spacing: 4px; text-transform: uppercase;
            background: linear-gradient(to bottom, #C5A059 20%, #FFF3A0 50%, #C5A059 80%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .info-text {
            color: var(--text-dim);
            font-size: 0.95rem;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        .input-group { position: relative; margin-bottom: 20px; text-align: left; }
        
        .input-group i {
            position: absolute; left: 15px; top: 50%;
            transform: translateY(-50%); color: var(--accent);
        }

        input {
            width: 100%; padding: 15px 15px 15px 45px;
            background: #000; border: 1px solid #333;
            color: #fff; border-radius: 12px;
            box-sizing: border-box; font-size: 1rem;
            transition: 0.3s;
        }

        input:focus { border-color: var(--accent); outline: none; box-shadow: 0 0 10px rgba(197, 160, 89, 0.1); }

        .btn-register {
            width: 100%; padding: 16px; background: var(--accent);
            border: none; border-radius: 12px; font-weight: 900;
            font-size: 1rem; cursor: pointer; transition: 0.3s;
            margin-top: 10px; color: #000;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(197, 160, 89, 0.3);
        }

        .back-link {
            display: inline-block; margin-top: 25px;
            color: var(--text-dim); text-decoration: none;
            font-size: 0.9rem; transition: 0.3s;
        }

        .back-link:hover { color: var(--accent); }

        .success-msg {
            background: rgba(197, 160, 89, 0.1);
            color: var(--accent);
            padding: 15px; border-radius: 10px;
            margin-bottom: 20px; font-size: 0.9rem;
            border: 1px solid var(--accent);
        }
    </style>
</head>
<body>

<div class="register-container">
    <a href="/index.php" rel="noopener noreferrer" class="back-link" style="margin-bottom: 20px; display: block;">
        <i class="fas fa-arrow-left"></i> Ana Sayfaya Dön
    </a>

    <h1>KATIL</h1>
    <p class="info-text">Etkileşimde bulunmak ve etkinliklere katılmak için aramıza katılın!</p>

    <?php if($hata != ""): ?>
        <div style="background: rgba(255, 0, 0, 0.1); color: #ff4d4d; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #ff4d4d;">
            <?php echo $hata; ?>
        </div>
    <?php endif; ?>

    <?php if($mesaj != ""): ?>
        <div class="success-msg"><?php echo $mesaj; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="tam_ad" placeholder="Adınız Soyadınız" required>
        </div>

        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="E-posta Adresiniz" required>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="sifre" placeholder="Şifre Oluşturun" required>
        </div>

        <div class="input-group">
            <i class="fas fa-shield-alt"></i>
            <input type="password" name="sifre_tekrar" placeholder="Şifreyi Yeniden Yazın" required>
        </div>

        <button type="submit" name="kayit_et" class="btn-register">KAYDI TAMAMLA</button>
    </form>

    <p style="color: var(--text-dim); font-size: 0.8rem; margin-top: 30px;">
        Zaten üye misiniz? <a href="/index.php?login=true" rel="noopener noreferrer">Giriş Yap</a>
    </p>
</div>

</body>
</html>
