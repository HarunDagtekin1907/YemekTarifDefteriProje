<?php
require_once 'header.php';

$hazir_menuler = json_oku('menuler');
?>

<div style="text-align: center; margin-bottom: 30px;">
    <h2 style="color: var(--koyu); margin-bottom: 5px;"><i class="fa-solid fa-bell-concierge"></i> Şefin Hazır Menüleri</h2>
    <p style="color: var(--gri); font-size: 15px;">"Bugün ne pişirsem?" derdine son! Admin ve şeflerimiz tarafından hazırlanan özel menüler.</p>
</div>

<?php if(empty($hazir_menuler)): ?>
    <div class="panel-kart" style="text-align: center; color: gray;">
        <i class="fa-solid fa-utensils fa-3x" style="margin-bottom: 15px; color: #ddd;"></i>
        <p>Henüz eklenmiş hazır bir menü bulunmuyor. Admin panelinden menü ekleyebilirsiniz.</p>
    </div>
<?php else: ?>
    <div class="tarif-grid">
        <?php foreach($hazir_menuler as $menu): 
            $renk = !empty($menu['renk']) ? guvenli_yaz($menu['renk']) : '#f1c40f';
            $ikon = !empty($menu['ikon']) ? guvenli_yaz($menu['ikon']) : 'fa-utensils';
        ?>
            <div class="tarif-kart" style="border-top: 5px solid <?= $renk ?>; display: flex; flex-direction: column;">
                
                <div style="padding: 20px; background: #fafafa; border-bottom: 1px solid #eee; text-align: center;">
                    <i class="fa-solid <?= $ikon ?> fa-3x" style="color: <?= $renk ?>; margin-bottom: 10px;"></i>
                    <h3 style="margin: 0; color: var(--koyu);"><?= guvenli_yaz($menu['baslik']) ?></h3>
                    <p style="font-size: 13px; color: gray; margin-top: 10px; line-height: 1.5; height: 40px; overflow: hidden; text-overflow: ellipsis;"><?= guvenli_yaz($menu['aciklama']) ?></p>
                </div>
                
                <div class="tarif-icerik" style="flex: 1;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        
                        <li style="padding: 12px 0; border-bottom: 1px dashed #ddd; display: flex; align-items: center; gap: 15px;">
                            <i class="fa-solid fa-mug-hot" style="color: var(--gri); width: 20px; text-align: center;"></i>
                            <div>
                                <div style="font-size: 11px; font-weight: 600; color: <?= $renk ?>; text-transform: uppercase;">Başlangıç</div>
                                <div style="font-size: 14px; font-weight: 500; color: var(--koyu);"><?= guvenli_yaz($menu['corba']) ?></div>
                            </div>
                        </li>
                        
                        <li style="padding: 12px 0; border-bottom: 1px dashed #ddd; display: flex; align-items: center; gap: 15px;">
                            <i class="fa-solid fa-drumstick-bite" style="color: var(--gri); width: 20px; text-align: center;"></i>
                            <div>
                                <div style="font-size: 11px; font-weight: 600; color: <?= $renk ?>; text-transform: uppercase;">Ana Yemek</div>
                                <div style="font-size: 14px; font-weight: 500; color: var(--koyu);"><?= guvenli_yaz($menu['ana_yemek']) ?></div>
                            </div>
                        </li>
                        
                        <li style="padding: 12px 0; border-bottom: 1px dashed #ddd; display: flex; align-items: center; gap: 15px;">
                            <i class="fa-solid fa-carrot" style="color: var(--gri); width: 20px; text-align: center;"></i>
                            <div>
                                <div style="font-size: 11px; font-weight: 600; color: <?= $renk ?>; text-transform: uppercase;">Yan Lezzet</div>
                                <div style="font-size: 14px; font-weight: 500; color: var(--koyu);"><?= guvenli_yaz($menu['yan_lezzet']) ?></div>
                            </div>
                        </li>
                        
                        <li style="padding: 12px 0; display: flex; align-items: center; gap: 15px;">
                            <i class="fa-solid fa-ice-cream" style="color: var(--gri); width: 20px; text-align: center;"></i>
                            <div>
                                <div style="font-size: 11px; font-weight: 600; color: <?= $renk ?>; text-transform: uppercase;">Tatlı</div>
                                <div style="font-size: 14px; font-weight: 500; color: var(--koyu);"><?= guvenli_yaz($menu['tatli']) ?></div>
                            </div>
                        </li>
                        
                    </ul>
                </div>
                
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>