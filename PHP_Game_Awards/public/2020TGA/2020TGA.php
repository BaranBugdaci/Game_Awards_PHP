<?php
/**
 * TGA Portal - 2020 Winners Page
 * PHP Architecture - Internationalized & Optimized
 */
require dirname(__FILE__) . "/../../includes/auth_nav.php";

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

// 2. DATA CONFIGURATION: 2020 Winners
$current_year = "2020";

// Game of the Year (GOTY) Data
$goty = [
    "title" => "The Last Of Us Part II",
    "dev_logo" => "TLOU LOGO.png",
    "dev_name" => "Naughty Dog",
    "score" => 93,
    "image" => "TLOU Part 2.jpg",
    "video" => "TLOU.mp4",
    "link" => "https://www.naughtydog.com/blog/the_last_of_us_part_ii",
    "extra_awards" => [
        "Best Game Direction",
        "Best Narrative",
        "Best Audio Design",
        "Best Performance (Laura Bailey as Abby)",
        "Best Action/Adventure Game"
    ]
];

// Other Categories Grid Data
$categories = [
    [
        "title" => "Best Art Direction",
        "winner" => "Ghost of Tsushima",
        "dev_logo" => "ghost of tsushima logopng.png",
        "dev_name" => "Sucker Punch Productions",
        "score" => 83,
        "image" => "Ghost of Tsu.jpg",
        "link" => "https://www.suckerpunch.com/"
    ],
    [
        "title" => "Best Score/Music + Best Role Playing (RPG)",
        "winner" => "Final Fantasy VII Remake",
        "dev_logo" => "FinalFantazy Logo.png",
        "dev_name" => "Square Enix",
        "score" => 92,
        "image" => "FF7.jpg",
        "link" => "https://ffvii.square-enix-games.com/en-us/games/rebirth"
    ],
    [
        "title" => "Games for Impact",
        "winner" => "Tell Me Why",
        "dev_logo" => "tell me why logo.png",
        "dev_name" => "Dontnod Entertainment",
        "score" => 78,
        "image" => "Tell me why.jpg",
        "link" => "https://dont-nod.com/en/games/tell-me-why/"
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
        "title" => "Best Action + Best Indie",
        "winner" => "Hades",
        "dev_logo" => "SuperGiantGames.png",
        "dev_name" => "Supergiant Games",
        "score" => 93,
        "image" => "Hades.jpg",
        "link" => "https://www.supergiantgames.com/games/hades/"
    ],
    [
        "title" => "Best Mobile Game",
        "winner" => "Among Us",
        "dev_logo" => "Among us logo.png",
        "dev_name" => "Innersloth",
        "score" => 85,
        "image" => "Among us.jpeg",
        "link" => "https://www.innersloth.com/games/among-us/"
    ],
    [
        "title" => "Best VR/AR Game",
        "winner" => "Half-Life: Alyx",
        "dev_logo" => "Half life alyx logopng.png",
        "dev_name" => "Valve",
        "score" => 93,
        "image" => "Half Life Alyx.jpg",
        "link" => "https://www.half-life.com/en/alyx/vr"
    ],
    [
        "title" => "Best Debut Indie",
        "winner" => "Phasmophobia",
        "dev_logo" => "Phasmophobia logo.jpeg",
        "dev_name" => "Kinetic Games",
        "score" => 76,
        "image" => "Phasmophobia.jpg",
        "link" => "https://kineticgames.co.uk/"
    ],
    [
        "title" => "Best Fighting Game",
        "winner" => "Mortal Kombat 11 Ultimate",
        "dev_logo" => "MK11LOGO.png",
        "dev_name" => "NetherRealm Studios",
        "score" => 88,
        "image" => "MK11Ultimate.jpg",
        "link" => "https://www.mortalkombat.com/en-us"
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
    <meta name="keywords" content="The Game Awards 2020, TLOU Part 2 GOTY, Winners 2020">
    <meta name="description" content="Official list of winners for The Game Awards 2020.">
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
        <?php foreach ($years_nav as $year): ?>
            <a href="../<?php echo $year; ?>TGA/<?php echo $year; ?>TGA.php" 
               class="year-link <?php echo ($year == $current_year) ? 'active' : ''; ?>" 
               rel="noopener noreferrer">
               <?php echo $year; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="detail-container">
        <a href="../Main.php" style="color: var(--accent); text-decoration: none; font-weight: bold; display: flex; align-items: center; gap: 5px;" rel="noopener noreferrer">
            <i class="fas fa-chevron-left"></i> Back to Home
        </a>

        <header style="text-align: left; margin-bottom: 40px; margin-top: 20px;">
            <h1 style="font-size: 2.5rem; letter-spacing: 2px;"><?php echo $current_year; ?> THE GAME AWARDS</h1>
            <p>Celebrating the best in gaming from <?php echo $current_year; ?>.</p> 
        </header>

        <section style="margin-bottom: 50px;">
            <h2 style="color: var(--accent); font-size: 1.2rem; opacity: 0.8; text-transform: uppercase;">Game of the Year</h2>
            <h3 class="game-title-highlight"><?php echo $goty['title']; ?></h3>
            
            <div class="goty-stats">
                <div class="metacritic-badge <?php echo getMetascoreClass($goty['score']); ?>" title="Metascore">
                    <?php echo $goty['score']; ?>
                </div>
                <div class="dev-section">
                    <img src="<?php echo $goty['dev_logo']; ?>" class="dev-logo" alt="studio logo">
                    <span class="dev-name"><?php echo $goty['dev_name']; ?></span>
                </div>
            </div>

            <div class="media-row">
                <img src="<?php echo $goty['image']; ?>" class="game-main-img" alt="GOTY Main Image">
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
                                <img src="<?php echo $cat['dev_logo']; ?>" class="dev-logo" alt="dev">
                                <span class="dev-name"><?php echo $cat['dev_name']; ?></span>
                            </div>
                            <div class="metacritic-badge <?php echo getMetascoreClass($cat['score']); ?>" style="width: 35px; height: 35px; font-size: 1rem;">
                                <?php echo $cat['score']; ?>
                            </div>
                        </div>

                        <img src="<?php echo $cat['image']; ?>" class="panel-img" alt="winner cover">
                        <a href="<?php echo $cat['link']; ?>" target="_blank" rel="noopener noreferrer" class="panel-btn">OFFICIAL WEBSITE</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php
        $pageKey = "2020";
        include "../../includes/comments_widget.php";
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