<?php
ob_start(); 
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Tarif Defteri</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --ana-renk: #1A5276;   
            --vurgu: #F4D03F;      
            --koyu-metin: #1C2833; 
            --arka-plan: #F8F9F9;  
            --beyaz: #ffffff;
            --gri: #95a5a6;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--arka-plan);
            color: var(--koyu-metin);
            height: 100%;
            overflow-x: hidden;
        }
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 70px;
            background-color: var(--ana-renk);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            box-sizing: border-box;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(26, 82, 118, 0.3);
        }

        .navbar-sol, .navbar-sag { display: flex; align-items: center; gap: 20px; }
        
        .hamburger-menu {
            color: var(--vurgu);
            font-size: 24px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .hamburger-menu:hover { transform: scale(1.1); }

        .logo-baslik {
            color: var(--beyaz);
            font-size: 22px;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-baslik i { color: var(--vurgu); }
        
        .arama-grubu { display: flex; align-items: center; }
        .arama-input {
            padding: 9px 15px;
            border-radius: 20px 0 0 20px;
            border: none;
            outline: none;
            width: 250px;
            font-size: 14px;
            background: rgba(255,255,255,0.9);
        }
        .arama-btn {
            background-color: var(--vurgu);
            color: var(--ana-renk);
            border: none;
            padding: 9px 15px;
            border-radius: 0 20px 20px 0;
            cursor: pointer;
            font-size: 14px;
            transition: 0.2s;
        }
        .arama-btn:hover { background-color: #D4AC0D; }
        
        .cikis-btn-top {
            background-color: rgba(255,255,255,0.1);
            color: var(--beyaz);
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }
        .cikis-btn-top:hover {
            background-color: var(--beyaz);
            color: var(--ana-renk);
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: -280px; 
            width: 260px;
            height: 100vh;
            background-color: var(--ana-renk); 
            z-index: 1001; 
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            box-shadow: 4px 0 15px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .sidebar.aktif { left: 0; }
        .sidebar::-webkit-scrollbar { width: 5px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 10px; }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 15px;
        }
        .sidebar-baslik {
            color: var(--beyaz);
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-baslik i { color: var(--vurgu); }
        .menu-kapat { color: rgba(255,255,255,0.7); font-size: 20px; cursor: pointer; transition: 0.2s; }
        .menu-kapat:hover { color: var(--beyaz); }

        .sidebar-menu { display: flex; flex-direction: column; gap: 5px; padding: 0 10px 40px 10px; }
        .sidebar-menu a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: 0.2s;
        }
        .sidebar-menu a:hover {
            background-color: rgba(255,255,255,0.15);
            color: var(--beyaz);
            padding-left: 20px; 
        }
        .sidebar-menu a i { color: var(--vurgu); width: 20px; text-align: center; }
        
        .overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 1000; display: none; opacity: 0; transition: opacity 0.3s; }
        .overlay.aktif { display: block; opacity: 1; }

        .ana-icerik { width: 100%; padding: 100px 40px 40px 40px; box-sizing: border-box; min-height: 100vh; }

        .btn {
            background-color: var(--vurgu);
            color: var(--ana-renk);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s, transform 0.2s;
            box-sizing: border-box;
            text-align: center;
        }
        .btn:hover { background-color: #D4AC0D; transform: translateY(-2px); }
        .btn-kucuk { padding: 7px 15px; font-size: 13px; }
        .btn-tehlike { background-color: #E74C3C; color: var(--beyaz); }
        .btn-tehlike:hover { background-color: #C0392B; }
        
        .panel-kart {
            background: var(--beyaz);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.04);
            margin-bottom: 30px;
            border-top: 4px solid var(--vurgu);
        }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: var(--ana-renk); font-size: 14px; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border 0.3s, box-shadow 0.3s;
            background-color: #fcfcfc;
        }
        .form-group input:focus, .form-group textarea:focus {
            border-color: var(--vurgu);
            box-shadow: 0 0 0 3px rgba(244, 208, 63, 0.2);
        }
        table { width: 100%; border-collapse: collapse; background: var(--beyaz); border-radius: 8px; overflow: hidden; }
        th { background: #fdf2f2; color: var(--ana-renk); padding: 14px 15px; text-align: left; font-size: 14px; font-weight: bold; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #444; }
        tr:hover { background-color: #fafafa; }
        
        .ikili-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .tarif-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }

        .tarif-kart { 
            background: var(--beyaz); 
            border-radius: 12px; 
            overflow: hidden; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            display: flex; 
            flex-direction: column;
            text-decoration: none; 
            color: inherit; 
            transition: transform 0.2s; 
            height: 100%;
        }
        .tarif-kart:hover { transform: translateY(-5px); }
        
        .tarif-resim { width: 100%; height: 140px; object-fit: cover; }
        
        .tarif-icerik { 
            padding: 20px; 
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        
        .tarif-icerik > div:last-child {
            margin-top: auto !important;
        }

        .kategori-etiket { background: var(--arka-plan); color: var(--ana-renk); padding: 4px 10px; border-radius: 5px; font-size: 12px; font-weight: bold; border: 1px solid #e2e8e0; }
        .tarif-baslik { margin: 10px 0 0 0; color: var(--koyu-metin); font-size: 18px; }

        @media (max-width: 768px) {
            .arama-input { width: 150px; }
            .cikis-btn-top span { display: none; }
            .ana-icerik { padding: 90px 15px 20px 15px; }
            .ikili-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="top-navbar">
    <div class="navbar-sol">
        <i class="fa-solid fa-bars hamburger-menu" onclick="menuGoster()"></i>
        <a href="index.php" class="logo-baslik">
            <i class="fa-solid fa-utensils"></i> Tarif Defterim
        </a>
    </div>
    <div class="navbar-sag">
        <form action="tum_tarifler.php" method="GET" class="arama-grubu">
            <input type="text" name="ara" placeholder="Tarif ara..." class="arama-input">
            <button type="submit" class="arama-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        
        <?php if(giris_yapilmis_mi()): ?>
            <a href="logout.php" class="cikis-btn-top">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Çıkış</span>
            </a>
        <?php else: ?>
            <a href="login.php" class="cikis-btn-top" style="background-color: var(--vurgu); color: var(--ana-renk); border:none; font-weight:bold;">
                <i class="fa-solid fa-user"></i> <span>Giriş</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="overlay" id="arkaPlanKarartici" onclick="menuGizle()"></div>

<div class="sidebar" id="solMenu">
    <div class="sidebar-header">
        <div class="sidebar-baslik"><i class="fa-solid fa-bars"></i> Ana Menü</div>
        <i class="fa-solid fa-xmark menu-kapat" onclick="menuGizle()"></i>
    </div>
    
    <div class="sidebar-menu">
        <a href="index.php"><i class="fa-solid fa-house"></i> Ana Sayfa</a>
        <a href="tum_tarifler.php"><i class="fa-solid fa-list"></i> Tüm Tarifler</a>
        <a href="topluluk.php"><i class="fa-solid fa-earth-europe"></i> Topluluk</a>
        <a href="kategoriler.php"><i class="fa-solid fa-layer-group"></i> Kategoriler</a>
        <a href="hazir_menuler.php"><i class="fa-solid fa-bell-concierge"></i> Hazır Menüler</a>
        <a href="hakkimizda.php"><i class="fa-solid fa-circle-info"></i> Hakkımızda</a>
        <a href="iletisim.php"><i class="fa-solid fa-envelope"></i> İletişim</a>
        
        <?php if(giris_yapilmis_mi()): ?>
            <div style="border-top: 1px solid rgba(255,255,255,0.15); margin: 10px 0;"></div>
            
            <a href="profil.php"><i class="fa-solid fa-id-badge"></i> Profilim</a>
            <a href="favoriler.php"><i class="fa-solid fa-heart"></i> Favorilerim</a>
            <a href="alisveris.php"><i class="fa-solid fa-cart-shopping"></i> Alışveriş Listem</a>
            <a href="tariflerim.php"><i class="fa-solid fa-book"></i> Tariflerim</a>
            <a href="tarif_ekle.php"><i class="fa-solid fa-circle-plus"></i> Tarif Ekle</a>
            
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin'): ?>
                <div style="border-top: 1px dashed rgba(255,255,255,0.3); margin: 20px 0 10px 0;"></div>
                
                <span style="color: rgba(255,255,255,0.7); font-size: 11px; font-weight: bold; margin-left: 20px; margin-bottom: 5px; letter-spacing: 1px; text-transform: uppercase;">
                    Sistem Yönetimi
                </span>
                
                <a href="admin.php" style="color: var(--vurgu); font-weight: bold; background: rgba(255,255,255,0.1);">
                    <i class="fa-solid fa-shield-halved"></i> Yönetim Paneli
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // JAVA script
    function menuGoster() { 
        document.getElementById('solMenu').classList.add('aktif'); 
        document.getElementById('arkaPlanKarartici').classList.add('aktif'); 
    }
    function menuGizle() { 
        document.getElementById('solMenu').classList.remove('aktif'); 
        document.getElementById('arkaPlanKarartici').classList.remove('aktif'); 
    }
</script>

<div class="ana-icerik">