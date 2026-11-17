<?php
$sade_gorunum = true; 
require_once 'header.php';
oturumKontrol(); 

$kategoriler = json_oku('kategoriler');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tarifler = json_oku('tarifler');
    
    $resim_yolu = trim($_POST['resim_url']);
    
    if (isset($_FILES['tarif_resmi']) && $_FILES['tarif_resmi']['error'] == 0) {
        $izin_verilenler = ['jpg', 'jpeg', 'png', 'webp'];
        $dosya_adi = $_FILES['tarif_resmi']['name'];
        $dosya_uzantisi = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));
        
        // uploads klasörü yoksa oluştur
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        // Uzantı ve Boyut (Max 3MB) Kontrolü
        if (in_array($dosya_uzantisi, $izin_verilenler)) {
            if ($_FILES['tarif_resmi']['size'] <= 3 * 1024 * 1024) { 
                $yeni_ad = 'tarif_' . uniqid() . '.' . $dosya_uzantisi;
                $hedef = 'uploads/' . $yeni_ad;
                
                if (move_uploaded_file($_FILES['tarif_resmi']['tmp_name'], $hedef)) {
                    $resim_yolu = $hedef; // Yüklenen dosyanın yolunu URL yerine kullan
                }
            }
        }
    }
    
    //  Yeni tarifi dizi olarak oluştur
    $yeni_tarif = [
        'id' => yeni_id_uret($tarifler),
        'user_id' => $_SESSION['aktif_kullanici_id'],
        'kategori_id' => (int)$_POST['kategori_id'],
        'baslik' => trim($_POST['baslik']),
        'malzemeler' => trim($_POST['malzemeler']),
        'hazirlanis' => trim($_POST['hazirlanis']),
        'resim_url' => $resim_yolu // Yüklenen resim veya girilen URL
    ];
    
    // Yeni tarifi diziye ekle ve kaydet
    $tarifler[] = $yeni_tarif;
    json_yaz('tarifler', $tarifler);
    
    echo "<div class='panel-kart' style='background-color: #e8f8f5; border-left: 4px solid #2ecc71; color: #27ae60; max-width: 600px; margin: 20px auto; border-top: none;'><i class='fa-solid fa-circle-check'></i> Tarifiniz başarıyla eklendi! <a href='index.php' style='color:#27ae60; font-weight:bold; text-decoration:underline;'>Ana sayfaya dön</a></div>";
}
?>

<div class="panel-kart" style="max-width: 600px; margin: 0 auto; border-top: 4px solid var(--vurgu);">
    <h2 style="margin-top: 0; color: var(--ana-renk);"><i class="fa-solid fa-folder-plus" style="color: var(--vurgu);"></i> Yeni Tarif Ekle</h2>
    
    <!-- ENCTYPE EKLENDİ: Dosya yükleme işlemi için zorunludur -->
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tarif Başlığı</label>
            <input type="text" name="baslik" placeholder="Örn: Fırında Somon" required>
        </div>
        
        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" required>
                <option value="">Seçiniz...</option>
                <?php foreach($kategoriler as $k): ?>
                    <option value="<?= $k['id'] ?>"><?= guvenli_yaz($k['kategori_ad']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Yemek Resmi Yükle (Bilgisayarınızdan)</label>
            <input type="file" name="tarif_resmi" accept="image/*" style="padding: 10px; background: var(--arka-plan); border-radius: 8px; width: 100%; border: 1px solid rgba(0,0,0,0.1); cursor: pointer; outline: none; box-sizing: border-box;">
            <small style="color: var(--koyu-metin); opacity: 0.7;">Sadece JPG, PNG veya WEBP. (Maksimum 3MB boyutunda)</small>
        </div>
        
        <div class="form-group">
            <label>Veya İnternet URL'si (İsteğe Bağlı)</label>
            <input type="url" name="resim_url" placeholder="https://ornek.com/resim.jpg">
            <small style="color: var(--koyu-metin); opacity: 0.7;">Eğer üstteki alandan bilgisayarınızdan resim seçerseniz, bu alan yok sayılır.</small>
        </div>
        
        <div class="form-group">
            <label>Malzemeler</label>
            <textarea name="malzemeler" rows="4" required></textarea>
        </div>
        
        <div class="form-group">
            <label>Hazırlanışı</label>
            <textarea name="hazirlanis" rows="5" required></textarea>
        </div>
        
        <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> Tarifi Paylaş</button>
    </form>
</div>

<?php require_once 'footer.php'; ?>