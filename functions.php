<?php

include('makeTicket.php');

/*
@ CONVERSION DES CHAINES ENVOYEES PAR LES FORMULAIRES
@
@
*/
if (!function_exists("GetSQLValueString")) {
	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = ""){
		if (PHP_VERSION < 6) {
			$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
		}
		
		$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
		
		switch ($theType) {
			case "text":
				$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;    
			case "long":
			case "int":
				$theValue = ($theValue != "") ? intval($theValue) : "NULL";
			break;
			case "double":
				$theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
			break;
			case "date":
				$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
			case "defined":
				$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			break;
			case "boolean":
				$theValue = $theValue ? '1' : '0';
			break;
		}
		return $theValue;
	}
}

// functions ///////////////////////////////////////////////////////////

$colorRubrique = array("#b2d5d8", "#f7dbaa", "#e99e95", "#a4d3ad", "#8ed0c2", "#c9a1b5");

// delete all previous thumbnail
function eraseThumbnail($id){

	// array of files
	$files = array("album_images/t_" . $id . ".jpg", "album_images/t_" . $id . ".gif", "album_images/t_" . $id . ".png", "album_images/t_" . $id . ".swf");
	
	
	// for each item...
	foreach($files as $path){
	
		// if image  exists ...
		if( file_exists($path)){
			
			// delete it !
			unlink($path);		
		}
	}
}


// delete all previous image 
function eraseImage($id){

	// array of files
	$files = array("album_images/" . $id . ".jpg", "album_images/" . $id . ".gif", "album_images/" . $id . ".png", "album_images/" . $id . ".swf");
	
	
	// for each item...
	foreach($files as $path){
	
		// if image  exists ...
		if( file_exists($path)){
			
			// delete it !
			unlink($path);		
		}
	}
}



// delete all posible files given a file id
function deleteFiles($id){

	// array of files
	$files = array("album_images/" . $id . ".jpg", "album_images/" . $id . ".gif", "album_images/" . $id . ".png", "album_images/" . $id . ".swf");
	$files_t = array("album_images/t_" . $id . ".jpg", "album_images/t_" . $id . ".gif", "album_images/t_" . $id . ".png", "album_images/t_" . $id . ".swf");
	
	
	// for each item...
	foreach($files as $path){
	
		// if image  exists ...
		if( file_exists($path)){
			
			// delete it !
			unlink($path);		
		}
	}
	
	
	// for each item...
	foreach($files_t as $path){
	
		// if image  exists ...
		if( file_exists($path)){
			
			// delete it !
			unlink($path);		
		}
	}
}


// delete file of image or swf
function deleteFile($id){

	// array of files
	$files = array("album_images/" . $id . ".jpg", "album_images/" . $id . ".gif", "album_images/" . $id . ".png", "album_images/" . $id . ".swf");
	$files_t = array("album_images/t_" . $id . ".jpg", "album_images/t_" . $id . ".gif", "album_images/t_" . $id . ".png", "album_images/t_" . $id . ".swf");
	
	
	// for each item...
	foreach($files as $path){
	
		// if image  exists ...
		if( file_exists($path)){
			
			// delete it !
			unlink($path);		
		}
	}
	
	
	// for each item...
	foreach($files_t as $path){
	
		// if image  exists ...
		if( file_exists($path)){
			
			// delete it !
			unlink($path);		
		}
	}
}


// returns the extension of file string
function getExtension($fileName){
	
	// get the extension
	$ext = substr(strrchr($fileName, '.'), 1);
	
	// to lower case
	$ext = "." . strtolower($ext);
	
	return $ext;
}

// returns the extension of file string
function mk_vignette( $img, $thumb_width, $newfilename ) {

	$max_width = $thumb_width;

	//Check if GD extension is loaded
	if (!extension_loaded('gd') && !extension_loaded('gd2')) {
		echo("GD is not loaded");
	    return false;
	}

	//Get Image size info
	list( $width_orig, $height_orig, $image_type ) = getimagesize($img);

	switch ($image_type) {
		case 1: $im = imagecreatefromgif($img); break;
		case 2: $im = imagecreatefromjpeg($img);  break;
		case 3: $im = imagecreatefrompng($img); break;
		default:  echo('Unsupported filetype!');  break;
	}

	/*** calculate the aspect ratio ***/
	$aspect_ratio = (float) $height_orig / $width_orig;

	/*** calulate the thumbnail width based on the height ***/
	$thumb_height = round($thumb_width * $aspect_ratio);

	while( $thumb_height > $max_width ) {
		$thumb_width -= 10;
		$thumb_height = round( $thumb_width * $aspect_ratio );
	}
	$newImg = imagecreatetruecolor( $thumb_width, $thumb_height );

	/* Check if this image is PNG or GIF, then set if Transparent*/  
	if(($image_type == 1) OR ($image_type==3)) {
		imagealphablending( $newImg, false );
		imagesavealpha( $newImg,true );
		$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
		imagefilledrectangle($newImg, 0, 0, $thumb_width, $thumb_height, $transparent);
	}
	imagecopyresampled($newImg, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $width_orig, $height_orig);

	//Generate the file, and rename it to $newfilename
	switch ($image_type) {
		case 1: imagegif( $newImg,$newfilename ); break;
		case 2: imagejpeg( $newImg,$newfilename );  break;
		case 3: imagepng( $newImg,$newfilename ); break;
		default:  echo( 'Failed resize image!' );  break;
	}

	return $newfilename;
}

// returns the path of the thumbnail image
function readThumb($id){

	// paths
	$gifPath = "album_images/t_" . $id . ".gif";
	$pngPath = "album_images/t_" . $id . ".png";
	$jpgPath = "album_images/t_" . $id . ".jpg";
	
	// if exist this file return path
	if(file_exists($gifPath) )	return $gifPath;
	if(file_exists($jpgPath) )	return $jpgPath;
	if(file_exists($pngPath) )	return $pngPath;	
}


// returns the path of the file
function readMyFile($id){

	// paths
	$gifPath = "album_images/" . $id . ".gif";
	$pngPath = "album_images/" . $id . ".png";
	$jpgPath = "album_images/" . $id . ".jpg";
	$swfPath = "album_images/" . $id . ".swf";
	
	// if exist this file return path
	if(file_exists($gifPath) )	return $gifPath;
	if(file_exists($jpgPath) )	return $jpgPath;
	if(file_exists($pngPath) )	return $pngPath;
	if(file_exists($swfPath) )	return $swfPath;		
}

function sendingMail( $body, $subject, $userEmail ) {
	$headers  = "From: $userName<$userEmail>\r\n";
	$headers .= "Reply-To: $userEmail\r\n";
	$headers .= "Return-Path: $userEmail\r\n";
	$headers .= "X-Mailer: Php\n";
	$headers .= 'MIME-Version: 1.0' . "\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

	return mail($userEmail, $subject, $body, $headers);
}
function sendingMailAttachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
	$file = $path.$filename;
	$file_size = filesize($file);
	$handle = fopen($file, "r");
	$content = fread($handle, $file_size);
	fclose($handle);
	$content = chunk_split(base64_encode($content));
	$uid = md5(uniqid(time()));
	$name = basename($file);
	$header = "From: ".$from_name." <".$from_mail.">\r\n";
	$header .= "Reply-To: ".$replyto."\r\n";
	$header .= "MIME-Version: 1.0\r\n";
	$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
	$header .= "This is a multi-part message in MIME format.\r\n";
	$header .= "--".$uid."\r\n";
	$header .= "Content-type:text/html; charset=utf-8\r\n";
	$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
	$header .= $message."\r\n\r\n";
	$header .= "--".$uid."\r\n";
	$header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
	$header .= "Content-Transfer-Encoding: base64\r\n";
	$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
	$header .= $content."\r\n\r\n";
	$header .= "--".$uid."--";
    return mail($mailto, $subject, $message, $header);
}

function buildBarCode( $name, $code, $nameEvent, $dateEvent, $horaireEvent, $adress1Event, $adress2Event, $adress3Event, $adress4Event, $organisationEvent, $typePublic, $noEtudiant, $inscriptionVision ) {
	$objCode = new makeTicket() ;
	$objCode->setText('');
	$objCode->setType('C39');
	$objCode->hideCodeType();
	$objCode->setColors('#131313');
	$objCode -> setCode($code);
	$objCode -> setInfos($name, $code, $nameEvent, $dateEvent, $horaireEvent, $adress1Event, $adress2Event, $adress3Event, $adress4Event, $organisationEvent, $typePublic, $noEtudiant, $inscriptionVision );
	$objCode -> writeBarcodeFile('img/code_EAN'.$code.'.png');
	return 'code_EAN'.$code.'.png';
}

function supprimerAccent($chaine){
	$accents = array('À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ');
	$sans = array('A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y');
	return str_replace($accents, $sans, $chaine);
}

function testSessionComplete($session_id, $totales, $prises){
	$sql = sprintf("SELECT ".$totales." AS spt, ".$prises." AS spp FROM sp_sessions WHERE session_id =%s", 
								GetSQLValueString($session_id, "int"));
	//$sql = sprintf("SELECT %s AS spt, %s AS spp FROM sp_sessions WHERE session_id =%s", GetSQLValueString($totales, "text"),GetSQLValueString($prises, "text"),GetSQLValueString($session_id, "int"));
	$res=mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	if($row['spp']>=$row['spt']){
		return true;
	}
	else{
		return false;
	}
}

function incrementeNbInscrits($session_id, $champ){
	$sql = sprintf("UPDATE sp_sessions SET ".$champ."=".$champ."+1 WHERE session_id =%s",
	 							GetSQLValueString($session_id, "int"));
	$res=mysql_query($sql) or die(mysql_error());
}

function dejaInscrit($mail, $session){ 
	$sql = sprintf("SELECT * FROM sp_inscrits WHERE inscrit_mail =%s AND inscrit_session_id=%s", 
								GetSQLValueString($mail, "text"),
								GetSQLValueString($session, "int"));
	
	$res=mysql_query($sql) or die(mysql_error());
	// return the number of images
	if(mysql_num_rows($res)==0){
		return false;
	}
	else{
		return true;
	}
}

function testeChamps($nom, $prenom, $mail){
	$syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#'; 
	if($nom=="" || $prenom=="" || $mail=="" || !preg_match($syntaxe,$mail)){ 
		return false;
	}
	else{ 
		return true;
	}
}

function envoiMail($nom, $prenom, $mail, $session, $date, $billet, $endroit){
	$mailEnvoi  = new phpmailer();
	$mailEnvoi -> IsMail();
	$mailEnvoi -> Host     = 'localhost';
	$mailEnvoi -> Hostname     = 'Sciences Po';
	$mailEnvoi -> Charset  = 'UTF-8';
	$mailEnvoi -> SMTPAuth = FALSE;
	$mailEnvoi -> From     = 	'no.reply@sciences-po.fr';
	$mailEnvoi -> FromName =	utf8_decode('Sciences Po / Evénements');
	$mailEnvoi -> AddReplyTo(  );
	$mailEnvoi -> WordWrap = 72;
	$mailEnvoi -> IsHTML( TRUE );
	
	if($endroit==1){
		$mailEnvoi -> Subject		= utf8_decode("Inscription à la retransmission: ".$session);
		$mailEnvoi -> Body		.= utf8_decode("Bonjour ".$prenom." ".$nom.".<br/>Vous êtes maintenant inscrit(e) à la retransmission:<br/>");
	}
	else{
		$mailEnvoi -> Subject		= utf8_decode("Inscription à ".$session);
		$mailEnvoi -> Body		.= utf8_decode("Bonjour ".$prenom." ".$nom.".<br/>Vous êtes maintenant inscrit(e) à l'événement:<br/>");
	}

	
	$mailEnvoi -> Body		.= utf8_decode("<strong>".$session."</strong><br/><br/>");
	
	$mailEnvoi -> Body		.= utf8_decode("Veuillez trouver le billet correspondant en pièce jointe.<br/>Merci de vous munir de ce billet imprimé et d'une pièce d'identité le jour de l'événement.<br/><br/>Ce billet est strictement personnel et incessible, il peut être contrôlé à l'entrée.<br/><br/>Cordialement<br/>Sciences Po Evénements");
		
	$mailEnvoi -> AddAttachment($billet);
	//$mailEnvoi -> AddAttachment('export/billet_7000000404633.pdf');
	
	$mailEnvoi -> AddAddress($mail);
	$mailEnvoi->Send();
	unset($mailEnvoi);
}

function envoiAlerte($session_id){ 
	$sqlSession = sprintf("SELECT session_nom, session_places_internes_totales AS spit, session_places_internes_prises AS spip FROM sp_sessions WHERE session_id =%s", 
								GetSQLValueString($session_id, "int"));
	$resSession=mysql_query($sqlSession) or die(mysql_error());
	$rowSession = mysql_fetch_array($resSession);
	
	if(($rowSession['spit']-$rowSession['spip'])==10){ 
		
		$sqlIdEvenement = sprintf("SELECT evenement_groupe_id FROM sp_sessions as sps, sp_evenements as spe WHERE spe.evenement_id=sps.evenement_id AND session_id =%s", 
									GetSQLValueString($session_id, "int"));
		$resIdEvenement=mysql_query($sqlIdEvenement) or die(mysql_error());
		$rowIdEvenement = mysql_fetch_array($resIdEvenement);
		
		$sqlUser=sprintf("SELECT * FROM sp_users as spu, sp_rel_user_groupe as spr WHERE (user_alerte = '1' AND user_type='1') OR (user_alerte='1' AND groupe_id=%s AND spr.user_id=spu.user_id) GROUP BY spu.user_id",GetSQLValueString($rowIdEvenement['evenement_groupe_id'], "int"));
		$resUser=mysql_query($sqlUser) or die(mysql_error());
		while($rowUser = mysql_fetch_array($resUser)){
			$mailAlerte  = new phpmailer();
			$mailAlerte -> IsMail();
			$mailAlerte -> Host     = 'localhost';
			$mailAlerte -> Charset  = 'UTF-8';
			$mailAlerte -> SMTPAuth = FALSE;
			$mailAlerte -> From     = 	'no.reply@sciences-po.fr';
			$mailAlerte -> FromName =	utf8_decode('Sciences Po - Evénements');
			$mailAlerte -> AddReplyTo( );
			$mailAlerte -> WordWrap = 72;
			$mailAlerte -> IsHTML( TRUE );
			$mailAlerte -> Subject		= utf8_decode("Alerte pour ".$rowSession['session_nom']);

			$mailAlerte -> Body		.= utf8_decode('Il ne reste que 10 places encore disponibles pour '.$rowSession['session_nom']);

			$mailAlerte -> AddAddress($rowUser['user_email']);

			$mailAlerte->Send();
			unset($mailAlerte);
		}
	}	
}

function basculeInscription($session_id, $totale){
	if($totale){ 
		$sql = sprintf("UPDATE sp_sessions SET session_statut_inscription=0, session_statut_visio=1 WHERE session_id =%s", 
									GetSQLValueString($session_id, "int"));
	}
	else{
		$sql = sprintf("UPDATE sp_sessions SET session_statut_visio=1 WHERE session_id =%s", 
									GetSQLValueString($session_id, "int"));
	}
	$res=mysql_query($sql) or die(mysql_error());
}

function uniqueID($id_event=NULL, $id_user=NULL){

	if(isset($id_event) && isset($id_user)){
	
		$id_diff = abs($id_event-$id_user);
		
		$a			= rand(1,9);	
		$d			= str_split( substr("0000".$id_diff		, -4) );
		$e			= str_split( substr("0000".$id_event	, -4) );
		$u			= str_split( substr("0000".$id_user		, -4) );

		$ordre		= $a%3;
	
		if($ordre == 0){
			$ID = $a . $d[0] . $e[0] . $u[0] . $d[1] . $e[1] . $u[1] . $d[2] . $e[2] . $u[2] . $d[3] . $e[3] . $u[3];
		}else if($ordre == 1){
			$ID = $a . $e[0] . $u[0] . $d[0] . $e[1] . $u[1] . $d[1] . $e[2] . $u[2] . $d[2] . $e[3] . $u[3] . $d[3];
		}else if($ordre == 2){
			$ID = $a . $u[0] . $d[0] . $e[0] . $u[1] . $d[1] . $e[1] . $u[2] . $d[2] . $e[2] . $u[3] . $d[3] . $e[3];
		}

		return $ID;

	}else{

		return NULL;

	}
}

function afficheSession($session_id, $provenance){
	$sqlMaSession = sprintf("SELECT * FROM sp_sessions WHERE session_id =%s", 
								GetSQLValueString($session_id, "int"));
	$resMaSession=mysql_query($sqlMaSession) or die(mysql_error());
	$rowMaSession = mysql_fetch_array($resMaSession);
	
	setlocale(LC_ALL, 'fr_FR');
	$jour = utf8_decode(strftime('%A %d/%m',$rowMaSession['session_debut']));
	$heureDebut = date('H:i', $rowMaSession['session_debut']);
	$heureFin = date('H:i', $rowMaSession['session_fin']);
	
	
	$sqllieux = sprintf("SELECT * FROM sp_lieux WHERE lieu_id =%s", 
								GetSQLValueString($rowMaSession['session_lieu'], "int"));
	$reslieux = mysql_query($sqllieux) or die(mysql_error());
	$rowlieu = mysql_fetch_array($reslieux);

	if($provenance == 1){
		$totalInterne = $rowMaSession['session_places_internes_totales']+$rowMaSession['session_places_internes_totales_visio'];
		$totalInternePrises = $rowMaSession['session_places_internes_prises']+$rowMaSession['session_places_internes_prises_visio'];
		$differenceInterneTotale = $totalInterne - $totalInternePrises;
		$differenceInterneAmphi = $rowMaSession['session_places_internes_totales'] - $rowMaSession['session_places_internes_prises'];
		$differenceInterneVisio = $rowMaSession['session_places_internes_totales_visio'] - $rowMaSession['session_places_internes_prises_visio'];

		if($rowMaSession['session_statut_inscription']==1 && $differenceInterneAmphi!=0){
			
			echo '<p class="titre_session"><input type="checkbox" name="sessions[]" value="'.$session_id.'"/>'.$rowMaSession['session_nom'].'</p>';
			if($rowMaSession['session_traduction']==1){
				echo '<p class="date_session">Réserver un casque pour la traduction : <input name="inscrit_casque[]" type="checkbox" id="inscrit_casque" value="'.$session_id.'"/></p>';
			}
			echo '<p class="date_session">'.$jour.' de '.$heureDebut.' à '.$heureFin.'</p>';
			echo '<p class="lieu_session">Lieu : '.utf8_encode($rowlieu['lieu_nom']).'</p>';   
			
			 
		}
		else{
			if($rowMaSession['session_statut_visio']==1 && $differenceInterneVisio!=0){
				echo '<p class="titre_session"><input type="checkbox" name="sessions[]" value="'.$session_id.'"/>'.$rowMaSession['session_nom'].'</p>';
				if($rowMaSession['session_traduction']==1){
					echo '<p class="date_session">Réserver un casque pour la traduction : <input name="inscrit_casque[]" type="checkbox" id="inscrit_casque" value="'.$session_id.'"/></p>';
				}
				echo '<p class="date_session">'.$jour.' de '.$heureDebut.' à '.$heureFin.' <span class="placement">(vous serez placé en salle de retransmission)</span></p>';
				echo '<p class="lieu_session">Lieu : '.utf8_encode($rowlieu['lieu_nom']).'</p>';
				
			}
		}
	}
	else{
		$totalExterne = $rowMaSession['session_places_externes_totales']+$rowMaSession['session_places_externes_totales_visio'];
		$totalExternePrises = $rowMaSession['session_places_externes_prises']+$rowMaSession['session_places_externes_prises_visio'];
		$differenceExterneTotale = $totalExterne - $totalExternePrises;
		$differenceExterneAmphi = $rowMaSession['session_places_externes_totales'] - $rowMaSession['session_places_externes_prises'];
		$differenceExterneVisio = $rowMaSession['session_places_externes_totales_visio'] - $rowMaSession['session_places_externes_prises_visio'];
		
		if($rowMaSession['session_statut_inscription']==1 && $differenceExterneAmphi!=0){
			echo '<p class="titre_session"><input type="checkbox" name="sessions[]" value="'.$session_id.'"/>'.$rowMaSession['session_nom'].'</p>';
			if($rowMaSession['session_traduction']==1){
				echo '<p class="date_session">Réserver un casque pour la traduction : <input name="inscrit_casque[]" type="checkbox" id="inscrit_casque" value="'.$session_id.'"/></p>';
			}
			echo '<p class="date_session">'.$jour.' de '.$heureDebut.' à '.$heureFin.'</p>';
			echo '<p class="lieu_session">Lieu : '.utf8_encode($rowlieu['lieu_nom']).'</p>';
				
		}
		else{
			if($rowMaSession['session_statut_visio']==1 && $differenceExterneVisio!=0){
				echo '<p class="titre_session"><input type="checkbox" name="sessions[]" value="'.$session_id.'"/>'.$rowMaSession['session_nom'].'</p>';
				if($rowMaSession['session_traduction']==1){
					echo '<p class="date_session">Réserver un casque pour la traduction : <input name="inscrit_casque[]" type="checkbox" id="inscrit_casque" value="'.$session_id.'"/></p>';
				}
				echo '<p class="date_session">'.$jour.' de '.$heureDebut.' à '.$heureFin.' <span class="placement">(vous serez placé en salle de retransmission)</span></p>';
				echo '<p class="lieu_session">Lieu : '.utf8_encode($rowlieu['lieu_nom']).'</p>';
				
			}
		}
	}
	 	
}

function genereCode(){
	$code = '';
	for($i=0;$i<6;$i++){
		switch(rand(1,3)){
			case 1: $code.=chr(rand(48,57));  break; //0-9
			case 2: $code.=chr(rand(65,90));  break; //A-Z
			case 3: $code.=chr(rand(97,122)); break; //a-z
		}
	}
	echo $code;
}
?>