<?php
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


// return the number of images of the album
function countImages($album_id){

	//query
	$sql="SELECT id FROM images 
			WHERE album_id = '". $album_id ."'";
	$res=mysql_query($sql) or die(mysql_error());
	
	// return the number of images
	return mysql_num_rows($res);
}


// return the name of the algum
function albumName($album_id){

	//query
	$sql="SELECT title FROM albums 
			WHERE event_id = '". $album_id ."'";
	$res=mysql_query($sql) or die(mysql_error());
	$row = mysql_fetch_array($res);
	
	// return the number of images
	return $row['title'];
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

function isExtAuthorized($ext){
	$format_autorise = array( ".jpg", ".jpeg", ".png", ".gif");
	if(in_array($ext, $format_autorise)){
		return true;
	}else{
		return false;
	}
}

function isExtAuthorizedMedia($ext){
	$format_autorise = array( ".jpg", ".jpeg", ".png", ".gif", ".doc", ".pdf", ".xls", ".odt");
	if(in_array($ext, $format_autorise)){
		return true;
	}else{
		return false;
	}
}

function make_miniature($img, $max_width=400, $max_height=400, $repertoire_destination='./', $prefixe='mini-', $supprimer_original=false){
	/*Fonction qui créer la mimiature d'une image.
	Retourne true si la miniature a bien été créée, un message d'erreur sinon

	Liste des paramètres :
	- $img : Chemin relatif du répertoire dans lequel se trouve l'image originale
	- $max_width : Largeur maximale pour la miniature
	- $max_height : Hauteur maximale pour la miniature
	- $repertoire_destination : Répertoire dans lequel doit être sauvegardée la miniature
	- $prefixe : Préfixe donné à la miniature (Ex : "ma-photo.jpg" devient "mini-ma-photo.jpg"
	- $supprimer_original : Si est égal à true on supprime l'image originale

	/*Initialisations*/
	$reussi = false;
	$message = '';

	/*on ouvre le fichier*/
	$file = fopen($img,'r');
	
	if ($file !== false){ //Le fichier existe
		/*On recupere le nom de l'image*/
		$nom = $prefixe.basename($img);
		
		/*getimagesize() renvoie FALSE si le fichier n'est pas une image*/
		if (false !== list($largeur_orig,$hauteur_orig,$extension) = getimagesize($img)){
			/*On récupère l'extension de l'image*/
			$extension_img = substr(strchr($img,'.'),1);
	
			/*On vérifie que le fichier soit bien au format jpg, gif ou png*/
			if(ereg('(jpeg|jpg|gif|png)$',$extension_img)){
				
				switch ($extension_img){
					case "gif": // GIF
					$type_img = imagecreatefromgif($img); break;
					case "jpg": //JPEG
					$type_img = imagecreatefromjpeg($img); break;
					case "jpeg": //JPEG
					$type_img = imagecreatefromjpeg($img); break;
					case "png": // PNG
					$type_img = imagecreatefrompng($img); break;
				}
				
		
				/*On verifie la taille*/
				if(($largeur_orig > $max_width) || ($hauteur_orig > $max_height)){
					// si l'image est trop large ou trop haute
					if ($largeur_orig > $hauteur_orig){
						// image plus large que haute
						$hauteur = round(($hauteur_orig * $max_width) / $largeur_orig);
						$largeur = $max_width;
					}
					else{
						// image plus haute que large
						$hauteur = $max_height;
						$largeur = round(($largeur_orig * $max_height) / $hauteur_orig);
					}
				}
				else{
					$largeur = $largeur_orig;
					$hauteur = $hauteur_orig;
				}

				/*On créer la miniature*/
				$src = imagecreatetruecolor($largeur,$hauteur);
				imagealphablending($src, false);
		
				imagecopyresampled($src,$type_img,0,0,0,0,$largeur,$hauteur,$largeur_orig,$hauteur_orig);
				
				imagesavealpha($src, true);
				
				/*On sauvegarde la miniature*/
				
				switch ($extension_img){
					case "gif": // GIF
					imagegif($src, $repertoire_destination.$nom); break;
					case "jpg": //JPEG
					imagejpeg($src, $repertoire_destination.$nom); break;
					case "jpeg": //JPEG
					imagejpeg($src, $repertoire_destination.$nom); break;
					case "png": // PNG
					imagepng($src, $repertoire_destination.$nom); break;
				}
				
				/*On libere la memoire utilisée par cette image.*/
				imagedestroy($src);
				
				/*On supprime éventuellement l'image originale*/
				if ($supprimer_original === true){
					unlink($img);
				}
				
				$reussi=true;
			}
			else{
				$message = 'Erreur : L\'extension de l\'image est '.$extension_img.' ! Elle devrait être JPG, JPEG, GIF ou PNG';
			}
		}
		else{
			$message = 'Erreur : Le fichier n\'est pas une image !';
		}
	}
	else{
		$message = 'Erreur : Le fichier n\'existe pas !';
	}

	if ($reussi){
		return true;
	}
	else{
		return $message;
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

function testChamps($titre="", $texte="", $rubrique="", $date="", $heure="", $texte_image="", $fichier=""){
	$erreur = "";
	
	if($titre==""){
		$erreur .= "le champ titre de l'événement est obligatoire<br/>";
	}
	if($texte==""){
		$erreur .= "le champ description de l'événement est obligatoire<br/>";
	}
	if($rubrique=="-1"){
		$erreur .= "Merci de choisir une rubrique pour l'événement<br/>";
	}
	if($date==""){
		$erreur .= "la date de début de l'événement est obligatoire<br/>";
	}
	if($heure==""){
		$erreur .= "l'heure de début de l'événement est obligatoire<br/>";
	}
	
	if($fichier!="" && $texte_image==""){
		$erreur .= "le texte alternatif de l'image est obligatoire<br/>";
	}
	
	return $erreur;
}


// pour convertir tes couleurs hexa en tableau RGB
// tu peux passer #000000 ou 000000 en paramètre
function Hex2RGB($color){
	$color = str_replace('#', '', $color);
	if (strlen($color) != 6){ return array(0,0,0); }
	$rgb = array();
	for ($x=0;$x<3;$x++){
		$rgb[$x] = hexdec(substr($color,(2*$x),2));
	}
	return $rgb;
}

// génération d'un triangle
// par defaut le triangle est noir de dimension 40x20
function triangle($couleur='#000000',$largeur=40,$hauteur=20,$name='default'){
	//triangle normal
	$col = Hex2RGB($couleur);
	
	$image				= imagecreate($largeur,$hauteur);
	$noir				= imagecolorallocate($image,0,0,0);
	$couleur_triangle	= imagecolorallocate($image,$col[0],$col[1],$col[2]);
	
	$points = array(0, $hauteur, $largeur/2, 0, $largeur, $hauteur);
	
	imagecolortransparent($image, $noir);
	
	ImageFilledPolygon ($image, $points, 3, $couleur_triangle);
	
	if($name == 'default'){
		$name = 'triangles/triangle_'.$couleur;
	}
	imagepng($image, $name.'.png');

	imagedestroy($image);
}

// génération d'un triangle
// par defaut le triangle est noir de dimension 40x20
function triangle_inverse($couleur='#000000',$largeur=40,$hauteur=20,$name='default'){
	$col = Hex2RGB($couleur);
	
	//triangle inversé
	$image				= imagecreate($largeur,$hauteur);
	$noir				= imagecolorallocate($image,0,0,0);
	$couleur_triangle	= imagecolorallocate($image,$col[0],$col[1],$col[2]);
	$points = array(0, 0, $largeur/2, $hauteur, $largeur, 0);
	
	imagecolortransparent($image, $noir);
	
	ImageFilledPolygon ($image, $points, 3, $couleur_triangle);
	
	if($name == 'default'){
		$name = 'triangles/triangle_inverse_'.$couleur;
	}
	imagepng($image, $name.'.png');

	imagedestroy($image);
}

function retournerMoisToutesLettres($mois){
	switch ($mois) {
	    case 1:
	        $mois="Janvier";
	        break;
	    case 2:
	        $mois="Février";
	        break;
	    case 3:
	        $mois="Mars";
	        break;
	  	case 4:
	        $mois="Avril";
	        break;
	    case 5:
	        $mois="Mai";
	        break;
	    case 6:
	        $mois="Juin";
	        break;
		case 7:
	        $mois="Juillet";
	        break;
	    case 8:
	        $mois="Août";
	        break;
	    case 9:
	        $mois="Septembre";
	        break;
		case 10:
	        $mois="Octobre";
	        break;
	    case 11:
	        $mois="Novembre";
	        break;
	    case 12:
	        $mois="Décembre";
	        break; 
	}
	return $mois;
}

function retourneStatutToutesLettres($statut){ 
	switch ($statut) {
	    case 1:
	        $statut="brouillon";
	        break;
	    case 2:
	         $statut="caché";
	        break;
	    case 3:
	        $statut="publié";
	        break;
	}
	return $statut;	
}

function getHoraires($jourDebut, $jourFin, $debut, $fin){
	
	if($jourDebut==$jourFin){
		if(date("H:i", $fin)!="23:59"){
			$horaires = date("H\hi", $debut)." > ".date("H\hi", $fin);
		}
		else{
			$horaires = "à ".date("H\hi", $debut);
		}	
	}
	else{
		if(date("H:i", $fin)!="23:59"){
			$horaires = "du ".date("d/m/Y", $debut)." à ".date("H\hi", $debut)." au ".date("d/m/Y", $fin)." à ".date("H\hi", $fin);
		}
		else{
			$horaires = "du ".date("d/m/Y", $debut)." à ".date("H\hi", $debut)." au ".date("d/m/Y", $fin);
		}
	}
	return $horaires;
} 

function getDates($debut, $fin){
	if($debut==$fin){
		$jour=$debut;
	}
	else{
		$jour=$debut."<span style=\"font-size:0.7em;\">></span>".$fin;
	} 
	return $jour;
} 

function retourneNumeroFinMois($mois, $annee){
	if($mois==1 || $mois==3 || $mois==5 || $mois==7 || $mois==8 || $mois==10 || $mois==12){
		$finmois = mktime(23,59,59,$mois,31,$annee);
	}
	else{
		if($mois==2){
			if(date("L", mktime(0,0,0,1,1,$annee))==1){
				$finmois = mktime(23,59,59,$mois,29,$annee);
			}
			else{
				$finmois = mktime(23,59,59,$mois,28,$annee);
			}
		}
		else{
			$finmois = mktime(23,59,59,$mois,30,$annee);
		}
	}
	return $finmois;
} 

function requeteListeTousEvents($debut, $fin, $complement){
	$sql = "SELECT * FROM sp_evenements WHERE evenement_statut!=4 AND evenement_date >='".$debut."' AND evenement_date <='".$fin."' ".$complement." ORDER BY evenement_date DESC";
	return $sql;
}

function requeteCompteListeTousEvents($debut, $fin, $complement){
	$sqlcount = mysql_query("SELECT COUNT(*) AS nb FROM sp_evenements WHERE evenement_statut!=4 ".$complement." AND evenement_date >='".$debut."' AND evenement_date <='".$fin."'");
	return $sqlcount;
}

function clearDir($dossier) {
	$ouverture=@opendir($dossier);
	if (!$ouverture) return;
	while($fichier=readdir($ouverture)) {
		if ($fichier == '.' || $fichier == '..') continue;
			if (is_dir($dossier."/".$fichier)) {
				$r=clearDir($dossier."/".$fichier);
				if (!$r) return false;
			}
			else {
				$r=@unlink($dossier."/".$fichier);
				if (!$r) return false;
			}
	}
	closedir($ouverture);
	$r=@rmdir($dossier);
	if (!$r) return false;
	return true;
} 

function retourneTimestamp($heure, $dateFin, $datedebut){
	if($heure!="inconnue"){
		$tableauHeureFin = explode(":",$heure);
	}
	else{
		$tableauHeureFin[0]=23;
		$tableauHeureFin[1]=59;
	}
	
	if($dateFin!=""){
		$tableauDateFin = explode("/",$dateFin);
		$timestamp = mktime($tableauHeureFin[0], $tableauHeureFin[1],0,$tableauDateFin[1],$tableauDateFin[0],$tableauDateFin[2]);
	}
	else{
		$tableauDateFin = explode("/",$datedebut);
		$timestamp = mktime($tableauHeureFin[0], $tableauHeureFin[1],0,$tableauDateFin[1],$tableauDateFin[0],$tableauDateFin[2]);
	}
	
	return $timestamp;
}

function retournePhoto($fichier, $cachee){
	if($fichier!=""){
		$extension = getExtension($fichier);
		if(isExtAuthorized($extension)){
			$photo = 'image'.$extension;
		}
	}
	else{
		$photo = $cachee;
	}
	return $photo;
}



?>