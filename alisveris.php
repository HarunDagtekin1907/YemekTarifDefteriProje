<?php
require_once 'header.php';
oturumKontrol();

$kullanici_id = $_SESSION['aktif_kullanici_id'];
$alisveris_liste = json_oku('alisveris');

// Listeye Malzemeleri buradan ekliyoruz
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['malzeme_ekle'])) 
{
    $yeni_madde = trim($_POST['malzeme']);
    if (!empty($yeni_madde)) 
        {
        $alisveris_liste[] = [
            'id' => yeni_id_uret($alisveris_liste),
            'user_id' => $kullanici_id,
            'madde' => $yeni_madde,
            'alindi' => false
        ];
        json_yaz('alisveris', $alisveris_liste);
    }
    header("Location: alisveris.php"); exit();
}

// Listeden Malzemeleri buradan Siliyoruz
if (isset($_GET['sil'])) {
    $silinecek_id = (int)$_GET['sil'];
    $yeni_liste = [];
    foreach ($alisveris_liste as $a) {
        if (!($a['id'] == $silinecek_id && $a['user_id'] == $kullanici_id)) {
            $yeni_liste[] = $a;
        }
    }
    json_yaz('alisveris', $yeni_liste);
    header("Location: alisveris.php"); exit();
}

$benim_listem = array_filter($alisveris_liste, function($a) use ($kullanici_id) 
{ 
  return $a['user_id'] == $kullanici_id; 
});
?>

<div style="max-width: 600px; margin: 0 auto;">
    <div class="panel-kart" style="border-top: 4px solid var(--vurgu);">
        <h2 style="margin-top: 0; color: var(--ana-renk);"><i class="fa-solid fa-cart-shopping" style="color: var(--vurgu);"></i> Alışveriş Listem</h2>
        
        <form method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
            <input type="text" name="malzeme" placeholder="Örn: 2 Litre Süt" required style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); outline: none;">
            <button type="submit" name="malzeme_ekle" class="btn" style="width: auto; background: var(--ana-renk); color: var(--beyaz);"><i class="fa-solid fa-plus"></i> Ekle</button>
        </form>

        <?php if(empty($benim_listem)): ?>
            <div style="text-align: center; color: var(--koyu-metin); opacity: 0.7; padding: 20px 0;">
                <i class="fa-solid fa-basket-shopping fa-3x" style="margin-bottom: 15px; color: #ccc;"></i>
                <p>Listeniz şu an boş. Alınacak malzemeleri eklemeye başlayın.</p>
            </div>
        <?php else: ?>
            <ul style="list-style: none; padding: 0;">
                <?php foreach($benim_listem as $m): ?>
                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid rgba(0,0,0,0.05); background: var(--arka-plan); margin-bottom: 5px; border-radius: 5px;">
                        <span style="color: var(--ana-renk); font-weight: 500;"><i class="fa-solid fa-basket-shopping" style="color: var(--vurgu); margin-right: 10px;"></i> 
                        <?= guvenli_yaz($m['madde']) ?></span>
                        <a href="alisveris.php?sil=<?= $m['id'] ?>" style="color: #e74c3c; text-decoration: none; padding: 5px; transition: 0.2s;" onmouseover="this.style.color='#c0392b'" onmouseout="this.style.color='#e74c3c'"><i class="fa-solid fa-trash-can"></i></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>