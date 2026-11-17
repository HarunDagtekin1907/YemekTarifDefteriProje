<?php
$sade_gorunum = true; 
require_once 'header.php';
oturumKontrol(); 

$kategoriler = json_oku('kategoriler');
$tarifler = json_oku('tarifler');

$tarif_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$secilen_tarif = null;
$tarif_index = -1;

foreach ($tarifler as $index => $t) {
    if ($t['id'] == $tarif_id) {
        $secilen_tarif = $t;
        $tarif_index = $index;
        break;
    }
}

if (!$secilen_tarif) {
    die("<div class='container'><div class='panel-kart' style='text-align:center; color: var(--koyu-metin);'>Tarif bulunamadı veya silinmiş.</div></div>");
}

if ($secilen_tarif['user_id'] != $_SESSION['aktif_kullanici_id'] && $_SESSION['rol'] != 'admin') {
    die("<div class='container'><div class='panel-kart' style='text-align:center; color: var(--koyu-metin);'>Bu tarifi düzenleme yetkiniz bulunmuyor.</div></div>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resim_yolu = $secilen_tarif['resim_url'];
    
    // Eğer internet URL'si girildiyse önce onu eşitle
    if (isset($_POST['resim_url']) && !empty(trim($_POST['resim_url']))) {
        $resim_yolu = trim($_POST['resim_url']);
    }
    
    //  BİLGİSAYARDAN FOTOĞRAF SEÇİLİRSE 
    if (isset($_FILES['tarif_resmi']) && $_FILES['tarif_resmi']['error'] == 0) {
        $izin_verilenler = ['jpg', 'jpeg', 'png', 'webp'];
        $dosya_adi = $_FILES['tarif_resmi']['name'];
        $dosya_uzantisi = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0777, true);
        }
        
        if (in_array($dosya_uzantisi, $izin_verilenler)) {
            if ($_FILES['tarif_resmi']['size'] <= 3 * 1024 * 1024) { // Maksimum 3MB
                $yeni_ad = 'tarif_' . uniqid() . '.' . $dosya_uzantisi;
                $hedef = 'uploads/' . $yeni_ad;
                
                if (move_uploaded_file($_FILES['tarif_resmi']['tmp_name'], $hedef)) {
                    // Eğer eski resim 'uploads' klasöründeyse kalıcı olarak siler
                    if (!empty($secilen_tarif['resim_url']) && strpos($secilen_tarif['resim_url'], 'uploads/') === 0 && file_exists($secilen_tarif['resim_url'])) {
                        @unlink($secilen_tarif['resim_url']);
                    }
                    $resim_yolu = $hedef; 
                }
            }
        }
    }
    
    //Güncelle
    $tarifler[$tarif_index]['baslik'] = trim($_POST['baslik']);
    $tarifler[$tarif_index]['kategori_id'] = (int)$_POST['kategori_id'];
    $tarifler[$tarif_index]['malzemeler'] = trim($_POST['malzemeler']);
    $tarifler[$tarif_index]['hazirlanis'] = trim($_POST['hazirlanis']);
    $tarifler[$tarif_index]['resim_url'] = $resim_yolu;
    
    json_yaz('tarifler', $tarifler);
      $secilen_tarif = $tarifler[$tarif_index];
    
    echo "<div class='panel-kart' style='background-color: #e8f8f5; border-left: 4px solid #2ecc71; color: #27ae60; max-width: 600px; margin: 20px auto; border-top: none;'><i class='fa-solid fa-circle-check'></i> Tarifiniz başarıyla güncellendi! <a href='tarif_detay.php?id=" . $tarif_id . "' style='color:#27ae60; font-weight:bold; text-decoration:underline;'>Tarifi Görüntüle</a></div>";
}
?>

<div class="panel-kart" style="max-width: 600px; margin: 0 auto; border-top: 4px solid var(--vurgu);">
    <h2 style="margin-top: 0; color: var(--ana-renk);"><i class="fa-solid fa-pen-to-square" style="color: var(--vurgu);"></i> Tarifi Düzenle</h2>
    
    <form method="POST" enctype="multipart/form-data">
        
        <div class="form-group">
            <label>Tarif Başlığı</label>
            <input type="text" name="baslik" value="<?= guvenli_yaz($secilen_tarif['baslik']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Kategori</label>
            <select name="kategori_id" required>
                <?php foreach($kategoriler as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $k['id'] == $secilen_tarif['kategori_id'] ? 'selected' : '' ?>><?= guvenli_yaz($k['kategori_ad']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="text-align: center; background: var(--arka-plan); padding: 15px; border-radius: 8px; border: 1px dashed rgba(0,0,0,0.1);">
            <label style="text-align: center; margin-bottom: 10px; display: block;">Şu Anki Tarif Resmi</label>
            <img src="<?= !empty($secilen_tarif['resim_url']) ? guvenli_yaz($secilen_tarif['resim_url']) : 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?auto=format&fit=crop&w=800&q=80' ?>" style="width: 120px; height: 90px; object-fit: cover; border-radius: 6px; border: 2px solid var(--white); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        </div>
        
        <div class="form-group">
            <label>Yeni Resim Seç (Bilgisayarınızdan)</label>
            <input type="file" name="tarif_resmi" accept="image/*" style="padding: 10px; background: var(--arka-plan); border-radius: 8px; width: 100%; border: 1px solid rgba(0,0,0,0.1); cursor: pointer; outline: none; box-sizing: border-box;">
            <small style="color: var(--koyu-metin); opacity: 0.7;">Değiştirmek istemiyorsanız boş bırakın. Sadece JPG, PNG veya WEBP (Max 3MB).</small>
        </div>
        
        <div class="form-group">
            <label>Veya Yeni İnternet URL'si Yapıştır</label>
            <input type="url" name="resim_url" placeholder="https://ornek.com/yeni_resim.jpg" value="<?= strpos($secilen_tarif['resim_url'], 'uploads/') === false ? guvenli_yaz($secilen_tarif['resim_url']) : '' ?>">
            <small style="color: var(--koyu-metin); opacity: 0.7;">Bilgisayardan bir dosya seçerseniz, bu kutu geçersiz sayılacaktır.</small>
        </div>
        
        <div class="form-group">
            <label>Malzemeler</label>
            <textarea name="malzemeler" rows="5" required><?= guvenli_yaz($secilen_tarif['malzemeler']) ?></textarea>
        </div>
        
        <div class="form-group">
            <label>Hazırlanışı</label>
            <textarea name="hazirlanis" rows="6" required><?= guvenli_yaz($secilen_tarif['hazirlanis']) ?></textarea>
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <a href="tariflerim.php" class="btn btn-tehlike" style="text-decoration: none; flex: 1;">İptal Et</a>
            <button type="submit" class="btn" style="flex: 2;"><i class="fa-solid fa-floppy-disk"></i> Değişiklikleri Kaydet</button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>