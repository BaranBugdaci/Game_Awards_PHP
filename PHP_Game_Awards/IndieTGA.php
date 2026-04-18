<?php
session_start(); // ← EN ÜSTE alındı

function getScoreClass($score) {
    if ($score >= 90) return 'bg-blue';
    if ($score >= 80) return 'bg-green';
    return 'bg-yellow';
}

$mesaj = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $kullanici = htmlspecialchars($_POST['username']);
    if (!empty($kullanici)) {
        $_SESSION['username'] = $kullanici;
        $mesaj = "Welcome, " . $_SESSION['username'] . "!";
    }
}

$games = [
    ["year" => "2025", "title" => "Expedition 33",           "score" => 92, "img" => "2025TGA/Expediton0.jpg"],
    ["year" => "2024", "title" => "Balatro",                 "score" => 90, "img" => "2024TGA/Balatro.jpg"],
    ["year" => "2023", "title" => "Sea of Stars",            "score" => 87, "img" => "2023TGA/Sea of Stars.jpeg"],
    ["year" => "2022", "title" => "Stray",                   "score" => 83, "img" => "2022TGA/Stray.jpeg"],
    ["year" => "2021", "title" => "Kena: Bridge of Spirits", "score" => 81, "img" => "2021TGA/Kena.jpg"],
    ["year" => "2020", "title" => "Hades",                   "score" => 93, "img" => "2020TGA/Hades.jpg"],
    ["year" => "2019", "title" => "Disco Elysium",           "score" => 89, "img" => "2019TGA/Disco Elysium.jpg"],
    ["year" => "2018", "title" => "Celeste",                 "score" => 92, "img" => "2018TGA/Celeste.jpeg"],
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Indie Kategorisi - TGA Arşivi</title>
    <link rel="stylesheet" href="TGA_CSS.css">
    <style>
        .auth-nav { position: absolute; top: 25px; right: 30px; z-index: 2000; }
        .auth-btn { background: transparent; color: var(--accent); border: 1px solid var(--accent); padding: 10px 20px; border-radius: 50px; font-weight: 800; cursor: pointer; transition: 0.3s; letter-spacing: 1px; font-size: 0.8rem; }
        .auth-btn:hover { background: var(--accent); color: #000; box-shadow: 0 0 15px rgba(197,160,89,0.3); }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); backdrop-filter: blur(10px); display: none; justify-content: center; align-items: center; z-index: 9999; }
        .modal-box { background: var(--card-bg); padding: 40px; border-radius: 24px; border: 1px solid var(--accent); width: 380px; text-align: center; position: relative; box-shadow: 0 25px 50px rgba(0,0,0,0.5); }
        .modal-box h2 { color: var(--accent); margin-bottom: 10px; letter-spacing: 2px; }
        .modal-box input { width: 100%; padding: 14px; margin-bottom: 15px; background: #000; border: 1px solid #333; color: #fff; border-radius: 10px; box-sizing: border-box; outline: none; }
        .modal-box input:focus { border-color: var(--accent); }
        .modal-box button { width: 100%; padding: 14px; background: var(--accent); border: none; font-weight: 900; border-radius: 10px; cursor: pointer; transition: 0.3s; margin-bottom: 20px; }
        body { background-color: #0a0a0a; padding-top: 80px; }
        .action-header { text-align: center; padding: 40px 20px 60px 20px; background: linear-gradient(to bottom, rgba(139,0,0,0.25), transparent); margin-top: -20px; }
        .timeline { position: relative; max-width: 900px; margin: 0 auto; padding: 40px 0; }
        .timeline::before { content: ''; position: absolute; left: 50px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, transparent, var(--accent), transparent); }
        .event { position: relative; margin-bottom: 50px; padding-left: 100px; }
        .event-year { position: absolute; left: 25px; top: 0; width: 50px; height: 50px; background: #1a1a1a; border: 2px solid var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--accent); box-shadow: 0 0 15px rgba(197,160,89,0.3); z-index: 2; }
        .action-card { background: rgba(255,255,255,0.03); border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); display: flex; transition: 0.4s; overflow: hidden; }
        .action-card:hover { transform: translateX(10px); border-color: #ff4d4d; background: rgba(255,77,77,0.05); }
        .action-card img { width: 200px; height: auto; object-fit: cover; }
        .action-content { padding: 20px; flex: 1; }
        .action-content h3 { margin: 0 0 10px 0; color: #fff; font-size: 1.5rem; }
        .category-nav { width: 100%; padding: 20px 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(197,160,89,0.1); position: fixed; top: 0; left: 0; z-index: 1001; }
        .category-title { font-size: 3.5rem; font-weight: 900; letter-spacing: 5px; margin-bottom: 10px; background: linear-gradient(to right, #fff, var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase; }
        .nav-container { max-width: 1000px; margin: 0 auto; display: flex; justify-content: center; gap: 30px; }
        .cat-item { color: rgba(197,160,89,0.7); text-decoration: none; font-size: 0.95rem; font-weight: 800; letter-spacing: 2.5px; padding: 12px 24px; border-radius: 6px; border: 1px solid rgba(197,160,89,0.2); transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275); opacity: 0.9; background: rgba(0,0,0,0.2); }
        .cat-item:hover { color: #fff; border-color: var(--accent); background: rgba(197,160,89,0.15); opacity: 1; transform: translateY(-3px) scale(1.05); box-shadow: 0 8px 20px rgba(197,160,89,0.2); }
        .cat-item.active { color: #000 !important; -webkit-text-fill-color: #000 !important; border-color: var(--accent) !important; background: var(--accent) !important; opacity: 1 !important; }
        .cat-item:not(.active) { background: rgba(0,0,0,0.2) !important; color: rgba(197,160,89,0.7) !important; -webkit-text-fill-color: rgba(197,160,89,0.7) !important; border-color: rgba(197,160,89,0.2) !important; }
    </style>
</head>
<body>

<div class="auth-nav">
    <?php if (!isset($_SESSION['username'])): ?>
        <button class="auth-btn" onclick="openModal()">SIGN UP / LOGIN</button>
    <?php else: ?>
        <span style="color:var(--accent); font-weight:900;">
            <i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
        </span>
    <?php endif; ?>
</div>

<div class="modal-overlay" id="loginModal">
    <div class="modal-box">
        <span onclick="closeModal()" style="position:absolute; top:15px; right:20px; cursor:pointer; color:var(--text-dim); font-size:24px;">&times;</span>
        <h2>LOGIN</h2>
        <p class="modal-info">Please login to interact, participate in polls and events.</p>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login_submit">SIGN IN</button>
        </form>
        <div class="modal-footer" style="color:var(--text-dim); font-size:0.85rem; border-top:1px solid #222; padding-top:20px;">
            Don't have an account? <a href="/PHP_Game_Awards/Register.php" style="color:var(--accent); text-decoration:none; font-weight:bold;">Register Now</a>
        </div>
    </div>
</div>

<nav class="category-nav">
    <div class="nav-container">
        <a href="ActionTGA.php"   class="cat-item"        rel="noopener noreferrer"><i class="fas fa-sword"></i>  AKSİYON</a>
        <a href="RPG_TGA.php"     class="cat-item"        rel="noopener noreferrer"><i class="fas fa-magic"></i>  RPG</a>
        <a href="IndieTGA.php"    class="cat-item active" rel="noopener noreferrer"><i class="fas fa-rocket"></i> INDIE</a>
        <a href="FightingTGA.php" class="cat-item"        rel="noopener noreferrer"><i class="fas fa-chess"></i>  DÖVÜŞ</a>
        <a href="NarrativeTGA.php" class="cat-item"       rel="noopener noreferrer"><i class="fas fa-trophy"></i> HİKAYE</a>
    </div>
</nav>

<div class="action-header">
    <h1 class="category-title">INDIE OYUNLAR KATEGORİSİ</h1>
    <p>Yıllara göre türün en iyi temsilcileri</p>
</div>

<div class="detail-container">
    <div style="max-width:900px; margin:0 auto; padding-left:20px;">
        <a href="Main.php" style="color:var(--accent); text-decoration:none;" rel="noopener noreferrer">← Ana Sayfaya Dön</a>
    </div>
    <div class="timeline">
        <?php foreach ($games as $game): ?>
        <div class="event">
            <div class="event-year"><?php echo htmlspecialchars($game['year']); ?></div>
            <div class="action-card">
                <img src="<?php echo htmlspecialchars($game['img']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>">
                <div class="action-content">
                    <h3><?php echo htmlspecialchars($game['title']); ?></h3>
                    <div class="metacritic-badge <?php echo getScoreClass($game['score']); ?>">
                        <?php echo htmlspecialchars($game['score']); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const modal = document.getElementById('loginModal');
    function openModal()  { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    window.onclick = function(e) { if (e.target == modal) closeModal(); }
</script>
</body>
</html>