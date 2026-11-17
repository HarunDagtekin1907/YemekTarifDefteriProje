<?php
require_once 'header.php';
oturumKontrol(); 

$kullanici_id = $_SESSION['aktif_kullanici_id'];
$tarifler = json_oku('tarifler');
$kategoriler = json_oku('kategoriler');

$kendi_tariflerim = [];

foreach (array_reverse($tarifler) as $t) {
    
    // SADECE AKTİF KULLANICIYA AİT TARİFLERİ FİLTRELE
    if ($t['user_id'] == $kullanici_id) {
        
        $kat_ad = 'Bilinmiyor';
        foreach($kategoriler as $k) { 
            if($k['id'] == $t['kategori_id']) { 
                $kat_ad = $k['kategori_ad']; 
                break; 
            } 
        }
        
        $t['kategori_ad'] = $kat_ad;
        $t['resim_url'] = !empty($t['resim_url']) ? $t['resim_url'] : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=800&q=80';
        
        $kendi_tariflerim[] = $t;
    }
}
?>

<div style="text-align: center; margin-bottom: 30px;">
    <h2 style="color: var(--koyu); margin-bottom: 5px;"><i class="fa-solid fa-book-open"></i> Benim Tariflerim</h2>
    <p style="color: var(--gri); font-size: 15px;">Kendi mutfağınızdan paylaştığınız tüm lezzetler burada.</p>
</div>

<?php if(empty($kendi_tariflerim)): ?>
    <div class="panel-kart" style="text-align: center; color: gray;">
        <i class="fa-solid fa-folder-open fa-3x" style="margin-bottom: 15px; color: #ddd;"></i>
        <p>Henüz kendi tarifinizi eklemediniz.</p>
        <br>
        <a href="tarif_ekle.php" class="btn" style="text-decoration:none; display: inline-block; width: auto;"><i class="fa-solid fa-plus"></i> Hemen İlk Tarifini Ekle</a>
    </div>
<?php else: ?>
    <div class="tarif-grid">
        <?php foreach($kendi_tariflerim as $tarif): ?>
            <div class="tarif-kart">
                <img src="<?= guvenli_yaz($tarif['resim_url']) ?>" alt="Yemek Resmi" class="tarif-resim">
                <div class="tarif-icerik">
                    <span class="kategori-etiket"><?= guvenli_yaz($tarif['kategori_ad']) ?></span>
                    <h3 class="tarif-baslik"><?= guvenli_yaz($tarif['baslik']) ?></h3>
                    
                    <p style="font-size: 13px; color: #555; height: 40px; overflow: hidden; text-overflow: ellipsis;">
                        <strong><i class="fa-solid fa-basket-shopping"></i> Malzemeler:</strong><br> 
                        <?= guvenli_yaz($tarif['malzemeler']) ?>
                    </p>
                    
                    <div class="aksiyon-butonlari">
                        <a href="tarif_duzenle.php?id=<?= $tarif['id'] ?>" class="btn btn-kucuk" style="text-align: center; text-decoration: none; flex: 1; background: #3498db; color: white;">
                            <i class="fa-solid fa-pen"></i> Düzenle
                        </a>
                        
                        <a href="tarifsil.php?id=<?= $tarif['id'] ?>" class="btn btn-kucuk btn-tehlike" onclick="return confirm('Bu tarifinizi tamamen silmek istediğinize emin misiniz? Bu işlem geri alınamaz.');" style="text-align: center; text-decoration: none; flex: 1;">
                            <i class="fa-solid fa-trash"></i> Sil
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>