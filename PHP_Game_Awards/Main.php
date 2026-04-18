<?php
// 1. DİNAMİK VERİ LİSTESİ
$tga_arsiv = [
    ["yil" => "2025", "link" => "/PHP_Game_Awards/2025TGA/2025TGA.php"],
    ["yil" => "2024", "link" => "/PHP_Game_Awards/2024TGA/2024TGA.php"],
    ["yil" => "2023", "link" => "/PHP_Game_Awards/2023TGA/2023TGA.php"],
    ["yil" => "2022", "link" => "/PHP_Game_Awards/2022TGA/2022TGA.php"],
    ["yil" => "2021", "link" => "/PHP_Game_Awards/2021TGA/2021TGA.php"],
    ["yil" => "2020", "link" => "/PHP_Game_Awards/2020TGA/2020TGA.php"],
    ["yil" => "2019", "link" => "/PHP_Game_Awards/2019TGA/2019TGA.php"],
    ["yil" => "2018", "link" => "/PHP_Game_Awards/2018TGA/2018TGA.php"],
    ["yil" => "2017", "link" => "/PHP_Game_Awards/2017TGA/2017TGA.php"]
];

// 2. FORM İŞLEME
$mesaj = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['giris_yap'])) {
    $kullanici = htmlspecialchars($_POST['kullanici_ad']);
    if (!empty($kullanici)) {
        $mesaj = "Hoş geldin, " . $kullanici . "!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TGA Archive | 2014-2025</title>
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0; padding: 0; line-height: 1.6;
        }

        /* NOISE EFEKTİ */
        body::before {
            content: ""; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0.04; z-index: 100; pointer-events: none;
            background-image: url('data:image/svg+xml,%3Csvg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg"%3E%3Cfilter id="n"%3E%3CfeTurbulence type="fractalNoise" baseFrequency="0.65"/%3E%3C/filter%3E%3Crect width="100%25" height="100%25" filter="url(%23n)"/%3E%3C/svg%3E');
        }

        /* SAĞ ÜST BUTON */
        .auth-nav { position: absolute; top: 25px; right: 30px; z-index: 2000; }
        .auth-btn {
            background: transparent; color: var(--accent); border: 1px solid var(--accent);
            padding: 10px 20px; border-radius: 50px; font-weight: 800; cursor: pointer;
            transition: 0.3s; letter-spacing: 1px; font-size: 0.8rem;
        }
        .auth-btn:hover { background: var(--accent); color: #000; box-shadow: 0 0 15px rgba(197, 160, 89, 0.3); }

        /* MODAL TASARIMI */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.9); backdrop-filter: blur(10px);
            display: none; justify-content: center; align-items: center; z-index: 9999;
        }

        .modal-box {
            background: var(--card-bg); padding: 40px; border-radius: 24px;
            border: 1px solid var(--accent); width: 380px; text-align: center;
            position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }

        .modal-box h2 { color: var(--accent); margin-bottom: 10px; letter-spacing: 2px; }
        
        /* EKLEDİĞİMİZ AÇIKLAMA METNİ */
        .modal-info {
            color: var(--text-dim); font-size: 0.85rem; margin-bottom: 25px;
            padding: 0 10px; line-height: 1.4;
        }

        .modal-box input {
            width: 100%; padding: 14px; margin-bottom: 15px;
            background: #000; border: 1px solid #333; color: #fff;
            border-radius: 10px; box-sizing: border-box; outline: none;
        }
        .modal-box input:focus { border-color: var(--accent); }

        .modal-box button {
            width: 100%; padding: 14px; background: var(--accent);
            border: none; font-weight: 900; border-radius: 10px; cursor: pointer;
            transition: 0.3s; margin-bottom: 20px;
        }
        .modal-box button:hover { opacity: 0.9; transform: translateY(-2px); }

        /* HESABINIZ YOK MU KISMI */
        .modal-footer { color: var(--text-dim); font-size: 0.85rem; border-top: 1px solid #222; padding-top: 20px; }
        .modal-footer a { color: var(--accent); text-decoration: none; font-weight: bold; }

        /* ANA TASARIM (BOYUTLAR AYNI TUTULDU) */
        .container { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }
        header { text-align: center; margin-bottom: 80px; }
        header h1 {
            font-size: 4rem; letter-spacing: 8px; text-transform: uppercase; font-weight: 900; margin-bottom: 15px;
            background: linear-gradient(to bottom, #C5A059 20%, #FFF3A0 50%, #C5A059 80%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 0 15px rgba(197, 160, 89, 0.4)); animation: shine 3s infinite linear;
        }
        @keyframes shine { 0%, 100% { filter: drop-shadow(0 0 10px rgba(197, 160, 89, 0.3)); } 50% { filter: drop-shadow(0 0 25px rgba(197, 160, 89, 0.6)); transform: scale(1.02); } }
        
        .category-nav { width: 100%; padding: 20px 0; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(197, 160, 89, 0.1); margin-bottom: 30px; position: sticky; top: 0; z-index: 1001; }
        .nav-container { max-width: 1000px; margin: 0 auto; display: flex; justify-content: center; gap: 30px; }
        .cat-item { color: rgba(197, 160, 89, 0.7); text-decoration: none; font-size: 0.95rem; font-weight: 800; letter-spacing: 2.5px; padding: 12px 24px; border-radius: 6px; border: 1px solid rgba(197, 160, 89, 0.2); transition: 0.4s; background: rgba(0, 0, 0, 0.2); }
        .cat-item:hover { color: #fff; border-color: var(--accent); background: rgba(197, 160, 89, 0.15); transform: translateY(-3px) scale(1.05); }

        .year-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
        .year-card { background: var(--card-bg); border-radius: 20px; padding: 50px 30px; text-align: center; border: 1px solid rgba(255, 255, 255, 0.03); position: relative; transition: all 0.4s ease; cursor: pointer; display: flex; flex-direction: column; justify-content: center; }
        .year-card:hover { transform: translateY(-10px); border-color: var(--accent); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5); }
        .year-card h2 { font-size: 3rem; margin: 0; }
    </style>
</head>
<body>

    <div class="auth-nav">
        <?php if($mesaj == ""): ?>
            <button class="auth-btn" onclick="openModal()">KAYIT OL / GİRİŞ YAP</button>
        <?php else: ?>
            <span style="color:var(--accent); font-weight:900;"><i class="fas fa-user-circle"></i> <?php echo $mesaj; ?></span>
        <?php endif; ?>
    </div>

    <div class="modal-overlay" id="loginModal">
        <div class="modal-box">
            <span onclick="closeModal()" style="position:absolute; top:15px; right:20px; cursor:pointer; color:var(--text-dim); font-size:24px;">&times;</span>
            
            <h2>GİRİŞ YAP</h2>
            <p class="modal-info">Etkileşimde bulunmak, anketlere ve etkinliklere katılmak için lütfen giriş yapınız.</p>
            
            <form method="POST">
                <input type="text" name="kullanici_ad" placeholder="Kullanıcı Adı" required>
                <input type="password" name="sifre" placeholder="Şifre" required>
                <button type="submit" name="giris_yap">OTURUM AÇ</button>
            </form>

            <div class="modal-footer">
                Hesabınız yok mu? <a href="/PHP_Game_Awards/Kayit_ol.php" rel="noopener noreferrer">Hemen Kayıt Olun</a>
            </div>
        </div>
    </div>

    <nav class="category-nav">
    <div class="nav-container">
        <a href="/PHP_Game_Awards/ActionTGA.php" rel="noopener noreferrer" class="cat-item">
            <i class="fas fa-sword"></i> AKSİYON
        </a>
        </a>
        <a href="/PHP_Game_Awards/RPG_TGA.php" class="cat-item" rel="noopener noreferrer"><i class="fas fa-magic"></i> RPG</a>
        <a href="/PHP_Game_Awards/IndieTGA.php" class="cat-item" rel="noopener noreferrer"><i class="fas fa-rocket"></i> INDIE</a>
        <a href="/PHP_Game_Awards/FightingTGA.php" class="cat-item" rel="noopener noreferrer"><i class="fas fa-chess"></i> DÖVÜŞ</a>
        <a href="/PHP_Game_Awards/NarrativeTGA.php" class="cat-item" rel="noopener noreferrer"><i class="fas fa-trophy"></i> HİKAYE</a>
    </div>
</nav>

    <div class="container">
        <header>
            <h1>The Game Awards</h1>
            <div style="width: 100px; height: 3px; background: var(--accent); margin: 20px auto; border-radius: 50px; box-shadow: 0 0 15px var(--accent);"></div>
            <p>Celebrating the Best in Gaming</p>
        </header>

        <main class="year-grid">
            <?php foreach ($tga_arsiv as $arsiv): ?>
                <a href="<?php echo $arsiv['link']; ?>" rel="noopener noreferrer" style="text-decoration: none; color: inherit;">
                    <div class="year-card">
                        <h2><?php echo $arsiv['yil']; ?> TGA Kazananları</h2>
                    </div>
                </a>
            <?php endforeach; ?>
        </main>
    </div>

    <script>
        const modal = document.getElementById('loginModal');
        function openModal() { modal.style.display = 'flex'; }
        function closeModal() { modal.style.display = 'none'; }
        // Dışarı tıklandığında kapansın
        window.onclick = function(e) { if (e.target == modal) closeModal(); }
        // Sayfa yüklendiğinde çalışır
    
        // Sayfa yüklendiğinde çalışır
        window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('login') === 'true') {
        // 1. Modalı aç
        openModal();
        
        // 2. URL'deki "?login=true" kısmını temizle (Sayfa yenilenmez, sadece adres çubuğu düzelir)
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({path: cleanUrl}, '', cleanUrl);
    }
};
    </script>
</body>
</html>