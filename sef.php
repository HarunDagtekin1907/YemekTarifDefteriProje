<?php
require_once 'header.php';

if (!isset($_GET['id'])) {
    die("<div class='container'><div class='panel-kart' style='text-align:center;'>Geçersiz istek. Lütfen Topluluk sayfasına dönün.</div></div>");
}

$hedef_id = (int)$_GET['id'];
$kullanicilar = json_oku('kullanicilar');
$hedef_kullanici = null;

// Tıklanan kullanıcıyı bulur
foreach ($kullanicilar as $k) {
    if ($k['id'] == $hedef_id) { $hedef_kullanici = $k; break; }
}

if (!$hedef_kullanici) {
    die("<div class='container'><div class='panel-kart' style='text-align:center;'>Şef bulunamadı. Silinmiş veya hesabı kapatılmış olabilir.</div></div>");
}

$ad_soyad = guvenli_yaz($hedef_kullanici['ad'] ?? (isset($hedef_kullanici['isim']) ? $hedef_kullanici['isim'] . ' ' . $hedef_kullanici['soyisim'] : 'Bilinmeyen Şef'));
$kullanici_adi = guvenli_yaz($hedef_kullanici['kullanici_adi'] ?? '');
$hakkimda = guvenli_yaz($hedef_kullanici['hakkimda'] ?? 'Mutfak aşığı bir lezzet ustası.');
$rol = $hedef_kullanici['rol'] ?? 'uye';

$tarifler = json_oku('tarifler');
$kategoriler = json_oku('kategoriler');
$sefin_tarifleri = [];

foreach (array_reverse($tarifler) as $t) {
    if ($t['user_id'] == $hedef_id) {
        $kat_ad = 'Bilinmiyor';
        foreach($kategoriler as $kat) { if($kat['id'] == $t['kategori_id']) { $kat_ad = $kat['kategori_ad']; break; } }
        $t['kategori_ad'] = $kat_ad;
        $t['resim_url'] = !empty($t['resim_url']) ? $t['resim_url'] : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=800&q=80';
        $sefin_tarifleri[] = $t;
    }
}
?>

<div class="panel-kart" style="max-width: 800px; margin: 0 auto 40px auto; text-align: center; border-top: 5px solid var(--birincil);">
    <div style="width: 100px; height: 100px; background: var(--acik); color: var(--koyu); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px auto;">
        <i class="fa-solid fa-user-astronaut"></i>
    </div>
    
    <h2 style="margin: 0; color: var(--koyu);"><?= $ad_soyad ?></h2>
    <div style="color: gray; font-size: 16px; margin-top: 5px;">@<?= $kullanici_adi ?></div>
    
    <?php if($rol == 'admin'): ?>
        <div style="margin-top: 10px;"><span style="background: var(--koyu); color: var(--birincil); padding: 5px 10px; border-radius: 5px; font-size: 12px; text-transform: uppercase;"><i class="fa-solid fa-shield"></i> Yönetici Şef</span></div>
    <?php endif; ?>

    <p style="color: #444; font-size: 16px; line-height: 1.6; margin: 25px auto; max-width: 600px; font-style: italic;">
        "<?= nl2br($hakkimda) ?>"
    </p>

    <div style="display: flex; justify-content: center; gap: 20px; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
        <div style="text-align: center;">
            <div style="font-size: 24px; font-weight: bold; color: var(--koyu);"><?= count($sefin_tarifleri) ?></div>
            <div style="font-size: 12px; color: gray; text-transform: uppercase;">Toplam Tarif</div>
        </div>
    </div>
</div>

<div style="text-align: center; margin-bottom: 30px;">
    <h3 style="color: var(--koyu); border-bottom: 2px solid var(--birincil); padding-bottom: 10px; display: inline-block;">
        <i class="fa-solid fa-utensils"></i> Şefin Mutfağından Çıkanlar
    </h3>
</div>

<?php if(empty($sefin_tarifleri)): ?>
    <div class="panel-kart" style="text-align: center; color: gray; max-width: 600px; margin: 0 auto;">
        <i class="fa-solid fa-plate-wheat fa-3x" style="margin-bottom: 15px; color: #ddd;"></i>
        <p>Bu şef henüz sisteme bir tarif eklememiş.</p>
    </div>
<?php else: ?>
    <div class="tarif-grid">
        <?php foreach($sefin_tarifleri as $tarif): ?>
            <div class="tarif-kart">
                <img src="<?= guvenli_yaz($tarif['resim_url']) ?>" alt="Yemek Resmi" class="tarif-resim">
                <div class="tarif-icerik">
                    <span class="kategori-etiket"><?= guvenli_yaz($tarif['kategori_ad']) ?></span>
                    <h3 class="tarif-baslik"><?= guvenli_yaz($tarif['baslik']) ?></h3>
                    
                    <p style="font-size: 13px; color: #555; height: 40px; overflow: hidden; text-overflow: ellipsis;">
                        <strong><i class="fa-solid fa-basket-shopping"></i> Malzemeler:</strong><br> 
                        <?= guvenli_yaz($tarif['malzemeler']) ?>
                    </p>
                    
                    <?php if(giris_yapilmis_mi()): ?>
                        <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                            <a href="favoriler.php?ekle=<?= $tarif['id'] ?>" class="btn btn-kucuk" style="display: block; text-align: center; text-decoration: none; background: #ffeaa7; color: #d63031;">
                                <i class="fa-solid fa-heart"></i> Favorilere Ekle
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>