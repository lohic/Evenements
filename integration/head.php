<?php
    include_once('../vars/config.php');
    include_once(REAL_LOCAL_PATH.'classe/classe_evenement.php');
    include_once(REAL_LOCAL_PATH.'classe/classe_session.php');
    include_once(REAL_LOCAL_PATH.'classe/classe_rubrique.php');
    include_once(REAL_LOCAL_PATH.'classe/classe_organisme.php');
    include_once(REAL_LOCAL_PATH.'classe/classe_keyword.php');

    $organisme = new organisme();
    $event     = new evenement();
    $rubrique  = new rubrique();
    $session   = new session();
    $keyword   = new keyword();


    if(isset($_GET['lang']) && $_GET['lang']=="en"){
        $lang="en";
        $complet = "FULL";
        $sinscrire = "SIGN UP";
        $aucun = "No event to come.";
    }
    else{
        $lang="fr";
        $complet = "COMPLET";
        $sinscrire = "S'INSCRIRE";
        $aucun = "Il n'y a aucun événement à venir.";
    }


    $rowOrganisme = $organisme->get_organisme();
    $rubriques_organisme = $rubrique->get_rubriques_organism($rowOrganisme['organisme_id']);
    $rubriques_partages = $rubrique->get_rubriques_partages($rowOrganisme['organisme_id']);
    if(count($rubriques_partages)>0){
        $rubriques_organisme = array_merge($rubriques_organisme, $rubriques_partages);
    }
    

    $evenements_organisme=array();
    $evenements_organisme = $event->get_events_organism();
    $evenements_partages = $event->get_events_partages();
    if(count($evenements_partages)>0){
        $evenements_organisme = array_merge($evenements_organisme, $evenements_partages);
    }

    $nomMoisAnglais = array(1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June', 7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December');
    $nomMoisFrancais = array(1=>'Janvier', 2=>'Février', 3=>'Mars', 4=>'Avril', 5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Août', 9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Décembre');
            
    $tableauMois = array();
    $tableauMois = $event->get_events_months($evenements_organisme);

    $keywords_organisme = $keyword->get_keywords_organism($rowOrganisme['organisme_id']);
    $keywords_partages = $keyword->get_keywords_partages($rowOrganisme['organisme_id']);
    if(count($keywords_partages)>0){
        $keywords_organisme = array_merge($keywords_organisme, $keywords_partages);
    }
?>
<html>
    <head>
        <title>Science po événement</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, maximum-scale=1.0">
        <link rel="stylesheet" type="text/css" href="styles.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="source/jquery.fancybox.css?v=2.1.4" media="screen" />

        <script type="text/javascript" src="js/jquery-1.10.1.min.js"></script>
        <script type="text/javascript" src="js/jquery.isotope.min.js"></script>
        <script type="text/javascript" src="js/ICanHaz.min.js"></script>
        <script type="text/javascript" src="js/jquery.resizend.js"></script>
        <script type="text/javascript" src="js/jquery.jpanelmenu.min.js"></script>
        <script type="text/javascript" src="js/jRespond.min.js"></script>
        <script type="text/javascript" src="lib/jquery.mousewheel-3.0.6.pack.js"></script>
        <script type="text/javascript" src="source/jquery.fancybox.js?v=2.1.4"></script>
        <!--<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>-->

        <script type="text/javascript" src="js/general.js"></script>
    </head>
    <body>