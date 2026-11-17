<?php
require_once 'header.php';

$tarifler = json_oku('tarifler');
$kategoriler = json_oku('kategoriler');
$kullanicilar = json_oku('kullanicilar');
$favoriler = json_oku('favoriler');

$aktif_user_id = giris_yapilmis_mi() ? $_SESSION['aktif_kullanici_id'] : 0;

$son_tarifler = array_slice(array_reverse($tarifler), 0, 6);//Son 6 tarif görünmesi için

$gosterilecek_tarifler = [];
foreach ($son_tarifler as $t) {
    $kat_ad = 'Bilinmiyor';
    foreach($kategoriler as $k) { if($k['id'] == $t['kategori_id']) { $kat_ad = $k['kategori_ad']; break; } }
    
    $kul_ad = 'Bilinmiyor';
    foreach($kullanicilar as $u) { 
        if($u['id'] == $t['user_id']) { $kul_ad = isset($u['isim']) ? $u['isim'] . ' ' . $u['soyisim'] : $u['ad']; break; } 
    }

    $t['kategori_ad'] = $kat_ad;
    $t['ekleyen'] = $kul_ad;
    $t['resim_url'] = !empty($t['resim_url']) ? $t['resim_url'] : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=800&q=80';
    $gosterilecek_tarifler[] = $t;
}
?>

<div class="panel-kart" style="text-align: center; margin-bottom: 40px; background: linear-gradient(135deg, var(--arka-plan), var(--beyaz)); border-top: 5px solid var(--vurgu);">
    <h1 style="color: var(--ana-renk); margin-bottom: 10px; font-size: 32px;"><i class="fa-solid fa-utensils" style="color: var(--vurgu);"></i> Lezzet Dünyasına Hoş Geldiniz</h1>
    <p style="color: var(--koyu-metin); font-size: 16px; max-width: 600px; margin: 0 auto;">En lezzetli tarifleri keşfedin, kendi tariflerinizi paylaşın ve mutfak şeflerimizden oluşan harika topluluğumuza katılın!</p>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 2px solid var(--vurgu); padding-bottom: 10px;">
    <h2 style="color: var(--ana-renk); margin: 0;"><i class="fa-solid fa-clock-rotate-left" style="color: var(--vurgu);"></i> Son Eklenen Tarifler</h2>
    <a href="tum_tarifler.php" class="btn btn-kucuk" style="background: var(--ana-renk); color: var(--beyaz); text-decoration: none;">Tümünü Gör <i class="fa-solid fa-arrow-right"></i></a>
</div>

<?php if(empty($gosterilecek_tarifler)): ?>
    <div class="panel-kart" style="text-align: center; color: gray;">
        <i class="fa-solid fa-bowl-rice fa-3x" style="margin-bottom: 15px; color: #ccc;"></i>
        <p>Sistemde henüz hiç tarif bulunmuyor. İlk tarifi siz ekleyin!</p>
    </div>
<?php else: ?>
    <div class="tarif-grid">
        <?php foreach($gosterilecek_tarifler as $tarif): 
            
            $favoride_mi = false;
            if ($aktif_user_id > 0) {
                foreach ($favoriler as $fav) {
                    if ($fav['user_id'] == $aktif_user_id && $fav['tarif_id'] == $tarif['id']) {
                        $favoride_mi = true; break;
                    }
                }
            }
        ?>
            <div class="tarif-kart" style="position: relative;">
                
                <?php if($favoride_mi): ?>
                    <div style="position: absolute; top: 10px; left: 10px; background: white; color: #e74c3c; padding: 5px 10px; border-radius: 8px; font-size: 14px; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 10;">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                <?php endif; ?>

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
                        <h3 class="tarif-baslik" style="cursor: pointer; color: var(--ana-renk);"><?= guvenli_yaz($tarif['baslik']) ?></h3>
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
                            <?php if($favoride_mi): ?>
                                <a href="favoriler.php?cikar=<?= $tarif['id'] ?>" class="btn btn-kucuk" style="flex: 1; text-align: center; text-decoration: none; background: #fab1a0; color: #c0392b;">
                                    <i class="fa-solid fa-heart-crack"></i> Çıkar
                                </a>
                            <?php else: ?>
                                <a href="favoriler.php?ekle=<?= $tarif['id'] ?>" class="btn btn-kucuk" style="flex: 1; text-align: center; text-decoration: none; background: #ffeaa7; color: #d63031;">
                                    <i class="fa-regular fa-heart"></i> Ekle
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>