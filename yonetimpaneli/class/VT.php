<?php

class VT {

    // sınıf içerisinde kullanılacak değişkenler
    var $sunucu ="localhost";
    var $user = "root";
    var $password = "";
    var $dbname ="yonetimpaneli";
    var $baglanti;  // bağlantının var olup olmadığını kontrol eder.


    function  __construct()
        //	Bu fonksiyonun içerisinde veritabanına bağlantı işlemi gerçekleştirildi.
        //	Hatalı girişlerde bir hata döndürmek ve hatayı yazdırmak için  “try-catch” bloğu oluşturuldu.
    {
        try{
            $this->baglanti=new PDO("mysql:host=".$this->sunucu.";dbname=".$this->dbname.";charset=utf8",$this->user,$this->password);
        } catch(PDOException $error) {
            echo $error-> getMessage();
            exit ();
        }
    }
    // select * from ayarlar where ID=1 ORDER BY ID ASC LIMIT 1
    public function VeriGetir($tablo,$wherealanlar="",$wherearraydeger="",$ordeby="ORDER BY ID ASC",$limit="")
    {
        $this->baglanti->query("SET CHARACTER SET utf8"); // türkçe karakter hassasiyetini burada belirttik.
        $sql="SELECT * FROM ".$tablo;  // select * from ayarlar
        if(!empty($wherealanlar)  && !empty($wherearraydeger) ) // tablonun içinin boş olup olmadıgını kontrol eder.
        {
            $sql.=" ".$wherealanlar; // select * from ayarlarWHERE   boşluk karakterini algılaması için " " kullanıldı.
            if(!empty($ordeby)) {$sql.=" ".$ordeby;}
            if(!empty($limit)) {$sql.=" LIMIT    ".$limit;} //  belirttiğimiz parametreleri yerleştiriyoruz
            $calistir=$this->baglanti->prepare($sql);  // sql cümlesinin çalıştırılmak istendiği belirtildi
            $sonuc=$calistir->execute($wherearraydeger); // calıistir metodu ile execute ile fbu fonksiyon calısıtılıyor.
            $veri=$calistir->fetchAll(PDO::FETCH_ASSOC); // veritabanındaki verileri sıralama yaparak getiriyor.
        } else
        {  // parametrenin belirtilmediği durumlarda komutlar yazıldıktan sonra çalıştırılır.
            if (!empty($ordeby)) { $sql.=" ".$ordeby;}
            if (!empty($limit)) { $sql.=" LIMIT ".$limit;}
            $veri=$this->baglanti->query($sql,PDO::FETCH_ASSOC);
        }
        if ($veri!=false && !empty($veri))  // dönen veriboş değilse ve yanlış değilse bu değer geri döndürülür.
        {
            $datalar=array();
            foreach ($veri as $bilgiler) // verileri okur.
            {$datalar[]=$bilgiler;  // belirtilen indexteki istenilen alanı getirir.
            }
            return $datalar;
        }
        else { return false ;  // bu veri boşsa veya yanlışsa bu veri yanlış değer döndürür.
        }
    }
    // sayfalardaki ortak alanlar üzerinde insert,delete,update gibi işlemleri yapmak için ortak bir fonksiyon .
    public function SorguCalistir($tablo,$alanlar="",$degerlerarray="",$limit=""){ //ortak olan kısımlar parametreler halinde yazıldı
        $this->baglanti->query("SET CHARACTER SET utf8"); // uygun karakter dizimi yapıldı.
        if(!empty($alanlar) && !empty($degerlerarray)) {
            $sql=$tablo." ".$alanlar; // yapılmak istenen sorgunun adı tablo ismi ile yazıldı.tablonun alanı dolu.
            if (!empty($limit)){$sql.=" LIMIT ".$limit;} // sql sorgusu boş değilse limit değerleri yerleştir
            $calistir=$this->baglanti->prepare($sql); // sql sorgusu çalışırıldı
            $sonuc=$calistir->execute($degerlerarray); // calıstır metodu ile parametre olarak alınan değerleri belirttik.
        }
        // alanlar ve değerler kısmı boş ise;
        else {
            $sql=$tablo;// tablonun alanı boş
            if (!empty($limit)){$sql.=" LIMIT ".$limit;} // limit parametresinin kontrolu yapılarak atandı.
            $sonuc=$this->baglanti->exec($sql); // baglanti üzerinden exec komutu ile sql calıstırıldı.
        }
        if($sonuc!=false){ // sonuc degerinin doğruluguna göre return işlemleri yapıldı.
            return true;
        } else {
            return false;
        }
        $this->baglanti->query("SET CHARACTER SET utf8");
    }

    // Türkçe karakter sıkıntısını önleyen fonksiyon
    public function sefLink($val) {
        $find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#','?','*','!','.','(',')');
        $replace = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp','','','','','','');
        $string = strtolower(str_replace($find, $replace, $val));
        $string = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $string);
        $string = trim(preg_replace('/\s+/', ' ', $string));
        $string = str_replace(' ', '-', $string);
        return $string;
    }
    // bu fonksiyon ile modül ekleme işlemi yapılır.
    public function ModulEkle()
    {
        // ilk olarak post metodu ile gelen baslik değerinin boş olup olmadığı kontrol edilir.
        if (!empty($_POST["baslik"])) {
            $baslik = $_POST["baslik"];
            if (!empty($_POST["durum"])) { // baslik değerinin aktif mi pasif mi oldugunu kontrol ediyor.
                $durum = 1;
            } else {
                $durum = 2;
            }
            $tablo = str_replace("-", "", $this->sefLink($baslik)); // baslik kısmına ne yazılırsa yazılsın sefLink metodu ile Türkçe karakterlerden arındırıyor.

            // kontrol değişkeni modül eklemeden önce ilgili başlıkla ilgili tablo olup olmadığını kontrol eder.
            $kontrol = $this->VeriGetir("modüller", "Where tablo=?", array($tablo), "ORDER BY ID ASC", 1);
            if ($kontrol != false) { // tablo varsa oluşturmaz
                return false;}
             else {
                 // sistem otomatik olarak eklenen modülün tablosunu oluşturuyor.
                 $tabloOlustur=$this->SorguCalistir('CREATE TABLE IF NOT EXISTS `'.$tablo.'` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `baslik` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `seflink` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `kategori` int(11) DEFAULT NULL,
  `metin` text COLLATE utf8_turkish_ci,
  `resim` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `anahtar` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_turkish_ci DEFAULT NULL,
  `durum` int(5) DEFAULT NULL,
  `sirano` int(11) DEFAULT NULL,
  `tarih` date DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1 ;');
                // tablo yoksa SorgulaCalistir metodu ile ekleme işlemi
                $modulekle = $this->SorguCalistir("INSERT INTO modüller", "SET baslik=?, tablo=?, durum=?, tarih=?",
                    array($baslik, $tablo, $durum, date("Y-m-d")));
                 $kategoriekle = $this->SorguCalistir("INSERT INTO kategoriler", "SET baslik=?, seflink=?, tablo=?, durum=?, tarih=?",
                     array($baslik,$tablo,'modul', 1, date("Y-m-d")));

                 if($modulekle!=false)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }

        }
        else
        {
            return false;
        }
    }

    // arama çubuğundaki tablo= kısmında olmayan bir tablo isminin girilmesi halinde filtreleme işlemi yapar.
    public function filter($val,$tf=false) {
        if ($tf==false) {$val=strip_tags($val);}  // html taglarını temizler.
        $val=addslashes(trim($val)); // 2 ayrı tırnak varken sql komutlarında sıkıntı yaşanmasını önler. tırnakların / işaretini koyar.
        return $val;
    }
    public function uzanti($dosyaadi)
    {
        $parca=explode(".",$dosyaadi);
        $uzanti=end($parca);
        $donustur=strtolower($uzanti);
        return $donustur;
    }

    public function upload($nesnename,$yuklenecekyer='images/',$tur='img',$w='',$h='',$resimyazisi='')
    {
        if($tur=="img")
        {
            if(!empty($_FILES[$nesnename]["name"]))
            {
                $dosyanizinadi=$_FILES[$nesnename]["name"];
                $tmp_name=$_FILES[$nesnename]["tmp_name"];
                $uzanti=$this->uzanti($dosyanizinadi);
                if($uzanti=="png" || $uzanti=="jpg" || $uzanti=="jpeg" || $uzanti=="gif")
                {
                    $classIMG=new upload($_FILES[$nesnename]);
                    if($classIMG->uploaded)
                    {
                        if(!empty($w))
                        {
                            if(!empty($h))
                            {
                                $classIMG->image_resize=true;
                                $classIMG->image_x=$w;
                                $classIMG->image_y=$h;
                            }
                            else
                            {
                                if($classIMG->image_src_x>$w)
                                {
                                    $classIMG->image_resize=true;
                                    $classIMG->image_ratio_y=true;
                                    $classIMG->image_x=$w;
                                }
                            }
                        }
                        else if(!empty($h))
                        {
                            if($classIMG->image_src_h>$h)
                            {
                                $classIMG->image_resize=true;
                                $classIMG->image_ratio_x=true;
                                $classIMG->image_y=$h;
                            }
                        }

                        if(!empty($resimyazisi))
                        {
                            $classIMG->image_text = $resimyazisi;

                            $classIMG->image_text_direction = 'v';

                            $classIMG->image_text_color = '#FFFFFF';

                            $classIMG->image_text_position = 'BL';
                        }
                        $rand=uniqid(true);
                        $classIMG->file_new_name_body=$rand;
                        $classIMG->Process($yuklenecekyer);
                        if($classIMG->processed)
                        {
                            $resimadi=$rand.".".$classIMG->image_src_type;
                            return $resimadi;
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else if($tur=="ds")
        {

            if(!empty($_FILES[$nesnename]["name"]))
            {

                $dosyanizinadi=$_FILES[$nesnename]["name"];
                $tmp_name=$_FILES[$nesnename]["tmp_name"];
                $uzanti=$this->uzanti($dosyanizinadi);
                if($uzanti=="doc" || $uzanti=="docx" || $uzanti=="pdf" || $uzanti=="xlsx" || $uzanti=="xls" || $uzanti=="ppt" || $uzanti=="xml" || $uzanti=="mp4" || $uzanti=="avi" || $uzanti=="mov")
                {

                    $classIMG=new upload($_FILES[$nesnename]);
                    if($classIMG->uploaded)
                    {
                        $rand=uniqid(true);
                        $classIMG->file_new_name_body=$rand;
                        $classIMG->Process($yuklenecekyer);
                        if($classIMG->processed)
                        {
                            $dokuman=$rand.".".$uzanti;
                            return $dokuman;
                        }
                        else
                        {
                            return false;
                        }
                    }
                }
            }
        }
        else
        {
            return false;
        }
    }
    // tablo adı, düzenleme işlemi yapılacak kategorinin id numarası,
//ana kategori ve alt kategori arasındaki seçimi kolaylaştırmak amacıyla uzunluk seçildi ve parametre olarak bu 3 değer gönderildi.
    public function kategoriGetir($tablo,$secID="",$uz=-1)
    {
        $uz++; // uzunluğu 0 yapıyoruz.
        // tablonun ismine göre kategori getiriyoruz.
        $kategori=$this->VeriGetir("kategoriler","WHERE tablo=?",array($tablo),"ORDER BY ID ASC");
        if($kategori!=false)
        {
            for($q=0;$q<count($kategori);$q++)
            {
                $kategoriseflink=$kategori[$q]["seflink"];
                $kategoriID=$kategori[$q]["ID"];
                if($secID==$kategoriID)
                {
                    // kategori değerinin option değerini yazdırıyoruz.
                    echo '<option value="'.$kategoriID.'" selected="selected">'.str_repeat("&nbsp;&nbsp;&nbsp;",
                            $uz).stripslashes($kategori[$q]["baslik"]).'</option>';
                }
                else // seçilen bir ID yoksa
                {
                    echo '<option value="'.$kategoriID.'">'.str_repeat("&nbsp;&nbsp;&nbsp;",
                            $uz).stripslashes($kategori[$q]["baslik"]).'</option>';
                }
                if($kategoriseflink==$tablo){break;} // kategori seflink değeri tablonun ismine eşitse döngüyü sonlandırır.
                $this->kategoriGetir($kategoriseflink,$secID,$uz); // oluşturulan kategori nesnesi yeniden çağırılır.
            }
        }
        else // kategori ismini bulamadıysa
        {
            return false;
        }
    }


    public function tekKategori($tablo,$secID="",$uz=-1)
    {
        $uz++; // uzunluğu 0 yapıyoruz.

        // seflink= tablonun ismini gönderilir ve tablo= modül ismi gönderilir.
        $kategori=$this->VeriGetir("kategoriler","WHERE seflink=? AND tablo=?",array($tablo,"modul"),"ORDER BY ID ASC");
        if($kategori!=false) // kategori başarılı bir değer dönerse
        {
            for($q=0;$q<count($kategori);$q++) //
            {
                $kategoriseflink=$kategori[$q]["seflink"];
                $kategoriID=$kategori[$q]["ID"];
                if($secID==$kategoriID) // seçilen ID varsa
                {
                    // kategori değerinin option değerini yazdırıyoruz.
                    echo '<option value="'.$kategoriID.'" selected="selected">'.str_repeat("&nbsp;&nbsp;&nbsp;",
                            $uz).stripslashes($kategori[$q]["baslik"]).'</option>';
                }
                else // seçilen bir ID yoksa
                {
                    echo '<option value="'.$kategoriID.'">'.str_repeat("&nbsp;&nbsp;&nbsp;",
                            $uz).stripslashes($kategori[$q]["baslik"]).'</option>';
                }

            }
        }
        else // kategori ismini bulamadıysa
        {
            return false;
        }
    }
}

?>
