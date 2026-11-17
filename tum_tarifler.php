<?php
require_once 'header.php';

$tarifler = json_oku('tarifler');
$kategoriler = json_oku('kategoriler');
$kullanicilar = json_oku('kullanicilar');
$favoriler = json_oku('favoriler');

$aktif_user_id = giris_yapilmis_mi() ? $_SESSION['aktif_kullanici_id'] : 0;
$arama_terimi = isset($_GET['ara']) ? trim(mb_strtolower($_GET['ara'], 'UTF-8')) : '';

$gosterilecek_tarifler = [];
foreach (array_reverse($tarifler) as $t) {
    $kat_ad = 'Bilinmiyor';
    foreach($kategoriler as $k) { if($k['id'] == $t['kategori_id']) { $kat_ad = $k['kategori_ad']; break; } }
    
    $kul_ad = 'Bilinmiyor';
    foreach($kullanicilar as $u) { 
        if($u['id'] == $t['user_id']) { $kul_ad = isset($u['isim']) ? $u['isim'] . ' ' . $u['soyisim'] : $u['ad']; break; } 
    }

    if ($arama_terimi !== '') {
        $baslik_kucuk = mb_strtolower($t['baslik'], 'UTF-8');
        $sef_kucuk = mb_strtolower($kul_ad, 'UTF-8');
        if (mb_strpos($baslik_kucuk, $arama_terimi, 0, 'UTF-8') === false && mb_strpos($sef_kucuk, $arama_terimi, 0, 'UTF-8') === false) { continue; }
    }

    $t['kategori_ad'] = $kat_ad;
    $t['ekleyen'] = $kul_ad;
    $t['resim_url'] = !empty($t['resim_url']) ? $t['resim_url'] : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=800&q=80';
    $gosterilecek_tarifler[] = $t;
}
?>

<div style="text-align: center; margin-bottom: 30px;">
    <h2 style="color: var(--ana-renk); margin-bottom: 5px;"><i class="fa-solid fa-list"></i> Tüm Tarifler</h2>
    <p style="color: var(--koyu-metin); font-size: 15px; opacity: 0.8;">Kullanıcılarımız tarafından eklenen tüm lezzet arşivi.</p>
</div>

<div class="panel-kart" style="max-width: 600px; margin: 0 auto 30px auto; padding: 15px;">
    <form action="tum_tarifler.php" method="GET" style="display: flex; gap: 10px;">
        <input type="text" name="ara" placeholder="Tarif adı veya şef ismi aratın..." value="<?= guvenli_yaz($_GET['ara'] ?? '') ?>" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
        <button type="submit" class="btn" style="width: auto; padding: 10px 20px; background: var(--ana-renk); color: var(--beyaz);"><i class="fa-solid fa-magnifying-glass"></i> Ara</button>
        <?php if(!empty($arama_terimi)): ?>
            <a href="tum_tarifler.php" class="btn btn-tehlike" style="width: auto; padding: 10px 20px; text-decoration: none;"><i class="fa-solid fa-xmark"></i> Temizle</a>
        <?php endif; ?>
    </form>
</div>

<?php if(empty($gosterilecek_tarifler)): ?>
    <div class="panel-kart" style="text-align: center; color: gray;">
        <i class="fa-solid fa-bowl-rice fa-3x" style="margin-bottom: 15px; color: #ccc;"></i>
        <p>Aradığınız kriterlere uygun tarif bulunamadı.</p>
    </div>
<?php else: ?>
    <div class="tarif-grid">
        <?php foreach($gosterilecek_tarifler as $tarif): 
            $favoride_mi = false;
            if ($aktif_user_id > 0) {
                foreach ($favoriler as $fav) {
                    if ($fav['user_id'] == $aktif_user_id && $fav['tarif_id'] == $tarif['id']) { $favoride_mi = true; break; }
                }
            }
        ?>
            <div class="tarif-kart" style="position: relative;" id="tarif-kart-<?= $tarif['id'] ?>">
                
                <div class="kalp-alani" style="position: absolute; top: 10px; left: 10px; z-index: 10;">
                    <?php if($favoride_mi): ?>
                        <div style="background: white; color: #e74c3c; padding: 5px 10px; border-radius: 8px; font-size: 14px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2);;">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if(giris_yapilmis_mi() && $_SESSION['aktif_kullanici_id'] == $tarif['user_id']): ?>
                    <div style="position: absolute; top: 10px; right: 10px; background: var(--vurgu); color: var(--ana-renk); padding: 5px 10px; border-radius: 8px; font-size: 12px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 10;">
                        <i class="fa-solid fa-star"></i> Sizin Tarifiniz
                    </div>
                <?php endif; ?>

                <a href="tarif_detay.php?id=<?= $tarif['id'] ?>">
                    <img src="<?= guvenli_yaz($tarif['resim_url']) ?>" alt="Yemek Resmi" class="tarif-resim" style="transition: opacity 0.3s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                </a>
                
                <div class="tarif-icerik">
                    <span class="kategori-etiket"><?= guvenli_yaz($tarif['kategori_ad']) ?></span>
                    <a href="tarif_detay.php?id=<?= $tarif['id'] ?>" style="text-decoration: none; color: inherit;">
                        <h3 class="tarif-baslik" style="color: var(--ana-renk);"><?= guvenli_yaz($tarif['baslik']) ?></h3>
                    </a>
                    
                    <div class="yazar-bilgi" style="color: var(--koyu-metin); font-weight: 500; margin-bottom: 10px; margin-top: 10px;">
                        <i class="fa-solid fa-hat-chef" style="color: var(--vurgu);"></i> Şef: <?= guvenli_yaz($tarif['ekleyen']) ?>
                    </div>
                    
                    <p style="font-size: 13px; color: #555; height: 40px; overflow: hidden; text-overflow: ellipsis;">
                        <strong style="color: var(--ana-renk);"><i class="fa-solid fa-basket-shopping"></i> Malzemeler:</strong><br> <?= guvenli_yaz($tarif['malzemeler']) ?>
                    </p>
                    
                    <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; display: flex; gap: 10px;">
                        <a href="tarif_detay.php?id=<?= $tarif['id'] ?>" class="btn btn-kucuk" style="flex: 1; text-align: center; text-decoration: none; background: var(--ana-renk); color: var(--beyaz);">
                            <i class="fa-solid fa-book-open"></i> Oku
                        </a>

                        <?php if(giris_yapilmis_mi()): ?>
                            <div class="buton-alani" style="flex: 1;">
                                <?php if($favoride_mi): ?>
                                    <button onclick="favoriIslem(<?= $tarif['id'] ?>, 'cikar')" class="btn btn-kucuk" style="width: 100%; background: #ffeaa7; color: #c0392b; border: none; cursor: pointer; font-weight: 600;">
                                        <i class="fa-solid fa-heart-crack"></i> Çıkar
                                    </button>
                                <?php else: ?>
                                    <button onclick="favoriIslem(<?= $tarif['id'] ?>, 'ekle')" class="btn btn-kucuk" style="width: 100%; background: #ffeaa7; color: #d63031; border: none; cursor: pointer; font-weight: 600;">
                                        <i class="fa-regular fa-heart"></i> Ekle
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
    // JAVA SCRİPT
function favoriIslem(tarifId, islem) {
    fetch('favoriler.php?' + islem + '=' + tarifId + '&ajax=1')
    .then(response => response.text())
    .then(data => {
        if(data.trim() === 'ok') {
            const kart = document.getElementById('tarif-kart-' + tarifId);
            const kalpAlani = kart.querySelector('.kalp-alani');
            const butonAlani = kart.querySelector('.buton-alani');
            
            if(islem === 'ekle') {
                kalpAlani.innerHTML = `
                    <div style="background: white; color: #e74c3c; padding: 5px 10px; border-radius: 8px; font-size: 14px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                        <i class="fa-solid fa-heart"></i>
                    </div>`;
                butonAlani.innerHTML = `
                    <button onclick="favoriIslem(${tarifId}, 'cikar')" class="btn btn-kucuk" style="width: 100%; background: #ffeaa7; color: #c0392b; border: none; cursor: pointer; font-weight: 600;">
                        <i class="fa-solid fa-heart-crack"></i> Çıkar
                    </button>`;
            } else {
                kalpAlani.innerHTML = '';
                butonAlani.innerHTML = `
                    <button onclick="favoriIslem(${tarifId}, 'ekle')" class="btn btn-kucuk" style="width: 100%; background: #ffeaa7; color: #d63031; border: none; cursor: pointer; font-weight: 600;">
                        <i class="fa-regular fa-heart"></i> Ekle
                    </button>`;
            }
        }
    });
}
</script>

<?php require_once 'footer.php'; ?>