<?php
require_once 'header.php';
oturumKontrol(); 

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $eski_sifre = trim($_POST['eski_sifre']);
    $yeni_sifre = trim($_POST['yeni_sifre']);
    $yeni_sifre_tekrar = trim($_POST['yeni_sifre_tekrar']);
    
    $kullanicilar = json_oku('kullanicilar');
    $aktif_id = $_SESSION['aktif_kullanici_id'];
    $kullanici_index = -1;
    
    // Kullanıcıyı bulur
    foreach($kullanicilar as $index => $k) { 
        if($k['id'] == $aktif_id) { $kullanici_index = $index; break; } 
    }
    
    if ($kullanici_index != -1) {
        
        if ($kullanicilar[$kullanici_index]['sifre'] != $eski_sifre) {
            $mesaj = "<div style='background: #fadbd8; color: #c0392b; padding: 15px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation'></i> Mevcut şifrenizi yanlış girdiniz. Lütfen tekrar deneyin.</div>";
        } elseif ($yeni_sifre !== $yeni_sifre_tekrar) {
            $mesaj = "<div style='background: #fadbd8; color: #c0392b; padding: 15px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation'></i> Girdiğiniz yeni şifreler birbiriyle eşleşmiyor.</div>";
        } elseif (strlen($yeni_sifre) < 6) {
            $mesaj = "<div style='background: #fadbd8; color: #c0392b; padding: 15px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-triangle-exclamation'></i> Yeni şifreniz güvenlik sebebiyle en az 6 karakterden oluşmalıdır.</div>";
        } else {
            // Şifreyi güncelle ve kaydet
            $kullanicilar[$kullanici_index]['sifre'] = $yeni_sifre;
            json_yaz('kullanicilar', $kullanicilar);
            
            $mesaj = "<div style='background: #d5f5e3; color: #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-check-circle'></i> Şifreniz başarıyla değiştirildi! Artık yeni şifrenizle giriş yapabilirsiniz.</div>";
        }
    }
}
?>

<div style="max-width: 500px; margin: 40px auto;">
    
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: var(--koyu); margin-bottom: 5px;"><i class="fa-solid fa-shield-halved"></i> Şifre Değiştir</h2>
        <p style="color: var(--gri); font-size: 15px;">Hesap güvenliğiniz için şifrenizi güçlü tutun.</p>
    </div>

    <?= $mesaj ?>

    <div class="panel-kart">
        <form method="POST">
            <div class="form-group">
                <label><i class="fa-solid fa-unlock-keyhole"></i> Mevcut Şifreniz</label>
                <input type="password" name="eski_sifre" required placeholder="Şu anki şifrenizi girin">
            </div>
            
            <div style="border-top: 1px dashed #ddd; margin: 20px 0;"></div>
            
            <div class="form-group">
                <label><i class="fa-solid fa-key"></i> Yeni Şifre</label>
                <input type="password" name="yeni_sifre" required placeholder="En az 6 karakter girin">
            </div>
            
            <div class="form-group">
                <label><i class="fa-solid fa-key"></i> Yeni Şifre (Tekrar)</label>
                <input type="password" name="yeni_sifre_tekrar" required placeholder="Yeni şifrenizi tekrar girin">
            </div>
            
            <button type="submit" class="btn" style="width: 100%; padding: 12px; font-size: 16px;"><i class="fa-solid fa-floppy-disk"></i> Şifreyi Güncelle</button>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="profil.php" style="color: gray; text-decoration: none; font-size: 14px;"><i class="fa-solid fa-arrow-left"></i> Profile Geri Dön</a>
            </div>
        </form>
    </div>

</div>

<?php require_once 'footer.php'; ?>