<?php
require_once 'functions.php';
oturumKontrol();// sisteme giriş yapmış mı 

if (isset($_GET['id'])) {
    $silinecek_id = (int)$_GET['id'];
    $tarifler = json_oku('tarifler');
    $yeniTarifler = [];
    
    foreach ($tarifler as $t) {
        if ($t['id'] === $silinecek_id) {
            if (admin_mi() || $t['user_id'] == $_SESSION['aktif_kullanici_id']) {
                continue; 
            } else {
                die("Bu tarifi silme yetkiniz yok!");
            }
        }
        $yeniTarifler[] = $t;
    }
    
    json_yaz('tarifler', $yeniTarifler);
}
header("Location: index.php");
exit();
?>