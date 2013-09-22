<?php

include_once('vars/config.php');
include_once(REAL_LOCAL_PATH.'classe/classe_connexion.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
include_once(REAL_LOCAL_PATH.'classe/tcpdf_min/tcpdf.php');
include_once(REAL_LOCAL_PATH.'classe/PKPass/PKPass.php');

// passbook Top-Level Keys
// https://developer.apple.com/library/ios/documentation/userexperience/Reference/PassKit_Bundle/Chapters/TopLevel.html#//apple_ref/doc/uid/TP40012026-CH2-SW6
// passbook Lower-Level Keys
// https://developer.apple.com/library/ios/documentation/userexperience/Reference/PassKit_Bundle/Chapters/LowerLevel.html#//apple_ref/doc/uid/TP40012026-CH3-SW2
// passbook Field Dictionary Keys
// https://developer.apple.com/library/ios/documentation/userexperience/Reference/PassKit_Bundle/Chapters/FieldDictionary.html#//apple_ref/doc/uid/TP40012026-CH4-SW6

class Billet {


	var $connexion;
	var $unique_id;

	function billet($_unique_id = NULL){

		if(!empty($_unique_id)){
			$this->unique_id = $_unique_id;

			$this->session_name	= "Le nom de la conférence à laquelle on assiste encore plus long";

			$this->date			= "30/12/2013";
			$this->horaire		= "20:30";


			$this->nom 			= 'Horellou jh hk kh k ljhkh lk hlkh hlkjhkhhkjhlkhlkhkjh';
			$this->prenom 		= 'Loïc';

			$this->lieu			= "27 Rue Saint-Guillaume\n75007 Paris";

			$this->organisateur = "Sciences Po Paris";
		}

		//$this->generate_pdf();
		$this->generate_passcode();
	}

	function generate_passcode(){
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
		        'altText'		  => $this->unique_id,
		        'messageEncoding' => 'iso-8859-1'
		    ),
		    'backgroundColor' => 'rgb(203,02,26)',
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
		//$pass->addFile(REAL_LOCAL_PATH.'template_front/default/images/billet-icon.png');
		$pass->addFile(REAL_LOCAL_PATH.'template_front/default/images/icon.png');
		$pass->addFile(REAL_LOCAL_PATH.'template_front/default/images/icon@2x.png');
		$pass->addFile(REAL_LOCAL_PATH.'template_front/default/images/logo.png');

		if(!$pass->create(true)) { // Create and output the PKPass
		    echo 'Error: '.$pass->getError();
		}
	}


	function generate_pdf(){

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Nicola Asuni');
		$pdf->SetTitle('TCPDF Example 050');
		$pdf->SetSubject('TCPDF Tutorial');
		$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 050', PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		    require_once(dirname(__FILE__).'/lang/eng.php');
		    $pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// NOTE: 2D barcode algorithms must be implemented on 2dbarcode.php class file.

		// set font
		$pdf->SetFont('helvetica', '', 11);

		// add a page
		$pdf->AddPage();

		// print a message
		$txt = "You can also export 2D barcodes in other formats (PNG, SVG, HTML). Check the examples inside the barcode directory.\n";
		$pdf->MultiCell(70, 50, $txt, 0, 'J', false, 1, 125, 30, true, 0, false, true, 0, 'T', false);


		$pdf->SetFont('helvetica', '', 10);

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		// set style for barcode
		$style = array(
		    'border' => true,
		    'vpadding' => 'auto',
		    'hpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255)
		    'module_width' => 1, // width of a single module in points
		    'module_height' => 1 // height of a single module in points
		);

		// write RAW 2D Barcode

		$code = '111011101110111,010010001000010,010011001110010,010010000010010,010011101110010';
		$pdf->write2DBarcode($code, 'RAW', 80, 30, 30, 20, $style, 'N');

		// write RAW2 2D Barcode
		$code = '[111011101110111][010010001000010][010011001110010][010010000010010][010011101110010]';
		$pdf->write2DBarcode($code, 'RAW2', 80, 60, 30, 20, $style, 'N');

		// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

		// set style for barcode
		$style = array(
		    'border' => 2,
		    'vpadding' => 'auto',
		    'hpadding' => 'auto',
		    'fgcolor' => array(0,0,0),
		    'bgcolor' => false, //array(255,255,255)
		    'module_width' => 1, // width of a single module in points
		    'module_height' => 1 // height of a single module in points
		);

		// QRCODE,L : QR-CODE Low error correction
		$pdf->write2DBarcode('youpi super ça fonctionne', 'QRCODE,L', 20, 30, 50, 50, $style, 'N');
		$pdf->Text(20, 25, 'QRCODE L / youpi super ça fonctionne');

		// QRCODE,M : QR-CODE Medium error correction
		$pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,M', 20, 90, 50, 50, $style, 'N');
		$pdf->Text(20, 85, 'QRCODE M');

		// QRCODE,Q : QR-CODE Better error correction
		$pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,Q', 20, 150, 50, 50, $style, 'N');
		$pdf->Text(20, 145, 'QRCODE Q');

		// QRCODE,H : QR-CODE Best error correction
		$pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,H', 20, 210, 50, 50, $style, 'N');
		$pdf->Text(20, 205, 'QRCODE H');

		// -------------------------------------------------------------------
		// PDF417 (ISO/IEC 15438:2006)

		/*

		 The $type parameter can be simple 'PDF417' or 'PDF417' followed by a
		 number of comma-separated options:

		 'PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6'

		 Possible options are:

		     a  = aspect ratio (width/height);
		     e  = error correction level (0-8);

		     Macro Control Block options:

		     t  = total number of macro segments;
		     s  = macro segment index (0-99998);
		     f  = file ID;
		     o0 = File Name (text);
		     o1 = Segment Count (numeric);
		     o2 = Time Stamp (numeric);
		     o3 = Sender (text);
		     o4 = Addressee (text);
		     o5 = File Size (numeric);
		     o6 = Checksum (numeric).

		 Parameters t, s and f are required for a Macro Control Block, all other parametrs are optional.
		 To use a comma character ',' on text options, replace it with the character 255: "\xff".

		*/

		$pdf->write2DBarcode('www.tcpdf.org', 'PDF417', 80, 90, 0, 30, $style, 'N');
		$pdf->Text(80, 85, 'PDF417 (ISO/IEC 15438:2006)');

		// -------------------------------------------------------------------
		// DATAMATRIX (ISO/IEC 16022:2006)

		$pdf->write2DBarcode('http://www.tcpdf.org', 'DATAMATRIX', 80, 150, 50, 50, $style, 'N');
		$pdf->Text(80, 145, 'DATAMATRIX (ISO/IEC 16022:2006)');

		// -------------------------------------------------------------------

		// new style
		$style = array(
		    'border' => 2,
		    'padding' => 'auto',
		    'fgcolor' => array(0,0,255),
		    'bgcolor' => array(255,255,64)
		);

		// QRCODE,H : QR-CODE Best error correction
		$pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,H', 80, 210, 50, 50, $style, 'N');
		$pdf->Text(80, 205, 'QRCODE H - COLORED');

		// new style
		$style = array(
		    'border' => false,
		    'padding' => 0,
		    'fgcolor' => array(128,0,0),
		    'bgcolor' => false
		);

		// QRCODE,H : QR-CODE Best error correction
		$pdf->write2DBarcode('www.tcpdf.org', 'QRCODE,H', 140, 210, 50, 50, $style, 'N');
		$pdf->Text(140, 205, 'QRCODE H - NO PADDING');

		// ---------------------------------------------------------

		//Close and output PDF document
		$pdf->Output('example_050.pdf', 'I');

		//============================================================+
		// END OF FILE
		//============================================================+

	}

}