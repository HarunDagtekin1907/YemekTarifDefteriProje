<?php
$sade_gorunum = true;
require_once 'header.php';
if (giris_yapilmis_mi()) { header("Location: index.php"); exit(); }

$hata = ''; $basari = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanicilar = json_oku('kullanicilar');
    
    $yeni_kullanici = [
        'id' => yeni_id_uret($kullanicilar),
        'isim' => trim($_POST['isim']),
        'soyisim' => trim($_POST['soyisim']),
        'tel' => trim($_POST['tel']),
        'tc' => trim($_POST['tc']),
        'dogum_tarihi' => trim($_POST['dogum_tarihi']),
        'cinsiyet' => trim($_POST['cinsiyet']),
        'eposta' => trim($_POST['eposta']),
        'ulke' => trim($_POST['ulke']),
        'sehir' => trim($_POST['sehir']),
        'kullanici_adi' => trim($_POST['kullanici_adi']),
        'sifre' => trim($_POST['sifre']),
        'rol' => 'kullanici', 
        'ad' => trim($_POST['isim']) . ' ' . trim($_POST['soyisim']) 
    ];

    $kullanimda_mi = false;
    foreach ($kullanicilar as $k) {
        if ($k['eposta'] === $yeni_kullanici['eposta']) {
            $hata = 'Bu e-posta adresi zaten kullanılıyor!';
            $kullanimda_mi = true; break;
        }
        if (isset($k['kullanici_adi']) && $k['kullanici_adi'] === $yeni_kullanici['kullanici_adi']) {
            $hata = 'Bu kullanıcı adı zaten alınmış!';
            $kullanimda_mi = true; break;
        }
    }

    if (!$kullanimda_mi) {
        $kullanicilar[] = $yeni_kullanici;
        json_yaz('kullanicilar', $kullanicilar);
        $basari = 'Hesabınız başarıyla oluşturuldu! Şimdi giriş yapabilirsiniz.';
    }
}
?>

<style>
    /* Kayıt formuna özel ikili grid yapısı (mobilde tek sütuna düşer) */
    .ikili-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: left; }
    .tam-genislik { grid-column: 1 / -1; }
    @media (max-width: 600px) { .ikili-grid { grid-template-columns: 1fr; } }
</style>

<div style="max-width: 600px; margin: 40px auto; text-align: center;">
    <div class="panel-kart">
        <i class="fa-solid fa-address-card fa-3x" style="color: var(--koyu); margin-bottom: 15px;"></i>
        <h2 style="margin-top: 0; color: var(--koyu);">Aramıza Katılın</h2>
        <p style="color: gray; font-size: 14px; margin-bottom: 25px;">Tüm tariflere ulaşmak ve kendi tariflerinizi paylaşmak için kayıt olun.</p>
        
        <?php if($hata): ?><div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?= guvenli_yaz($hata) ?></div><?php endif; ?>
        <?php if($basari): ?><div style="background: #e8f8f5; color: #27ae60; padding: 10px; border-radius: 5px; margin-bottom: 15px;"><?= guvenli_yaz($basari) ?> <br><br> <a href="login.php" class="btn btn-kucuk" style="text-decoration:none;">Giriş Sayfasına Git</a></div><?php endif; ?>

        <?php if(!$basari): // Başarılıysa formu gizle ?>
        <form method="POST">
            <div class="ikili-grid">
                <div class="form-group"><label>İsim</label><input type="text" name="isim" required></div>
                <div class="form-group"><label>Soyisim</label><input type="text" name="soyisim" required></div>
                
                <div class="form-group"><label>T.C. Kimlik No</label><input type="text" name="tc" maxlength="11" pattern="\d{11}" title="11 haneli TC kimlik numaranızı girin" required></div>
                <div class="form-group"><label>Doğum Tarihi</label><input type="date" name="dogum_tarihi" required></div>
                
                <div class="form-group"><label>Telefon Numarası</label><input type="tel" name="tel" placeholder="0555..." required></div>
                <div class="form-group">
                    <label>Cinsiyet</label>
                    <select name="cinsiyet" required>
                        <option value="">Seçiniz...</option>
                        <option value="Kadin">Kadın</option>
                        <option value="Erkek">Erkek</option>
                    </select>
                </div>

                <div class="form-group"><label>Ülke</label><input type="text" name="ulke" value="Türkiye" required></div>
                <div class="form-group"><label>Şehir</label><input type="text" name="sehir" placeholder="Örn: İzmir" required></div>
                
                <hr class="tam-genislik" style="border:0; border-top: 1px solid #eee; margin: 10px 0;">

                <div class="form-group"><label>E-posta Adresi</label><input type="email" name="eposta" required></div>
                <div class="form-group"><label>Kullanıcı Adı</label><input type="text" name="kullanici_adi" placeholder="Örn: sef_harun" required></div>
                
                <div class="form-group tam-genislik"><label>Şifre</label><input type="password" name="sifre" required></div>
                
                <div class="tam-genislik">
                    <button type="submit" class="btn" style="margin-top: 10px;"><i class="fa-solid fa-user-check"></i> Hesabımı Oluştur</button>
                </div>
            </div>
        </form>
        <p style="margin-top: 20px; font-size: 13px;">Zaten bir hesabınız var mı? <a href="login.php" style="color: var(--birincil); font-weight:600;">Giriş Yapın</a></p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>