<?php if (!empty($girisHata)): ?>
    <div style="color:#ff4d4d;background:rgba(255,77,77,0.1);border:1px solid #ff4d4d;
                padding:10px 14px;border-radius:8px;margin-bottom:15px;
                font-size:0.85rem;text-align:left;">
        <i class="fas fa-exclamation-circle"></i> <?= $girisHata ?>
    </div>
<?php endif; ?>
<?php if (!empty($girisBasari)): ?>
    <div style="color:var(--accent);background:rgba(197,160,89,0.1);border:1px solid var(--accent);
                padding:10px 14px;border-radius:8px;margin-bottom:15px;
                font-size:0.85rem;text-align:left;">
        <i class="fas fa-check-circle"></i> <?= $girisBasari ?>
    </div>
<?php endif; ?>