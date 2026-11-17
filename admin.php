<?php
ob_start();
require_once 'header.php';

$kullaniciRolu = isset($_SESSION['rol']) ? strtolower(trim($_SESSION['rol'])) : 'tanimsiz';

if (!giris_yapilmis_mi() || $kullaniciRolu !== 'admin') {
    die("<div style='text-align:center; margin-top:100px; font-family:sans-serif;'>
            <i class='fa-solid fa-triangle-exclamation' style='font-size:50px; color:#e74c3c;'></i>
            <h2 style='color:var(--ana-renk);'>Erişim Engellendi!</h2>
            <p style='color:var(--koyu-metin); opacity:0.8;'>Bu sayfayı görme yetkiniz yok.</p>
            <div style='background:var(--arka-plan); display:inline-block; padding:15px 30px; border-radius:10px; margin-top:20px;'>
                Sistemin şu an senin için okuduğu rol: <b style='color:#e74c3c; font-size:18px;'>" . strtoupper($kullaniciRolu) . "</b>
            </div>
            <br><br>
            <a href='logout.php' style='background:var(--vurgu); color:var(--ana-renk); padding:10px 20px; text-decoration:none; border-radius:5px; font-weight:bold;'>Lütfen Buraya Tıklayarak Çıkış Yapıp Tekrar Girin</a>
         </div>");
}

$bildirim = '';
$mesaj = ''; 
if (isset($_GET['mesaj'])) { 
    $mesaj = $_GET['mesaj']; 
}

// Kategori 
if (isset($_GET['kategoriSil'])) {
    $silinecekİD = (int)$_GET['kategoriSil'];
    $kategoriler = json_oku('kategoriler');
    $yeni_kategoriler = [];
    foreach ($kategoriler as $k) { 
        if ($k['id'] != $silinecekİD) { 
            $yeni_kategoriler[] = $k; 
        } 
    }
    json_yaz('kategoriler', $yeni_kategoriler);
    header("Location: admin.php?mesaj=kategori_silindi"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kategori_ekle'])) {
    $yeni_ad = trim($_POST['kategori_adi']);
    if (!empty($yeni_ad)) {
        $kategoriler = json_oku('kategoriler');
        $kategoriler[] = ['id' => yeni_id_uret($kategoriler), 'kategori_ad' => $yeni_ad];
        json_yaz('kategoriler', $kategoriler);
        header("Location: admin.php?mesaj=kategori_eklendi"); 
        exit();
    }
}

// Kullanıcı 
if (isset($_GET['kullanici_sil'])) {
    $sil_id = (int)$_GET['kullanici_sil'];
    if ($sil_id != $_SESSION['aktif_kullanici_id']) { 
        $kullanicilar = json_oku('kullanicilar');
        $yeni_kullanicilar = [];
        foreach ($kullanicilar as $kul) { 
            if ($kul['id'] != $sil_id) {
                 $yeni_kullanicilar[] = $kul; 
            }
        }
        json_yaz('kullanicilar', $yeni_kullanicilar);
        header("Location: admin.php?mesaj=kullanici_silindi"); 
        exit();
    }
}

// HAZIR MENÜ  
if (isset($_GET['menu_sil'])) {
    $silinecekİD = (int)$_GET['menu_sil'];
    $menuler = json_oku('menuler');
    $yeni_menuler = [];
    foreach ($menuler as $m) { 
        if ($m['id'] != $silinecekİD) { 
            $yeni_menuler[] = $m;
        }
    }
    json_yaz('menuler', $yeni_menuler);
    header("Location: admin.php?mesaj=menu_silindi"); 
    exit();
}

// TARİF 
if (isset($_GET['tarif_sil'])) {
    $silinecekİD = (int)$_GET['tarif_sil'];
    $tarifler = json_oku('tarifler');
    $yeniTarifler = [];
    foreach ($tarifler as $t) { 
        if ($t['id'] != $silinecekİD) { 
            $yeniTarifler[] = $t; 
        } 
    }
    json_yaz('tarifler', $yeniTarifler);
    header("Location: admin.php?mesaj=tarif_silindi"); 
    exit();
}

// YORUM 
if (isset($_GET['yorum_sil'])) {
    $silinecekİD = (int)$_GET['yorum_sil'];
    $yorumlar = json_oku('yorumlar');
    $yeni_yorumlar = [];
    foreach ($yorumlar as $y) { 
        if ($y['id'] != $silinecekİD) { 
            $yeni_yorumlar[] = $y; 
        }
    }
    json_yaz('yorumlar', $yeni_yorumlar);
    header("Location: admin.php?mesaj=yorum_silindi"); 
    exit();
}

// Hazır Menü  
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['menu_ekle'])) {
    $menuler = json_oku('menuler');
    $menuler[] = [
        'id' => yeni_id_uret($menuler),
        'baslik' => trim($_POST['baslik']),
        'aciklama' => trim($_POST['aciklama']),
        'corba' => trim($_POST['corba']),
        'ana_yemek' => trim($_POST['ana_yemek']),
        'yan_lezzet' => trim($_POST['yan_lezzet']),
        'tatli' => trim($_POST['tatli']),
        'malzemeler' => trim($_POST['malzemeler']), 
        'hazirlanis' => trim($_POST['hazirlanis']), 
        'renk' => $_POST['renk'],
        'ikon' => $_POST['ikon']
    ];
    json_yaz('menuler', $menuler);
    header("Location: admin.php?mesaj=menu_eklendi"); 
    exit();
}

if ($mesaj == 'kategori_silindi') $bildirim = "<div style='background: #ffeaa7; color: #d35400; padding: 12px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-check'></i> Kategori silindi.</div>";
if ($mesaj == 'kategori_eklendi') $bildirim = "<div style='background: #d5f5e3; color: #27ae60; padding: 12px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-check'></i> Yeni kategori eklendi.</div>";
if ($mesaj == 'kullanici_silindi') $bildirim = "<div style='background: #ffeaa7; color: #d35400; padding: 12px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-check'></i> Kullanıcı silindi.</div>";
if ($mesaj == 'menu_silindi') $bildirim = "<div style='background: #ffeaa7; color: #d35400; padding: 12px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-check'></i> Hazır menü silindi.</div>";
if ($mesaj == 'menu_eklendi') $bildirim = "<div style='background: #d5f5e3; color: #27ae60; padding: 12px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-check'></i> Yeni hazır menü sisteme eklendi!</div>";
if ($mesaj == 'tarif_silindi') $bildirim = "<div style='background: #ffeaa7; color: #d35400; padding: 12px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-check'></i> Tarif sistemden kalıcı olarak silindi.</div>";
if ($mesaj == 'yorum_silindi') $bildirim = "<div style='background: #ffeaa7; color: #d35400; padding: 12px; border-radius: 8px; margin-bottom: 20px;'><i class='fa-solid fa-check'></i> Yorum başarıyla silindi.</div>";

$kategoriler = json_oku('kategoriler'); if (!is_array($kategoriler)) $kategoriler = [];
$kullanicilar = json_oku('kullanicilar'); if (!is_array($kullanicilar)) $kullanicilar = [];
$tarifler = json_oku('tarifler'); if (!is_array($tarifler)) $tarifler = [];
$iletisim = json_oku('iletisim'); if (!is_array($iletisim)) $iletisim = [];
$hazir_menuler = json_oku('menuler'); if (!is_array($hazir_menuler)) $hazir_menuler = [];
$yorumlar = json_oku('yorumlar'); if (!is_array($yorumlar)) $yorumlar = [];
?>

<style>
    .admin-tablo { width: 100%; border-collapse: collapse; }
    .admin-tablo th { background: var(--arka-plan); color: var(--ana-renk); padding: 12px 15px; text-align: left; font-size: 14px; }
    .admin-tablo td { padding: 15px; border-bottom: 1px solid var(--arka-plan); font-size: 14px; }
    .tam-genislik { grid-column: 1 / -1; }
</style>

<div style="text-align: center; margin-bottom: 30px;">
    <h2 style="color: var(--ana-renk); margin-bottom: 5px;"><i class="fa-solid fa-shield-halved"></i> Yönetim Paneli</h2>
    <p style="color: var(--koyu-metin); opacity: 0.8; font-size: 15px;">Sistemin tüm kontrolü elinizde.</p>
</div>

<?= $bildirim ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
    <div style="background: var(--ana-renk); color: var(--beyaz); padding: 20px; border-radius: 12px; text-align: center;">
        <i class="fa-solid fa-users fa-2x" style="color: var(--vurgu);"></i>
        <h3><?= count($kullanicilar) ?></h3><span style="font-size: 13px;">Kullanıcı</span>
    </div>
    <div style="background: var(--ana-renk); color: var(--beyaz); padding: 20px; border-radius: 12px; text-align: center;">
        <i class="fa-solid fa-book-open fa-2x" style="color: var(--vurgu);"></i>
        <h3><?= count($tarifler) ?></h3><span style="font-size: 13px;">Tarif</span>
    </div>
    <div style="background: var(--ana-renk); color: var(--beyaz); padding: 20px; border-radius: 12px; text-align: center;">
        <i class="fa-solid fa-layer-group fa-2x" style="color: var(--vurgu);"></i>
        <h3><?= count($kategoriler) ?></h3><span style="font-size: 13px;">Kategori</span>
    </div>
    <div style="background: var(--ana-renk); color: var(--beyaz); padding: 20px; border-radius: 12px; text-align: center;">
        <i class="fa-solid fa-bell-concierge fa-2x" style="color: var(--vurgu);"></i>
        <h3><?= count($hazir_menuler) ?></h3><span style="font-size: 13px;">Hazır Menü</span>
    </div>
    <div style="background: var(--ana-renk); color: var(--beyaz); padding: 20px; border-radius: 12px; text-align: center;">
        <i class="fa-regular fa-comments fa-2x" style="color: var(--vurgu);"></i>
        <h3><?= count($yorumlar) ?></h3><span style="font-size: 13px;">Yorum</span>
    </div>
</div>

<div class="panel-kart">
    <h3 style="color: var(--ana-renk); border-bottom: 2px solid var(--arka-plan); padding-bottom: 10px;"><i class="fa-regular fa-comments"></i> Yorum Yönetimi</h3>
    <?php if(empty($yorumlar)): ?>
        <p style="text-align: center; color: var(--koyu-metin); opacity: 0.7; margin-top: 15px;">Sistemde henüz hiç yorum bulunmuyor.</p>
    <?php else: ?>
        <div style="overflow-x: auto; margin-top: 15px;">
            <table class="admin-tablo">
                <tr>
                    <th>Yazan</th>
                    <th>Tarif</th>
                    <th>Yorum</th>
                    <th>Tarih</th>
                    <th style="text-align: right;">İşlem</th>
                </tr>
                <?php foreach(array_reverse($yorumlar) as $y): 
                    $y_adi = 'Bilinmeyen Kullanıcı';
                    $y_id = 0;
                    foreach($kullanicilar as $ku) 
                { if($ku['id'] == $y['user_id'])                      
                    { 
                     $y_adi = $ku['ad'] ?? ($ku['isim'] ?? 'İsimsiz'); $y_id = $ku['id']; break;
                    } 
                }
                    
                    $t_adi = 'Silinmiş Tarif';
                    $t_id = 0;
                    foreach($tarifler as $t) { if($t['id'] == $y['tarif_id']) { $t_adi = $t['baslik']; $t_id = $t['id']; break; } }
                ?>
                <tr>
                    <td>
                        <a href="profil.php?id=<?= $y_id ?>" style="color: var(--ana-renk); text-decoration: none; font-weight: bold; transition: color 0.2s;" onmouseover="this.style.color='var(--vurgu)'" onmouseout="this.style.color='var(--ana-renk)'">
                            @<?= guvenli_yaz($y_adi) ?>
                        </a>
                    </td>
                    <td>
                        <?php if($t_id > 0): ?>
                            <a href="tarif_detay.php?id=<?= $t_id ?>" style="color: var(--koyu-metin); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--ana-renk)'" onmouseout="this.style.color='var(--koyu-metin)'">
                                <?= guvenli_yaz($t_adi) ?>
                            </a>
                        <?php else: ?>
                            <span style="color: #e74c3c; font-style: italic;"><?= guvenli_yaz($t_adi) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <p style="margin: 0; font-size: 13px; color: var(--koyu-metin); max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= guvenli_yaz($y['yorum']) ?>">
                            <?= guvenli_yaz($y['yorum']) ?>
                        </p>
                    </td>
                    <td><span style="font-size: 12px; color: var(--koyu-metin); opacity: 0.6;"><?= guvenli_yaz($y['tarih']) ?></span></td>
                    <td style="text-align: right;">
                        <a href="admin.php?yorum_sil=<?= $y['id'] ?>" class="btn btn-kucuk btn-tehlike" onclick="return confirm('Bu yorumu kalıcı olarak silmek istediğinize emin misiniz?');" style="text-decoration: none; display: inline-block;">
                            <i class="fa-solid fa-trash"></i> Sil
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="panel-kart">
    <h3 style="color: var(--ana-renk); border-bottom: 2px solid var(--arka-plan); padding-bottom: 10px;"><i class="fa-solid fa-book"></i> Tarif Yönetimi</h3>
    <?php if(empty($tarifler)): ?>
        <p style="text-align: center; color: var(--koyu-metin); opacity: 0.7; margin-top: 15px;">Sistemde kayıtlı tarif bulunmuyor.</p>
    <?php else: ?>
        <div style="overflow-x: auto; margin-top: 15px;">
            <table class="admin-tablo">
                <tr>
                    <th>Tarif Adı</th>
                    <th>Kategori</th>
                    <th>Ekleyen Şef</th>
                    <th style="text-align: right;">İşlem</th>
                </tr>
                <?php foreach(array_reverse($tarifler) as $t): 
                    $k_adi = 'Bilinmiyor';
                    foreach($kategoriler as $k) { if($k['id'] == $t['kategori_id']) { $k_adi = $k['kategori_ad']; break; } }
                    $u_adi = 'Bilinmiyor';
                    $u_id = 0;
                    foreach($kullanicilar as $u) { if($u['id'] == $t['user_id']) { $u_adi = $u['ad'] ?? ($u['isim'] ?? 'Şef'); $u_id = $u['id']; break; } }
                ?>
                <tr>
                    <td>
                        <a href="tarif_detay.php?id=<?= $t['id'] ?>" style="color: var(--koyu-metin); text-decoration: none; font-weight: bold; transition: color 0.2s;" onmouseover="this.style.color='var(--ana-renk)'" onmouseout="this.style.color='var(--koyu-metin)'">
                            <?= guvenli_yaz($t['baslik']) ?>
                        </a>
                    </td>
                    <td><span class="kategori-etiket" style="border: 1px solid rgba(0,0,0,0.05); font-size:12px;"><?= guvenli_yaz($k_adi) ?></span></td>
                    <td>
                        <a href="profil.php?id=<?= $u_id ?>" style="color: var(--ana-renk); text-decoration: none; font-weight: 500; transition: color 0.2s;" onmouseover="this.style.color='var(--vurgu)'" onmouseout="this.style.color='var(--ana-renk)'">
                            @<?= guvenli_yaz($u_adi) ?>
                        </a>
                    </td>
                    <td style="text-align: right;">
                        <a href="admin.php?tarif_sil=<?= $t['id'] ?>" class="btn btn-kucuk btn-tehlike" onclick="return confirm('Bu tarifi kalıcı olarak silmek istediğinize emin misiniz?');" style="text-decoration: none; display: inline-block;">
                            <i class="fa-solid fa-trash"></i> Sil
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="panel-kart">
    <h3 style="color: var(--ana-renk); border-bottom: 2px solid var(--arka-plan); padding-bottom: 10px;"><i class="fa-solid fa-bell-concierge"></i> Hazır Menü Yönetimi</h3>
    
    <div style="background: var(--arka-plan); padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(0,0,0,0.05);">
        <h4 style="margin-top:0; color: var(--ana-renk);">Yeni Menü Oluştur</h4>
        <form method="POST">
            <div class="ikili-grid">
                <div class="form-group"><label>Menü Başlığı</label><input type="text" name="baslik" required></div>
                <div class="form-group"><label>Kısa Açıklama</label><input type="text" name="aciklama" required></div>
                <div class="form-group"><label>Başlangıç (Çorba)</label><input type="text" name="corba" required></div>
                <div class="form-group"><label>Ana Yemek</label><input type="text" name="ana_yemek" required></div>
                <div class="form-group"><label>Yan Lezzet (Salata vs.)</label><input type="text" name="yan_lezzet" required></div>
                <div class="form-group"><label>Tatlı</label><input type="text" name="tatli" required></div>
                
                <div class="form-group tam-genislik">
                    <label>Alışveriş / Malzeme Listesi</label>
                    <textarea name="malzemeler" rows="4" placeholder="Örn: 2 adet domates, 500gr kıyma... (Alt alta yazabilirsiniz)"></textarea>
                </div>

                <div class="form-group tam-genislik">
                    <label>Şefin Notu / Hazırlanış Detayları</label>
                    <textarea name="hazirlanis" rows="5" placeholder="Hangi yemek önce yapılmalı? Püf noktaları nelerdir? (Alt alta yazabilirsiniz)"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Tema Rengi</label>
                    <input type="color" name="renk" value="#f1c40f" style="height: 45px; padding: 2px; cursor: pointer;">
                </div>
                
                <div class="form-group">
                    <label>Menü İkonu</label>
                    <select name="ikon" required style="cursor: pointer;">
                        <option value="fa-utensils">🍽️ Standart Çatal Bıçak</option>
                        <option value="fa-fire-burner">🔥 Ocak / Ateş</option>
                        <option value="fa-gem">💎 Pırlanta / Özel Menü</option>
                        <option value="fa-leaf">🍃 Yaprak / Sağlıklı Menü</option>
                        <option value="fa-clock">⏱️ Saat / Hızlı Menü</option>
                        <option value="fa-heart">❤️ Kalp</option>
                        <option value="fa-star">⭐ Yıldız</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="menu_ekle" class="btn"><i class="fa-solid fa-plus"></i> Menüyü Kaydet ve Yayınla</button>
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table class="admin-tablo">
            <tr>
                <th>Menü Başlığı</th>
                <th>Ana Yemek</th>
                <th>Renk / İkon</th>
                <th style="text-align: right;">İşlem</th>
            </tr>
            <?php foreach($hazir_menuler as $menu): ?>
            <tr>
                <td><strong style="color: var(--koyu-metin);"><?= guvenli_yaz($menu['baslik']) ?></strong></td>
                <td><?= guvenli_yaz($menu['ana_yemek']) ?></td>
                <td>
                    <i class="fa-solid <?= guvenli_yaz($menu['ikon']) ?>" style="color: <?= guvenli_yaz($menu['renk']) ?>; font-size: 20px;"></i>
                </td>
                <td style="text-align: right;">
                    <a href="hazir_menu_duzenle.php?id=<?= $menu['id'] ?>" class="btn btn-kucuk" style="background: var(--ana-renk); color: var(--beyaz); text-decoration: none; display: inline-block; margin-right: 5px;">
                        <i class="fa-solid fa-pen"></i> Düzenle
                    </a>
                    
                    <a href="admin.php?menu_sil=<?= $menu['id'] ?>" class="btn btn-kucuk btn-tehlike" onclick="return confirm('Silmek istediğinize emin misiniz?');" style="text-decoration: none; display: inline-block;">
                        <i class="fa-solid fa-trash"></i> Sil
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="panel-kart">
    <h3 style="color: var(--ana-renk); border-bottom: 2px solid var(--arka-plan); padding-bottom: 10px;"><i class="fa-solid fa-layer-group"></i> Kategori Yönetimi</h3>
    <form method="POST" style="display: flex; gap: 10px; margin-bottom: 20px; margin-top: 15px;">
        <input type="text" name="kategori_adi" placeholder="Yeni kategori adı yazın..." required style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ddd; outline: none;">
        <button type="submit" name="kategori_ekle" class="btn" style="width: auto;"><i class="fa-solid fa-plus"></i> Ekle</button>
    </form>
    <div style="overflow-x: auto;">
        <table class="admin-tablo">
            <tr><th>ID</th><th>Kategori Adı</th><th style="text-align: right;">İşlem</th></tr>
            <?php foreach($kategoriler as $k): ?>
            <tr>
                <td>#<?= $k['id'] ?></td>
                <td><strong style="color: var(--koyu-metin);"><?= guvenli_yaz($k['kategori_ad']) ?></strong></td>
                <td style="text-align: right;">
                    <a href="admin.php?kategoriSil=<?= $k['id'] ?>" class="btn btn-kucuk btn-tehlike" onclick="return confirm('Silmek istediğinize emin misiniz?');" style="text-decoration: none; display: inline-block;"><i class="fa-solid fa-trash"></i> Sil</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="panel-kart">
    <h3 style="color: var(--ana-renk); border-bottom: 2px solid var(--arka-plan); padding-bottom: 10px;"><i class="fa-solid fa-users"></i> Üye Yönetimi</h3>
    <div style="overflow-x: auto;">
        <table class="admin-tablo">
            <tr><th>Ad Soyad</th><th>Kullanıcı Adı</th><th>E-Posta</th><th>Rol</th><th style="text-align: right;">İşlem</th></tr>
            <?php foreach($kullanicilar as $kul): ?>
            <tr>
                <td>
                    <a href="profil.php?id=<?= $kul['id'] ?>" style="color: var(--ana-renk); text-decoration: none; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='var(--vurgu)'" onmouseout="this.style.color='var(--ana-renk)'">
                        <?= guvenli_yaz($kul['ad'] ?? (isset($kul['isim']) ? $kul['isim'] . ' ' . $kul['soyisim'] : '')) ?>
                    </a>
                </td>
                <td>
                    <a href="profil.php?id=<?= $kul['id'] ?>" style="color: var(--koyu-metin); text-decoration: none; font-weight: 500; opacity: 0.9; transition: color 0.2s;" onmouseover="this.style.color='var(--ana-renk)'" onmouseout="this.style.color='var(--koyu-metin)'">
                        @<?= guvenli_yaz($kul['kullanici_adi'] ?? '') ?>
                    </a>
                </td>
                <td><?= guvenli_yaz($kul['eposta']) ?></td>
                <td>
                    <?php if($kul['rol'] == 'admin'): ?>
                        <span style="background: var(--ana-renk); color: var(--vurgu); padding: 3px 8px; border-radius: 5px; font-size: 12px;">Admin</span>
                    <?php else: ?>
                        <span style="background: rgba(0,0,0,0.05); color: var(--koyu-metin); padding: 3px 8px; border-radius: 5px; font-size: 12px;">Üye</span>
                    <?php endif; ?>
                </td>
                <td style="text-align: right;">
                    <?php if($kul['id'] != $_SESSION['aktif_kullanici_id']): ?>
                        <a href="admin.php?kullanici_sil=<?= $kul['id'] ?>" class="btn btn-kucuk btn-tehlike" onclick="return confirm('Silmek istediğinize emin misiniz?');" style="text-decoration: none; display: inline-block;"><i class="fa-solid fa-user-xmark"></i> Sil</a>
                    <?php else: ?>
                        <span style="color: var(--koyu-metin); opacity: 0.6; font-size: 12px;">(Siz)</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="panel-kart">
    <h3 style="color: var(--ana-renk); border-bottom: 2px solid var(--arka-plan); padding-bottom: 10px;"><i class="fa-solid fa-envelope-open-text"></i> Gelen Mesajlar</h3>
    <?php if(empty($iletisim)): ?>
        <p style="text-align: center; color: var(--koyu-metin); opacity: 0.7; margin-top: 15px;">Henüz hiç mesaj almadınız.</p>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
            <?php foreach(array_reverse($iletisim) as $mesaj): ?>
                <div style="background: var(--arka-plan); border-left: 4px solid var(--vurgu); padding: 15px; border-radius: 5px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <strong style="color: var(--ana-renk);"><?= guvenli_yaz($mesaj['ad_soyad']) ?> (<?= guvenli_yaz($mesaj['eposta']) ?>)</strong>
                        <span style="font-size: 12px; color: var(--koyu-metin); opacity: 0.7;"><i class="fa-regular fa-clock"></i> <?= guvenli_yaz($mesaj['tarih']) ?></span>
                    </div>
                    <div style="font-weight: 600; color: var(--ana-renk); margin-bottom: 5px;">Konu: <?= guvenli_yaz($mesaj['konu']) ?></div>
                    <p style="margin: 0; font-size: 14px; color: var(--koyu-metin); opacity: 0.9; line-height: 1.5;"><?= nl2br(guvenli_yaz($mesaj['icerik'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>