<?php
/**
 * comments_widget.php
 * -------------------------------------------------------
 * Dahil edildiği sayfada $pageKey değişkeni tanımlı olmalı.
 * Örnek: $pageKey = "fighting";
 * Her sayfa kendi .txt dosyasına yazar: comments_fighting.txt
 * -------------------------------------------------------
 */

// Güvenli sayfa anahtarı — sadece harf/rakam/alt çizgi kabul et
$safeKey  = preg_replace('/[^a-z0-9_]/', '', strtolower($pageKey ?? 'genel'));
$dosyaAdi = "comments/comments_{$safeKey}.txt";

// comments/ klasörü yoksa oluştur
if (!file_exists("comments")) {
    mkdir("comments", 0755, true);
}

$mesajlar    = [];
$hata        = "";
$basari      = "";
// Oturumdan kullanıcı adını al; giriş yapılmamışsa null
$loggedInUser = $_SESSION['username'] ?? null;

// PRG sonrası başarı mesajını URL parametresinden oku
if (isset($_GET['yorum']) && $_GET['yorum'] === 'ok') {
    $basari = "Yorumunuz eklendi!";
}

// ── Yorum Kaydet (fopen / fwrite / fclose) ──────────────────
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['yorum_gonder'])) {
    // Kullanıcı adı: oturum açıksa session'dan al, değilse formdan oku
    $kullanici = $loggedInUser
        ? $loggedInUser
        : trim(htmlspecialchars($_POST['yorum_kullanici'] ?? ''));
    $yorum = trim(htmlspecialchars($_POST['yorum_metin'] ?? ''));

    if (empty($kullanici) || empty($yorum)) {
        $hata = "Lütfen tüm alanları doldurun.";
    } elseif (strlen($yorum) > 500) {
        $hata = "Yorum en fazla 500 karakter olabilir.";
    } else {
        $tarih  = date("d.m.Y H:i");
        $satir  = $tarih . "|" . $kullanici . "|" . $yorum . "\n";

        // fopen  → "a" modunda aç (append — var olana ekle, yoksa oluştur)
        // fwrite → satırı yaz
        // fclose → dosyayı kapat
        $dosya = fopen($dosyaAdi, "a");
        if ($dosya) {
            fwrite($dosya, $satir);
            fclose($dosya);
            // PRG (Post-Redirect-Get): header() HTML çıktısından önce çalışmalı.
            // ob_get_level() ile buffer varsa temizle, sonra yönlendir.
            if (ob_get_level()) ob_end_clean();
            $redirectUrl = strtok($_SERVER["REQUEST_URI"], '?') . "?yorum=ok";
            header("Location: " . $redirectUrl);
            exit();
        } else {
            $hata = "Dosya yazılamadı, lütfen tekrar deneyin.";
        }
    }
}

// ── Yorumları Oku (fopen / fgets / fclose) ──────────────────
if (file_exists($dosyaAdi)) {
    $dosya = fopen($dosyaAdi, "r");
    if ($dosya) {
        while (!feof($dosya)) {
            $satir = fgets($dosya);
            if (trim($satir) !== "") {
                $parcalar = explode("|", trim($satir), 3);
                if (count($parcalar) === 3) {
                    $mesajlar[] = [
                        "tarih"    => $parcalar[0],
                        "kullanici"=> $parcalar[1],
                        "yorum"    => $parcalar[2],
                    ];
                }
            }
        }
        fclose($dosya);
    }
    // En yeni yorum üstte görünsün
    $mesajlar = array_reverse($mesajlar);
}
?>

<!-- ═══════════════════════════════════════
     YORUM BÖLÜMÜ HTML
═══════════════════════════════════════ -->
<div class="comments-section">
    <h2 class="comments-title">💬 Yorumlar</h2>

    <!-- Yorum Formu -->
    <form class="comment-form" method="POST">
        <?php if ($hata):   ?><div class="comment-alert error"><?= $hata   ?></div><?php endif; ?>
        <?php if ($basari): ?><div class="comment-alert success"><?= $basari ?></div><?php endif; ?>

        <div class="comment-inputs">
            <?php if ($loggedInUser): ?>
                <!-- Giriş yapılmış: adı göster, input gizle -->
                <div class="comment-logged-user">
                    <i class="fas fa-user-circle"></i>
                    <strong><?= htmlspecialchars($loggedInUser) ?></strong> olarak yorum yapıyorsun
                </div>
                <input type="hidden" name="yorum_kullanici" value="<?= htmlspecialchars($loggedInUser) ?>">
            <?php else: ?>
                <!-- Misafir: adını manuel gir -->
                <input
                    type="text"
                    name="yorum_kullanici"
                    placeholder="Kullanıcı adın"
                    maxlength="50"
                    value="<?= htmlspecialchars($_POST['yorum_kullanici'] ?? '') ?>"
                    required
                >
            <?php endif; ?>
            <textarea
                name="yorum_metin"
                placeholder="Bu kategori hakkında ne düşünüyorsun? (max 500 karakter)"
                maxlength="500"
                rows="3"
                required
            ><?= htmlspecialchars($_POST['yorum_metin'] ?? '') ?></textarea>
        </div>
        <button type="submit" name="yorum_gonder" class="comment-btn">
            <i class="fas fa-paper-plane"></i> Yorum Gönder
        </button>
    </form>

    <!-- Yorum Listesi -->
    <div class="comment-list">
        <?php if (empty($mesajlar)): ?>
            <p class="no-comments">Henüz yorum yok. İlk yorumu sen yap!</p>
        <?php else: ?>
            <?php foreach ($mesajlar as $m): ?>
            <div class="comment-item">
                <div class="comment-header">
                    <span class="comment-user">
                        <i class="fas fa-user-circle"></i>
                        <?= htmlspecialchars($m['kullanici']) ?>
                    </span>
                    <span class="comment-date"><?= htmlspecialchars($m['tarih']) ?></span>
                </div>
                <p class="comment-body"><?= nl2br(htmlspecialchars($m['yorum'])) ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
/* ── Yorum Bölümü Stilleri ────────────────────────────── */
.comments-section {
    max-width: 900px;
    margin: 60px auto 40px auto;
    padding: 0 20px 60px 20px;
    border-top: 1px solid rgba(197,160,89,0.2);
}
.comments-title {
    font-size: 1.6rem;
    font-weight: 900;
    letter-spacing: 3px;
    color: var(--accent);
    margin-bottom: 30px;
    text-transform: uppercase;
}
.comment-form {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(197,160,89,0.15);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 40px;
}
.comment-inputs {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
}
.comment-form input,
.comment-form textarea {
    width: 100%;
    padding: 12px 16px;
    background: #000;
    border: 1px solid #333;
    color: #fff;
    border-radius: 8px;
    font-size: 0.9rem;
    box-sizing: border-box;
    outline: none;
    resize: vertical;
    font-family: inherit;
    transition: border-color 0.3s;
}
.comment-form input:focus,
.comment-form textarea:focus { border-color: var(--accent); }
.comment-btn {
    background: var(--accent);
    color: #000;
    border: none;
    padding: 12px 28px;
    border-radius: 8px;
    font-weight: 900;
    font-size: 0.9rem;
    letter-spacing: 1px;
    cursor: pointer;
    transition: 0.3s;
}
.comment-btn:hover { opacity: 0.85; transform: translateY(-2px); }
.comment-logged-user {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--accent);
    font-size: 0.88rem;
    padding: 10px 14px;
    background: rgba(197,160,89,0.08);
    border: 1px solid rgba(197,160,89,0.2);
    border-radius: 8px;
}
.comment-logged-user i { font-size: 1.1rem; }
.comment-alert {
    padding: 10px 16px;
    border-radius: 8px;
    margin-bottom: 14px;
    font-size: 0.88rem;
    font-weight: 700;
}
.comment-alert.error   { background: rgba(255,77,77,0.15); border: 1px solid #ff4d4d; color: #ff4d4d; }
.comment-alert.success { background: rgba(197,160,89,0.15); border: 1px solid var(--accent); color: var(--accent); }
.comment-list { display: flex; flex-direction: column; gap: 16px; }
.no-comments  { color: rgba(255,255,255,0.3); font-style: italic; text-align: center; padding: 40px 0; }
.comment-item {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 10px;
    padding: 18px 20px;
    transition: 0.3s;
}
.comment-item:hover { border-color: rgba(197,160,89,0.3); background: rgba(197,160,89,0.04); }
.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.comment-user  { color: var(--accent); font-weight: 800; font-size: 0.9rem; }
.comment-user i { margin-right: 6px; }
.comment-date  { color: rgba(255,255,255,0.3); font-size: 0.78rem; }
.comment-body  { color: rgba(255,255,255,0.75); font-size: 0.9rem; line-height: 1.6; margin: 0; }
</style>