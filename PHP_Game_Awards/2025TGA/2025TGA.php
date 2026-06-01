<?php

require dirname(__FILE__) . "/../auth_nav.php";
// ... geri kalan kodlar
                                
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login_submit'])) {
    $kullanici = trim(htmlspecialchars($_POST['username'] ?? ''));
    $sifre     = trim($_POST['password'] ?? ''); // Gelen şifrenin boşluklarını temizle
    $usersFile = __DIR__ . "/../users/users.json"; // JSON dosya yolun

    $girisHata = "";
    if (!empty($kullanici) && !empty($sifre)) {
        
        // JSON dosyasını oku ve PHP dizisine çevir
        if (file_exists($usersFile)) {
            $jsonData = file_get_contents($usersFile);
            $users = json_decode($jsonData, true) ?? [];
        } else {
            $users = [];
        }

        // Kullanıcı var mı ve şifresi eşleşiyor mu kontrolü
        if (isset($users[$kullanici]) && $sifre === $users[$kullanici]['password']) {
            $_SESSION['username'] = $kullanici;
            
            // PRG: POST sonrası GET'e yönlendir
            header("Location: " . $_SERVER['PHP_SELF'] . "?giris=ok");
            exit();
        } else {
            $girisHata = "Kullanıcı adı veya şifre yanlış.";
        }
    }
}

// GET: giriş başarı mesajı
if (isset($_GET['giris']) && $_GET['giris'] === 'ok') {
    $girisBasari = "Giriş başarılı, hoş geldin!";
}
// 1. FUNCTIONS: Automatically determines CSS class based on Metascore
function getMetascoreClass($score) {
    if ($score >= 90) {
        return 'bg-blue';   // 90+ Blue (#00bbff)
    } elseif ($score >= 80) {
        return 'bg-green';  // 80-89 Green (#66cc33)
    } else {
        return 'bg-yellow'; // Below 80 Yellow (#ffcc33)
    }
}

// 2. DATA STRUCTURE (Arrays): Managing Categories
$categories = [
    [
        "title" => "Best Action/Adventure Game",
        "winner" => "Hollow Knight: Silksong",
        "dev_logo" => "Team Cherry.png",
        "dev_name" => "Team Cherry",
        "score" => 90,
        "image" => "Silksong.jpg",
        "link" => "https://www.teamcherry.com.au/"
    ],
    [
        "title" => "Best Fighting Game",
        "winner" => "Fatal Fury: City Of The Wolves",
        "dev_logo" => "SNK_Logo.png",
        "dev_name" => "SNK Games",
        "score" => 80,
        "image" => "Fatal_Fury_City_of_the_Wolves.jpg",
        "link" => "https://www.snk-corp.co.jp/us/"
    ],
    [
        "title" => "Best Adaptation",
        "winner" => "The Last Of Us",
        "dev_logo" => "Noughty-Dog1.jpeg",
        "dev_name" => "Naughty Dog",
        "score" => 85,
        "image" => "TLOU .jpg",
        "link" => "https://www.imdb.com/title/tt3581920/"
    ],
    [
        "title" => "Best Action Game",
        "winner" => "Hades 2",
        "dev_logo" => "SuperGiantGames.png",
        "dev_name" => "Super Giant Games",
        "score" => 95,
        "image" => "Hades2.jpg",
        "link" => "https://www.supergiantgames.com/"
    ],
    [
        "title" => "Best Ongoing Game",
        "winner" => "No Man's Sky",
        "dev_logo" => "HelloGames Logo.jpeg",
        "dev_name" => "Hello Games",
        "score" => 71,
        "image" => "No Mans Sky.jpg",
        "link" => "https://www.nomanssky.com/"
    ],
    [
        "title" => "Best Mobile Game",
        "winner" => "Umamasume: Pretty Derby",
        "dev_logo" => "Cygames.jpeg",
        "dev_name" => "Cygames",
        "score" => 84,
        "image" => "UmamasumePretty Derby.jpeg",
        "link" => "https://www.cygames.co.jp/en/"
    ],
    [
        "title" => "Best Multiplayer Game",
        "winner" => "Arc Raiders",
        "dev_logo" => "Embark.png",
        "dev_name" => "Embark Games",
        "score" => 86,
        "image" => "Arc Raiders.jpeg",
        "link" => "https://www.embark-studios.com/"
    ]
];

$years = ["2025", "2024", "2023", "2022", "2021", "2020", "2019", "2018", "2017"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2025 Winners | TGA Portal</title>
    <link rel="stylesheet" href="../TGA_CSS.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="keywords" content="The Game Awards 2025, Expedition 33 GOTY, TGA 2025 Winners">
    <meta name="description" content="The Game Awards 2025 winners and details for Game of the Year: Clair Obscur: Expedition 33.">
</head>
<body>
<?php render_nav("../"); ?>


<div class="modal-overlay" id="loginModal">
    <div class="modal-box">
        <span onclick="closeModal()" style="position:absolute; top:15px; right:20px; cursor:pointer; color:var(--text-dim); font-size:24px;">&times;</span>
        
        <h2>LOGIN</h2>
        <p class="modal-info">Please login to interact, participate in polls and events.</p>

        <?php include "../modal_alert.php"; ?>  

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login_submit">SIGN IN</button>
        </form>

        <div class="modal-footer" style="color: var(--text-dim); font-size: 0.85rem; border-top: 1px solid #222; padding-top: 20px;">
            Don't have an account? <a href="/PHP_Game_Awards/Register.php" style="color: var(--accent); text-decoration: none; font-weight: bold;">Register Now</a>
        </div>
    </div>
</div>



    <nav class="side-nav">
        <?php foreach($years as $year): ?>
            <a href="../<?php echo $year; ?>TGA/<?php echo $year; ?>TGA.php" 
               class="year-link <?php echo ($year == '2025') ? 'active' : ''; ?>" 
               title="<?php echo $year; ?>"><?php echo $year; ?></a>
        <?php endforeach; ?>
    </nav>

    <div class="detail-container">
        <a href="../Main.php" rel="noopener noreferrer" style="color: var(--accent); text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-chevron-left"></i> Back to Home
        </a>

        <header style="text-align: left; margin-bottom: 40px; margin-top: 20px;">
            <h1 style="font-size: 2.5rem; letter-spacing: 2px;">2025 THE GAME AWARDS</h1>
            <p>The best games of the year and full winners list.</p> 
        </header>

        <section style="margin-bottom: 50px;">
            <h2 style="color: var(--accent); font-size: 1.2rem; opacity: 0.8; text-transform: uppercase;">Game of the Year</h2>
            <h3 class="game-title-highlight">Clair Obscur: Expedition 33</h3>
            
            <div class="goty-stats">
                <div class="metacritic-badge <?php echo getMetascoreClass(92); ?>" title="Metascore">92</div>
                <div class="dev-section">
                    <img src="SandfallLogo.jpeg" alt="Sandfall" class="dev-logo">
                    <span class="dev-name">Sandfall Interactive</span>
                </div>
            </div>

            <div class="media-row">
                <img src="Expediton0.jpg" class="game-main-img" alt="Expedition 33">
                <div class="video-section">
                    <div class="video-wrapper">
                        <video controls style="width: 100%; height: 100%;">
                            <source src="Expedition.mp4" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>

            <div class="achievements-section">
                <h4 class="achievements-title">ADDITIONAL AWARDS WON</h4>
                <div class="achievements-list">
                    <?php 
                    $awards = ["Best RPG", "Best Art Direction", "Best Narrative", "Best Game Direction", "Best Performance", "Best Score & Music", "Best Independent Game"];
                    foreach($awards as $award): ?>
                        <div class="achievement-badge">
                            <span class="badge-icon">🏆</span>
                            <span class="badge-text"><?php echo $award; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <a href="https://www.expedition33.com/" target="_blank" rel="noopener noreferrer" class="panel-btn" style="display: block; text-align: center;">OFFICIAL WEBSITE</a>
            </div>
        </section>

        <h2 style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Other Categories</h2>
        
        <div class="category-grid">
            <?php foreach($categories as $category): ?>
                <div class="category-item" onclick="togglePanel(this)">
                    <span class="category-name"><?php echo $category['title']; ?></span>
                    <span class="winner-name"><?php echo $category['winner']; ?></span>
            
                    <div class="category-panel">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div class="dev-section">
                                <img src="<?php echo $category['dev_logo']; ?>" class="dev-logo" alt="logo">
                                <span class="dev-name"><?php echo $category['dev_name']; ?></span>
                            </div>
                            <div class="metacritic-badge <?php echo getMetascoreClass($category['score']); ?>" style="width: 35px; height: 35px; font-size: 1rem;">
                                <?php echo $category['score']; ?>
                            </div>
                        </div>

                        <img src="<?php echo $category['image']; ?>" class="panel-img" alt="game cover">
                        <a href="<?php echo $category['link']; ?>" target="_blank" rel="noopener noreferrer" class="panel-btn">OFFICIAL WEBSITE</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
        $pageKey = "2025";
        include "../comments_widget.php";
    ?>

    <footer style="margin-top: 80px; padding: 40px; border-top: 1px solid rgba(197, 160, 89, 0.1); color: var(--text-dim); font-size: 0.8rem; text-align: center;">
        <p>&copy; <?php echo date("Y"); ?> TGA Portal. All rights reserved.</p>
    </footer>
    
    <script>
    /**
     * Accordion (Toggle Panel) Function
     */

    const modal = document.getElementById('loginModal');
    function openModal() { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    window.onclick = function(e) { if (e.target == modal) closeModal(); }

    function togglePanel(element) {
        if (element.classList.contains('active')) {
            element.classList.remove('active');
            return;
        }
        document.querySelectorAll('.category-item').forEach(item => {
            item.classList.remove('active');
        });
        element.classList.add('active');
    }
    </script>
</body>
</html>