<?php
require_once 'header.php';
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div class='container'><div class='panel-kart' style='text-align:center; color: var(--koyu-metin);'>Geçersiz istek. Lütfen bir tarif seçin.</div></div>");
}

$tarif_id = (int)$_GET['id'];
$tarifler = json_oku('tarifler');
$kategoriler = json_oku('kategoriler');
$kullanicilar = json_oku('kullanicilar');
$favoriler = json_oku('favoriler');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['yorum_ekle'])) {
    if (giris_yapilmis_mi()) {
        $yorumlar = json_oku('yorumlar');
        if (!is_array($yorumlar)) $yorumlar = [];
        
        $yeni_yorum = [
            'id' => yeni_id_uret($yorumlar),
            'tarif_id' => $tarif_id,
            'user_id' => $_SESSION['aktif_kullanici_id'],
            'yorum' => trim($_POST['yorum']),
            'tarih' => date('d.m.Y H:i')
        ];
        
        $yorumlar[] = $yeni_yorum;
        json_yaz('yorumlar', $yorumlar);
        
        header("Location: tarif_detay.php?id=" . $tarif_id . "&mesaj=yorum_eklendi");
        exit();
    }
}


$tum_yorumlar = json_oku('yorumlar');
if (!is_array($tum_yorumlar)) $tum_yorumlar = [];
$bu_tarifin_yorumlari = [];
foreach ($tum_yorumlar as $y) {
    if ($y['tarif_id'] == $tarif_id) {
        $bu_tarifin_yorumlari[] = $y;
    }
}
$secilen_tarif = null;

foreach ($tarifler as $t) {
    if ($t['id'] == $tarif_id) {
        $secilen_tarif = $t;
        break;
    }
}

if (!$secilen_tarif) {
    die("<div class='container'><div class='panel-kart' style='text-align:center; color: var(--koyu-metin);'>Tarif bulunamadı veya silinmiş.</div></div>");
}


$kat_ad = 'Bilinmiyor';
foreach ($kategoriler as $k) { 
    if ($k['id'] == $secilen_tarif['kategori_id']) { $kat_ad = $k['kategori_ad']; break; } 
}

$sef = null;
foreach ($kullanicilar as $u) { 
    if ($u['id'] == $secilen_tarif['user_id']) { $sef = $u; break; } 
}
$sef_ad = $sef ? guvenli_yaz(($sef['isim'] ?? $sef['ad'] ?? '') . ' ' . ($sef['soyisim'] ?? '')) : 'Bilinmeyen Şef';
$sef_id = $sef ? $sef['id'] : 0;

$aktif_user_id = giris_yapilmis_mi() ? $_SESSION['aktif_kullanici_id'] : 0;
$favoride_mi = false;
if ($aktif_user_id > 0) {
    foreach ($favoriler as $fav) {
        if ($fav['user_id'] == $aktif_user_id && $fav['tarif_id'] == $tarif_id) {
            $favoride_mi = true; break;
        }
    }
}

$resim = !empty($secilen_tarif['resim_url']) ? guvenli_yaz($secilen_tarif['resim_url']) : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=1200&q=80';

$mesaj_html = '';
if (isset($_GET['mesaj']) && $_GET['mesaj'] == 'yorum_eklendi') {
    $mesaj_html = "<div style='background: #d5f5e3; color: #27ae60; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-check'></i> Yorumunuz başarıyla eklendi!</div>";
}
?>

<div style="max-width: 900px; margin: 0 auto;">

    <!-- TARİF BAŞLIĞI VE GÖRSELİ -->
    <div class="panel-kart" style="padding: 0; overflow: hidden; margin-bottom: 30px;">
        <div style="position: relative; height: 400px;">
            <img src="<?= $resim ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.8)); padding: 40px 20px 20px 20px; color: var(--beyaz);">
                <span style="background: var(--vurgu); color: var(--ana-renk); padding: 5px 12px; border-radius: 5px; font-size: 13px; font-weight: bold; margin-bottom: 10px; display: inline-block;">
                    <?= $kat_ad ?>
                </span>
                <h1 style="margin: 0; font-size: 32px; text-shadow: 2px 2px 4px rgba(0,0,0,0.5);"><?= guvenli_yaz($secilen_tarif['baslik']) ?></h1>
            </div>
            
            <?php if($favoride_mi): ?>
                <div style="position: absolute; top: 20px; right: 20px; background: var(--beyaz); color: #e74c3c; padding: 10px 15px; border-radius: 50%; font-size: 24px; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                    <i class="fa-solid fa-heart"></i>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="padding: 20px; display: flex; justify-content: space-between; align-items: center; background: var(--arka-plan); border-bottom: 1px solid rgba(0,0,0,0.05);">
            <a href="profil.php?id=<?= $sef_id ?>" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--ana-renk);">
                <div style="width: 40px; height: 40px; background: rgba(0,0,0,0.05); color: var(--ana-renk); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-user-astronaut"></i>
                </div>
                <div>
                    <div style="font-size: 12px; color: var(--koyu-metin); opacity: 0.7;">Tarif Sahibi (Şef)</div>
                    <div style="font-weight: bold;"><?= $sef_ad ?></div>
                </div>
            </a>
            
            <?php if(giris_yapilmis_mi()): ?>
                <div>
                    <?php if($favoride_mi): ?>
                        <a href="favoriler.php?cikar=<?= $tarif_id ?>" class="btn btn-kucuk" style="background: #fab1a0; color: #c0392b; text-decoration: none;"><i class="fa-solid fa-heart-crack"></i> Favorilerden Çıkar</a>
                    <?php else: ?>
                        <a href="favoriler.php?ekle=<?= $tarif_id ?>" class="btn btn-kucuk" style="background: #ffeaa7; color: #d63031; text-decoration: none;"><i class="fa-regular fa-heart"></i> Favorilere Ekle</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="ikili-grid" style="align-items: start;">
        
        <!-- MALZEMELER KISMI -->
        <div class="panel-kart" style="background: var(--beyaz); border-left: 5px solid var(--vurgu); margin-bottom: 0;">
            <h3 style="color: var(--ana-renk); margin-bottom: 15px; border-bottom: 2px dashed rgba(0,0,0,0.1); padding-bottom: 10px;">
                <i class="fa-solid fa-basket-shopping" style="color: var(--vurgu);"></i> Malzemeler
            </h3>
            <p style="line-height: 1.8; color: var(--koyu-metin); opacity: 0.9; font-size: 15px;">
                <?= nl2br(guvenli_yaz($secilen_tarif['malzemeler'])) ?>
            </p>
        </div>

        <div class="panel-kart" style="margin-bottom: 0;">
            <h3 style="color: var(--ana-renk); margin-bottom: 15px; border-bottom: 2px dashed rgba(0,0,0,0.1); padding-bottom: 10px;">
                <i class="fa-solid fa-fire-burner" style="color: var(--vurgu);"></i> Hazırlanışı
            </h3>
            <p style="line-height: 1.8; color: var(--koyu-metin); opacity: 0.9; font-size: 15px;">
                <?= nl2br(guvenli_yaz($secilen_tarif['hazirlanis'] ?? 'Bu tarif için hazırlanış adımları girilmemiş.')) ?>
            </p>
        </div>

    </div>

    <!-- YORUMLAR BÖLÜMÜ -->
    <div class="panel-kart" style="margin-top: 30px; border-top: 4px solid var(--vurgu);">
        <h3 style="color: var(--ana-renk); margin-top: 0; border-bottom: 2px solid var(--arka-plan); padding-bottom: 10px; margin-bottom: 20px;">
            <i class="fa-regular fa-comments" style="color: var(--vurgu);"></i> Yorumlar (<?= count($bu_tarifin_yorumlari) ?>)
        </h3>

        <?= $mesaj_html ?>

        <!-- Listeleme Alanı -->
        <?php if(empty($bu_tarifin_yorumlari)): ?>
            <p style="color: var(--koyu-metin); opacity: 0.7; font-style: italic; margin-bottom: 30px;">Bu tarife henüz yorum yapılmamış. Düşüncelerini paylaşan ilk kişi sen ol!</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 15px; margin-bottom: 30px;">
                <?php foreach($bu_tarifin_yorumlari as $yorum): 
                    $y_ad = 'Bilinmeyen Kullanıcı'; 
                    $y_id = 0;
                    foreach($kullanicilar as $ku) { 
                        if($ku['id'] == $yorum['user_id']) { 
                            $y_ad = $ku['ad'] ?? ($ku['isim'] ?? 'İsimsiz'); 
                            $y_id = $ku['id']; 
                            break; 
                        } 
                    }
                ?>
                <div style="background: var(--arka-plan); padding: 15px; border-radius: 10px; border-left: 3px solid var(--ana-renk);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <a href="profil.php?id=<?= $y_id ?>" style="color: var(--ana-renk); font-weight: bold; text-decoration: none; transition: 0.2s;" onmouseover="this.style.color='var(--vurgu)'" onmouseout="this.style.color='var(--ana-renk)'">
                            @<?= guvenli_yaz($y_ad) ?>
                        </a>
                        <span style="font-size: 12px; color: var(--koyu-metin); opacity: 0.6;"><i class="fa-regular fa-clock"></i> <?= guvenli_yaz($yorum['tarih']) ?></span>
                    </div>
                    <p style="margin: 0; color: var(--koyu-metin); font-size: 14px; line-height: 1.5; opacity: 0.9;">
                        <?= nl2br(guvenli_yaz($yorum['yorum'])) ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Yorum Yapma Formu -->
        <?php if(giris_yapilmis_mi()): ?>
            <div style="background: rgba(0,0,0,0.02); padding: 20px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.05);">
                <h4 style="margin-top: 0; margin-bottom: 15px; color: var(--ana-renk);">Yorum Bırak</h4>
                <form method="POST">
                    
                    <!-- EMOJİ ÇUBUĞU -->
                    <div style="display: flex; gap: 15px; margin-bottom: 10px; padding: 10px 15px; background: var(--arka-plan); border-radius: 8px; border: 1px solid rgba(0,0,0,0.05); user-select: none; overflow-x: auto;">
                        <span onclick="emojiEkle('😋')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">😋</span>
                        <span onclick="emojiEkle('😍')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">😍</span>
                        <span onclick="emojiEkle('🔥')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🔥</span>
                        <span onclick="emojiEkle('👏')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">👏</span>
                        <span onclick="emojiEkle('🤤')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🤤</span>
                        <span onclick="emojiEkle('💯')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">💯</span>
                        <span onclick="emojiEkle('🥗')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🥗</span>
                        <span onclick="emojiEkle('🍰')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">🍰</span>
                        <span onclick="emojiEkle('👨‍🍳')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">👨‍🍳</span>
                        <span onclick="emojiEkle('👩‍🍳')" style="cursor: pointer; font-size: 22px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.2)'" onmouseout="this.style.transform='scale(1)'">👩‍🍳</span>
                    </div>

                    <textarea id="yorumKutusu" name="yorum" rows="3" required placeholder="Tarifi denediniz mi? Püf noktalarınız veya tavsiyeleriniz var mı?" style="width: 100%; padding: 15px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); margin-bottom: 15px; outline: none; font-family: inherit; resize: vertical; box-sizing: border-box; font-size: 14px;"></textarea>
                    
                    <div style="text-align: right;">
                        <button type="submit" name="yorum_ekle" class="btn" style="background: var(--ana-renk); color: var(--beyaz); padding: 10px 25px;"><i class="fa-solid fa-paper-plane"></i> Gönder</button>
                    </div>
                </form>
                
                <script>
                function emojiEkle(emoji) {
                    const kutu = document.getElementById('yorumKutusu');
                    kutu.value += emoji;
                    kutu.focus(); // Tıkladıktan sonra kullanıcının yazmaya devam edebilmesi için imleci kutuya geri al
                }
                </script>
            </div>
        <?php else: ?>
            <div style="background: var(--arka-plan); padding: 20px; border-radius: 10px; text-align: center; color: var(--koyu-metin); opacity: 0.9;">
                <i class="fa-solid fa-lock" style="margin-bottom: 10px; font-size: 20px; color: var(--ana-renk);"></i><br>
                Yorum yapabilmek için <a href="login.php" style="color: var(--ana-renk); font-weight: bold; text-decoration: underline;">giriş yapmalısınız</a>.
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once 'footer.php'; ?>