<?php
require_once 'header.php';

$kategoriler = json_oku('kategoriler');
$tarifler = json_oku('tarifler');
$kullanicilar = json_oku('kullanicilar');

$secili_kategori = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<div class="panel-kart" style="text-align: center; border-top: 4px solid var(--vurgu);">
    <h2 style="color: var(--ana-renk); margin-top: 0;"><i class="fa-solid fa-layer-group" style="color: var(--vurgu);"></i> Tarif Kategorileri</h2>
    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 20px;">
        
        <a href="kategoriler.php" class="btn btn-kucuk" style="background: <?= $secili_kategori === 0 ? 'var(--ana-renk)' : 'var(--beyaz)' ?>; color: <?= $secili_kategori === 0 ? 'var(--vurgu)' : 'var(--ana-renk)' ?>; border: 1px solid <?= $secili_kategori === 0 ? 'transparent' : 'var(--ana-renk)' ?>; text-decoration: none;">Tümü</a>
        
        <?php foreach($kategoriler as $k): ?>
            <a href="kategoriler.php?id=<?= $k['id'] ?>" class="btn btn-kucuk" style="background: <?= $secili_kategori === $k['id'] ? 'var(--ana-renk)' : 'var(--beyaz)' ?>; color: <?= $secili_kategori === $k['id'] ? 'var(--vurgu)' : 'var(--ana-renk)' ?>; border: 1px solid <?= $secili_kategori === $k['id'] ? 'transparent' : 'var(--ana-renk)' ?>; text-decoration: none;"><?= guvenli_yaz($k['kategori_ad']) ?></a>
        <?php endforeach; ?>
        
    </div>
</div>

<div class="tarif-grid">
    <?php 
    $gosterilen_sayi = 0;
    foreach(array_reverse($tarifler) as $t): 
        if ($secili_kategori !== 0 && $t['kategori_id'] !== $secili_kategori) continue;
        
        $kat_ad = 'Bilinmiyor';
        foreach($kategoriler as $k) { if($k['id'] == $t['kategori_id']) { $kat_ad = $k['kategori_ad']; break; } }
        
        $t['resim_url'] = !empty($t['resim_url']) ? $t['resim_url'] : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=800&q=80';
        $gosterilen_sayi++;
    ?>
        <a href="tarif_detay.php?id=<?= $t['id'] ?>" class="tarif-kart" style="display: block; text-decoration: none; color: inherit;">
            <img src="<?= guvenli_yaz($t['resim_url']) ?>" alt="Resim" class="tarif-resim">
            <div class="tarif-icerik">
                <span class="kategori-etiket"><?= guvenli_yaz($kat_ad) ?></span>
                <h3 class="tarif-baslik" style="color: var(--ana-renk);"><?= guvenli_yaz($t['baslik']) ?></h3>
                
                <?php if(giris_yapilmis_mi()): ?>
                    <object><a href="favoriler.php?ekle=<?= $t['id'] ?>" class="btn btn-kucuk" style="display: block; text-align: center; margin-top: 15px; text-decoration: none; background: #ffeaa7; color: #d63031;"><i class="fa-solid fa-heart"></i> Favorilere Ekle</a></object>
                <?php endif; ?>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<?php if($gosterilen_sayi === 0): ?>
    <div class="panel-kart" style="text-align: center; color: var(--koyu-metin); opacity: 0.7;">
        <i class="fa-solid fa-folder-open fa-3x" style="margin-bottom: 15px; color: #ccc;"></i>
        <p>Bu kategoride henüz tarif bulunmuyor.</p>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>