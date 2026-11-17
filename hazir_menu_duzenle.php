<?php
$sade_gorunum = true; 
require_once 'header.php';
$kullaniciRolu = isset($_SESSION['rol']) ? strtolower(trim($_SESSION['rol'])) : 'tanimsiz';
if (!giris_yapilmis_mi() || $kullaniciRolu !== 'admin') {
    die("<div class='container'><div class='panel-kart' style='text-align:center; color:#e74c3c;'>Bu sayfayı görüntüleme yetkiniz yok.</div></div>");
}

$menuler = json_oku('menuler');
$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$secilen_menu = null;
$menu_index = -1;

foreach ($menuler as $index => $m) {
    if ($m['id'] == $menu_id) {
        $secilen_menu = $m;
        $menu_index = $index;
        break;
    }
}


if (!$secilen_menu) {
    die("<div class='container'><div class='panel-kart' style='text-align:center; color: var(--koyu-metin);'>Menü bulunamadı veya silinmiş.</div></div>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // gunceller
    $menuler[$menu_index]['baslik'] = trim($_POST['baslik']);
    $menuler[$menu_index]['aciklama'] = trim($_POST['aciklama']);
    $menuler[$menu_index]['corba'] = trim($_POST['corba']);
    $menuler[$menu_index]['ana_yemek'] = trim($_POST['ana_yemek']);
    $menuler[$menu_index]['yan_lezzet'] = trim($_POST['yan_lezzet']);
    $menuler[$menu_index]['tatli'] = trim($_POST['tatli']);
    $menuler[$menu_index]['malzemeler'] = trim($_POST['malzemeler']);
    $menuler[$menu_index]['hazirlanis'] = trim($_POST['hazirlanis']);
    $menuler[$menu_index]['renk'] = $_POST['renk'];
    $menuler[$menu_index]['ikon'] = $_POST['ikon'];
    
    json_yaz('menuler', $menuler);
    // güncel veriyi ekranda göstermek için yeniler
    $secilen_menu = $menuler[$menu_index];
    
    echo "<div class='panel-kart' style='background-color: #e8f8f5; border-left: 4px solid #2ecc71; color: #27ae60; max-width: 600px; margin: 20px auto; border-top: none;'><i class='fa-solid fa-circle-check'></i> Menü başarıyla güncellendi! <a href='admin.php' style='color:#27ae60; font-weight:bold; text-decoration:underline;'>Admin Paneline Dön</a></div>";
}
?>

<div class="panel-kart" style="max-width: 800px; margin: 0 auto; border-top: 4px solid var(--vurgu);">
    <h2 style="margin-top: 0; color: var(--ana-renk);"><i class="fa-solid fa-pen-to-square" style="color: var(--vurgu);"></i> Hazır Menüyü Düzenle</h2>
    
    <form method="POST">
        <div class="ikili-grid">
            <div class="form-group"><label>Menü Başlığı</label><input type="text" name="baslik" value="<?= guvenli_yaz($secilen_menu['baslik']) ?>" required></div>
            <div class="form-group"><label>Kısa Açıklama</label><input type="text" name="aciklama" value="<?= guvenli_yaz($secilen_menu['aciklama']) ?>" required></div>
            <div class="form-group"><label>Başlangıç (Çorba)</label><input type="text" name="corba" value="<?= guvenli_yaz($secilen_menu['corba']) ?>" required></div>
            <div class="form-group"><label>Ana Yemek</label><input type="text" name="ana_yemek" value="<?= guvenli_yaz($secilen_menu['ana_yemek']) ?>" required></div>
            <div class="form-group"><label>Yan Lezzet (Salata vs.)</label><input type="text" name="yan_lezzet" value="<?= guvenli_yaz($secilen_menu['yan_lezzet']) ?>" required></div>
            <div class="form-group"><label>Tatlı</label><input type="text" name="tatli" value="<?= guvenli_yaz($secilen_menu['tatli']) ?>" required></div>
            
            <div class="form-group tam-genislik">
                <label>Alışveriş / Malzeme Listesi</label>
                <textarea name="malzemeler" rows="4"><?= guvenli_yaz($secilen_menu['malzemeler'] ?? '') ?></textarea>
            </div>

            <div class="form-group tam-genislik">
                <label>Şefin Notu / Hazırlanış Detayları</label>
                <textarea name="hazirlanis" rows="5"><?= guvenli_yaz($secilen_menu['hazirlanis'] ?? '') ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Tema Rengi</label>
                <input type="color" name="renk" value="<?= guvenli_yaz($secilen_menu['renk']) ?>" style="height: 45px; padding: 2px; cursor: pointer;">
            </div>
            
            <div class="form-group">
                <label>Menü İkonu</label>
                <select name="ikon" required style="cursor: pointer;">
                    <option value="fa-utensils" <?= $secilen_menu['ikon'] == 'fa-utensils' ? 'selected' : '' ?>>🍽️ Standart Çatal Bıçak</option>
                    <option value="fa-fire-burner" <?= $secilen_menu['ikon'] == 'fa-fire-burner' ? 'selected' : '' ?>>🔥 Ocak / Ateş</option>
                    <option value="fa-gem" <?= $secilen_menu['ikon'] == 'fa-gem' ? 'selected' : '' ?>>💎 Pırlanta / Özel Menü</option>
                    <option value="fa-leaf" <?= $secilen_menu['ikon'] == 'fa-leaf' ? 'selected' : '' ?>>🍃 Yaprak / Sağlıklı Menü</option>
                    <option value="fa-clock" <?= $secilen_menu['ikon'] == 'fa-clock' ? 'selected' : '' ?>>⏱️ Saat / Hızlı Menü</option>
                    <option value="fa-heart" <?= $secilen_menu['ikon'] == 'fa-heart' ? 'selected' : '' ?>>❤️ Kalp</option>
                    <option value="fa-star" <?= $secilen_menu['ikon'] == 'fa-star' ? 'selected' : '' ?>>⭐ Yıldız</option>
                </select>
            </div>
        </div>
        
        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <a href="admin.php" class="btn btn-tehlike" style="text-decoration: none; flex: 1; text-align: center;">İptal Et</a>
            <button type="submit" class="btn" style="flex: 2; background: var(--ana-renk); color: var(--beyaz);"><i class="fa-solid fa-floppy-disk"></i> Değişiklikleri Kaydet</button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>