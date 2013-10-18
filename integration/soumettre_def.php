<?php

// connection to data base
include_once('../vars/config.php');

include_once(REAL_LOCAL_PATH.'classe/classe_evenement.php');
include_once(REAL_LOCAL_PATH.'classe/classe_fonctions.php');
include_once(REAL_LOCAL_PATH.'classe/class.phpmailer.php');
include_once(REAL_LOCAL_PATH.'classe/class.smtp.php');
// functions library
include_once(REAL_LOCAL_PATH.'classe/fonctions.php');

$evenement = new evenement();

session_start();

if(!isset($_SESSION['nomSP'])){ 
    header('Location:identification.php');
}

$erreur="";
$reussi = false;
//Création d'un événement
if( isset($_POST['evenement_id']) ){
    $debutEvenement = 0; 
    $finEvenement = 0;

    $tableauHeureDebut = explode(":",$_SESSION["session_heure_debut"]);
    $tableauDateDebut = explode("/",$_SESSION["session_date_debut"]);

    $debutEvenement = mktime($tableauHeureDebut[0], $tableauHeureDebut[1],0,$tableauDateDebut[1],$tableauDateDebut[0],$tableauDateDebut[2]);
    if($_SESSION["session_heure_fin"]!="inconnue"){
        $tableauHeureFin = explode(":",$_SESSION["session_heure_fin"]);
    }
    else{ 
        $tableauHeureFin[0]=23;
        $tableauHeureFin[1]=59;
    }
    $tableauDateFin = explode("/",$_SESSION["session_date_fin"]);
    $finEvenement = mktime($tableauHeureFin[0], $tableauHeureFin[1],0,$tableauDateFin[1],$tableauDateFin[0],$tableauDateFin[2]);
    
    $sqlCountUser = sprintf("SELECT COUNT(*) AS nb FROM ".TB."users WHERE user_email=%s", func::GetSQLValueString($_SESSION['mailSP'], "text"));
    $sqlCountUsers = mysql_query($sqlCountUser) or die(mysql_error());         
    $resCountUsers = mysql_fetch_array($sqlCountUsers);

    if($resCountUsers['nb']==0){
        $sql =sprintf("INSERT INTO ".TB."users (user_nom, user_prenom, user_email, user_type, user_rubrique, user_alerte, user_groupe)
                        VALUES(%s, %s, %s, 10, 0, 0, 0)",
                        func::GetSQLValueString($_SESSION['nomSP'], "text"),
                        func::GetSQLValueString($_SESSION['prenomSP'], "text"),
                        func::GetSQLValueString($_SESSION['mailSP'], "text"));

        mysql_query($sql) or die(mysql_error());

        $lastIdInsert = mysql_insert_id();
    }
    else{ 
        $sqlUser = sprintf("SELECT * FROM ".TB."users WHERE user_email=%s", func::GetSQLValueString($_SESSION['mailSP'], "text"));
        $sqlUsers = mysql_query($sqlUser) or die(mysql_error());         
        $resUsers = mysql_fetch_array($sqlUsers);
        $lastIdInsert = $resUsers['user_id']; 
    }

    $sauvUser = $lastIdInsert;
    
    $sqlOrganisme = sprintf("SELECT groupe_id FROM ".TB."groupes, ".TB."organismes WHERE groupe_organisme_id=organisme_id AND organisme_url_front=%s LIMIT 1", func::GetSQLValueString(CHEMIN_FRONT_OFFICE, "text"));
    $resOrganisme = mysql_query($sqlOrganisme)or die(mysql_error());
    $rowOrganisme = mysql_fetch_array($resOrganisme);
    
    $groupe=$rowOrganisme['groupe_id'];
    
    $sql =sprintf("INSERT INTO ".TB."evenements (evenement_statut, evenement_organisateur, evenement_organisateur_en, evenement_coorganisateur, 
                        evenement_coorganisateur_en, evenement_rubrique, evenement_titre, evenement_titre_en, evenement_texte, evenement_texte_en, 
                        evenement_image, evenement_date, evenement_datetime, evenement_user_id, evenement_groupe_id)
                        VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, FROM_UNIXTIME(%s), %s, %s)",
                        func::GetSQLValueString($_SESSION['evenement_statut'], "int"),
                        func::GetSQLValueString($_SESSION['evenement_organisateur'], "text"),
                        func::GetSQLValueString($_SESSION['evenement_organisateur_en'], "text"),
                        func::GetSQLValueString($_SESSION['evenement_coorganisateur'], "text"),
                        func::GetSQLValueString($_SESSION['evenement_coorganisateur_en'], "text"),
                        func::GetSQLValueString($_SESSION['evenement_rubrique'], "int"),
                        func::GetSQLValueString($_SESSION['evenement_titre'], "text"),
                        func::GetSQLValueString($_SESSION['evenement_titre_en'], "text"),
                        func::GetSQLValueString($_SESSION['evenement_texte'], "text"),
                        func::GetSQLValueString($_SESSION['evenement_texte_en'], "text"),
                        func::GetSQLValueString($_SESSION['image'], "text"),
                        func::GetSQLValueString($debutEvenement, "text"),
                        func::GetSQLValueString($debutEvenement, "int"),
                        func::GetSQLValueString($lastIdInsert, "int"),
                        func::GetSQLValueString($groupe, "int"));

    mysql_query($sql) or die(mysql_error());

    $lastIdInsert = mysql_insert_id(); 
    $codeExterne = func::genereCode();

    $sql2 =sprintf("INSERT INTO ".TB."sessions (evenement_id, session_nom, session_nom_en, session_debut, session_debut_datetime, session_fin, 
                        session_fin_datetime, session_langue, session_lien, session_lien_en, session_code_externe)
                        VALUES(%s, %s, %s, %s, FROM_UNIXTIME(%s), %s, FROM_UNIXTIME(%s), %s, %s, %s, %s)",
                        func::GetSQLValueString($lastIdInsert, "int"),
                        func::GetSQLValueString($_SESSION['evenement_titre'], "text"),
                        func::GetSQLValueString($_SESSION['evenement_titre_en'], "text"),
                        func::GetSQLValueString($debutEvenement, "int"),
                        func::GetSQLValueString($debutEvenement, "int"),
                        func::GetSQLValueString($finEvenement, "int"),
                        func::GetSQLValueString($finEvenement, "int"),
                        func::GetSQLValueString($_SESSION['session_langue'], "text"),
                        func::GetSQLValueString($_SESSION['session_lien'], "text"),
                        func::GetSQLValueString($_SESSION['session_lien_en'], "text"),
                        func::GetSQLValueString($codeExterne, "text"));
    mysql_query($sql2) or die(mysql_error());

    if($_SESSION['image']!=""){
        mkdir(REAL_LOCAL_PATH.CHEMIN_UPLOAD."evenement_".$lastIdInsert);
        // Renseigne ici le chemin de destination de la photo
        $file_url = REAL_LOCAL_PATH.CHEMIN_UPLOAD.'evenement_'.$lastIdInsert;
        $old = REAL_LOCAL_PATH.CHEMIN_UPLOAD.'evenement_1500000';
        $img=$file_url."/".$_SESSION['image'];
        rename($old, $file_url);
        func::envoiImage($img, $lastIdInsert, $sauvUser, $_SESSION['image']);
    }
    else{
        func::envoiImage("", $lastIdInsert, $sauvUser, "");
    }
    $reussi = true; 
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Sciences Po | Événements</title>
    <link href="styles.css" rel="stylesheet" type="text/css" />
    <link href="css/stylesoumission.css" rel="stylesheet" type="text/css" />
</head>

<body class="iframe">
<?php

if(isset($_SESSION['nomSP'])){  
?>
<div id="cartouche" style="display: block;">
    <h2 class="little_bigger">Proposer un événement</h2>
    
        <?php
       
        if($reussi==false){
        ?>
            
            <form id="formcreer" name="formcreer" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']."?menu_actif=nouvelevenement"?>">
                <input type="submit" name="button" value="Valider" class="buttonenregistrer boutons_soumettre_def"/>
                <a href="soumettre.php?statut=modifier" class="buttonenregistrer boutons_soumettre_def"/>Modifier</a>     
                <input type="hidden" name="evenement_statut" id="evenement_statut" value="4"/>
            
                    <?php
                        $sqlRubrique = sprintf("SELECT * FROM ".TB."rubriques WHERE rubrique_id=%s", func::GetSQLValueString($_SESSION['evenement_rubrique'], "int"));
                        $resRubrique = mysql_query($sqlRubrique)or die(mysql_error());
                        $rowRubrique= mysql_fetch_array($resRubrique);                        
                    ?>
                    <article class="event resume">
                        <div class="resumeContent">
                            <div class="fermer" style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>">
                                <a href="#" id="close"></a>
                            </div>
                            <div class="row">
                                <div class="conteneur_detail">
                                    <div class="col visuel">
                                    <?php
                                    if($_SESSION['image']!=""){
                                    ?>
                                        <div class="illus">
                                            <img src="../admin/upload/photos/evenement_1500000/moyen-<?php echo $_SESSION['image']; ?>?cache=<?php echo time(); ?>" alt="" id="vignette" width="320"/>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                        <h1 class="bit_big"><span class="bit_small"><?php echo $rowRubrique['rubrique_titre']; ?></span><?php echo $_SESSION['evenement_titre']; ?></h1>
                                    </div>
                                    
                                    <div class="col informations">
                                        <?php
                                
                                            $datesDebut=explode("/",  $_SESSION['session_date_debut']);
                                            $datesFin=explode("/",  $_SESSION['session_date_fin']);
                                            
                                            $jourDebut = $datesDebut[0]."/".$datesDebut[1];
                                            $jourFin = $datesFin[0]."/".$datesFin[1]; 
                                            
                                            if($jourDebut==$jourFin){
                                                if($_SESSION['session_heure_fin']!="23:59"){
                                                    $horaires = $jourDebut." | ".$_SESSION['session_heure_debut']."-".$_SESSION['session_heure_fin'];   
                                                }
                                                else{
                                                    $horaires = $jourDebut." | ".$_SESSION['session_heure_debut'];
                                                }   
                                            }
                                            else{
                                                if($_SESSION['session_heure_fin']!="23:59"){
                                                    $horaires = "du ".$jourDebut." | ".$_SESSION['session_heure_debut']." au ".$jourFin." | ".$_SESSION['session_heure_fin'];
                                                }
                                                else{
                                                    $horaires = "du ".$jourDebut." | ".$_SESSION['session_heure_debut']." au ".$jourFin;
                                                }
                                            }
                                        ?>
                                        <h2 class="biggest"><?php echo $horaires; ?></h2>

                                        <?php
                                            $langues_evenement = array("Français"=>"33", "Anglais"=>"44", "Chinois"=>"86", "Allemand"=>"49", "Danois"=>"45", "Espagnol"=>"34", "Italien"=>"39", "Japonais"=>"83", "Polonais"=>"48", "Russe"=>"7", "Tchèque"=>"420");
                                            if($_SESSION['session_langue']!="" && $_SESSION['session_langue']!="33"){ 
                                                foreach($langues_evenement as $cle => $valeur){
                                                    if($_SESSION['session_langue']==$valeur){
                                                        $langue=$cle;
                                                    }
                                                }
                                            }
                                            else{
                                                $langue = "Français";
                                            }
                                        ?>
                                        <p class="langue"><span style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"></span><?php echo $langue; ?></p>
                                        <p class="organisateur">
                                            <span style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"></span><?php echo $_SESSION['evenement_organisateur']; ?>
                                        <?php
                                            if($_SESSION['evenement_coorganisateur']!=""){
                                        ?>
                                                <span class="coorganisateur"><?php echo $_SESSION['evenement_coorganisateur']; ?></span>
                                        <?php
                                            }
                                        ?>
                                        </p>
                                        
                                        <?php
                                        if($_SESSION["session_lien"]!=""){
                                        ?>
                                            <p class="infos"><span style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"></span><a href="<?php echo $_SESSION['session_lien']; ?>" target="_blank"><?php echo $_SESSION['session_lien']; ?></a></p>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>  

                                <div class="col contenu">
                                    <div class="texte bit_big">
                                        <?php echo $_SESSION['evenement_texte']; ?>
                                    </div>
                                </div>

                            </div>
                            <div class="reset"></div>
                            <div class="meta">
                                <div>
                                    <a href="#" class="reseaux facebook" style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"  target="_blank"></a>

                                    <a href="#" class="reseaux twitter" style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');">
                                    </a>

                                    <a href="#" target="_blank" class="reseaux ical" style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>">
                                    </a>
                                </div>
                            </div>
                            <div class="reset"></div>
                            <div class="bottom"  style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"></div>
                        </div>
                    </article>

                    <article class="event resume">
                        <div class="resumeContent">
                            <div class="fermer" style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>">
                                <a href="#" id="close"></a>
                            </div>
                            <div class="row">
                                <div class="conteneur_detail">
                                    <div class="col visuel">
                                    <?php
                                    if($_SESSION['image']!=""){
                                    ?>
                                        <div class="illus">
                                            <img src="../admin/upload/photos/evenement_1500000/moyen-<?php echo $_SESSION['image']; ?>?cache=<?php echo time(); ?>" alt="" id="vignette" width="320"/>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                        <h1 class="bit_big"><span class="bit_small"><?php echo $rowRubrique['rubrique_titre_en']; ?></span><?php echo $_SESSION['evenement_titre_en']; ?></h1>
                                    </div>
                                    
                                    <div class="col informations">
                                        <?php
                                
                                            $datesDebut=explode("/",  $_SESSION['session_date_debut']);
                                            $datesFin=explode("/",  $_SESSION['session_date_fin']);
                                            
                                            $jourDebut = $datesDebut[0]."/".$datesDebut[1];
                                            $jourFin = $datesFin[0]."/".$datesFin[1]; 
                                            
                                            if($jourDebut==$jourFin){
                                                if($_SESSION['session_heure_fin']!="23:59"){
                                                    $horaires = $jourDebut." | ".$_SESSION['session_heure_debut']."-".$_SESSION['session_heure_fin'];   
                                                }
                                                else{
                                                    $horaires = $jourDebut." | ".$_SESSION['session_heure_debut'];
                                                }   
                                            }
                                            else{
                                                if($_SESSION['session_heure_fin']!="23:59"){
                                                    $horaires = "du ".$jourDebut." | ".$_SESSION['session_heure_debut']." au ".$jourFin." | ".$_SESSION['session_heure_fin'];
                                                }
                                                else{
                                                    $horaires = "du ".$jourDebut." | ".$_SESSION['session_heure_debut']." au ".$jourFin;
                                                }
                                            }
                                        ?>
                                        <h2 class="biggest"><?php echo $horaires; ?></h2>

                                        <?php
                                            $langues_evenement = array("Français"=>"33", "Anglais"=>"44", "Chinois"=>"86", "Allemand"=>"49", "Danois"=>"45", "Espagnol"=>"34", "Italien"=>"39", "Japonais"=>"83", "Polonais"=>"48", "Russe"=>"7", "Tchèque"=>"420");
                                            if($_SESSION['session_langue']!="" && $_SESSION['session_langue']!="33"){ 
                                                foreach($langues_evenement as $cle => $valeur){
                                                    if($_SESSION['session_langue']==$valeur){
                                                        $langue=$cle;
                                                    }
                                                }
                                            }
                                            else{
                                                $langue = "Français";
                                            }
                                        ?>
                                        <p class="langue"><span style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"></span><?php echo $langue; ?></p>
                                        
                                        <?php
                                            if($_SESSION['evenement_organisateur_en']!=""){
                                        ?>
                                                <p class="organisateur">
                                                    <span style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"></span><?php echo $_SESSION['evenement_organisateur_en']; ?>
                                                <?php
                                                    if($_SESSION['evenement_coorganisateur_en']!=""){
                                                ?>
                                                        <span class="coorganisateur"><?php echo $_SESSION['evenement_coorganisateur_en']; ?></span>
                                                <?php
                                                    }
                                                ?>
                                                </p>
                                        <?php
                                            }
                                        ?>

                                        <?php
                                        if($_SESSION["session_lien_en"]!=""){
                                        ?>
                                            <p class="infos"><span style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"></span><a href="<?php echo $_SESSION['session_lien_en']; ?>" target="_blank"><?php echo $_SESSION['session_lien_en']; ?></a></p>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>  

                                <div class="col contenu">
                                    <div class="texte bit_big">
                                        <?php echo $_SESSION['evenement_texte_en']; ?>
                                    </div>
                                </div>

                            </div>
                            <div class="reset"></div>
                            <div class="meta">
                                <div>
                                    <a href="#" class="reseaux facebook" style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"  target="_blank"></a>

                                    <a href="#" class="reseaux twitter" style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');">
                                    </a>

                                    <a href="#" target="_blank" class="reseaux ical" style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>">
                                    </a>
                                </div>
                            </div>
                            <div class="reset"></div>
                            <div class="bottom"  style="background-color:<?php echo $rowRubrique['rubrique_couleur']; ?>"></div>
                        </div>
                    </article>
                <input name="evenement_id" type="hidden" id="evenement_id" value="1500000" />   
                <input type="submit" name="button" value="Valider" class="buttonenregistrer boutons_soumettre_def"/>
                <a href="soumettre.php?statut=modifier" class="buttonenregistrer boutons_soumettre_def"/>Modifier</a>
            </form>
        <?php
        }
        else{
        ?>
            <div id="content_inscription" style="width:780px;"> 
                <div id="formulaire">   
                    <div class="partie_droite">
                        <div class="alerte"><p><?php echo "Votre soumission a bien été prise en compte. Un administrateur l'examinera rapidement.";?></p></div><a href="index.php" class="revenir" style="width:240px;">Revenir aux événements</a>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>  
</div>
<?php
}
else{
header('Location:identification.php');
}
?>
</body>
</html>
