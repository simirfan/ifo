<?php
class ifo extends pdo
{/*** ifo-intelligent functions object > ***/
	
	/** CLASS VARIABLES > **/
		
		/* Sql query sentence */				      
		public 	$sql; 			
		/* Query source */  
		public $source;
		/* Query error */  
		public $error;
		
		/*unordered list*/		  
		private $ul;

		/* Total records */
		public 	$count;	
		/* Page number */
		public 	$pagenum;	
		/* Total page */
		public 	$totalpage;
		
		/* Client ip address */ 
		public  $ip;	
		/* Now Y-m-d G:i:s */
		public 	$now;
		/* Today Y-m-d */
		public 	$today;
		/* This Year Y */
		public 	$thisyear;	
		/* This Year Y */
		public 	$thismonth;	
		
	/** < CLASS VARIABLES **/
	
	/** CLASS STATIC FUNCTIONS > **/                 
  
		public static function enigma($value)
		{/* returns by encrypting the transmitted value */
			return md5(md5(md5(trim($value))));
		}
	
		public static function fdate($value,$format='%d %B %Y, %A %H:%M')
		{/* Returns date in the required format */
			return strftime($format,strtotime($value));
		}
		
		/* Function permit > */
		
		/* Authority name in session */
		public static $aname='permit';
		/* You are not authorized message*/
		public static $amsj=IFO_AUTHORIZED_MSJ;
		/* Redirected url*/
		public static $rurl= WEB_ADRES;
		/* Redirected delay*/
		public static $rdelay='1';
				
		public static function permit($authority='',$control='page')
		{/* Checks by the authority permission to view the page */
			$authority=explode(';',$authority);
			if(empty($_SESSION[self::$aname]) or  ($authority[0]!='' and !in_array($_SESSION[self::$aname],$authority)))
				if($control=='page') die('<h4>'.self::$amsj.'</h4><meta http-equiv="refresh" content="'.self::$rdelay.';URL='.self::$rurl.'">'); else return false;
			elseif($control=='permit')return true;
		}
		/* < Function permit */
	
		public static function seflink($string)
		{/* Returns search engine friendly link */
			$find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#');
			$replace = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp');
			$string = strtolower(str_replace($find, $replace, $string));
			$string = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $string);
			$string = trim(preg_replace('/\s+/', ' ', $string));
			$string = str_replace(' ', '-', $string);
			return $string;
		}
		
		public static function bosluksil($veri)
		{
		$veri = str_replace("/s+/","",$veri);
		$veri = str_replace(" ","",$veri);
		$veri = str_replace(" ","",$veri);
		$veri = str_replace(" ","",$veri);
		$veri = str_replace("_","",$veri);
		$veri = str_replace("/s/g","",$veri);
		$veri = str_replace("/s+/g","",$veri);		
		$veri = trim($veri);
		return $veri; 
		}

		
		public static function cevir($deger)
		{ //ilk harf büyük sonrası küçük
			$deger = str_replace("ç","Ç",$deger);
			 $deger = str_replace("ğ","Ğ",$deger);
			 $deger = str_replace("ı","I",$deger);
			 $deger = str_replace("i","İ",$deger);
			 $deger = str_replace("ö","Ö",$deger);
			 $deger = str_replace("ü","Ü",$deger);
			 $deger = str_replace("ş","Ş",$deger);

			 $deger = strtoupper($deger);
			 $deger = trim($deger);

         return   mb_convert_case($deger, MB_CASE_TITLE, 'UTF-8');
		}
		
		public static function control($value,$type='text',$definedValue='',$undefinedValue='') 
		{/* Returns a value , which are controlled by types */
			$value=trim($value);
			$value = (!get_magic_quotes_gpc()) ? addslashes($value) : $value;
			switch ($type) {
			case 'blob': case 'string': case 'text': case 'VAR_STRING': case 'STRING': case 'BLOB':
				$value = ($value != '') ? "'" . $value . "'" : 'NULL';
				break;
			case 'long': case 'int':case 'LONGLONG':case 'LONG':case 'TINY':case 'SHORT':
				$value = ($value != '') ? intval($value) : 'NULL';
				break;
			case 'double':case 'DOUBLE':
				$value = ($value != '') ? "'" . doubleval($value) . "'" : 'NULL';
				break;
			case 'date': case 'datetime': case 'DATETIME':case 'DATE': case 'TIMESTAMP':
				$value = ($value != '') ? "'" . $value . "'" : 'NULL';
				break;
			case "defined":
				$value = ($value != '') ? $definedValue : $undefinedValue;
				break;
			}
			return $value;
		}
	
		public static function sendmail($email='',$subject='',$message='',$name='')
		{/* Send e-mail using phpmailer class */
			$mail= new PHPMailer();
			$mail->IsSMTP(); 
			//$mail->SMTPSecure = 'ssl';
			$mail->Host       = SMTP_HOST;
			$mail->SMTPDebug  = 1;                     
			$mail->SMTPAuth   = true;                 
			$mail->Host       = SMTP_HOST;    
			$mail->Port       = SMTP_PORT;
			$mail->CharSet = 'UTF-8';                   
			$mail->Username   = MAIL_USER;  
			$mail->Password   = MAIL_PASS;            
			$mail->SetFrom(MAIL_USER, MAIL_SENDER);				  
			$mail->Subject    = $subject;				  
			$mail->MsgHTML($message);
			$mail->AddAddress($email, $name);
			return($mail->Send());
		}
	
		public static function autolink($text)
		{/* Automaticaly convert links in the text to hyperlink and returns converted string*/
			/* http formatted */
			$text = preg_replace("#([\n ])([a-z]+?)://([a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]+)#ie",
				"'\\1<a href=\"\\2://\\3\" >\\2://\\3</a>'", $text);
			/* www formatted */
			$text = preg_replace("#^(www)\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[a-z0-9\-\.,\?!%\*_\#:;~\\&$@\/=\+]*)?)#i",
				"<a href=\"http://www.\\2.\\3\">www.\\2.\\3\</a>", $text);
			/* e-mail formatted */
			$text = preg_replace("#^([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)?[\w]+)#i",
				"<a href=\"mailto:\\1@\\2\">\\1@\\2</a>", $text);
				return $text;
		}
		
		public static function ccode($count=4)
		{/* Returns contains code of numbers and letters */
			$char = array();
			$char = array_merge(range(0,9),range('a','z'),range('A','Z')); 
			$code='';
			for ($i = 0; $i < $count; $i++) $code .= $char[mt_rand(0,61)];
			return $code;
		}
		
		public static function timeago($ptime) { 
				$etime = time() - strtotime($ptime); 
				$a = array( 12 * 30 * 24 * 60 * 60 => YEAR, 30 * 24 * 60 * 60 => MONTH, 7 * 24 * 60 * 60 => WEEK, 24 * 60 * 60 => DAY, 60 * 60    => HOUR,  60 => MINUTE, 1 => SECOND ); 
				foreach($a as $secs => $str) { $d = $etime / $secs; if($d >= 1) {  $r = round($d); return $r.' '.$str.' '.AGO; } } 
		} 
		public static function diff($s,$e) {return round((strtotime($s)-strtotime($e))/86400); }
	
	public static function shorten($word, $str = 10) {
		if (strlen($word) > $str) { 
			if (function_exists("mb_substr")) $word = mb_substr($word, 0, $str, "UTF-8").'..'; 
			else $word = substr($word, 0, $str).'..'; }
		return $word; 
	}

	public static function fileUpload($file,$name,$target,$type,$width=false,$twidth=false,$kalite=90){
		$return = null;
		$avaible = null;
		$location = $file['tmp_name']; 
		$exists = strtolower(substr($file["name"], strrpos($file["name"], ".")));
		if($type == 'image'){	$avaible = array(".jpg", ".jpeg", ".png", ".gif"); }
		elseif($type == 'video'){	$avaible = array(".mp4", ".ogg"); }
		elseif($type == 'music'){	$avaible = array(".mp3", '.mp4'); }
		elseif($type == 'catalog'){ $avaible = array(".pdf", ".doc", ".docx");	}
		elseif($type == 'belge'){ $avaible = array(".jpg", ".jpeg", ".png", ".bmp", ".gif",".pdf");	}	
		
		if(in_array($exists, $avaible)){ 
			$new_name = $name.$exists;	
			$to_location = $target.'/'.$new_name;
			if (move_uploaded_file($location, $to_location)){ 
	         if($type == 'image' AND $width){
			  $to_location_img = $target.'/img_'.$new_name;
			  $to_location_t_img = $target.'/t_img_'.$new_name;
			  list($mevcutGenislik, $mevcutYukseklik) = getimagesize($to_location);
			  $genislik = $width;
       	 	  $yukseklik = round(($mevcutYukseklik * $genislik) / $mevcutGenislik);
        	  $hedef = imagecreatetruecolor($genislik, $yukseklik);
        	  if($exists=='.jpg' OR $exists=='.jpeg') $kaynak = imagecreatefromjpeg($to_location);
        	  elseif($exists=='.png') $kaynak = imagecreatefrompng($to_location);
        	  elseif($exists=='.gif') $kaynak = imagecreatefromgif($to_location);
              imagecopyresampled($hedef, $kaynak, 0, 0, 0, 0, $genislik, $yukseklik, $mevcutGenislik, $mevcutYukseklik);
			  if($exists=='.jpg' OR $exists=='.jpeg') imagejpeg($hedef, $to_location_img, $kalite);
        	  elseif($exists=='.png') $kaynak = imagepng($hedef, $to_location_img);
        	  elseif($exists=='.gif') $kaynak = imagegif($hedef, $to_location_img);
			  if($twidth){
			   $genislik = $twidth;
       	 	   $yukseklik = round(($mevcutYukseklik * $genislik) / $mevcutGenislik);
			   $hedeft = imagecreatetruecolor($genislik, $yukseklik);
			   imagecopyresampled($hedeft, $kaynak, 0, 0, 0, 0, $genislik, $yukseklik, $mevcutGenislik, $mevcutYukseklik);             
			   if($exists=='.jpg' OR $exists=='.jpeg') imagejpeg($hedeft, $to_location_t_img, $kalite);
        	   elseif($exists=='.png') $kaynak = imagepng($hedeft, $to_location_t_img);
        	   elseif($exists=='.gif') $kaynak = imagegif($hedeft, $to_location_t_img);
			   imagedestroy($hedeft);
			  }
			  imagedestroy($hedef);
			  unlink($to_location);
		      $return = 'img_'.$new_name;
			 }else return $new_name;
			}
		}else{ $return = null; }
		return $return;
	}
		/*
		public static function fileUpload($file,$name,$target,$type){
		$return = null;
		$avaible = null;
		$location = $file['tmp_name']; 
		
		$exists = strtolower(substr($file["name"], strrpos($file["name"], ".")));
		if($type == 'image'){	$avaible = array(".jpg", ".jpeg", ".png", ".bmp", ".gif"); }
		elseif($type == 'video'){	$avaible = array(".mp4", ".ogg"); }
		elseif($type == 'music'){	$avaible = array(".mp3", '.mp4'); }
		elseif($type == 'catalog'){ $avaible = array(".pdf", ".doc", ".docx");	}	
		elseif($type == 'belge'){ $avaible = array(".jpg", ".jpeg", ".png", ".bmp", ".gif",".pdf");	}	
		if(in_array($exists, $avaible)){ 
			$new_name = $name.$exists;	
			$to_location = $target.'/'.$new_name;
			if (move_uploaded_file($location, $to_location)){ $return = $new_name; }
		}else{ $return = null; }
		return $return;
	}*/
	/** < CLASS STATIC FUNCTIONS **/  
	
	
	/** CLASS FUNCTIONS > **/ 
	
		public function __construct()
		{/* Construct function */
		 /* 1:Create defaults 2:Connect database with pdo */
			$this->defaults();
			$dns = DNS.':dbname='.DB_NAME.";charset=utf8;host=".HOST;
			try { parent::__construct($dns,DB_USER, DB_PASS);} 
			catch (PDOException $e) {die( '<h3 style="color:red">Hata: '.$e->getMessage()).'</h3>';}  
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	
		private function defaults()
		{/* Create defaults */
			if(!empty($_SERVER['HTTP_CLIENT_IP']))$this->ip=$_SERVER['HTTP_CLIENT_IP'];
			elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))$this->ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			else $this->ip=$_SERVER['REMOTE_ADDR']; 
			$this->now=date('Y-m-d G:i:s' ,strtotime('now'));
			$this->today=date('Y-m-d',strtotime('now'));
			$this->thisyear=date('Y',strtotime('now'));
			$this->thismonth=date('m',strtotime('now'));
		}
		
		public function __destruct()
		{/* Destruct function */
			$this->source = null;
		}
		
		public function error()
		{/* Returns the last error that occurred in the query source*/
			$error = $this->source->errorInfo();
			return $error[2]?$error[2]:'';
		}
	    
		public function log($islem='',$uid=0) {
         $_POST['trh']=$this->now;
         $_POST['uid']=$uid?$uid: $_SESSION['id'];
         $_POST['ip']=$_SERVER['REMOTE_ADDR'].",".$_SERVER['HTTP_USER_AGENT'];
         $_POST['islem']=$islem;
         $this->form_insert('log');
	    }
		  
		public function select($sutunlar,$tablolar,$sartlar='',$sirala='',$limit='')
		{/* Create Select SQL*/
			$this->sql="SELECT $sutunlar FROM $tablolar";
			$this->sql.=$sartlar?" WHERE $sartlar":'';
			$this->sql.=$sirala?" ORDER BY $sirala":'';
			
			$this->source=$this->prepare($this->sql);
			try {$this->source->execute();}
			catch(PDOException $e){$this->error='Hata: '.$e->getMessage();}
			
			$this->count=$this->query("SELECT FOUND_ROWS()")->fetchColumn();
			
			if($limit)
			{
			$this->pagenum = isset($_GET['pagenum']) ? (int) $_GET['pagenum'] : 1;
			$this->totalpage=ceil($this->count/$limit);
			$this->pagenum=($this->pagenum >$this->totalpage)?$this->totalpage:$this->pagenum;
			$this->pagenum=($this->pagenum<1)?1:$this->pagenum;
			$bkayit=($this->pagenum-1)*$limit;
			$this->sql.=" LIMIT $bkayit,$limit";
			$this->source=$this->prepare($this->sql);
			try {$this->source->execute();}
				catch(PDOException $e){$this->error='Hata: '.$e->getMessage();}
			}
			
			return $this;
		}
		
		/* Read single record*/
		public function read(){ return $this->source->fetch();}
		/* Read multiple record*/
		public function readAll(){ return $this->source->fetchAll();}
		
		//create unordered list
		public function create_ul($tablo,$link=1,$ust=0,$ul='')
		{
			//$ul boşsa istenilen tabloyu diziye aktar
			if(!$ul) $this->ul=$this->select('*',$tablo,'onay=1')->readAll();
			//$uste göre yeni dizi oluştur
			foreach($this->ul as $dal) 
				if($dal['ust']==$ust) $agac[]=$dal;
			//yeni dizide eleman varsa liste oluştur
			if(isset($agac)){
				$ul.='<ul>';
				foreach($agac as $dal)
				{	//aktif olan öğe için class hazırla
					$class=($link==$dal['link'])?'aktif':'';
					$cocuk=$this->select('*',$tablo,'onay=1 and ust='.$dal['id'])->read();
					$class.=($cocuk['id'])?' cocuk-var':'';
					//ana id yi bul
					$aid=($dal['ust']==0)?$dal['id'] :$dal['ust'];
					//cocuk id yi bul
					$cid=$dal['id'];
					//linki oluştur
					$url=$dal['link'];
					//recursive fonksiyon ile ulyi oluştur
					$yetkiler=explode(';',$dal['yetki']);
					if((isset($_SESSION['yetki']) and in_array($_SESSION['yetki'],$yetkiler)) OR $dal['yetki']==5){
					$ul.='<li class="'.$class.'"><a href="'.$url.'" >'.$dal['adi'].' </a>';
						$ul = $this->create_ul($tablo,$link,$cid,$ul);
					$ul.='</li>';}
				}
				$ul.='</ul>';
			}
			return $ul;
		}
		
		public function delete($tablo,$id)
		{/* Delete single record with id */
			 $this->sql="DELETE FROM $tablo WHERE id='$id'";
			 return $this->run();
		}
		
		public function run()
		{/* Run SQL*/
			@$this->source=$this->exec($this->sql);
			return $this->source;
		} 
		
		public function options($table,$select='',$value='id',$name="name",$where='active=1',$order="id ASC")
		{/* Create options for html select object */
			$options=$this->select("id,$name,$value",$table,"$where",$order)->readAll();
			//echo $this->sql;
			$o='';
			$select=explode(';',$select);
			foreach($options as $option){
				$sl=(in_array($option[$value],$select)) ? 'selected' : '';
				$o.='<option '.$sl.' value="'.$option[$value].'">'.$option[$name].'</option>';
			}
			return $o;
		}
		
		public function form_insert($tablo)
		{/* Adding all the form data to the selected table */
			$degerler=$this->create_value($this->table_info($tablo),"ekle");
			$this->sql="INSERT INTO $tablo $degerler";
			return $this->run();
		}
		
		public function form_update($tablo,$id)
		{/* Updating selected record with all the form data */
			$degerler=$this->create_value($this->table_info($tablo),"guncelle");
			$this->sql="UPDATE $tablo SET $degerler WHERE id='$id'";
			return $this->run();
		}
		
		public function table_info($tablo)
		{/* Returns field names and field types are selected table*/
			$this->sql="SELECT * FROM $tablo";
			$this->source=$this->query($this->sql);
			$alanSay = $this->source->columnCount(); //alan-adlarını say
			for ($i=0; $i<$alanSay ; $i++ ) {
			$meta = $this->source->getColumnMeta($i);
			$a = @$meta['name']; // alan-adi
			$t = @$meta['native_type']; // alan-tipi
			$alan[] = array($a,$t); //bilgileri diziye ata
			}
			return $alan;		
		}
		
		public function create_value($tablo,$tip)
		{/* Creating value for the appropriate type for the update and insert operations */
			$s='';$d='';
			if($tip=="guncelle"){
			foreach($tablo as $t) if(isset($_POST[$t[0]]))$d.=$t[0].'='.ifo::control($_POST[$t[0]],$t[1]).',';
			$sondeger=substr($d,0,-1); // sütun1=değer1,sütun2=değer2
			}elseif($tip=="ekle"){
			foreach($tablo as $t){ if(isset($_POST[$t[0]])){$s.=$t[0].',';$d.=ifo::control($_POST[$t[0]],$t[1]).',';} }
			$s=substr($s,0,-1);$d=substr($d,0,-1);
			$sondeger="($s) VALUES ($d)"; // sütunlar VALUES değerler
			}
			return $sondeger;
		}
		
		public function navigation($adres='',$link='',$konum='left',$sgoster=11)
		{
			$_GET['link']=$link;
			$ul='<div class="row"><div class="col-md-12"><ul id="nav_'.$adres.'" class="pagination pull-'.$konum.'">';
			$en_az_orta = ceil($sgoster/2);
			$en_fazla_orta = ($this->totalpage+1) - $en_az_orta;
			$sayfa_orta = $this->pagenum;
			$sayfa_orta=($sayfa_orta < $en_az_orta)?$en_az_orta:$this->pagenum;
			$sayfa_orta=($sayfa_orta > $en_fazla_orta)?$en_fazla_orta:$this->pagenum;
			$sol_sayfalar = round($sayfa_orta - (($sgoster-1) / 2));
			$sag_sayfalar = round((($sgoster-1) / 2) + $sayfa_orta);
			$sol_sayfalar=($sol_sayfalar < 1) ?1:$sol_sayfalar;
			$sag_sayfalar=($sag_sayfalar > $this->totalpage)?$this->totalpage:$sag_sayfalar;
			$ul.=($this->pagenum != 1)?'<li style="margin:0px;"><a title="İlk Sayfa" pagenum="1" link="'.@$_GET['link'].'"> <span class="glyphicon glyphicon-step-backward"></span></a></li>':'<li><a style="color:gray;" title="İlk Sayfa" pagenum="1" link="'.@$_GET['link'].'"> <span class="glyphicon glyphicon-step-backward"></span></a></li>';
			$ul.=($this->pagenum != 1)?'<li style="margin:0px;"><a title="Önceki Sayfa" pagenum="'.($this->pagenum-1).'" link="'.@$_GET['link'].'"><span class="glyphicon glyphicon-chevron-left"></span></a></li>':'<li><a style="color:gray;" title="Önceki Sayfa" pagenum="'.($this->pagenum-1).'" link="'.@$_GET['link'].'"><span class="glyphicon glyphicon-chevron-left"></span></a></li>';
			for($s = $sol_sayfalar; $s <= $sag_sayfalar; $s++) 
			$ul.=($this->pagenum == $s)?'<li class="active" style="margin:0px;"><a pagenum="'.$s.'" title="Aktif Sayfa" link="'.@$_GET['link'].'">'.$s.'</a></li>':'<li><a title="'.$s.'.Sayfa" pagenum="'.$s.'" link="'.@$_GET['link'].'">'.$s.'</a></li> ';
			$ul.=($this->pagenum != $this->totalpage)?' <li style="margin:0px;"><a title="Sonraki Sayfa" pagenum="'.($this->pagenum+1).'" link="'.@$_GET['link'].'"><span class="glyphicon glyphicon-chevron-right"></span></a></li>':'<li><a style="color:gray;" title="Sonraki Sayfa" pagenum="'.($this->pagenum+1).'" link="'.@$_GET['link'].'"><span class="glyphicon glyphicon-chevron-right"></span></a></li>';
			$ul.=($this->pagenum != $this->totalpage)?' <li style="margin:0px;"><a title="Son Sayfa" pagenum="'.$this->totalpage.'" link="'.@$_GET['link'].'"><span class="glyphicon glyphicon-step-forward"></span></a></li>':'<li><a style="color:gray;" title="Son Sayfa" pagenum="'.$this->totalpage.'" link="'.@$_GET['link'].'"><span class="glyphicon glyphicon-step-forward"></span></a></li>';
			$ul.='</ul></div></div>';echo $ul;
			}
			
		public function checkUser($userData = array())
		{//facebook login function
		
		  if(!empty($userData))
		  {
			  $user=$this->select('*','users',"(oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."') OR (email='".$userData['email']."' AND oauth_uid ='' AND permit='U')")->read();
			  //echo $this->sql;
			  if($this->count > 0)
			  {//Update user data if already exists
				  $this->sql = "UPDATE users SET ad = '".$userData['first_name']."', soy = '".$userData['last_name']."', email = '".$userData['email']."', cins = '".$userData['gender']."', locale = '".$userData['locale']."', picture = '".$userData['picture']."', link = '".$userData['link']."', modified = '".date("Y-m-d H:i:s")."', oauth_provider = '".$userData['oauth_provider']."', oauth_uid = '".$userData['oauth_uid']."' WHERE id='".$user['id']."'";
				 // echo $this->sql;
				//  $this->run();
			  }else
			  {//Insert user data
			  	$this->select('*','users',"(email='".$userData['email']."')");
				if($this->count == 0){
				  $this->sql = "INSERT INTO users SET oauth_provider = '".$userData['oauth_provider']."', oauth_uid = '".$userData['oauth_uid']."', ad = '".$userData['first_name']."', soy = '".$userData['last_name']."', email = '".$userData['email']."', cins = '".$userData['gender']."', locale = '".$userData['locale']."', picture = '".$userData['picture']."', link = '".$userData['link']."', created = '".date("Y-m-d H:i:s")."', modified = '".date("Y-m-d H:i:s")."' , durum='onaylandi'";				  
				  $this->run();}
			  }
			  $userData=$this->select('*','users',"oauth_provider = '".$userData['oauth_provider']."' AND oauth_uid = '".$userData['oauth_uid']."'")->read();
			 
		  }
		 
		  //Return user data
		  return $userData;
		}

}/*** << ifo-intelligent functions object ***/
	
