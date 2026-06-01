<?php
/**
 * TGA Portal - 2019 Winners Page
 * PHP Architecture - Internationalized & Optimized
 */
require dirname(__FILE__) . "/../auth_nav.php";

$mesaj = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login_submit'])) {
    $kullanici = trim(htmlspecialchars($_POST['username'] ?? ''));
    $sifre     = trim($_POST['password'] ?? '');
    
    // Alt klasörlerdeki sayfalar için (2025TGA/, 2024TGA/ vb.) bir üst klasördeki users'a bakar:
    $usersFile = __DIR__ . "/../users/users.json"; 

    $girisHata = "";
    if (!empty($kullanici) && !empty($sifre)) {
        
        // JSON dosyasını oku
        if (file_exists($usersFile)) {
            $jsonData = file_get_contents($usersFile);
            $users = json_decode($jsonData, true) ?? [];
        } else {
            $users = [];
        }

        // Kullanıcı var mı ve şifre JSON'daki şifreyle birebir aynı mı?
        if (isset($users[$kullanici]) && $sifre === $users[$kullanici]['password']) {
            $_SESSION['username'] = $kullanici;
            
            // PRG: Sayfa yenilendiğinde formun tekrar gönderilmesini engeller
            header("Location: " . $_SERVER['PHP_SELF'] . "?giris=ok");
            exit();
        } else {
            $girisHata = "Kullanıcı adı veya şifre yanlış.";
        }
    }
}
// 1. HELPER FUNCTIONS: Determines Metacritic CSS class based on score
function getMetascoreClass($score) {
    if ($score >= 90) {
        return 'bg-blue';   // 90+ Blue
    } elseif ($score >= 80) {
        return 'bg-green';  // 80-89 Green
    } else {
        return 'bg-yellow'; // Below 80 Yellow
    }
}

// 2. DATA CONFIGURATION: 2019 Winners
$current_year = "2019";

// Game of the Year (GOTY) Data
$goty = [
    "title" => "Sekiro: Shadows Die Twice",
    "dev_logo" => "Sekiro Logo.png",
    "dev_name" => "FromSoftware",
    "score" => 90,
    "image" => "Sekiro3.jpg",
    "video" => "Sekiro.mp4",
    "link" => "https://www.sekirothegame.com/",
    "extra_awards" => [
        "Best Action/Adventure Game"
    ]
];

// Other Categories Grid Data
$categories = [
    [
        "title" => "Best Action Game",
        "winner" => "Devil May Cry 5",
        "dev_logo" => "devil my cry logo.png",
        "dev_name" => "Capcom",
        "score" => 87,
        "image" => "devil my cry 5 .jpg",
        "link" => "https://www.devilmaycry.com/5/"
    ],
    [
        "title" => "Best Fighting Game",
        "winner" => "Super Smash Bros. Ultimate",
        "dev_logo" => "SSB Ultimate.png",
        "dev_name" => "Bandai Namco / Nintendo",
        "score" => 93,
        "image" => "super-smash-btros-ultimate.webp",
        "link" => "https://www.smashbros.com/en_GB/"
    ],
    [
        "title" => "Best Multiplayer Game",
        "winner" => "Apex Legends",
        "dev_logo" => "Apex LOGO.jpeg",
        "dev_name" => "Respawn Entertainment / EA",
        "score" => 89,
        "image" => "apex.jpg",
        "link" => "https://www.ea.com/games/apex-legends/apex-legends"
    ],
    [
        "title" => "Best Audio Design",
        "winner" => "Call of Duty: Modern Warfare",
        "dev_logo" => "COD logopng.png",
        "dev_name" => "Activision",
        "score" => 80,
        "image" => "CODMW.webp",
        "link" => "https://www.callofduty.com/modernwarfare"
    ],
    [
        "title" => "Best Ongoing Game",
        "winner" => "Fortnite",
        "dev_logo" => "Fortnite LOGO.png",
        "dev_name" => "Epic Games",
        "score" => 78,
        "image" => "Fortnite.jpg",
        "link" => "https://www.fortnite.com/"
    ],
    [
        "title" => "Best Mobile Game",
        "winner" => "Call of Duty: Mobile",
        "dev_logo" => "COD logopng.png",
        "dev_name" => "Activision",
        "score" => 81,
        "image" => "cod mobile.webp",
        "link" => "https://www.callofduty.com/mobile"
    ],
    [
        "title" => "Best Role Playing Game (RPG)",
        "winner" => "Tales of Arise",
        "dev_logo" => "Tekken8_logo.png",
        "dev_name" => "Bandai Namco",
        "score" => 87,
        "image" => "tales of arise.jpg",
        "link" => "https://www.bandainamcoent.com/games/tales-of-arise"
    ],
    [
        "title" => "Best Strategy Game",
        "winner" => "Fire Emblem: Three Houses",
        "dev_logo" => "fire emblem LOGO.jpeg",
        "dev_name" => "Koei Tecmo / Nintendo",
        "score" => 89,
        "image" => "fire emblem.avif",
        "link" => "https://www.nintendo.com/games/detail/fire-emblem-three-houses-switch/"
    ],
    [
        "title" => "Best Game Direction + Performance + Music",
        "winner" => "Death Stranding",
        "dev_logo" => "Death Stranding.jpeg",
        "dev_name" => "Kojima Productions",
        "score" => 82,
        "image" => "Death Stranding.jpeg",
        "link" => "https://www.kojimaproductions.jp/en/death-stranding"
    ],
    [
        "title" => "Best Narrative + Indie + RPG + Debut Indie",
        "winner" => "Disco Elysium",
        "dev_logo" => "Disco Elysium LOGO.png",
        "dev_name" => "ZA/UM Studio",
        "score" => 89,
        "image" => "Disco Elysium.jpg",
        "link" => "https://discoelysium.com/"
    ]
];

$years_nav = ["2025", "2024", "2023", "2022", "2021", "2020", "2019", "2018", "2017"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../TGA_CSS.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title><?php echo $current_year; ?> Winners | TGA Portal</title>
    <meta name="keywords" content="The Game Awards 2019, Sekiro Shadows Die Twice GOTY, TGA Winners 2019">
    <meta name="description" content="Complete list of winners for The Game Awards 2019.">
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
        <?php foreach ($years_nav as $year): ?>
            <a href="../<?php echo $year; ?>TGA/<?php echo $year; ?>TGA.php" 
               class="year-link <?php echo ($year == $current_year) ? 'active' : ''; ?>" 
               rel="noopener noreferrer">
               <?php echo $year; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="detail-container">
        <a href="../Main.php" style="color: var(--accent); text-decoration: none; font-weight: bold;" rel="noopener noreferrer">
            ← Back to Home
        </a>

        <header style="text-align: left; margin-bottom: 40px; margin-top: 20px;">
            <h1 style="font-size: 2.5rem; letter-spacing: 2px;"><?php echo $current_year; ?> THE GAME AWARDS</h1>
            <p>Full list of winners and highlights from the ceremony.</p> 
        </header>

        <section style="margin-bottom: 50px;">
            <h2 style="color: var(--accent); font-size: 1.2rem; opacity: 0.8; text-transform: uppercase;">Game of the Year</h2>
            <h3 class="game-title-highlight"><?php echo $goty['title']; ?></h3>
            
            <div class="goty-stats">
                <div class="metacritic-badge <?php echo getMetascoreClass($goty['score']); ?>" title="Metascore">
                    <?php echo $goty['score']; ?>
                </div>
                <div class="dev-section">
                    <img src="<?php echo $goty['dev_logo']; ?>" class="dev-logo" alt="studio">
                    <span class="dev-name"><?php echo $goty['dev_name']; ?></span>
                </div>
            </div>

            <div class="media-row">
                <img src="<?php echo $goty['image']; ?>" class="game-main-img" alt="Main Game Cover">
                <div class="video-section">
                    <div class="video-wrapper">
                        <video controls style="width: 100%; height: 100%;">
                            <source src="<?php echo $goty['video']; ?>" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>

            <div class="achievements-section">
                <h4 class="achievements-title">OTHER AWARDS WON</h4>
                <div class="achievements-list">
                    <?php foreach ($goty['extra_awards'] as $award): ?>
                        <div class="achievement-badge">
                            <span class="badge-icon">🏆</span>
                            <span class="badge-text"><?php echo $award; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <a href="<?php echo $goty['link']; ?>" target="_blank" rel="noopener noreferrer" class="panel-btn">OFFICIAL WEBSITE</a>
            </div>
        </section>

        <h2 style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Other Categories</h2>
        
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <div class="category-item" onclick="toggleCategory(this)">
                    <span class="category-name"><?php echo $cat['title']; ?></span>
                    <span class="winner-name"><?php echo $cat['winner']; ?></span>
            
                    <div class="category-panel">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div class="dev-section">
                                <img src="<?php echo $cat['dev_logo']; ?>" class="dev-logo" alt="developer">
                                <span class="dev-name"><?php echo $cat['dev_name']; ?></span>
                            </div>
                            <div class="metacritic-badge <?php echo getMetascoreClass($cat['score']); ?>" style="width: 35px; height: 35px; font-size: 1rem;">
                                <?php echo $cat['score']; ?>
                            </div>
                        </div>

                        <img src="<?php echo $cat['image']; ?>" class="panel-img" alt="winner thumbnail">
                        <a href="<?php echo $cat['link']; ?>" target="_blank" rel="noopener noreferrer" class="panel-btn">OFFICIAL WEBSITE</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
        $pageKey = "2019";
        include "../comments_widget.php";
    ?>

    <footer style="margin-top: 80px; padding: 40px; border-top: 1px solid rgba(197, 160, 89, 0.1); color: var(--text-dim); font-size: 0.8rem; text-align: center;">
        <p>&copy; <?php echo $current_year; ?> TGA Portal. All rights reserved.</p>
    </footer>

    <script>
        
    const modal = document.getElementById('loginModal');
    function openModal() { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    window.onclick = function(e) { if (e.target == modal) closeModal(); }   
     
    function toggleCategory(element) {
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