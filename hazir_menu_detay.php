<?php
require_once 'header.php';

$menuler = json_oku('menuler');
$menu_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$secilen_menu = null;

// Gelen ID ile eşleşen menüyü bulmak için forach yaptık
if (is_array($menuler)) 
{
    foreach ($menuler as $m) 
    {
        if ($m['id'] === $menu_id) 
        {
            $secilen_menu = $m;
            break;
        }
    }
}
// Menü yoksa hata ver
if (!$secilen_menu) 
{
    die("<div style='text-align:center; padding:100px; color:var(--koyu-metin); opacity:0.8;'><h2><i class='fa-solid fa-circle-exclamation' style='color:var(--ana-renk);'></i> Menü bulunamadı veya silinmiş.</h2><a href='hazir_menuler.php' class='btn' style='width:auto; display:inline-flex;'>Menülere Dön</a></div>");
}
// Verileri güvenli şekilde değişkene alır, boşsa varsayılan değer yazdırır
$renk = guvenli_yaz($secilen_menu['renk'] ?? '#f1c40f');
$ikon = guvenli_yaz($secilen_menu['ikon'] ?? 'fa-utensils');
$hazirlanis = guvenli_yaz($secilen_menu['hazirlanis'] ?? 'Bu menü için henüz hazırlanış detayı eklenmemiş.');
$malzemeler = guvenli_yaz($secilen_menu['malzemeler'] ?? 'Malzeme listesi henüz eklenmemiş. Lütfen tarif detaylarına göz atın.');
?>

<div style="max-width: 1000px; margin: 0 auto; padding-top: 20px;">
    
    <a href="hazir_menuler.php" style="display: inline-block; margin-bottom: 20px; color: var(--ana-renk); text-decoration: none; font-weight: bold;">
        <i class="fa-solid fa-arrow-left"></i> Menülere Dön
    </a>

    <div style="background: var(--beyaz); border-radius: 15px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
        <div style="background: var(--ana-renk); padding: 50px 30px; text-align: center; border-bottom: 6px solid <?= $renk ?>; position: relative;">
            <i class="fa-solid <?= $ikon ?>" style="font-size: 60px; color: <?= $renk ?>; margin-bottom: 20px;"></i>
            <h1 style="margin: 0; color: var(--beyaz); font-size: 36px;"><?= guvenli_yaz($secilen_menu['baslik']) ?></h1>
            <p style="color: rgba(255,255,255,0.8); font-size: 18px; margin-top: 15px; max-width: 700px; margin-left: auto; margin-right: auto;">
                <?= guvenli_yaz($secilen_menu['aciklama']) ?>
            </p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 50px;">
        
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <div style="background: var(--beyaz); padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <h3 style="color: var(--ana-renk); margin-top: 0; border-bottom: 2px solid var(--arka-plan); padding-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-list-check" style="color: <?= $renk ?>;"></i> Menü İçeriği
                </h3>
                
                <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 20px; font-size: 16px;">
                    <li style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: var(--arka-plan); display: flex; align-items: center; justify-content: center; color: var(--ana-renk); font-size: 22px;"><i class="fa-solid fa-mug-hot"></i></div>
                        <div><span style="display: block; font-size: 12px; color: var(--koyu-metin); opacity: 0.7; font-weight: bold; text-transform: uppercase;">Başlangıç</span> <strong style="color: var(--koyu-metin);"><?= guvenli_yaz($secilen_menu['corba']) ?></strong></div>
                    </li>
                    <li style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: var(--arka-plan); display: flex; align-items: center; justify-content: center; color: var(--ana-renk); font-size: 22px;"><i class="fa-solid fa-bowl-rice"></i></div>
                        <div><span style="display: block; font-size: 12px; color: var(--koyu-metin); opacity: 0.7; font-weight: bold; text-transform: uppercase;">Ana Yemek</span> <strong style="color: var(--koyu-metin);"><?= guvenli_yaz($secilen_menu['ana_yemek']) ?></strong></div>
                    </li>
                    <li style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: var(--arka-plan); display: flex; align-items: center; justify-content: center; color: var(--ana-renk); font-size: 22px;"><i class="fa-solid fa-carrot"></i></div>
                        <div><span style="display: block; font-size: 12px; color: var(--koyu-metin); opacity: 0.7; font-weight: bold; text-transform: uppercase;">Yan Lezzet</span> <strong style="color: var(--koyu-metin);"><?= guvenli_yaz($secilen_menu['yan_lezzet']) ?></strong></div>
                    </li>
                    <li style="display: flex; align-items: center; gap: 15px;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: var(--arka-plan); display: flex; align-items: center; justify-content: center; color: var(--ana-renk); font-size: 22px;"><i class="fa-solid fa-ice-cream"></i></div>
                        <div><span style="display: block; font-size: 12px; color: var(--koyu-metin); opacity: 0.7; font-weight: bold; text-transform: uppercase;">Tatlı</span> <strong style="color: var(--koyu-metin);"><?= guvenli_yaz($secilen_menu['tatli']) ?></strong></div>
                    </li>
                </ul>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 30px;">
            
            <div style="background: var(--beyaz); padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border-left: 5px solid #27ae60;">
                <h3 style="color: #27ae60; margin-top: 0; border-bottom: 2px solid var(--arka-plan); padding-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-cart-shopping"></i> Alışveriş / Malzeme Listesi
                </h3>
                <div style="font-size: 16px; line-height: 1.8; color: var(--koyu-metin); white-space: pre-line;">
                    <?= $malzemeler ?>
                </div>
            </div>

            <div style="background: var(--beyaz); padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); border-left: 5px solid <?= $renk ?>;">
                <h3 style="color: var(--ana-renk); margin-top: 0; border-bottom: 2px solid var(--arka-plan); padding-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-kitchen-set" style="color: <?= $renk ?>;"></i> Hazırlanış Sırası & Şefin Notu
                </h3>
                <div style="font-size: 16px; line-height: 1.8; color: var(--koyu-metin); white-space: pre-line;">
                    <?= $hazirlanis ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .ikili-grid { grid-template-columns: 1fr !important; }
        div[style*="grid-template-columns: 1fr 2fr;"] { grid-template-columns: 1fr !important; }
    }
</style>

<?php require_once 'footer.php'; ?>