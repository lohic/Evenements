<?php

// connection to data base
//include_once('../vars/config.php');

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
if( isset($_POST['evenement_titre']) ){
    $erreur=func::testChampsSoumission($_POST['evenement_titre'], $_POST['evenement_texte'], $_POST["evenement_organisateur"], $_POST['evenement_rubrique'], $_POST['session_date_debut'], $_POST['session_heure_debut']);
    
    $_SESSION['evenement_titre']                = $_POST['evenement_titre'];
    $_SESSION['evenement_titre_en']             = $_POST['evenement_titre_en'];
    $_SESSION['evenement_texte']                = $_POST['evenement_texte'];
    $_SESSION['evenement_texte_en']             = $_POST['evenement_texte_en'];
    $_SESSION['evenement_statut']               = $_POST['evenement_statut'];
    $_SESSION['evenement_organisateur']         = $_POST['evenement_organisateur'];
    $_SESSION['evenement_organisateur_en']      = $_POST['evenement_organisateur_en'];
    $_SESSION['evenement_coorganisateur']       = $_POST['evenement_coorganisateur'];
    $_SESSION['evenement_coorganisateur_en']    = $_POST['evenement_coorganisateur_en'];
    $_SESSION['evenement_rubrique']             = $_POST['evenement_rubrique'];
    $_SESSION['session_langue']                 = $_POST['session_langue'];
    $_SESSION['session_lien']                   = $_POST['session_lien'];
    $_SESSION['session_lien_en']                = $_POST['session_lien_en'];
    $_SESSION['session_date_debut']             = $_POST['session_date_debut'];
    $_SESSION['session_heure_debut']            = $_POST['session_heure_debut'];
    
    if($_POST['session_date_fin']==""){
        $_SESSION['session_date_fin']           = $_SESSION['session_date_debut'];
    }
    else{
        $_SESSION['session_date_fin']           = $_POST['session_date_fin'];
    }

    if($_POST['session_heure_fin']==""){
        $_SESSION['session_heure_fin']          = "inconnue";
    }
    else{
        $_SESSION['session_heure_fin']          = $_POST['session_heure_fin'];
    }


    if($erreur==""){ 
        if($_FILES['evenement_image']['name']!=""){
            mkdir(REAL_LOCAL_PATH.CHEMIN_UPLOAD."evenement_1500000");
            // Renseigne ici le chemin de destination de la photo
            $file_url = REAL_LOCAL_PATH.CHEMIN_UPLOAD.'evenement_1500000';
            // Définition des extensions de fichier autorisées (avec le ".")
            $extension = func::getExtension($_FILES['evenement_image']['name']);

            if(func::isExtAuthorized($extension)){ 
                $photo = 'image'.$extension; 
                $original = 'original'.$extension;      
                    
                // Upload fichier
                if (@move_uploaded_file($_FILES['evenement_image']['tmp_name'], $file_url.'/'.$photo)){
                    @chmod("$file_url/$photo", 0777);
                    $img="$file_url/$photo";
                    $repertoire_destination=$file_url."/"; 
                    $destination = "$file_url/$original"; 
                    copy($img, $destination);
                    func::make_miniature($img, 320, 180, $repertoire_destination, "moyen-");
                    func::make_miniature($img, 160, 90, $repertoire_destination, "mini-");
                }
                else{
                    echo "Erreur, impossible d'envoyer le fichier $photo";
                }
            }else{
                echo ("les fichiers avec l'extension $extension ne sont pas acceptés.") ;
            }
        }

        $_SESSION['image'] = $photo;    
        
        //echo 'soumettre_def.php';
        header('Location:soumettre_def.php');
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Sciences Po | Événements</title>
    <link href="<?php echo $template_file_url ; ?>css/stylesoumission.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $template_file_url ; ?>styles.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo $template_file_url ; ?>jquery-ui/css/ui-lightness/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
    
    <script type="text/javascript" src="<?php echo $template_file_url ; ?>jquery-ui/js/jquery-1.4.2.min.js"></script>
    <script type="text/javascript" src="<?php echo $template_file_url ; ?>jquery-ui/js/jquery-ui-1.8.5.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo $template_file_url ; ?>jquery-ui/js/jquery.ui.datepicker-fr.js"></script>
    <script type="text/javascript" src="<?php echo $template_file_url ; ?>tiny_mce/jquery.tinymce.js"></script>
</head>

<body class="iframe">
    
<?php

if(isset($_SESSION['nomSP'])){  
?>
<div id="cartouche" style="display: block;">
    <h2 class="little_bigger">Proposer un événement</h2>
    <div class="formulaire_interne">
        <?php
        if($erreur!=""){
            echo '<p class="erreur">'.$erreur.'</p>';
        }
        if($reussi==false){
        ?>
            <p style="color:#333333;">Formulaire à compléter pour toute demande de publication dans le Guide des Evénements.<br/>Attention : La soumission d'un événement ne vaut pas réservation de la salle.<br/>La réservation doit avoir été confirmée au préalable par le service du planning ou par votre référent à Sciences Po en lien avec le service du planning.</p>
            <form id="formcreer" name="formcreer" method="post" enctype="multipart/form-data" action="">
                <input type="submit" name="button" value="Visualiser" class="buttonenregistrer"/>
                        
                <input type="hidden" name="evenement_statut" id="evenement_statut" value="4"/>
            
                <fieldset>
                    <p class="legend">informations sur l'événement</p>
                    <p>
                        <label for="evenement_titre">Titre* : </label>
                        <input name="evenement_titre" type="text" class="inputField french" id="evenement_titre" value="<?php echo $_SESSION["evenement_titre"] ;?>"/>
                        <input name="evenement_titre_en" type="text" class="inputField english inputdroit" id="evenement_titre_en" value="<?php echo $_SESSION["evenement_titre_en"] ;?>"/>
                    </p>
                
                    <p>
                        <label for="evenement_texte">Description* :</label>
                        <textarea name="evenement_texte" cols="80" rows="4" class="inputField tinymce french" id="evenement_texte"><?php echo $_SESSION["evenement_texte"] ;?></textarea>
                        <textarea name="evenement_texte_en" cols="80" rows="4" class="inputField tinymce english inputdroit" id="evenement_texte_en"><?php echo $_SESSION["evenement_texte_en"] ;?></textarea>
                    </p>
                
                                
                    <p>
                        <label for="evenement_organisateur">Organisateur* : </label>
                        <input type="text" name="evenement_organisateur" value="<?php echo $_SESSION["evenement_organisateur"] ;?>" class="inputField french" id="evenement_organisateur"/>
                        <input type="text" name="evenement_organisateur_en" value="<?php echo $_SESSION["evenement_organisateur_en"] ;?>" class="inputField english inputdroit" id="evenement_organisateur_en"/>
                    </p>
                
                    <p>
                        <label for="evenement_coorganisateur" class="pas-obligatoire">Co-organisateur : </label>
                        <input type="text" name="evenement_coorganisateur" value="<?php echo $_SESSION["evenement_coorganisateur"] ;?>" class="inputField french" id="evenement_coorganisateur"/>
                        <input type="text" name="evenement_coorganisateur_en" value="<?php echo $_SESSION["evenement_coorganisateur_en"] ;?>" class="inputField english inputdroit" id="evenement_coorganisateur_en"/>
                    </p>

                    <p>
                        <label for="evenement_rubrique" class="inline">Rubrique* :</label>
                        <select name="evenement_rubrique" id="evenement_rubrique">
                            <option value="-1" selected="selected">Choisir</option>
                        <?php
                            $sqlOrganisme = sprintf("SELECT * FROM ".TB."organismes WHERE organisme_url_front=%s", func::GetSQLValueString(CHEMIN_FRONT_OFFICE, "text"));
                            $resOrganisme = mysql_query($sqlOrganisme)or die(mysql_error());
                            $rowOrganisme = mysql_fetch_array($resOrganisme);
                            
                            $sqlrubriques = sprintf("SELECT * FROM ".TB."rubriques AS spr, ".TB."groupes AS spg WHERE spg.groupe_organisme_id=%s AND spg.groupe_id=spr.rubrique_groupe_id ORDER BY rubrique_titre ASC", func::GetSQLValueString($rowOrganisme['organisme_id'], "int"));
                            $resrubriques = mysql_query($sqlrubriques)or die(mysql_error());
                           
                            while($rowrubrique = mysql_fetch_array($resrubriques)){
                        ?>
                                <option value="<?php echo $rowrubrique['rubrique_id'];?>" <?php if($_SESSION['evenement_rubrique']==$rowrubrique['rubrique_id']){echo "selected=\"selected\"";} ?>><?php echo utf8_encode($rowrubrique['rubrique_titre']);?></option>
                        <?php
                            }
                        ?>
                        </select>
                    </p>
                </fieldset>
            
                <?php   
                        $jour_debut = date("d/m/Y",$row2['session_debut']);
                        $heure_debut = date("H:i",$row2['session_debut']);

                        $jour_fin = date("d/m/Y",$row2['session_fin']);
                        $heure_fin = date("H:i",$row2['session_fin']);

                        if($heure_fin=="23:59"){
                            $heure_fin="inconnue";
                        }
                ?>
                <fieldset>
                    <p>
                        <label for="session_date_debut" class="inline">Date de début* : </label>
                        <input name="session_date_debut" type="text" class="inputFieldShort datepicker" id="session_date_debut" value="<?php echo $_SESSION["session_date_debut"] ;?>"/>
                        <label for="session_date_fin" class="inline labeldroit pas-obligatoire">Date de fin : </label>
                        <input name="session_date_fin" type="text" class="inputFieldShort datepicker inputdroit" id="session_date_fin" value="<?php echo $_SESSION["session_date_fin"];?>"/>
                    </p>
                    <p>
                        <label for="session_heure_debut" class="inline">Horaire de début* :</label>
                        <input name="session_heure_debut" type="text" id="session_heure_debut" class="inputFieldShort" value="<?php echo $_SESSION["session_heure_debut"] ;?>"/>
                        <label for="session_heure_fin" class="inline labeldroit pas-obligatoire">Horaire de fin : </label>
                        <input name="session_heure_fin" type="text" class="inputFieldShort inputdroit" id="session_heure_fin" value="<?php echo $_SESSION["session_heure_fin"] ;?>"/>
                    </p>

                    <p id="slider_heure_debut"></p>
                    <p id="slider_heure_fin" class="inputdroit"></p>    
                </fieldset>
            
                <fieldset>
                    <p>
                        <label for="session_lien" class="inline pas-obligatoire">Lien pour en savoir plus :</label>
                        <input name="session_lien" type="text" class="inputField french" id="session_lien" value="<?php echo $_SESSION["session_lien"] ;?>"/>
                        <input name="session_lien_en" type="text" class="inputField inputdroit english" id="session_lien_en" value="<?php echo $_SESSION["session_lien_en"] ;?>"/>
                    </p>
                  
                    <p>
                        <label for="evenement_image" class="pas-obligatoire">Image :</label><br /><input type="file" name="evenement_image" id="evenement_image"/><span class="image">L'image doit être en png, jpg ou gif. Résolution minimum : 480*270</span>
                    </p>    
                   
                </fieldset>

                <fieldset>
                    <p>
                        <label for="session_langue" class="inline pas-obligatoire">Langue :</label>
                        <select name="session_langue" id="session_langue">
                        <?php
                            foreach($langues_evenement as $cle => $valeur){
                                echo '<option value="'.$valeur.'"';
                                if($_SESSION['session_langue']==$valeur){echo "selected=\"selected\"";}
                                echo '>'.$cle.'</option>';
                            }
                        ?>
                        </select>
                    </p>
                </fieldset>
                <input type="submit" name="button" value="Visualiser" class="buttonenregistrer" />
                <input name="evenement_id" type="hidden" id="evenement_id" value="<?php echo $row['evenement_id']?>" />
            </form>
        <?php
        }
        ?>  
    </div>
</div>
<?php
}
else{
    header('Location:identification.php');
}
?>
<script type="text/javascript">
    $(window).load(function(){
        $( "#slider_heure_debut" ).slider({
                    value:47,
                    min: 0,
                    max: 95,
                    slide: function( event, ui ) {
                        var totalMinutes = ui.value;
                        var heures = Math.floor(totalMinutes / 4);
                        if(heures<10){
                            heures="0"+heures;
                        }
                        var minutes = (totalMinutes % 4)*15;
                        if(minutes==0){
                            minutes="00";
                        }
                        $( "#session_heure_debut" ).val( heures+":"+minutes );
                    }
                });
        //$( "#session_heure_debut" ).val( "" );
        
        $( "#slider_heure_fin" ).slider({
                    value:47,
                    min: 0,
                    max: 96,
                    slide: function( event, ui ) {
                        if(ui.value!=96){
                            var totalMinutes = ui.value;
                            var heures = Math.floor(totalMinutes / 4);
                            if(heures<10){
                                heures="0"+heures;
                            }
                            var minutes = (totalMinutes % 4)*15;
                            if(minutes==0){
                                minutes="00";
                            }
                            $( "#session_heure_fin" ).val( heures+":"+minutes );
                        }
                        else{
                            $( "#session_heure_fin" ).val("inconnue");
                        }
                    }
                });
        //$( "#session_heure_fin" ).val( "inconnue" );
        
        
        $.datepicker.setDefaults($.datepicker.regional['fr']);
        var dateDuJour = new Date();
    
        $('.datepicker').datepicker({
            onSelect:function(dateText, inst){
                if($('#session_date_debut').val()!=""){
                    var tableauDateDebut=$('#session_date_debut').val().split("/");
                    var dateBorneBasse = new Date(tableauDateDebut[2],tableauDateDebut[1]-1,tableauDateDebut[0]);
                    $('#session_date_fin').datepicker( "option", "minDate", dateBorneBasse );
                }
                else{
                    $('#session_date_fin').datepicker( "option", "minDate", dateDuJour );
                }

                if($('#session_date_fin').val()!=""){
                    var tableauDateFin=$('#session_date_fin').val().split("/");
                    var dateBorneHaute = new Date(tableauDateFin[2],tableauDateFin[1]-1,tableauDateFin[0]);
                    $('#session_date_debut').datepicker( "option", "maxDate", dateBorneHaute );
                }
                else{
                    $('#session_date_debut').datepicker( "option", "minDate", dateDuJour );
                }
            }
        ,minDate: dateDuJour
        });

        $('textarea.tinymce').tinymce({
            
            // Location of TinyMCE script
            script_url : 'tiny_mce/tiny_mce.js',

            // General options
            theme : "advanced",
            plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

            // Theme options
            theme_advanced_buttons1 : "pastetext,|,bold,italic,underline,forecolor,|,bullist,numlist,|,link,unlink,|,fullscreen",
            theme_advanced_buttons2 : "",
            theme_advanced_buttons3 : "",
            theme_advanced_buttons4 : "",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,

            // Example content CSS (should be your site CSS)
            content_css : "css/content.css",

            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "lists/template_list.js",
            external_link_list_url : "lists/link_list.js",
            external_image_list_url : "lists/image_list.js",
            media_external_list_url : "lists/media_list.js",

            // Replace values for the template plugin
            template_replace_values : {
                username : "Some User",
                staffid : "991234"
            }
        });
    });
    
</script>
</body>
</html>
