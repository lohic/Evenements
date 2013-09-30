<?php

include_once('../vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
// TCPDF pour pdf - http://www.tcpdf.org
include_once(REAL_LOCAL_PATH.'classe/tcpdf_min/tcpdf.php');
// PKPass pour passbook - https://github.com/tschoffelen/PHP-PKPass
include_once(REAL_LOCAL_PATH.'classe/PKPass/PKPass.php');
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
	var $big_PDF;

	/**
	 * [billet description]
	 * @param  [type] $_unique_id [description]
	 * @return [type]             [description]
	 */
	function billet($_unique_id = NULL){

		if(!empty($_unique_id)){

			$template = 'default';
			$this->code_couleur = '#cb021a';
			if(!is_dir( REAL_LOCAL_PATH.'template_front/'. $template . '/billet/' ) ){
				$template = 'default';
				$this->code_couleur = '#cb021a';
			}

			$this->localBilletFolder 	= REAL_LOCAL_PATH.'template_front/'. $template . '/billet/';
			$this->absoluteBilletFolder = ABSOLUTE_URL.'template_front/'. $template . '/billet/';

			$this->unique_id = $_unique_id;

			$this->session_name	= "Le nom de la conférence à laquelle on assiste encore plus long";

			$this->date			= "30/12/2013";
			$this->horaire		= "20:30";
			$this->lang			= "FR";

			// attention sert pour le chemin du billet
			$temp_date 			= explode('/',$this->date);
			$this->jour			= $temp_date[0];
			$this->mois			= $temp_date[1];
			$this->annee		= $temp_date[2];

			$this->nom 			= 'Horellou jh hk kh k ljhkh lk hlkh hlkjhkhhkjhlkhlkhkjh';
			$this->prenom 		= 'Loïc';
			$this->statut		= 'interne';

			$this->acces		= 'retransmission'; // Retransmission ou le nom de la salle
			$this->ecouteurs	= true;

			$this->image		= '';
			$this->url_image	= '';

			$this->lieu			= "27 Rue Saint-Guillaume\n75007 Paris";
			$this->organisateur = "Sciences Po Paris";

			$this->imageBillet  = "http://www.sciencespo.fr/evenements/admin/upload/photos/evenement_1980/grande-image.jpg?cache=1380555674";
			$this->url_image	= "http://www.sciencespo.fr/evenements/#/?lang=fr&id=1980"; 

			$this->big_PDF		= true;
		}

		//echo $this->generate_pdf(true);
		$this->generate_passcode();
		echo $this->generate_mail();
	}

	/**
	 * [generate_passcode description]
	 * @return [type] [description]
	 */
	function generate_passcode($show=false){
		$pass = new PKPass\PKPass();

		$pass->setCertificate(REAL_LOCAL_PATH.'certificat/CertificatsBillet.p12');  // 2. Set the path to your Pass Certificate (.p12 file)
		$pass->setCertificatePassword('frmdbl@sciencespo');     // 2. Set password for certificate
		$pass->setWWDRcertPath(REAL_LOCAL_PATH.'certificat/AppleWWDRCA.pem'); // 3. Set the path to your WWDR Intermediate certificate (.pem file)

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

		if(!$pass->create(true)) { // Create and output the PKPass
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

		// on précise le chemin d'export du billet en fonction de la date yyyy/mm/jj/ de la session
		$date_dir = $this->annee.'/'.$this->mois.'/'.$this->jour.'/';
		$localOutputFolder = REAL_LOCAL_PATH.BILLETS_FOLDER.$date_dir;
		$absoluteOutputFolder = ABSOLUTE_URL.BILLETS_FOLDER.$date_dir;
		
		$uploadDir = $this->createPath($localOutputFolder);


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
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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
	

		$barcode1D = '<tcpdf method="write1DBarcode" params="'.$pdf->serializeTCPDFtagParameters(array($this->unique_id, 'C128B', '', '', 90, 30, 0.4, $style, 'N')).'" />';

		ob_start();
			include_once($template_billet);
			$tbl = ob_get_contents();
		ob_end_clean();


		$pdf->writeHTML($tbl, true, false, false, false, 'left');


		// EXPORTE LE BILLET OU L'AFFICHE suivant la variable $show passée en paramètre de la fonction
		if($show){
			$pdf->Output('billet_'.$this->unique_id.'.pdf', 'I');
		}else{
			$pdf->Output($localOutputFolder.'billet_'.$this->unique_id.'.pdf', 'F');
			return $absoluteOutputFolder.'billet_'.$this->unique_id.'.pdf';
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

}