<?php
/*VT.php class dosyanıza eklenecek fonksiyon*/

public function MailGonder($mail,$konu="",$mesaj)
	   {
			   $posta = new PHPMailer();
			   	$posta->CharSet = "UTF-8";
				$posta->IsSMTP();                                   // send via SMTP
				$posta->Host     = 	"mail.siteadi.com"; // SMTP servers
				$posta->SMTPAuth = true;     // turn on SMTP authentication
				$posta->Username = "mail@siteadi.com";  // SMTP username
				$posta->Password = "mailsifresi"; // SMTP password
				$posta->Port     = 587; 
				$posta->From     = "mail@siteadi.com"; // smtp kullanýcý adýnýz ile ayný olmalý
				$posta->Fromname = "mail@siteadi.com";
				$posta->AddAddress($mail, "mail@siteadi.com");
				$posta->Subject  =  $konu;
				$posta->Body     =  $mesaj;
				
				if(!$posta->Send())
				{
				  return false;
				}
				else
				{
					return true;
				}
	   }
?>