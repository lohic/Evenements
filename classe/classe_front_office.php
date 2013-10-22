<?php

include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');

/**
 * 
 */
class FrontOffice {
	
	var $evenement_db	= NULL;
	var $id				= NULL;

	var $template		= 'default';

	var $lang			= 'fr';
	var $organisme		= 'dircom';
	var $organisme_id;
	var $url			= '/index.php';
	
	static $updated		= false;
	
	/**
	 * [frontoffice description]
	 * @return [type] [description]
	 */
	function frontoffice(){

		if(!MAINTENANCE){

			global $connexion_info;
			$this->evenement_db		= new connexion($connexion_info['server'],$connexion_info['user'],$connexion_info['password'],$connexion_info['db']);

			if(self::$updated == false){
				$this->updater();
			}

			$this->route();
			$this->debug(false);

			$this->generate();

		}else{
			// on va chercher le fichier structure maintenance
			include('structure/maintenance.php');
		}
	}



	/**
	 * [updater description]
	 * @return [type] [description]
	 */
	function updater(){
		
		self::$updated = true;
	}




	/**
	 * [route description]
	 * @return [type] [description]
	 */
	function route(){
		// on attribue la langue
		if(isset($_GET['lang']) && !empty($_GET['lang'])){
			$this->lang = $_GET['lang'];
		}

		// on vérifie l'organisme
		if(isset($_GET['organisme']) && !empty($_GET['organisme'])){
			$organisme = $_GET['organisme'];
		}

		if(empty($organisme)){
			$organisme = $this->organisme;
		}

		$sql = sprintf("SELECT organisme_id, organisme_shortcode
						FROM sp_organismes
						WHERE organisme_shortcode=%s",
						func::GetSQLValueString($organisme, 'text'));
		$query	= mysql_query($sql) or die(mysql_error());
		$result = mysql_fetch_assoc($query);

		$this->organisme 	= $result['organisme_shortcode'];
		$this->organisme_id = $result['organisme_id'];

		// on récupère et on normalise l'url
		if(isset($_GET['url']) && !empty($_GET['url'])){

			if(substr($_GET['url'],0,1) != '/'){
				$this->url = '/'.$_GET['url'];
			}else if($_GET['url'] != '/'){
				$this->url = $_GET['url'];
			}
		}
	}




	/**
	 * [debug description]
	 * @param  boolean $show [description]
	 * @return [type]        [description]
	 */
	function debug($show = false){
		if($show){
			//echo '<!--'. "\n";
			echo '<pre>'. "\n";
			echo 'LANG :      '.$this->lang . "\n";
			echo 'ORGANISME : '.$this->organisme . "\n";
			echo 'URL :       '.$this->url . "\n";
			echo '</pre>'. "\n";
			//echo '-->'. "\n";
		}
	}


		
	/**
	 * [generate description]
	 * @return [type] [description]
	 */
	function generate(){
		// cette mecanique devra être placée dans une classe
		$this->template = 'default' ;

		// on verifie qu'un ORGANISME a bien été envoyé par le HTACCESS
		if( ! empty( $_GET['organisme'] )){

			// on verifie que l'ORGANISME correspond bien à un dossier
			if ( is_dir ( REAL_LOCAL_PATH.'template_front/' . $_GET['organisme'] ) ){

				$this->template  = $_GET['organisme'];		

			}
		}

		$template_url 		 = ABSOLUTE_URL.'template_front/' . $this->template . '/';
		$template_local_path = REAL_LOCAL_PATH.'template_front/' . $this->template . '/';


		$file = $this->url;


		// on vérifie le fichier index.php du template
		if( is_file($template_local_path . $file) ){
			// on créé les variables locales et absolues pour le chemin du template
			$file_to_show 	   = $template_local_path.$file;
			$template_file_url = $template_url;
		}else{
			$file_to_show 	   = REAL_LOCAL_PATH . 'template_front/default' . $file;
			$template_file_url = ABSOLUTE_URL . 'template_front/default/';
		}

		//echo $template_local_path . 'style.css'."\n";

		// on vérifie le fichier style.css du template
		if(is_file($template_local_path . 'style.css')){
			$template_css = $template_url . 'style.css';
		}else{
			$template_css = ABSOLUTE_URL . 'template_front/default/style.css';
		}


		//echo $template_css;

		$organisme_id = $this->organisme_id;

		ob_start();

			include($file_to_show);
			$contents = ob_get_contents();

		ob_end_clean();

		echo $contents;
	}

}

	