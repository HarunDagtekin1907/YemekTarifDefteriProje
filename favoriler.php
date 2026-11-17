<?php
require_once 'functions.php'; 
// Giriş kontrolü 
if (!giris_yapilmis_mi()) {
    require_once 'header.php';
    echo "<div class='container'><div class='panel-kart' style='text-align:center;'>Favorilerinizi görmek için <a href='login.php'>giriş yapmalısınız</a>.</div></div>";
    require_once 'footer.php';
    exit();
}

$aktif_user_id = $_SESSION['aktif_kullanici_id'];
$favoriler = json_oku('favoriler');


if (isset($_GET['ekle'])) {
    $tarif_id = (int)$_GET['ekle'];
    
    $zaten_ekli = false;
    foreach ($favoriler as $f) {
        if ($f['user_id'] == $aktif_user_id && $f['tarif_id'] == $tarif_id) { 
            $zaten_ekli = true; 
            break; 
        }
    }
    
    if (!$zaten_ekli) {
        $favoriler[] = ['user_id' => $aktif_user_id, 'tarif_id' => $tarif_id];
        json_yaz('favoriler', $favoriler);
    }
    
    // Eğer istek JavaScript (AJAX) ile geldiyse sadece 'ok' yaz ve kodun çalışmasını durdur
    if (isset($_GET['ajax'])) 
    { 
        echo "ok"; 
        exit(); 
    }
    
    $gelinen_sayfa = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'tum_tarifler.php';
    header("Location: " . $gelinen_sayfa);
    exit();
}


if (isset($_GET['cikar'])) {
    $tarif_id = (int)$_GET['cikar'];
    $yeni_favoriler = [];
    foreach ($favoriler as $f) {
        if (!($f['user_id'] == $aktif_user_id && $f['tarif_id'] == $tarif_id)) {
            $yeni_favoriler[] = $f;
        }
    }
    json_yaz('favoriler', $yeni_favoriler);
    
    if (isset($_GET['ajax'])) { 
        echo "ok"; 
        exit(); 
    }
    
    $gelinen_sayfa = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'tum_tarifler.php';
    header("Location: " . $gelinen_sayfa);
    exit(); 
}


require_once 'header.php';

$tarifler = json_oku('tarifler');
$kategoriler = json_oku('kategoriler');
$kullanicilar = json_oku('kullanicilar');

// Sadece giriş yapan kişinin favori id sini bulur
$favori_tarif_idleri = [];
foreach ($favoriler as $f) {
    if ($f['user_id'] == $aktif_user_id) {
        $favori_tarif_idleri[] = $f['tarif_id'];
    }
}

// Sadece favoriler gösterir
$gosterilecek_tarifler = [];
foreach (array_reverse($tarifler) as $t) 
{
    if (in_array($t['id'], $favori_tarif_idleri))
    {
        $kat_ad = 'Bilinmiyor';
        foreach($kategoriler as $k) 
        { if($k['id'] == $t['kategori_id']) 
          { 
            $kat_ad = $k['kategori_ad']; break;
          } 
        }
        $kul_ad = 'Bilinmiyor';
        foreach($kullanicilar as $u) 
        { if($u['id'] == $t['user_id']) 
        { 
            $kul_ad = isset($u['isim']) ? $u['isim'] . ' ' . $u['soyisim'] : $u['ad']; break;
        }
         }
        $t['kategori_ad'] = $kat_ad;
        $t['ekleyen'] = $kul_ad;
        $t['resim_url'] = !empty($t['resim_url']) ? $t['resim_url'] : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=800&q=80';
        $gosterilecek_tarifler[] = $t;
    }
}
?>

<div style="text-align: center; margin-bottom: 30px;">
    <h2 style="color: var(--koyu); margin-bottom: 5px;"><i class="fa-solid fa-heart" style="color: #e74c3c;"></i> Favori Tariflerim</h2>
    <p style="color: var(--gri); font-size: 15px;">Daha sonra denemek veya tekrar yapmak için kaydettiğiniz tarifleriniz burada görebilirsiniz.</p>
</div>

<?php if(empty($gosterilecek_tarifler)):
     ?>
    <div class="panel-kart" style="text-align: center; color: gray;">
        <i class="fa-solid fa-heart-crack fa-3x" style="margin-bottom: 15px; color: #ddd;"></i>
        <p>Henüz favorilerinize hiç tarif eklememişsiniz.</p>
        <a href="tum_tarifler.php" class="btn" style="display: inline-block; margin-top: 15px;"><i class="fa-solid fa-magnifying-glass"></i> Tarifleri Keşfet</a>
    </div>
<?php else: ?>
    <div class="tarif-grid">
        <?php foreach($gosterilecek_tarifler as $tarif): ?>
            <div class="tarif-kart" style="position: relative;">
                
                <a href="tarif_detay.php?id=<?= $tarif['id'] ?>">
                    <img src="<?= guvenli_yaz($tarif['resim_url']) ?>" alt="Yemek" class="tarif-resim" style="transition: opacity 0.3s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                </a>
                
                <div class="tarif-icerik">
                    <span class="kategori-etiket"><?= guvenli_yaz($tarif['kategori_ad']) ?></span>
                    
                    <a href="tarif_detay.php?id=<?= $tarif['id'] ?>" style="text-decoration: none; color: inherit;">
                        <h3 class="tarif-baslik" style="cursor: pointer;"><?= guvenli_yaz($tarif['baslik']) ?></h3>
                    </a>
                    
                    <p style="font-size: 13px; color: #555; height: 40px; overflow: hidden; text-overflow: ellipsis;">
                        <strong><i class="fa-solid fa-basket-shopping"></i> Malzemeler:</strong><br> <?= guvenli_yaz($tarif['malzemeler']) ?>
                    </p>
                    
                    <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px; display: flex; gap: 10px;">
                        <a href="tarif_detay.php?id=<?= $tarif['id'] ?>" class="btn btn-kucuk" style="flex: 1; text-align: center; text-decoration: none; background: var(--koyu); color: white;">
                            <i class="fa-solid fa-book-open"></i> Oku
                        </a>
                        <a href="favoriler.php?cikar=<?= $tarif['id'] ?>" class="btn btn-kucuk" style="flex: 1; text-align: center; text-decoration: none; background: #fab1a0; color: #c0392b;">
                            <i class="fa-solid fa-trash"></i> Kaldır
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>