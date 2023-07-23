<?php

// güvenlik amacıyla link kısmında olmayan bir tablo çağrıldığı zaman verilen hatayı kontrol eder.
if (!empty($_GET["tablo"]) && !empty($_GET["ID"]))
{
    $tablo=$VT->filter($_GET["tablo"]); // güvenlik yapısı kurar.
    $ID=$VT->filter($_GET["ID"]);
    $kontrol=$VT->VeriGetir("modüller","WHERE tablo=? AND durum=?", // tabloda kayıt olup olmadığını kontrol eder.
        array($tablo,1),"ORDER BY ID ASC",1);
    if ($kontrol!=false) {
        $veri=$VT->VeriGetir($kontrol[0]["tablo"],"WHERE ID=?",array($ID),"ORDER BY ID ASC",1);
        if ($veri!=false) // eğer öyle bir kayıt varsa
        {
            // silme işlemi
            $sil=$VT->SorguCalistir("DELETE FROM ".$tablo,"WHERE ID=?",array($ID),1);

           // silme işleminden sonra liste sayfasında yer alan tabloya yönlendirir
            ?>
            <meta http-equiv="refresh" content="0;url=<?=SITE?>liste/<?=$kontrol[0]["tablo"]?>">
            <?php

        }
        else {
// eğer öyle bir kayıt bulunamadıysa liste sayfasında yer alan tabloya yönlendirir.
            ?>
            <meta http-equiv="refresh" content="0;url=<?=SITE?>liste/<?=$kontrol[0]["tablo"]?>">
            <?php
        }

    }
    else{
        ?>
        <meta http-equiv="refresh" content="0;url=<?=SITE?>">
        <?php
    }
}  else{  // herhangi bir tablo ismi gönderilmediyse de anasayfaya yönlendirir.
    ?>
    <meta http-equiv="refresh" content="0;url=<?=SITE?>">
    <?php
}
?>