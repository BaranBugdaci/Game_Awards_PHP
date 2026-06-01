<?php
/**
 * TGA Portal - 2023 Winners Page (English Version)
 * Optimized for Global Deployment
 */
require dirname(__FILE__) . "/../../includes/auth_nav.php";

$mesaj = "";

// ORTAK JSON GİRİŞ KODU
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

// Giriş başarılı uyarısı için değişken kontrolü
$girisBasari = "";
if (isset($_GET['giris']) && $_GET['giris'] === 'ok') {
    $girisBasari = "Giriş başarılı, hoş geldin!";
}
// 1. FUNCTIONS: Determine CSS class based on Metascore
function get_metascore_class($score) {
    if ($score >= 90) {
        return 'bg-blue';   // 90+ Blue (Universal Excellence)
    } elseif ($score >= 80) {
        return 'bg-green';  // 80-89 Green (Great)
    } else {
        return 'bg-yellow'; // Below 80 Yellow (Mixed/Average)
    }
}

// 2. DATA STRUCTURE: 2023 Winners
$current_year = "2023";

// GOTY (Game of the Year) Data
$goty_details = [
    "title" => "Baldur’s Gate 3",
    "dev_logo" => "Baldur’s Gate 3_Logo.jpg",
    "dev_name" => "Larian Studios",
    "score" => 96,
    "image" => "Baldur’s Gate 3.jpeg",
    "video" => "Baldur's Gate 3_ Launch Trailer.mp4",
    "official_site" => "https://baldursgate3.game/",
    "additional_awards" => [
        "Best Role Playing Game",
        "Best Community Support",
        "Best Multiplayer Game",
        "Best Performance"
    ]
];

// Other Categories List
$categories = [
    [
        "category" => "Best Narrative + Art Direction + Game Direction",
        "winner" => "Alan Wake 2",
        "dev_logo" => "AlanWake_Logo.png",
        "dev_name" => "Remedy Entertainment",
        "score" => 89,
        "image" => "Alan_Wake_2.jpeg",
        "link" => "https://www.remedygames.com/games/alan-wake-2"
    ],
    [
        "category" => "Best Score and Music",
        "winner" => "Final Fantasy VII Rebirth",
        "dev_logo" => "FinalFantazy Logo.png",
        "dev_name" => "Square Enix",
        "score" => 87,
        "image" => "FF17.jpg",
        "link" => "https://ffvii.square-enix-games.com/en-us/games/rebirth"
    ],
    [
        "category" => "Best Audio Design",
        "winner" => "Hi-Fi Rush",
        "dev_logo" => "Hifi Logo.png",
        "dev_name" => "Tango Game Works",
        "score" => 87,
        "image" => "HİFİ Rush.jpeg",
        "link" => "https://tangogameworks.com/2025/11/11/the-tango-way-of-game-creation-hi-fi-rush-dev-interview/"
    ],
    [
        "category" => "Games for Impact",
        "winner" => "Tchia",
        "dev_logo" => "Tchia_Logo.jpeg",
        "dev_name" => "Awaceb Games",
        "score" => 77,
        "image" => "Tchia.jpeg",
        "link" => "https://www.awaceb.com/tchia"
    ],
    [
        "category" => "Best Ongoing Game",
        "winner" => "Cyberpunk 2077",
        "dev_logo" => "CyberPunk_logo.jpeg",
        "dev_name" => "CD Projekt Red",
        "score" => 86,
        "image" => "cyberpunk2077.jpg",
        "link" => "https://www.cyberpunk.net/us/en/"
    ],
    [
        "category" => "Best Independent Game",
        "winner" => "Sea of Stars",
        "dev_logo" => "Sea of stars_Logo.png",
        "dev_name" => "Sabotage Studio",
        "score" => 87,
        "image" => "Sea of Stars.jpeg",
        "link" => "https://sabotagestudio.com/presskits/sea-of-stars/"
    ],
    [
        "category" => "Best Debut Indie Game",
        "winner" => "Cocoon",
        "dev_logo" => "Cocoon LOGO.png",
        "dev_name" => "Geometric Interactive",
        "score" => 88,
        "image" => "Cocoon.jpeg",
        "link" => "https://www.cocoongame.com/"
    ],
    [
        "category" => "Best Mobile Game",
        "winner" => "Honkai: Star Rail",
        "dev_logo" => "Honkai Logo.jpeg",
        "dev_name" => "Cognosphere",
        "score" => 80,
        "image" => "Honkai.jpg",
        "link" => "https://hsr.hoyoverse.com/en-us/"
    ],
    [
        "category" => "Best Action Game",
        "winner" => "Armored Core VI: Fires of Rubicon",
        "dev_logo" => "Armored Core 6 LOGO.png",
        "dev_name" => "FromSoftware",
        "score" => 86,
        "image" => "Armored Core 6.jpg",
        "link" => "https://armoredcore6.bn-ent.net/en/"
    ],
    [
        "category" => "Best Action/Adventure Game",
        "winner" => "The Legend of Zelda: Tears of the Kingdom",
        "dev_logo" => "Zelda Logo.png",
        "dev_name" => "Nintendo",
        "score" => 96,
        "image" => "zelda.jpeg",
        "link" => "https://zelda.nintendo.com/"
    ],
    [
        "category" => "Best Fighting Game",
        "winner" => "Street Fighter 6",
        "dev_logo" => "SF_logo.png",
        "dev_name" => "Capcom",
        "score" => 92,
        "image" => "Street Fighter 6.jpeg",
        "link" => "https://www.streetfighter.com/6"
    ]
];

$year_navigation = ["2025", "2024", "2023", "2022", "2021", "2020", "2019", "2018", "2017"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../TGA_CSS.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title><?php echo $current_year; ?> Winners | TGA Portal</title>
    <meta name="description" content="Explore the winners of The Game Awards <?php echo $current_year; ?>, including Baldur's Gate 3 and more.">
</head>
<body>
<?php render_nav("../"); ?>

    

<div class="modal-overlay" id="loginModal">
    <div class="modal-box">
        <span onclick="closeModal()" style="position:absolute; top:15px; right:20px; cursor:pointer; color:var(--text-dim); font-size:24px;">&times;</span>
        
        <h2>LOGIN</h2>
        <p class="modal-info">Please login to interact, participate in polls and events.</p>

        <?php include "../../includes/modal_alert.php"; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login_submit">SIGN IN</button>
        </form>

        <div class="modal-footer" style="color: var(--text-dim); font-size: 0.85rem; border-top: 1px solid #222; padding-top: 20px;">
            Don't have an account? <a href="/Register.php" style="color: var(--accent); text-decoration: none; font-weight: bold;">Register Now</a>
        </div>
    </div>
</div>

 

    <nav class="side-nav">
        <?php foreach ($year_navigation as $year): ?>
            <a href="../<?php echo $year; ?>TGA/<?php echo $year; ?>TGA.php" 
               class="year-link <?php echo ($year == $current_year) ? 'active' : ''; ?>" 
               rel="noopener noreferrer">
               <?php echo $year; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="detail-container">
        <a href="../Main.php" style="color: var(--accent); text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px;" rel="noopener noreferrer">
            <i class="fas fa-chevron-left"></i> Back to Home
        </a>

        <header style="text-align: left; margin-bottom: 40px; margin-top: 20px;">
            <h1 style="font-size: 2.5rem; letter-spacing: 2px;"><?php echo $current_year; ?> THE GAME AWARDS</h1>
            <p>Celebrating the best in gaming. Full winners list.</p> 
        </header>

        <section style="margin-bottom: 50px;">
            <h2 style="color: var(--accent); font-size: 1.2rem; opacity: 0.8; text-transform: uppercase;">Game of the Year</h2>
            <h3 class="game-title-highlight"><?php echo $goty_details['title']; ?></h3>
            
            <div class="goty-stats">
                <div class="metacritic-badge <?php echo get_metascore_class($goty_details['score']); ?>" title="Metascore">
                    <?php echo $goty_details['score']; ?>
                </div>
                <div class="dev-section">
                    <img src="<?php echo $goty_details['dev_logo']; ?>" class="dev-logo" alt="developer">
                    <span class="dev-name"><?php echo $goty_details['dev_name']; ?></span>
                </div>
            </div>

            <div class="media-row">
                <img src="<?php echo $goty_details['image']; ?>" class="game-main-img" alt="GOTY Main Image">
                <div class="video-section">
                    <div class="video-wrapper">
                        <video controls style="width: 100%; height: 100%;">
                            <source src="<?php echo $goty_details['video']; ?>" type="video/mp4">
                        </video>
                    </div>
                </div>
            </div>

            <div class="achievements-section">
                <h4 class="achievements-title">ADDITIONAL AWARDS WON</h4>
                <div class="achievements-list">
                    <?php foreach ($goty_details['additional_awards'] as $award): ?>
                        <div class="achievement-badge">
                            <span class="badge-icon">🏆</span>
                            <span class="badge-text"><?php echo $award; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="margin-top: 20px;">
                <a href="<?php echo $goty_details['official_site']; ?>" target="_blank" rel="noopener noreferrer" class="panel-btn">OFFICIAL WEBSITE</a>
            </div>
        </section>

        <h2 style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">Other Categories</h2>
        
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
                <div class="category-item" onclick="togglePanel(this)">
                    <span class="category-name"><?php echo $cat['category']; ?></span>
                    <span class="winner-name"><?php echo $cat['winner']; ?></span>
            
                    <div class="category-panel">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <div class="dev-section">
                                <img src="<?php echo $cat['dev_logo']; ?>" class="dev-logo" alt="studio logo">
                                <span class="dev-name"><?php echo $cat['dev_name']; ?></span>
                            </div>
                            <div class="metacritic-badge <?php echo get_metascore_class($cat['score']); ?>" style="width: 35px; height: 35px; font-size: 1rem;">
                                <?php echo $cat['score']; ?>
                            </div>
                        </div>

                        <img src="<?php echo $cat['image']; ?>" class="panel-img" alt="game preview">
                        <a href="<?php echo $cat['link']; ?>" target="_blank" rel="noopener noreferrer" class="panel-btn">OFFICIAL WEBSITE</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
        $pageKey = "2023";
        include "../../includes/comments_widget.php";
    ?>

    <footer style="margin-top: 80px; padding: 40px; border-top: 1px solid rgba(197, 160, 89, 0.1); color: var(--text-dim); font-size: 0.8rem; text-align: center;">
        <p>&copy; <?php echo date("Y"); ?> TGA Portal. All rights reserved.</p>
    </footer>

    <script>

    const modal = document.getElementById('loginModal');
    function openModal() { modal.style.display = 'flex'; }
    function closeModal() { modal.style.display = 'none'; }
    window.onclick = function(e) { if (e.target == modal) closeModal(); }

    function togglePanel(element) {
        if (element.classList.contains('active')) {
            element.classList.remove('active');
            return;
        }
        document.querySelectorAll('.category-item').forEach(item => item.classList.remove('active'));
        element.classList.add('active');
    }
    </script>
</body>
</html>