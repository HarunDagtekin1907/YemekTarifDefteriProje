<?php
$sade_gorunum = true; 
require_once 'header.php';

$mesaj = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mesajlar = json_oku('iletisim');
    
    $mesajlar[] = [
        'id' => yeni_id_uret($mesajlar),
        'ad_soyad' => trim($_POST['ad_soyad']),
        'eposta' => trim($_POST['eposta']),
        'konu' => trim($_POST['konu']),
        'icerik' => trim($_POST['icerik']),
        'tarih' => date('d.m.Y H:i')
    ];
    
    json_yaz('iletisim', $mesajlar);
    $mesaj = "<div style='background: #e8f8f5; color: #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #27ae60;'><i class='fa-solid fa-paper-plane'></i> Mesajınız başarıyla gönderildi. En kısa sürede dönüş yapacağız.</div>";
}
?>

<div style="max-width: 600px; margin: 40px auto;">
    <div class="panel-kart" style="border-top: 4px solid var(--vurgu);">
        <h2 style="margin-top: 0; color: var(--ana-renk); text-align: center;"><i class="fa-solid fa-envelope" style="color: var(--vurgu);"></i> Bize Ulaşın</h2>
        <p style="text-align: center; color: var(--koyu-metin); opacity: 0.8; margin-bottom: 25px;">Soru, öneri ve işbirlikleri için aşağıdaki formu doldurabilirsiniz.</p>
        
        <?= $mesaj ?>

        <form method="POST">
            <div class="ikili-grid">
                <div class="form-group"><label>Adınız Soyadınız</label><input type="text" name="ad_soyad" required></div>
                <div class="form-group"><label>E-posta Adresiniz</label><input type="email" name="eposta" required></div>
            </div>
            
            <div class="form-group"><label>Konu</label><input type="text" name="konu" required></div>
            <div class="form-group"><label>Mesajınız</label><textarea name="icerik" rows="5" required></textarea></div>
            
            <button type="submit" class="btn"><i class="fa-solid fa-paper-plane"></i> Mesajı Gönder</button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>