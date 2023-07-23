<?php
error_reporting(0);
@session_start();
@ob_start();
define("DATA","data/");
define("SAYFA","include/");
define("SINIF","class/"); // sınıfların kullanılması için sabit bir değişken tanımlandı.
include_once(DATA."baglanti.php"); //  baglantı.php sayfası  index.php sayfasının içerisine dahil edildi.
define("SITE",$siteURL);

if ($_POST) // eğer post metodu ile gönderilmişse
{
    if (!empty($_POST["tablo"]) && !empty($_POST["ID"]) && !empty($_POST["durum"])) // tablo,ID,durum değerleri boş değilse
    {
        $tablo=$VT->filter($_POST["tablo"]);
        $ID=$VT->filter($_POST["ID"]);
        $durum=$VT->filter($_POST["durum"]);
        $guncelle=$VT->SorguCalistir("UPDATE ".$tablo,"SET durum=? WHERE ID=?",array($durum,$ID),1);
        if ($guncelle!=false){ // güncelleme işlemi başarılıysa
            echo "TAMAM";
        }
        else {
            echo "HATA";
        }
    } else
    {
        echo "BOS";
    }
}
?>

