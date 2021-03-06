<?php
//include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
// TCPDF pour pdf - http://www.tcpdf.org
include_once(REAL_LOCAL_PATH.'classe/tcpdf_min/tcpdf.php');
// PKPass pour passbook - https://github.com/tschoffelen/PHP-PKPass
// uncomment for php 5.1.3
//include_once(REAL_LOCAL_PATH.'classe/PKPass/PKPass.php');
include_once(REAL_LOCAL_PATH.'classe/PKPass/PKPass.5.2.php');
// Image_QRCode-0.1.3 https://wiki.php.net/pear/packages/image_qrcode
include_once(REAL_LOCAL_PATH.'classe/Image_QRCode-0.1.3/Image/QRCode.php');

// passbook Top-Level Keys
// https://developer.apple.com/library/ios/documentation/userexperience/Reference/PassKit_Bundle/Chapters/TopLevel.html#//apple_ref/doc/uid/TP40012026-CH2-SW6
// passbook Lower-Level Keys
// https://developer.apple.com/library/ios/documentation/userexperience/Reference/PassKit_Bundle/Chapters/LowerLevel.html#//apple_ref/doc/uid/TP40012026-CH3-SW2
// passbook Field Dictionary Keys
// https://developer.apple.com/library/ios/documentation/userexperience/Reference/PassKit_Bundle/Chapters/FieldDictionary.html#//apple_ref/doc/uid/TP40012026-CH4-SW6

class Billet {

	var $connexion;
	var $unique_id;
	var $code_couleur;
	var $localBilletFolder;
	var $absoluteBilletFolder;
	var $session_name;
	var $date;
	var $horaire;
	var $lang;
	var $jour;
	var $mois;
	var $annee;
	var $nom;
	var $prenom;
	var $statut;
	var $acces;
	var $ecouteurs;
	var $lieu;
	var $organisateur;
	var $imageBillet;
	var $url_image;

	var $big_PDF;

	var $PDFurl;
	var $passbookFile;
	var $HTMLticket;

	var $localOutputFolder;
	var $absoluteOutputFolder;
	var $uploadDir;


	/**
	 * création d'un billet dans les 3 formats utiles en fonction des informations de l'inscrit et de l'événement auquel il s'est inscrit
	 * @param  int $_unique_id identifiant unique de l'inscrit
	 * @param  text $couleur couleur de l'organisme pour le billet
	 * @param  text $session_nom nom de la session
	 * @param  text $date date de la session au format jj/mm/aaaa
	 * @param  text $heure heure de la session au format hh:mm
	 * @param  text $langue langue de la session (abbréviation)
	 * @param  text $inscrit_nom nom de l'inscrit
	 * @param  text $inscrit_prenom prénom de l'inscrit
	 * @param  text $inscrit_type inscription interne ou externe 
	 * @param  text $inscrit_acces salle de la session (ou retransmission)
	 * @param  boolean $inscrit_traduction vrai si l'inscrit a demandé un casque pour la traduction
	 * @param  text $session_lieu lieu de l'événement
	 * @param  text $session_organisateur organisateur de l'événement
	 * @param  text $organisme_image_billet chemin vers l'image optionnelle pour le billet, liée à l'organisme
	 * @param  text $organisme_url_image url vers laquelle pointe l'image optionnelle, liée à l'organisme
	 * @return [type]             [description]
	 */
	function billet($_unique_id = NULL, $couleur='#cb021a', $session_nom='', $date='21/10/2013', $heure='', $langue='', $inscrit_nom='', $inscrit_prenom='', $inscrit_type='', $inscrit_acces='', $inscrit_traduction='', $session_lieu='', $session_organisateur='', $organisme_image_billet='', $organisme_url_image=''){
		if(!empty($_unique_id)){
			$template = 'default';
			$this->code_couleur = $couleur;
			if(!is_dir( REAL_LOCAL_PATH.'template_front/'. $template . '/billet/' ) ){
				$template = 'default';
				$this->code_couleur = '#cb021a';
			}

			$this->localBilletFolder 	= REAL_LOCAL_PATH.'template_front/'. $template . '/billet/';
			$this->absoluteBilletFolder = ABSOLUTE_URL.'template_front/'. $template . '/billet/';

			



			$this->unique_id = $_unique_id;

			$this->session_name	= $session_nom;

			$this->date			= $date;
			$this->horaire		= $heure;
			$this->lang			= $langue;

			// attention sert pour le chemin du billet
			$temp_date 			= explode('/',$this->date);
			$this->jour			= $temp_date[0];
			$this->mois			= $temp_date[1];
			$this->annee		= $temp_date[2];

			$this->nom 			= $inscrit_nom;
			$this->prenom 		= $inscrit_prenom;
			$this->statut		= $inscrit_type;

			$this->acces		= $inscrit_acces; // Retransmission ou le nom de la salle
			$this->ecouteurs	= $inscrit_traduction;

			$this->lieu			= $session_lieu;
			$this->organisateur = $session_organisateur;

			$this->imageBillet	= $organisme_image_billet;
			$this->url_image	= $organisme_url_image; 

			$this->big_PDF		= true;


			// on précise le chemin d'export du billet en fonction de la date yyyy/mm/jj/ de la session
			$date_dir = $this->annee.'/'.$this->mois.'/'.$this->jour.'/';
			$this->localOutputFolder = REAL_LOCAL_PATH.BILLETS_FOLDER.$date_dir;
			$this->absoluteOutputFolder = ABSOLUTE_URL.BILLETS_FOLDER.$date_dir;
			
			$this->uploadDir = $this->createPath($this->localOutputFolder);
		}

		$this->PDFurl 		= $this->generate_pdf();
		$this->passbookFile = $this->generate_passcode();
		$this->HTMLticket	= $this->generate_mail();
	}

	/**
	 * [generate_passcode description]
	 * @return [type] [description]
	 */
	function generate_passcode($show=false){
		// uncomment for php 5.1.3
		//$pass = new PKPass\PKPass();
		$pass = new PKPass();

		$pass->setCertificate(REAL_LOCAL_PATH.'certificat/CertificatsBilletSciencesPo.p12');  	// 1. Set the path to your Pass Certificate (.p12 file)
		$pass->setCertificatePassword('passbook@sciencespo');     								// 2. Set password for certificate
		$pass->setWWDRcertPath(REAL_LOCAL_PATH.'certificat/AppleWWDRCA.pem'); 					// 3. Set the path to your WWDR Intermediate certificate (.pem file)
		// Top-Level Keys http://developer.apple.com/library/ios/#documentation/userexperience/Reference/PassKit_Bundle/Chapters/TopLevel.html
		$standardKeys         = array(
		    'description'        => 'Demo pass',
		    'formatVersion'      => 1,
		    'organizationName'   => 'Flight Express',
		    'passTypeIdentifier' => 'pass.net.formidable-studio.scanevent', // 4. Set to yours
		    'serialNumber'       => $this->unique_id,
		    'teamIdentifier'     => 'GWDABM458G'           // 4. Set to yours
		);
		$associatedAppKeys    = array();
		$relevanceKeys        = array();
		$styleKeys            = array(
		    'eventTicket' => array(
		    	'headerFields'  => array(
		    		array(
		                'key'   => 'date1',
		                'label' => 'Évenement du',
		                'value' => $this->date .' '. $this->horaire
		            )
		    	),
		        'primaryFields' => array(
		            array(
		                'key'   => 'session_name',
		                'label' => 'Nom de l’événement',
		                'value' => $this->session_name
		            )
		        ),
		        'secondaryFields' => array(
		            array(
		                'key'   => 'lieu',
		                'label' => 'Où',
		                'value' => $this->lieu
		            ),
		            array(
		                'key'   => 'date',
		                'label' => 'Date',
		                'value' => $this->date
		            ),
		            array(
		                'key'   => 'horaire',
		                'label' => 'Horaire',
		                'value' => $this->horaire
		            )
		        ),
		        'auxiliaryFields' => array(
		            array(
		                'key'   => 'lieu2',
		                'label' => 'Où',
		                'value' => $this->lieu
		            ),
		            array(
		                'key'   => 'date2',
		                'label' => 'Date',
		                'value' => $this->date
		            ),
		            array(
		                'key'   => 'horaire2',
		                'label' => 'Horaire',
		                'value' => $this->horaire
		            )
		        ),
		        'backFields' => array(
		            array(
		                'key'   => 'participant',
		                'label' => 'Participant',
		                'value' => $this->prenom .' '. $this->nom
		            ),
		            array(
		                'key'   => 'organisateur',
		                'label' => 'Organisateur',
		                'value' => $this->organisateur
		            ),
		            array(
		                'key'   => 'date',
		                'label' => 'Quand',
		                'value' => $this->date .' '. $this->horaire
		            ),
		            array(
		                'key'   => 'lieu',
		                'label' => 'Où',
		                'value' => $this->lieu
		            ),
		            array(
		            	'key'	=> 'session_name',
		            	'label' => 'Événemenent',
		            	'value' => $this->session_name
		            ),
		            array(
		            	'key'	=> 'unique_id',
		            	'label' => 'Numéro d‘inscrit',
		            	'value' => $this->unique_id
		            )
		        )
		    )
		);
		

		$visualAppearanceKeys = array(
		    'barcode'         => array(
		        'format'          => 'PKBarcodeFormatQR',
		        'message'         => $this->unique_id,
		        //'altText'		  => $this->unique_id,
		        'messageEncoding' => 'iso-8859-1'
		    ),
		    //'backgroundColor' => 'rgb(203,02,26)',
		    'backgroundColor' => '#cb021a',
		    //'foregroundColor' => 'rgb(100, 10, 110)'
		    //'logoText'        => 'Sciences Po'
		);

		$webServiceKeys       = array();

		// Merge all pass data and set JSON for $pass object
		$passData = array_merge(
		    $standardKeys,
		    $associatedAppKeys,
		    $relevanceKeys,
		    $styleKeys,
		    $visualAppearanceKeys,
		    $webServiceKeys
		);


		$pass->setJSON(json_encode($passData));

		// Add files to the PKPass package
		$pass->addFile( $this->localBilletFolder .'images/icon.png');
		$pass->addFile( $this->localBilletFolder .'images/icon@2x.png');
		$pass->addFile( $this->localBilletFolder .'images/logo.png');


		// Create and output the PKPass
		if(!$show){
			//return $pass->create(false);
			file_put_contents( $this->localOutputFolder.'billet_'.$this->unique_id.'.pkpass',$pass->create(false) );
			return $this->localOutputFolder.'billet_'.$this->unique_id.'.pkpass';
		}else if(!$pass->create(true)) {
		    echo 'Error: '.$pass->getError();
		}
	}

	/**
	 * [generate_html description]
	 * @return [type] [description]
	 */
	function generate_mail(){
		
		//echo $this->base64QRcode();
		$QRcode = $this->base64QRcode(true);

		ob_start();

			include_once($this->localBilletFolder .'billet_mail.php');
			$billet = ob_get_contents();

		ob_end_clean();

		return $billet;
	}

	/**
	 * Génère un QRcode au format PNG, on peut le récupérer comme une chaîne base64 ou comme une balise IMG contenant cette chaîne
	 * @param  boolean $wrapImg indique si on souhaite retourner la chaine base64 ou la balise IMG contenant la chaine base64 de l'image PNG
	 * @return string           chaine base64 de l'image PNG ou balise IMG contenant la chaine base64 de l'image PNG
	 */
	function base64QRcode($wrapImg = false){

		ob_start();

	        $qrcode = new Image_QRCode();
			imagepng( $qrcode->makeCode( $this->unique_id , array(
			    //'image_type' => 'png',
			    'output_type' => 'return', // 'display' or 'return'
			    'error_correct' => 'M', // L: 7% error level M: 15% error level Q: 25% error level H: 30% error level
			    'module_size' => 6,
			)));

			$imageString = base64_encode(ob_get_contents());

		ob_end_clean();

		return !$wrapImg ? $imageString : '<img src="data:image/png;base64,'. $imageString .'"/>';
	}


	/**
	 * [generate_pdf description]
	 * @return [type] [description]
	 */
	function generate_pdf($show = false){

		$template_billet = $this->localBilletFolder.'billet_pdf_small.php';

		if($this->big_PDF == true && is_file($this->localBilletFolder.'billet_pdf_big.php') ){
			$template_billet = $this->localBilletFolder.'billet_pdf_big.php';
		}

		


		/**
		 * GENERATION DU PDF
		 */
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		

		// set document information
		$pdf->SetCreator('Sciences Po - Formidable Studio');
		$pdf->SetAuthor('Sciences Po - Formidable Studio');
		$pdf->SetTitle('Billet '.$this->session_name.' N° '.$this->presentUniqueID());
		$pdf->SetSubject('N° '.$this->presentUniqueID());
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


		// set font
		$pdf->SetFont('helvetica', '', 11);

		// add a page
		$pdf->AddPage();

		$pdf->SetFont('helvetica', '', 10);

		// set style for barcode
		$style = array(
		    'border' => 2,
		    'vpadding' => 'auto',
		    'hpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => array(255,255,255),
		    'module_width' => 1, // width of a single module in points
		    'module_height' => 1, // height of a single module in points
		    'position'=>'S'
		);

		// QRCODE,L : QR-CODE Low error correction
		//$pdf->write2DBarcode('youpi super ça fonctionne', 'QRCODE,L', 20, 30, 50, 50, $style, 'N');
		//$pdf->Text(20, 25, 'QRCODE L / youpi super ça fonctionne');

		$QRcode = $this->base64QRcode(true);

		$style = array(
			'hpadding'		=> 'auto',
			'vpadding'		=> 5,
			'text'			=> true,
			'label'			=> $this->unique_id,
		    'bgcolor' 		=> array(255,255,255),
			'fontsize'		=> 8,
			'stretchtext'	=> 4
		);
	

		$barcode1D = '<tcpdf method="write1DBarcode" params="'.$pdf->serializeTCPDFtagParameters(array($this->unique_id, 'C128B', '', '', 90, 20, 0.4, $style, 'N')).'" />';

		ob_start();
			include_once($template_billet);
			$tbl = ob_get_contents();
		ob_end_clean();

		// trame de fond pour le billet pdf
		/*
		// get the current page break margin
		$bMargin = $pdf->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $pdf->getAutoPageBreak();
		// disable auto-page-break
		$pdf->SetAutoPageBreak(false, 0);
		// set bacground image
		$img_file = $this->absoluteBilletFolder.'images/pdf/trame-billet.png';
		$pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$pdf->setPageMark();
		*/

		$pdf->writeHTML($tbl, true, false, false, false, 'left');


		// EXPORTE LE BILLET OU L'AFFICHE suivant la variable $show passée en paramètre de la fonction
		if($show){
			$pdf->Output('billet_'.$this->unique_id.'.pdf', 'I');
		}else{
			$pdf->Output($this->localOutputFolder.'billet_'.$this->unique_id.'.pdf', 'F');
			//return $absoluteOutputFolder.'billet_'.$this->unique_id.'.pdf';
			return $this->localOutputFolder.'billet_'.$this->unique_id.'.pdf';
		}
	}

	/**
	 * [presentUniqueID description]
	 * @return [type] [description]
	 */
	function presentUniqueID(){
		return strrev(implode(' ',str_split(strrev($this->unique_id), 4)));
	}

	/**
	 * Description
	 * @param type $chemin 
	 * @return type
	 */
	function createPath($chemin){	
		if(!is_dir($chemin)){
			mkdir($chemin, 0777, true);
		}
		return $chemin;
	}

	/**
	 * Description
	 * @param type $chaine 
	 * @return type
	 */
	function uc_strtoupper_fr($chaine){
		$chaine = strtoupper($chaine);
		$chaine = strtr($chaine, "äâàáåãéèëêòóôõöøìíîïùúûüýñçþÿæœðø","ÄÂÀÁÅÃÉÈËÊÒÓÔÕÖØÌÍÎÏÙÚÛÜÝÑÇÞÝÆŒÐØ");
		return $chaine;
	}



	/**
	 * [dateFormat description]
	 * @param  [type] $date [description]
	 * @return [type]       [description]
	 */
	function dateFormat($date){
		date_default_timezone_set("Europe/Paris");
		setlocale(LC_TIME, 'fr_FR');
		return utf8_encode(strftime('%d %B %Y',strtotime($date)));
	}

}
