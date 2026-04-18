<?php
/**
 * TGA Portal - 2024 Winners Page
 * Fully Internationalized for Global Deployment
 */
session_start(); // Session'ı başlatmayı unutma

$mesaj = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_submit'])) {
    $kullanici = htmlspecialchars($_POST['username']);
    if (!empty($kullanici)) {
        $_SESSION['username'] = $kullanici; // Basit bir session ataması
        $mesaj = "Welcome, " . $_SESSION['username'] . "!";
    }
}
// 1. FUNCTIONS: Automatically determines CSS class based on Metascore
function getMetascoreClass($score) {
    if ($score >= 90) {
        return 'bg-blue';   // 90+ Blue
    } elseif ($score >= 80) {
        return 'bg-green';  // 80-89 Green
    } else {
        return 'bg-yellow'; // Below 80 Yellow
    }
}

// 2. DATA STRUCTURE: 2024 Winners
$current_year = "2024";

// GOTY (Game of the Year) Data
$goty = [
    "title" => "Astro Bot",
    "dev_logo" => "Astro_Bot_Logo.png",
    "dev_name" => "Team Asobi",
    "score" => 94,
    "image" => "Astro_Bot.jpg",
    "video" => "Astro Bot.mp4",
    "link" => "https://www.teamasobi.com/",
    "extra_awards" => [
        "Best Game Direction",
        "Best Action/Adventure Game",
        "Best Family Game"
    ]
];

// Other Categories Grid Data
$categories = [
    [
        "title" => "Narrative + RPG + Art Dir.",
        "winner" => "Metaphor: ReFantazio",
        "dev_logo" => "Metaphor_ReFantazio_Logo.jpeg",
        "dev_name" => "Atlus Games",
        "score" => 94,
        "image" => "Metaphor.jpg",
        "link" => "https://metaphor.atlus.com/"
    ],
    [
        "title" => "Best Music",
        "winner" => "Final Fantasy VII Rebirth",
        "dev_logo" => "FinalFantazy Logo.png",
        "dev_name" => "Square Enix",
        "score" => 92,
        "image" => "FF7.jpg",
        "link" => "https://ffvii.square-enix-games.com/"
    ],
    [
        "title" => "Audio Design + Performance",
        "winner" => "Senua's Saga: Hellblade 2",
        "dev_logo" => "hellblade2 Logo.png",
        "dev_name" => "Ninja Theory",
        "score" => 81,
        "image" => "HellBlade2.jpg",
        "link" => "https://ninjatheory.com/"
    ],
    [
        "title" => "Ongoing + Multiplayer",
        "winner" => "Helldivers 2",
        "dev_logo" => "helldivers Logo.png",
        "dev_name" => "Arrowhead Games",
        "score" => 82,
        "image" => "Helldiversjpg.jpg",
        "link" => "https://www.arrowheadgamestudios.com/"
    ],
    [
        "title" => "Indie + Mobile",
        "winner" => "Balatro",
        "dev_logo" => "Balatro_Logo.jpeg",
        "dev_name" => "LocalThunk",
        "score" => 90,
        "image" => "Balatro.jpg",
        "link" => "https://localthunk.com/"
    ],
    [
        "title" => "Action + Players' Voice",
        "winner" => "Black Myth: Wukong",
        "dev_logo" => "Wukong_Logo.jpeg",
        "dev_name" => "Game Science",
        "score" => 81,
        "image" => "Wukong.jpg",
        "link" => "https://gamesci.cn/"
    ],
    [
        "title" => "Best Fighting Game",
        "winner" => "Tekken 8",
        "dev_logo" => "Tekken8_logo.png",
        "dev_name" => "Bandai Namco",
        "score" => 90,
        "image" => "Tekken8.jpeg",
        "link" => "https://en.bandainamcoent.eu/tekken/tekken-8"
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
</head>
<body>

    <div class="auth-nav">
    <?php if(!isset($_SESSION['username'])): ?>
        <button class="auth-btn" onclick="openModal()">SIGN UP / LOGIN</button>
    <?php else: ?>
        <span style="color:var(--accent); font-weight:900;">
            <i class="fas fa-user-circle"></i> Welcome, <?php echo $_SESSION['username']; ?>
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

        <div class="modal-footer" style="color: var(--text-dim); font-size: 0.85rem; border-top: 1px solid #222; padding-top: 20px;">
            Don't have an account? <a href="/PHP_Game_Awards/Register.php" style="color: var(--accent); text-decoration: none; font-weight: bold;">Register Now</a>
        </div>
    </div>
</div>

    <nav class="category-nav">
        <div class="nav-container">
            <a href="../ActionTGA.php" rel="noopener noreferrer" class="cat-item"><i class="fas fa-sword"></i> ACTION</a>
            <a href="../RPG_TGA.php" rel="noopener noreferrer" class="cat-item"><i class="fas fa-magic"></i> RPG</a>
            <a href="../IndieTGA.php" rel="noopener noreferrer" class="cat-item"><i class="fas fa-rocket"></i> INDIE</a>
            <a href="../FightingTGA.php" rel="noopener noreferrer" class="cat-item"><i class="fas fa-chess"></i> FIGHTING</a>
            <a href="../NarrativeTGA.php" rel="noopener noreferrer" class="cat-item"><i class="fas fa-trophy"></i> STORY</a>
        </div>
    </nav> 

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