

<?php

// güvenlik amacıyla link kısmında olmayan bir tablo çağrıldığı zaman verilen hatayı kontrol eder. ve gelen ıd değeri boş değilse
if (!empty($_GET["tablo"]) && !empty($_GET["ID"]))
{
    $tablo=$VT->filter($_GET["tablo"]); // güvenlik yapısı kurar.
    $ID=$VT->filter($_GET["ID"]);  // gelen ıd değeri filtreden geçtikten sonra tutuldu.
    $kontrol=$VT->VeriGetir("modüller","WHERE tablo=? AND durum=?", // tabloda kayıt olup olmadığını kontrol eder.
        array($tablo,1),"ORDER BY ID ASC",1);
    if ($kontrol!=false)
    {
        // VeriGetir ile gelen kaydın ilgili tablodaki id değeri kontrol edildi.
        $veri=$VT->VeriGetir($kontrol[0]["tablo"],"WHERE ID=?",array($ID),"ORDER BY ID ASC",1);
        if ($veri!=false) // eğer öyle bir kayıt varsa
        {



        ?>



        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark"><?=$kontrol[0]["baslik"]?> Düzenleme Sayfası</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?=SITE?>">Anasayfa</a></li>
                                <li class="breadcrumb-item active"><?=$kontrol[0]["baslik"]?></li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="<?=SITE?>liste/<?=$kontrol[0]["tablo"]?>" class="btn btn-info" style="float: right; margin-bottom: 10px; margin-left: 10px;"><i class="fa fa-bars"></i> LİSTE </a>
                            <a href="<?=SITE?>ekle/<?=$kontrol[0]["tablo"]?>" class="btn btn-success" style="float: right; margin-bottom: 10px;"><i class="fa fa-plus"></i> YENİ EKLE </a>

                        </div> </div>
                    <?php
                    if($_POST)
                    {
                        if(!empty($_POST["kategori"]) && !empty($_POST["baslik"]) && !empty($_POST["anahtar"]) &&
                            !empty($_POST["description"]) && !empty($_POST["sirano"]))  // ortak alanların boş bırakılmaması gerekiyor
                        {
                            $kategori=$VT->filter($_POST["kategori"]);
                            $baslik=$VT->filter($_POST["baslik"]);
                            $anahtar=$VT->filter($_POST["anahtar"]);
                            $seflink=$VT->seflink($baslik);
                            $description=$VT->filter($_POST["description"]);
                            $sirano=$VT->filter($_POST["sirano"]);
                            $metin=$VT->filter($_POST["metin"],true);  // html taglarını temizlemesin diye true değeri verildi.
                            if(!empty($_FILES["resim"]["name"]))  // files nesnesi içerisinde yer alan resim alanı ve ismi boş değilse yani resim dosyası seçilmişse
                            {

                                // yüklenecek resimlerin klasör içindeki yolu belirtildi.
                                $yukle=$VT->upload("resim","../images/".$kontrol[0]["tablo"]."/"); // VT sınıfındaki upload sınıfını çağırdık
                                if($yukle!=false) // yükleme işlemi başarılı olduysa
                                {
                                    $ekle=$VT->SorguCalistir(" UPDATE ".$kontrol[0]["tablo"]," SET baslik=?, seflink=?,
                                    kategori=?, metin=?, resim=?, anahtar=?, description=?, durum=?, sirano=?, tarih=? WHERE ID=?",
                                        array($baslik,$seflink,$kategori,$metin,$yukle,$anahtar,
                                            $description,1,$sirano,date("Y-m-d"),$veri[0]["ID"])); // EKLEME VE GÜNCELLEME İŞLEMLERİ SORGUCALISTIR FONKSIYONU ÜZERİNDEN YAPILIR
                                }
                                else // yükleme başarısız olduysa hata mesajı bastırılır
                                {
                                    ?>
                                    <div class="alert alert-info">Resim yükleme işleminiz başarısız.</div>
                                    <?php
                                }
                            }
                            else // eğer seçilmemişse resim dosyası olmadan ekleme işlemi yapılır.
                            {
                                $ekle=$VT->SorguCalistir(" UPDATE ".$kontrol[0]["tablo"]," SET baslik=?, seflink=?,
                                 kategori=?, metin=?, anahtar=?,description=?, durum=?, sirano=?, tarih=? WHERE ID=?",array($baslik,$seflink,
                                    $kategori,$metin, $anahtar,$description,1,$sirano,date("Y-m-d"),$veri[0]["ID"]));

                            }
                            if($ekle!=false){   // ekleme işlemi başarılıysa çıkan uyarı mesajı

                                $veri=$VT->VeriGetir($kontrol[0]["tablo"],"WHERE ID=?",array($veri[0]["ID"]),"ORDER BY ID ASC",1);
                                ?>
                                <div class="alert alert-success">İşleminiz başarıyla kaydedildi.</div>
                                <?php
                            }
                            else{ // ekleme işlemi başarısızsa çıkan uyarı mesajı
                                ?>
                                <div class="alert alert-danger">İşleminiz sırasında bir sorunla karşılaşıldı. Lütfen daha sonra tekrar deneyiniz.</div>
                                <?php

                            }
                        }
                        else  // bu alanlaran biri boş ise bir hata mesajı bastırır.
                        {
                            ?>
                            <div class="alert alert-danger">Boş bıraktığınız alanları doldurunuz.</div>
                            <?php
                        }
                    }
                    ?>

                    <form action="#" method="post" enctype="multipart/form-data">
                        <div class="col-md-8">
                            <div class="card-body card card-primary">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Kategori Seç</label>
                                            <select class="form-control select2" style="width: 100%;" name="kategori">
                                                <?php
                                                // kategoriGetir fonksiyonu VT sınıfından getirilmek istenen kategorinin ilgili tablo ismi çağırıldı.
                                                // kategori ID numarası gönderilir ve seçili halde gelmesi sağlanır.
                                                $sonuc=$VT->kategoriGetir($kontrol[0]["tablo"],$veri[0]["kategori"],-1);
                                                if ($sonuc!=false) { // sonuc degeri false dönmemişse
                                                    echo $sonuc;
                                                }
                                                else { // sonucu bulamadıysa
                                                    $VT->tekKategori($kontrol[0]["tablo"]);
                                                }
                                                ?>
                                            </select>

                                            <!-- /.form-group -->
                                        </div>

                                    </div>
                                    <!-- /.row -->
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Başlık</label>
                                            <!-- başlık alanı getirildi. -->
                                            <input type="text" class="form-control" placeholder="Başlık ..." name="baslik" value="<?=stripslashes($veri[0]["baslik"])?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Açıklama</label>
                                            <!-- açıklama alanı getirildi. -->
                                            <textarea class="textarea" placeholder="Açıklama Alanıdır." name="metin"
                                                      style="width: 100%; height: 350px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
                                                <?=stripslashes($veri[0]["metin"])?>
                                            </textarea>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Anahtar</label>
                                            <!-- anahtar alanı getirildi. -->
                                            <input type="text" class="form-control" placeholder="Anahtar ..." name="anahtar" value="<?=stripslashes($veri[0]["anahtar"])?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Description</label>
                                            <!-- Description alanı getirildi. -->
                                            <input type="text" class="form-control" placeholder="Description ..." name="description" value="<?=stripslashes($veri[0]["description"])?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Resim</label>
                                            <!-- resim alanı getirildi. -->
                                            <input type="file" class="form-control" placeholder="Resim Seçiniz ..." name="resim">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Sıra No</label>
                                            <!-- sıra no alanı getirildi. -->
                                            <input type="number" class="form-control" placeholder="Sıra No ..." name="sirano" style="width:100px;" value="<?=stripslashes($veri[0]["sirano"])?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-block btn-primary">KAYDET</button>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <!-- /.card -->

                        </div>
                    </form>

                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>


        <!-- istenilen sayfa bulunamadıysa anasayfaya yönlendirir. -->
        <?php
        }
        else { // eğer öyle bir kayıt bulunamadıysa liste sayfasında yer alan tabloya yönlendirir.
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