<?php
/**
 * TGA Portal - 2022 Winners Page
 * Professional PHP Format - Internationalized
 */

// 1. FUNCTIONS: Determines CSS class based on Metascore

require dirname(__FILE__) . "/../../includes/auth_nav.php";

function getMetascoreClass($score) {
    if ($score >= 90) {
        return 'bg-blue';   // 90+ Blue
    } elseif ($score >= 80) {
        return 'bg-green';  // 80-89 Green
    } else {
        return 'bg-yellow'; // Below 80 Yellow
    }
}

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
// 2. DATA STRUCTURE: 2022 Winners
$current_year = "2022";

// GOTY Data
$goty = [
    "title" => "Elden Ring",
    "dev_logo" => "Armored Core 6 LOGO.png",
    "dev_name" => "FromSoftware/Bandai Namco",
    "score" => 96,
    "image" => "Elden_Ring.jpg",
    "video" => "EldenRing.mp4",
    "link" => "https://en.bandainamcoent.eu/elden-ring/elden-ring",
    "extra_awards" => [
        "Best Game Direction",
        "Best Art Direction",
        "Best Role Playing Game"
    ]
];

// Other Categories
$categories = [
    [
        "title" => "Games for Impact",
        "winner" => "As Dusk Falls",
        "dev_logo" => "Asduskfalls LOGO.jpeg",
        "dev_name" => "Interior/Night",
        "score" => 77,
        "image" => "AsduskFalls.png",
        "link" => "https://www.interiornight.com/asduskfalls"
    ],
    [
        "title" => "Best Ongoing Game",
        "winner" => "Final Fantasy XIV",
        "dev_logo" => "FinalFantazy Logo.png",
        "dev_name" => "Square Enix Games",
        "score" => 92,
        "image" => "FF14.jpeg",
        "link" => "https://www.square-enix-games.com/en_EU/games/final-fantasy-xiv-online"
    ],
    [
        "title" => "Best Indie + Best Debut Indie",
        "winner" => "Stray",
        "dev_logo" => "StrayLogo.png",
        "dev_name" => "Blue Twelve Studio",
        "score" => 83,
        "image" => "Stray.jpeg",
        "link" => "#" // Official site placeholder
    ],
    [
        "title" => "Best Mobile Game",
        "winner" => "Marvel Snap",
        "dev_logo" => "MarvelSnapLOGO.png",
        "dev_name" => "Nuverse",
        "score" => 85,
        "image" => "MarvelSnap.jpeg",
        "link" => "https://marvelsnap.com/"
    ],
    [
        "title" => "Best Action Game",
        "winner" => "Bayonetta 3",
        "dev_logo" => "Bayonetta LOGO.png",
        "dev_name" => "Platinum Games",
        "score" => 86,
        "image" => "Bayonetta_3.png",
        "link" => "https://www.platinumgames.com/works/bayonetta-3"
    ],
    [
        "title" => "Best Fighting Game",
        "winner" => "MultiVersus",
        "dev_logo" => "MultiversusLogo.jpeg",
        "dev_name" => "Player First Games",
        "score" => 75,
        "image" => "Multiversus.jpeg",
        "link" => "#"
    ],
    [
        "title" => "Best Multi-player Game",
        "winner" => "Splatoon 3",
        "dev_logo" => "Zelda Logo.png",
        "dev_name" => "Nintendo",
        "score" => 83,
        "image" => "Splatoon 3.jpeg",
        "link" => "https://splatoon.nintendo.com/"
    ],
    [
        "title" => "Best Narrative + Performance + Score + Audio + Action/Adventure",
        "winner" => "God of War Ragnarök",
        "dev_logo" => "Gow Logo.png",
        "dev_name" => "Santa Monica Studio",
        "score" => 94,
        "image" => "Gow Ragnorök.jpg",
        "link" => "https://www.santamonicastudios.com/"
    ]
];

$years_list = ["2025", "2024", "2023", "2022", "2021", "2020", "2019", "2018", "2017"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../TGA_CSS.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title><?php echo $current_year; ?> Winners | TGA Portal</title>
    <meta name="keywords" content="The Game Awards 2022, GOTY, Elden Ring, TGA 2022 Winners">
    <meta name="description" content="Official winners of The Game Awards 2022 including Elden Ring and God of War Ragnarök.">
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
        <?php foreach ($years_list as $year): ?>
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
            <p>The best games of the year and full winners list.</p> 
        </header>

        <section style="margin-bottom: 50px;">
            <h2 style="color: var(--accent); font-size: 1.2rem; opacity: 0.8; text-transform: uppercase;">Game of the Year</h2>
            <h3 class="game-title-highlight"><?php echo $goty['title']; ?></h3>
            
            <div class="goty-stats">
                <div class="metacritic-badge <?php echo getMetascoreClass($goty['score']); ?>" title="Metascore">
                    <?php echo $goty['score']; ?>
                </div>
                <div class="dev-section">
                    <img src="<?php echo $goty['dev_logo']; ?>" class="dev-logo" alt="developer">
                    <span class="dev-name"><?php echo $goty['dev_name']; ?></span>
                </div>
            </div>

            <div class="media-row">
                <img src="<?php echo $goty['image']; ?>" class="game-main-img">
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
                <div class="category-item" onclick="togglePanel(this)">
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

                        <img src="<?php echo $cat['image']; ?>" class="panel-img" alt="game cover">
                        <a href="<?php echo $cat['link']; ?>" target="_blank" rel="noopener noreferrer" class="panel-btn">OFFICIAL WEBSITE</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
        $pageKey = "2022";
        include "../../includes/comments_widget.php";
    ?>

    <footer style="margin-top: 80px; padding: 40px; border-top: 1px solid rgba(197, 160, 89, 0.1); color: var(--text-dim); font-size: 0.8rem; text-align: center;">
        <p>&copy; <?php echo date("Y"); ?> TGA Portal. All rights reserved.</p>
    </footer>

    <script>
    function togglePanel(element) {
        if (element.classList.contains('active')) {
            element.classList.remove('active');
            return;
        }
        document.querySelectorAll('.category-item').forEach(item => item.classList.remove('active'));
        element.classList.add('active');
    }

    const modal = document.getElementById('loginModal');
function openModal() { modal.style.display = 'flex'; }
function closeModal() { modal.style.display = 'none'; }
window.onclick = function(e) { if (e.target == modal) closeModal(); }
    </script>
</body>
</html>