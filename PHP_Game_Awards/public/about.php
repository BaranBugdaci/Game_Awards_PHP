<?php
if (!ob_get_level()) ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/login.php";

if (!function_exists('render_nav')) {
    function render_nav(string $prefix = "") {
        $self = basename($_SERVER['PHP_SELF']);
        $u    = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : null;

        $links = [
            "ActionTGA.php"    => ["AKSİYON", "fa-sword"],
            "RPG_TGA.php"      => ["RPG",     "fa-magic"],
            "IndieTGA.php"     => ["INDIE",   "fa-rocket"],
            "FightingTGA.php"  => ["DÖVÜŞ",   "fa-chess"],
            "NarrativeTGA.php" => ["HİKAYE",  "fa-trophy"],
        ];

        $avatar = "";
        if ($u) {
            $usersFile = __DIR__ . "/users/users.txt";
            if (file_exists($usersFile)) {
                $f = fopen($usersFile, "r");
                if ($f) {
                    while (!feof($f)) {
                        $line = trim(fgets($f));
                        if ($line === "") continue;
                        $p = explode("|", $line, 3);
                        if (count($p) === 3 && $p[0] === $u) {
                            $avatar = $p[2];
                            break;
                        }
                    }
                    fclose($f);
                }
            }
        }

        echo '<nav class="category-nav">';
        echo '<div class="nav-container">';

        foreach ($links as $file => $info) {
            $label  = $info[0];
            $icon   = $info[1];
            $active = ($self === $file) ? "active" : "";
            echo '<a href="' . $prefix . $file . '" class="cat-item ' . $active . '" rel="noopener noreferrer">';
            echo '<i class="fas ' . $icon . '"></i> ' . $label;
            echo '</a>';
        }

        echo '</div>';
        echo '<div class="auth-nav">';

        if ($u) {
            echo '<a href="' . $prefix . 'Profile.php" class="auth-profile-link">';
            if ($avatar && file_exists(__DIR__ . "/" . $avatar)) {
                echo '<img src="' . $prefix . htmlspecialchars($avatar) . '"
                          style="width:32px;height:32px;border-radius:50%;
                                 object-fit:cover;border:2px solid var(--accent);
                                 vertical-align:middle;margin-right:6px;">';
            } else {
                echo '<i class="fas fa-user-circle" style="font-size:1.4rem;margin-right:6px;"></i>';
            }
            echo $u . '</a>';

            echo '<form method="POST" style="display:inline;margin-left:10px;">';
            echo '<button type="submit" name="cikis_yap"
                         style="background:transparent;border:1px solid rgba(197,160,89,0.3);
                                color:rgba(197,160,89,0.6);padding:6px 12px;border-radius:6px;
                                cursor:pointer;font-size:0.75rem;letter-spacing:1px;transition:0.3s;"
                         onmouseover="this.style.borderColor=\'#ff4d4d\';this.style.color=\'#ff4d4d\';"
                         onmouseout="this.style.borderColor=\'rgba(197,160,89,0.3)\';this.style.color=\'rgba(197,160,89,0.6)\';">';
            echo '<i class="fas fa-sign-out-alt"></i> ÇIKIŞ';
            echo '</button></form>';
        } else {
            echo '<button class="auth-btn" onclick="openModal()">SIGN UP / LOGIN</button>';
        }

        echo '</div>';
        echo '</nav>';

        if (!empty($GLOBALS['girisHata'])) {
            echo '<script>document.addEventListener("DOMContentLoaded",function(){if(typeof openModal==="function")openModal();});</script>';
        }
    }
}
