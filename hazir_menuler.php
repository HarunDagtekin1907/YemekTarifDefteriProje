<?php
require_once 'header.php';
$hazir_menuler = json_oku('menuler');
if (!is_array($hazir_menuler)) 
{  
   $hazir_menuler = [];
}
?>

<div style="max-width: 1200px; margin: 0 auto; padding-top: 20px;">

    <div style="text-align: center; margin-bottom: 40px; background: var(--beyaz); padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-top: 4px solid var(--vurgu);">
        <h2 style="color: var(--ana-renk); font-size: 28px; margin-top: 0; margin-bottom: 10px;"><i class="fa-solid fa-bell-concierge" style="color: var(--vurgu);"></i> Şefin Hazır Menüleri</h2>
        <p style="color: var(--koyu-metin); opacity: 0.8; font-size: 15px; margin: 0;">Bugün ne pişirsem derdine son! Birbirini tamamlayan harika lezzet kombinasyonları.</p>
    </div>

    <?php if(empty($hazir_menuler)): ?>
        <div style="text-align: center; padding: 50px; background: var(--beyaz); border-radius: 15px; color: var(--koyu-metin); opacity: 0.7; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <i class="fa-solid fa-plate-wheat fa-3x" style="margin-bottom: 15px; color: #ddd;"></i>
            <p>Şu an için sisteme eklenmiş hazır menü bulunmuyor.</p>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; margin-bottom: 50px;">
            <?php foreach(array_reverse($hazir_menuler) as $menu): ?>
                
                <a href="hazir_menu_detay.php?id=<?= $menu['id'] ?>" style="display: block; text-decoration: none; color: inherit; background: var(--beyaz); border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-top: 5px solid <?= guvenli_yaz($menu['renk']) ?>; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.05)';">
                    
                    <div style="padding: 20px; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; gap: 15px;">
                        <i class="fa-solid <?= guvenli_yaz($menu['ikon']) ?>" style="font-size: 28px; color: <?= guvenli_yaz($menu['renk']) ?>;"></i>
                        <h3 style="margin: 0; color: var(--ana-renk); font-size: 20px; font-weight: 700;"><?= guvenli_yaz($menu['baslik']) ?></h3>
                    </div>
                    
                    <div style="padding: 20px; flex: 1;">
                        <p style="color: var(--koyu-metin); opacity: 0.8; font-size: 14px; margin-top: 0; margin-bottom: 20px; line-height: 1.5;"><?= guvenli_yaz($menu['aciklama']) ?></p>
                        
                        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 15px; color: var(--koyu-metin);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--arka-plan); display: flex; align-items: center; justify-content: center; color: var(--ana-renk); font-size: 12px;"><i class="fa-solid fa-mug-hot"></i></div>
                                <span><strong style="color: var(--ana-renk);">Çorba:</strong> <?= guvenli_yaz($menu['corba']) ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--arka-plan); display: flex; align-items: center; justify-content: center; color: var(--ana-renk); font-size: 12px;"><i class="fa-solid fa-bowl-rice"></i></div>
                                <span><strong style="color: var(--ana-renk);">Ana Yemek:</strong> <?= guvenli_yaz($menu['ana_yemek']) ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--arka-plan); display: flex; align-items: center; justify-content: center; color: var(--ana-renk); font-size: 12px;"><i class="fa-solid fa-carrot"></i></div>
                                <span><strong style="color: var(--ana-renk);">Yan Lezzet:</strong> <?= guvenli_yaz($menu['yan_lezzet']) ?></span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--arka-plan); display: flex; align-items: center; justify-content: center; color: var(--ana-renk); font-size: 12px;"><i class="fa-solid fa-ice-cream"></i></div>
                                <span><strong style="color: var(--ana-renk);">Tatlı:</strong> <?= guvenli_yaz($menu['tatli']) ?></span>
                            </div>
                        </div>
                    </div>

                    <div style="background: var(--arka-plan); padding: 15px; text-align: center; color: var(--ana-renk); font-size: 13px; font-weight: bold; border-top: 1px solid rgba(0,0,0,0.05);">
                        Detayları ve Hazırlanışı Gör <i class="fa-solid fa-arrow-right" style="margin-left: 5px;"></i>
                    </div>
                </a>
                
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once 'footer.php'; ?>