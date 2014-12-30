<?php

include_once('PKPass.5.2.php');


generate_passcode(true);


/**
 * [generate_passcode description]
 * @return [type] [description]
 */
function generate_passcode($show=false){
	$pass = new PKPass();

	$pass->setCertificate('certificat/CertificatsBillet.p12');  // 2. Set the path to your Pass Certificate (.p12 file)
	$pass->setCertificatePassword('frmdbl@sciencespo');     // 2. Set password for certificate
	$pass->setWWDRcertPath('certificat/AppleWWDRCA.pem'); // 3. Set the path to your WWDR Intermediate certificate (.pem file)
	// Top-Level Keys http://developer.apple.com/library/ios/#documentation/userexperience/Reference/PassKit_Bundle/Chapters/TopLevel.html
	$standardKeys         = array(
	    'description'        => 'Demo pass',
	    'formatVersion'      => 1,
	    'organizationName'   => 'Flight Express',
	    'passTypeIdentifier' => 'pass.net.formidable-studio.scanevent', // 4. Set to yours
	    'serialNumber'       => '1234567891234',
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
	                'value' => 'mercredi 6 novembre'
	            )
	    	),
	        'primaryFields' => array(
	            array(
	                'key'   => 'session_name',
	                'label' => 'Nom de l’événement',
	                'value' => 'nom de la session'
	            )
	        ),
	        'secondaryFields' => array(
	            array(
	                'key'   => 'lieu',
	                'label' => 'Où',
	                'value' => 'l’endroit où ça se passe'
	            ),
	            array(
	                'key'   => 'date',
	                'label' => 'Date',
	                'value' => 'mercredi 6 novembre'
	            ),
	            array(
	                'key'   => 'horaire',
	                'label' => 'Horaire',
	                'value' => 'à 20h30'
	            )
	        ),
	        'auxiliaryFields' => array(
	            array(
	                'key'   => 'lieu2',
	                'label' => 'Où',
	                'value' => 'l’endroit où ça se passe'
	            ),
	            array(
	                'key'   => 'date2',
	                'label' => 'Date',
	                'value' => 'mercredi 6 novembre'
	            ),
	            array(
	                'key'   => 'horaire2',
	                'label' => 'Horaire',
	                'value' => 'à 20h30'
	            )
	        ),
	        'backFields' => array(
	            array(
	                'key'   => 'participant',
	                'label' => 'Participant',
	                'value' => 'Loïc Horellou'
	            ),
	            array(
	                'key'   => 'organisateur',
	                'label' => 'Organisateur',
	                'value' => 'Formidable studio'
	            ),
	            array(
	                'key'   => 'date',
	                'label' => 'Quand',
	                'value' => 'mercredi 6 novembre à 20h30'
	            ),
	            array(
	                'key'   => 'lieu',
	                'label' => 'Où',
	                'value' => 'l’endroit où ça se passe'
	            ),
	            array(
	            	'key'	=> 'session_name',
	            	'label' => 'Événemenent',
	            	'value' => 'nom de la session'
	            ),
	            array(
	            	'key'	=> 'unique_id',
	            	'label' => 'Numéro d‘inscrit',
	            	'value' => '1234567891234'
	            )
	        )
	    )
	);
	

	$visualAppearanceKeys = array(
	    'barcode'         => array(
	        'format'          => 'PKBarcodeFormatQR',
	        'message'         => '1234567891234',
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
	$pass->addFile( 'images/icon.png');
	$pass->addFile( 'images/icon@2x.png');
	$pass->addFile( 'images/logo.png');


	// Create and output the PKPass
	if(!$show){
		return $pass->create(false);
	}else if(!$pass->create(true)) {
	    echo 'Error: '.$pass->getError();
	}
}
