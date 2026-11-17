<?php
$sade_gorunum = true; 
require_once 'header.php';
if (giris_yapilmis_mi()) { header("Location: index.php"); exit(); }

$hata = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $giris_bilgisi = trim($_POST['giris_bilgisi']); 
    $sifre = trim($_POST['sifre']);
    
    $kullanicilar = json_oku('kullanicilar');
    $giris_basarili = false;
    
    foreach ($kullanicilar as $k) 
    {
        $kullanici_adi_var_mi = isset($k['kullanici_adi']) ? $k['kullanici_adi'] : '';
    
        if (($k['eposta'] === $giris_bilgisi || $kullanici_adi_var_mi === $giris_bilgisi) && $k['sifre'] === $sifre) {
            $_SESSION['aktif_kullanici_id'] = $k['id'];
            $_SESSION['kullanici_adi'] = isset($k['kullanici_adi']) ? $k['kullanici_adi'] : (isset($k['ad']) ? $k['ad'] : 'Şef'); 
            $_SESSION['rol'] = $k['rol'];
            $giris_basarili = true;
            header("Location: index.php");
            exit();
        }
    }
    
    if (!$giris_basarili) { $hata = 'Girdiğiniz bilgiler hatalı!'; }
}
?>

<div style="max-width: 400px; margin: 40px auto; text-align: center;">
    <div class="panel-kart">
        <i class="fa-solid fa-circle-user fa-3x" style="color: var(--koyu); margin-bottom: 15px;"></i>
        <h2 style="margin-top: 0; color: var(--koyu);">Giriş Yap</h2>
        <p style="color: gray; font-size: 13px; margin-bottom: 20px;">E-posta adresiniz veya kullanıcı adınız ile giriş yapabilirsiniz.</p>
        
        <?php if($hata): ?><div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?= guvenli_yaz($hata) ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label style="text-align: left;">E-posta veya Kullanıcı Adı</label>
                <input type="text" name="giris_bilgisi" required>
            </div>
            <div class="form-group">
                <label style="text-align: left;">Şifre</label>
                <input type="password" name="sifre" required>
            </div>
            <button type="submit" class="btn"><i class="fa-solid fa-right-to-bracket"></i> Giriş Yap</button>
        </form>
        
        <p style="margin-top: 20px; font-size: 13px;">Hesabınız yok mu? <a href="kayit.php" style="color: var(--birincil); font-weight:600;">Kayıt Olun</a></p>
    </div>
</div>
<?php require_once 'footer.php'; ?>