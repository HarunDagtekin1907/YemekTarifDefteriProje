<?php
session_start();
define('VERI_KLASORU', __DIR__ . '/veri');

// Eğer 'veri' klasörü yoksa hemen oluştur
if (!is_dir(VERI_KLASORU)) {
    mkdir(VERI_KLASORU, 0777, true);
}
function guvenli_yaz($veri) 
{ return htmlspecialchars(trim($veri), ENT_QUOTES, 'UTF-8');
 }
function giris_yapilmis_mi()
 { return isset($_SESSION['aktif_kullanici_id']); 
 }
function admin_mi() 
{ return isset($_SESSION['aktif_rol']) && $_SESSION['aktif_rol'] === 'admin';
 }

function oturumKontrol() {
    if (!giris_yapilmis_mi()) {
        header("Location: login.php");
        exit();
    }
}

function json_oku($dosya_adi) {
    $yol = VERI_KLASORU . '/' . $dosya_adi . '.json';
    
    // Eğer dosya ilk defa çalışıyor ise 
    if (!file_exists($yol)) 
    {
        $varsayilan_veri = [];
        
        // Yöneticinin hesabı ve kategorileri otomatiktir
        if ($dosya_adi === 'kullanicilar') {
            $varsayilan_veri = [
                [
                    'id' => 1, 
                    'ad' => 'Yönetici', 
                    'isim' => 'Sistem', 
                    'soyisim' => 'Yöneticisi', 
                    'eposta' => 'admin@site.com', 
                    'kullanici_adi' => 'admin', 
                    'sifre' => '123456', 
                    'rol' => 'admin'
                ]
            ];
        } elseif ($dosya_adi === 'kategoriler') {
            $varsayilan_veri = [
                ['id' => 1, 'kategori_ad' => 'Çorbalar'],
                ['id' => 2, 'kategori_ad' => 'Başlangıçlar & Ara Sıcaklar'],
                ['id' => 3, 'kategori_ad' => 'Ana Yemekler'],
                ['id' => 4, 'kategori_ad' => 'Tatlılar'],
                ['id' => 5, 'kategori_ad' => 'Salatalar & Mezeler'],
                ['id' => 6, 'kategori_ad' => 'İçecekler']
            ];
        }
        
        file_put_contents($yol, json_encode($varsayilan_veri, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $varsayilan_veri;
    }
    
    // Dosya var ise içeriğini okur
    $veri = file_get_contents($yol);
    return json_decode($veri, true) ?: [];
}

function json_yaz($dosya_adi, $veri) {
    if (!is_dir(VERI_KLASORU)) {
        mkdir(VERI_KLASORU, 0777, true);
    }
    $yol = VERI_KLASORU . '/' . $dosya_adi . '.json';
    file_put_contents($yol, json_encode($veri, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function yeni_id_uret($dizi) 
{
    if (empty($dizi)) return 1;
    $idler = array_column($dizi, 'id');
    return max($idler) + 1;
}


?>