<?php
// upload işlemleri için kullanılacak sınıf index.php sayfasına dahil edildi.
include_once (SINIF."class.upload.php");
// class klasörünün içerisindeki “VT” isimli php sayfası baglanti sayfasının içerisine dahil edildi.
include_once(SINIF."VT.php");

// 	VT olarak oluşturulan sınıfı baglantı.php sayfasında cağırır.
$VT = new VT();

// çekmek istediğimiz verinin komutunu yazdık.
$ayarlar=$VT->VeriGetir("ayarlar","WHERE ID=?",array(1),"ORDER BY ID ASC",1);
if($ayarlar!=false)
{
    $sitebaslik=$ayarlar[0]["baslik"];
    $siteanahtar=$ayarlar[0]["anahtar"];
    $siteaciklama=$ayarlar[0]["aciklama"];
    $sitetelefon=$ayarlar[0]["telefon"];
    $sitemail=$ayarlar[0]["mail"];
    $siteadres=$ayarlar[0]["adres"];
    $sitefax=$ayarlar[0]["fax"];
    $siteURL=$ayarlar[0]["url"];
}
?>