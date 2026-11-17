<?php
require_once 'header.php';

$kullanicilar = json_oku('kullanicilar');
$tarifler = json_oku('tarifler');

$sef_listesi = [];
foreach ($kullanicilar as $kul) {
    $tarif_sayisi = 0;
    foreach ($tarifler as $t) { if ($t['user_id'] == $kul['id']) { $tarif_sayisi++; } }
    $ad_soyad = guvenli_yaz($kul['ad'] ?? (isset($kul['isim']) ? $kul['isim'] . ' ' . $kul['soyisim'] : 'Bilinmeyen Şef'));
    
    $sef_listesi[] = [
        'id' => $kul['id'], 'ad_soyad' => $ad_soyad, 'kullanici_adi' => guvenli_yaz($kul['kullanici_adi'] ?? ''),
        'hakkimda' => guvenli_yaz($kul['hakkimda'] ?? 'Mutfak aşığı bir lezzet ustası.'), 'tarif_sayisi' => $tarif_sayisi, 'rol' => $kul['rol'] ?? 'uye'
    ];
}

usort($sef_listesi, function($a, $b) { return $b['tarif_sayisi'] <=> $a['tarif_sayisi']; });
?>

<div style="text-align: center; margin-bottom: 40px;">
    <h2 style="color: var(--ana-renk); margin-bottom: 5px;"><i class="fa-solid fa-earth-europe" style="color: var(--vurgu);"></i> Topluluk & Şeflerimiz</h2>
    <p style="color: var(--koyu-metin); opacity: 0.8; font-size: 15px;">Lezzet ailemizin usta şefleriyle tanışın ve tariflerini keşfedin.</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
    
    <?php $sira = 1; foreach($sef_listesi as $sef): 
        $madalya = ""; $madalya_renk = "";
        
        if ($sira == 1 && $sef['tarif_sayisi'] > 0) { $madalya = "fa-medal"; $madalya_renk = "var(--vurgu)"; } 
        elseif ($sira == 2 && $sef['tarif_sayisi'] > 0) { $madalya = "fa-medal"; $madalya_renk = "#bdc3c7"; } 
        elseif ($sira == 3 && $sef['tarif_sayisi'] > 0) { $madalya = "fa-medal"; $madalya_renk = "#cd7f32"; }
    ?>
    
        <a href="profil.php?id=<?= $sef['id'] ?>" style="text-decoration: none; color: inherit; display: block; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
            <div class="panel-kart" style="text-align: center; position: relative; border-top: 4px solid <?= $madalya_renk ? $madalya_renk : 'transparent' ?>; height: 100%; box-sizing: border-box;">
                
                <?php if($madalya): ?>
                    <div style="position: absolute; top: -15px; right: 20px; font-size: 30px; color: <?= $madalya_renk ?>; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"><i class="fa-solid <?= $madalya ?>"></i></div>
                <?php endif; ?>

                <div style="width: 80px; height: 80px; background: rgba(26, 82, 118, 0.05); color: var(--ana-renk); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 30px; margin: 0 auto 15px auto;">
                    <i class="fa-solid fa-user-astronaut"></i>
                </div>
                
                <h3 style="margin: 0; color: var(--ana-renk);"><?= $sef['ad_soyad'] ?></h3>
                <div style="color: var(--vurgu); font-size: 13px; font-weight: bold; margin-bottom: 10px;">@<?= $sef['kullanici_adi'] ?></div>
                
                <?php if($sef['rol'] == 'admin'): ?>
                    <span style="background: var(--ana-renk); color: var(--vurgu); padding: 3px 8px; border-radius: 5px; font-size: 11px; text-transform: uppercase;">Yönetici Şef</span>
                <?php endif; ?>

                <p style="color: var(--koyu-metin); font-size: 14px; line-height: 1.5; margin: 15px 0; height: 42px; overflow: hidden; opacity: 0.8;">"<?= $sef['hakkimda'] ?>"</p>
                
                <div style="background: var(--arka-plan); padding: 10px; border-radius: 8px; border: 1px dashed rgba(26, 82, 118, 0.2); display: inline-block;">
                    <i class="fa-solid fa-book-open" style="color: var(--ana-renk);"></i> <strong style="color: var(--ana-renk);"><?= $sef['tarif_sayisi'] ?></strong> Tarif Ekledi
                </div>
            </div>
        </a>
        
    <?php $sira++; endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>