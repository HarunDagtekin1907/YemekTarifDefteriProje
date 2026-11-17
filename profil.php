<?php
require_once 'header.php';
oturumKontrol();

$kullanicilar = json_oku('kullanicilar');
$tarifler = json_oku('tarifler');
$kategoriler = json_oku('kategoriler');

$aktif_id = $_SESSION['aktif_kullanici_id'];
$goruntulenen_id = (isset($_GET['id']) && $_GET['id'] != '') ? (int)$_GET['id'] : $aktif_id;
$kendi_profili = ($aktif_id === $goruntulenen_id);

$profil_sahibi = null;
$kullanici_index = -1;

foreach ($kullanicilar as $index => $k) {
    if ($k['id'] == $goruntulenen_id) { $profil_sahibi = $k; $kullanici_index = $index; break; }
}

if (!$profil_sahibi) { die("<div class='container'><div class='panel-kart' style='text-align:center; color: var(--koyu-metin);'>Kullanıcı bulunamadı.</div></div>"); }

$mesaj = '';

if (isset($_GET['islem'])) {
    if ($_GET['islem'] == 'foto_silindi') {
        $mesaj = "<div style='background: #d5f5e3; color: #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center;'><i class='fa-solid fa-check-circle'></i> Profil fotoğrafınız başarıyla kaldırıldı.</div>";
    } elseif ($_GET['islem'] == 'guncellendi') {
        $mesaj = "<div style='background: #d5f5e3; color: #27ae60; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center;'><i class='fa-solid fa-check-circle'></i> İşlem başarıyla kaydedildi!</div>";
    } elseif ($_GET['islem'] == 'hata_boyut') {
        $mesaj = "<div style='background: #fadbd8; color: #c0392b; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center;'><i class='fa-solid fa-triangle-exclamation'></i> Fotoğraf boyutu en fazla 3MB olabilir.</div>";
    } elseif ($_GET['islem'] == 'hata_uzanti') {
        $mesaj = "<div style='background: #fadbd8; color: #c0392b; padding: 15px; border-radius: 8px; margin-bottom: 25px; text-align: center;'><i class='fa-solid fa-triangle-exclamation'></i> Sadece JPG, PNG veya WEBP formatı yükleyebilirsiniz.</div>";
    }
}

if ($kendi_profili && isset($_POST['foto_kaldir_aksiyon'])) {
    if (!empty($profil_sahibi['profil_foto']) && file_exists($profil_sahibi['profil_foto'])) {
        @unlink($profil_sahibi['profil_foto']);
    }
    $kullanicilar[$kullanici_index]['profil_foto'] = '';
    json_yaz('kullanicilar', $kullanicilar);
    header("Location: profil.php?islem=foto_silindi");
    exit();
}

// SİLME 
if ($kendi_profili && isset($_POST['profili_kaldir'])) {
    if (!empty($profil_sahibi['profil_foto']) && file_exists($profil_sahibi['profil_foto'])) {
        @unlink($profil_sahibi['profil_foto']);
    }
    unset($kullanicilar[$kullanici_index]);
    $kullanicilar = array_values($kullanicilar);
    json_yaz('kullanicilar', $kullanicilar);
    header("Location: logout.php");
    exit();
}


//  FOTOĞRAF YÜKLEME 

if ($kendi_profili && isset($_POST['foto_guncelle_aksiyon'])) {
    if (isset($_FILES['profil_foto']) && $_FILES['profil_foto']['error'] == 0) {
        $izin_verilen_uzantilar = ['jpg', 'jpeg', 'png', 'webp'];
        $dosya_adi = $_FILES['profil_foto']['name'];
        $dosya_boyutu = $_FILES['profil_foto']['size'];
        $dosya_gecici_yol = $_FILES['profil_foto']['tmp_name'];
        $dosya_uzantisi = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));
        
        if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
        
        if (in_array($dosya_uzantisi, $izin_verilen_uzantilar)) {
            if ($dosya_boyutu <= 3 * 1024 * 1024) { 
                $yeni_dosya_adi = 'foto_' . uniqid() . '.' . $dosya_uzantisi;
                $hedef_yukleme_yolu = 'uploads/' . $yeni_dosya_adi;
                
                if (move_uploaded_file($dosya_gecici_yol, $hedef_yukleme_yolu)) {
                    if (!empty($profil_sahibi['profil_foto']) && file_exists($profil_sahibi['profil_foto'])) {
                        @unlink($profil_sahibi['profil_foto']);
                    }
                    $kullanicilar[$kullanici_index]['profil_foto'] = $hedef_yukleme_yolu;
                    json_yaz('kullanicilar', $kullanicilar);
                    header("Location: profil.php?islem=guncellendi");
                    exit();
                }
            } else {
                header("Location: profil.php?islem=hata_boyut");
                exit();
            }
        } else {
            header("Location: profil.php?islem=hata_uzanti");
            exit();
        }
    }
}


 // GENEL BİLGİLERİ KAYDETME 

if ($kendi_profili && isset($_POST['bilgi_guncelle_aksiyon'])) {
    $kullanicilar[$kullanici_index]['isim'] = trim($_POST['isim']);
    $kullanicilar[$kullanici_index]['soyisim'] = trim($_POST['soyisim']);
    $kullanicilar[$kullanici_index]['eposta'] = trim($_POST['eposta']);
    $kullanicilar[$kullanici_index]['hakkimda'] = trim($_POST['hakkimda']);
    
    if (isset($_POST['cinsiyet'])) { $kullanicilar[$kullanici_index]['cinsiyet'] = trim($_POST['cinsiyet']); }
    if (isset($_POST['dogum_tarihi'])) { $kullanicilar[$kullanici_index]['dogum_tarihi'] = trim($_POST['dogum_tarihi']); } 
    
    json_yaz('kullanicilar', $kullanicilar);
    header("Location: profil.php?islem=guncellendi");
    exit();
}


$isim_soyisim = guvenli_yaz(($profil_sahibi['isim'] ?? $profil_sahibi['ad'] ?? '') . ' ' . ($profil_sahibi['soyisim'] ?? ''));
$eposta = guvenli_yaz($profil_sahibi['eposta'] ?? '');
$kullanici_adi = guvenli_yaz($profil_sahibi['kullanici_adi'] ?? '');
$cinsiyet = guvenli_yaz($profil_sahibi['cinsiyet'] ?? '');
$dogum_tarihi = guvenli_yaz($profil_sahibi['dogum_tarihi'] ?? '');
$hakkimda = guvenli_yaz($profil_sahibi['hakkimda'] ?? 'Mutfak aşığı bir lezzet ustası.');
$rol = $profil_sahibi['rol'] ?? 'uye';
$foto_yolu = $profil_sahibi['profil_foto'] ?? ''; 

$yas = '';
if (!empty($dogum_tarihi)) {
    try {
        $dt_dogum = new DateTime($dogum_tarihi); $dt_bugun = new DateTime();
        $yas = $dt_dogum->diff($dt_bugun)->y;
    } catch (Exception $e) { $yas = ''; }
}
?>

<div style="max-width: 800px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h2 style="color: var(--ana-renk); margin-bottom: 5px;"><i class="fa-solid <?= $kendi_profili ? 'fa-id-badge' : 'fa-user-astronaut' ?>" style="color: var(--vurgu);"></i> <?= $kendi_profili ? 'Profilim' : 'Şef Profili' ?></h2>
    </div>

    <?= $mesaj ?>

    <div class="panel-kart" style="text-align: center; border-top: 5px solid var(--vurgu); margin-bottom: 30px;">
        <?php if($kendi_profili): ?>
            
            <form id="foto_yukleme_formu" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="foto_guncelle_aksiyon" value="1">
                
                <input type="file" name="profil_foto" id="gizli_foto_input" accept="image/*" style="display: none;" onchange="document.getElementById('foto_yukleme_formu').submit();">
                
                <div onclick="document.getElementById('gizli_foto_input').click()" style="cursor: pointer; position: relative; width: 120px; height: 120px; margin: 0 auto 10px auto; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: var(--arka-plan); border: 4px solid var(--vurgu); box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.6); color: white; display: flex; flex-direction: column; justify-content: center; align-items: center; opacity: 0; transition: opacity 0.3s; z-index: 5;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                        <i class="fa-solid fa-camera" style="font-size: 28px; margin-bottom: 5px;"></i>
                        <span style="font-size: 11px; font-weight: bold; letter-spacing: 1px;">DEĞİŞTİR</span>
                    </div>
                    <?php if(!empty($foto_yolu)): ?>
                        <img src="<?= $foto_yolu ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="font-size: 50px; color: var(--ana-renk);">
                            <?php if($cinsiyet == 'Kadın'): ?><i class="fa-solid fa-user-tie-hair"></i><?php else: ?><i class="fa-solid fa-user"></i><?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
            
            <div style="font-size: 12px; color: var(--koyu-metin); opacity: 0.7; margin-bottom: 10px;">Fotoğrafı değiştirmek için üzerine tıklayın</div>

            <?php if(!empty($foto_yolu)): ?>
                <form method="POST" style="margin-bottom: 15px;">
                    <button type="submit" name="foto_kaldir_aksiyon" class="btn btn-kucuk" style="background: white; color: #c0392b; border: 1px solid #c0392b; padding: 5px 15px; font-size: 12px; border-radius: 20px; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.background='#c0392b'; this.style.color='white';" onmouseout="this.style.background='white'; this.style.color='#c0392b';" onclick="return confirm('Profil fotoğrafınızı kaldırmak istediğinize emin misiniz?');">
                        <i class="fa-solid fa-trash-can"></i> Fotoğrafı Kaldır
                    </button>
                </form>
            <?php endif; ?>

        <?php else: ?>
            <div style="width: 120px; height: 120px; margin: 0 auto 15px auto; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: var(--arka-plan); border: 4px solid var(--vurgu); box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                <?php if(!empty($foto_yolu)): ?>
                    <img src="<?= $foto_yolu ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="font-size: 50px; color: var(--ana-renk);">
                        <?php if($cinsiyet == 'Kadın'): ?><i class="fa-solid fa-user-tie-hair"></i><?php else: ?><i class="fa-solid fa-user"></i><?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <h3 style="margin: 0; color: var(--ana-renk); font-size: 22px;"><?= $isim_soyisim ?></h3>
        <div style="color: var(--ana-renk); background: var(--vurgu); display: inline-block; padding: 3px 12px; border-radius: 12px; font-weight: bold; font-size: 13px; margin-top: 8px;">@<?= $kullanici_adi ?></div>
        
        <div style="color: var(--koyu-metin); opacity: 0.8; font-size: 13px; margin-top: 15px; margin-bottom: 15px; display: flex; justify-content: center; gap: 15px;">
            <?php if(!empty($cinsiyet)): ?><span><i class="fa-solid <?= $cinsiyet == 'Kadın' ? 'fa-venus' : 'fa-mars' ?>"></i> <?= $cinsiyet ?></span><?php endif; ?>
            <?php if(!empty($yas)): ?><span><i class="fa-solid fa-cake-candles"></i> <?= $yas ?> Yaşında</span><?php endif; ?>
        </div>
    </div>

    <?php if($kendi_profili): ?>
        
        <div class="panel-kart" style="margin-bottom: 25px;">
            <h3 style="color: var(--ana-renk); border-bottom: 2px solid var(--arka-plan); padding-bottom: 10px; margin-bottom: 20px;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--vurgu);"></i> Kayıt Bilgilerimi Düzenle
            </h3>
            
            <form method="POST">
                <input type="hidden" name="bilgi_guncelle_aksiyon" value="1">

                <div class="ikili-grid">
                    <div class="form-group"><label>Kullanıcı Adı (Değiştirilemez)</label><input type="text" value="<?= $kullanici_adi ?>" disabled style="background: var(--arka-plan); cursor: not-allowed; color: var(--koyu-metin); opacity: 0.7;"></div>
                    <div class="form-group"><label>E-Posta Adresi</label><input type="email" name="eposta" value="<?= $eposta ?>" required></div>
                </div>

                <div class="ikili-grid">
                    <div class="form-group"><label>İsim</label><input type="text" name="isim" value="<?= guvenli_yaz($profil_sahibi['isim'] ?? $profil_sahibi['ad'] ?? '') ?>" required></div>
                    <div class="form-group"><label>Soyisim</label><input type="text" name="soyisim" value="<?= guvenli_yaz($profil_sahibi['soyisim'] ?? '') ?>" required></div>
                </div>

                <div class="ikili-grid">
                    <div class="form-group"><label>Cinsiyet</label>
                        <select name="cinsiyet" style="cursor: pointer;">
                            <option value="" <?= empty($cinsiyet) ? 'selected' : '' ?>>Belirtmek İstemiyorum</option>
                            <option value="Kadın" <?= $cinsiyet == 'Kadın' ? 'selected' : '' ?>>Kadın</option>
                            <option value="Erkek" <?= $cinsiyet == 'Erkek' ? 'selected' : '' ?>>Erkek</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Doğum Tarihi</label><input type="date" name="dogum_tarihi" value="<?= $dogum_tarihi ?>"></div>
                </div>

                <div class="form-group">
                    <label>Hakkımda (Topluluk sayfasında görünür)</label>
                    <textarea name="hakkimda" rows="4"><?= guvenli_yaz($profil_sahibi['hakkimda'] ?? '') ?></textarea>
                </div>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
                    <a href="sifre_degistir.php" class="btn btn-kucuk" style="background: #e74c3c; color: white; text-decoration: none; padding: 10px 20px;"><i class="fa-solid fa-key"></i> Şifremi Değiştir</a>
                    <button type="submit" class="btn" style="width: auto; padding: 12px 25px;"><i class="fa-solid fa-floppy-disk"></i> Değişiklikleri Kaydet</button>
                </div>
            </form>
        </div>

        <div class="panel-kart" style="border-top: 4px solid #d63031; background: #fff5f5;">
            <h3 style="color: #c0392b; margin-top: 0; margin-bottom: 10px;"><i class="fa-solid fa-triangle-exclamation"></i> Tehlikeli Bölge</h3>
            <p style="color: #c0392b; opacity: 0.8; font-size: 14px; margin-bottom: 20px;">Hesabınızı sildiğinizde profil verileriniz ve sistemdeki tüm kayıtlarınız kalıcı olarak silinecektir. Bu işlem geri alınamaz.</p>
            
            <form method="POST" onsubmit="return confirm('Hesabınızı kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz ve tüm profil verileriniz kaybolacaktır!');">
                <button type="submit" name="profili_kaldir" class="btn btn-kucuk" style="background: #d63031; color: white; border: none; padding: 10px 20px; width: auto; cursor: pointer;">
                    <i class="fa-solid fa-user-xmark"></i> Hesabımı Sil / Üyeliği Kapat
                </button>
            </form>
        </div>

    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>