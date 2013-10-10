<?php
    include_once('../classe/classe_evenement.php');
    include_once('../classe/classe_session.php');
    include_once('../classe/classe_rubrique.php');
    include_once('../classe/classe_organisme.php');
    include_once('../classe/classe_keyword.php');

    $organisme = new organisme();
    $event = new evenement();
    $rubrique = new rubrique();
    $session = new session();
    $keyword = new keyword();

    if($_GET['lang']=="en"){
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
?>

<html>
    <head>
        <title>Science po événement</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        
        <link rel="stylesheet" type="text/css" href="styles.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="source/jquery.fancybox.css?v=2.1.4" media="screen" />

        <script type="text/javascript" src="js/jquery-1.10.1.min.js"></script>
        <script type="text/javascript" src="js/ICanHaz.min.js"></script>
        <script type="text/javascript" src="lib/jquery.mousewheel-3.0.6.pack.js"></script>
        <script type="text/javascript" src="source/jquery.fancybox.js?v=2.1.4"></script>

        <script type="text/javascript" src="js/general_inscription_externe.js"></script>
    </head>
    <body>