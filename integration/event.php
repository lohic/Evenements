<?php
    if (in_array($row['evenement_id'], $evenements_partages)) {
        $rowRubrique = $rubrique->get_rubrique_event_partage($row['evenement_id']);
        $rubrique_id = $rowRubrique['rubrique_id'];
        $rubrique_couleur = $rowRubrique['rubrique_couleur'];
        $lacouleur = explode("#",$rowRubrique['rubrique_couleur']);
    }
    else{
        $rubrique_id = $row['rubrique_id'];
        $rubrique_couleur = $row['rubrique_couleur'];
        $lacouleur = explode("#",$row['rubrique_couleur']);
    }
    $multiple = 10*$multiplicateur;
    $multiplicateur++;

    $moisId = $event->get_event_unique_month($row['evenement_id']);
    $finEvenement = $event->get_fin_event($row['evenement_id']);
    $horaires=func::getHorairesEvent($row['evenement_datetime'],$finEvenement,$lang);

    $lesmots = $event->get_mots($row['evenement_id']);
?>
<div class="event rubrique_<?php echo $rubrique_id;?> mois_<?php echo $moisId;?> <?php echo $lesmots;?> <?php echo $lang;?>" data-sort="<?php echo $multiple;?>" id="bloc_<?php echo $row['evenement_id'];?>">
    <?php
        if($row['evenement_image']!=""){
    ?>
            <img src="<?php echo CHEMIN_IMAGES; ?>evenement_<?php echo $row['evenement_id'];?>/moyen-<?php echo $row['evenement_image'];?>" alt="<?php echo $row['evenement_texte_image'];?>" width="320" height="180"/>
            <div class="triangle" style="border-bottom-color:<?php echo $rubrique_couleur;?>;"></div>
    <?php
        }
    ?>
    <h1 style="background-color:<?php echo $rubrique_couleur;?>" class="titre">
        <a href="/?lang=<?php echo $lang;?>&amp;id=<?php echo $row['evenement_id'];?>" rel="address:/?lang=<?php echo $lang;?>&amp;id=<?php echo $row['evenement_id'];?>" class="lien_event couleur_<?php echo $rubrique_couleur;?>" id="titre_lien_<?php echo $row['evenement_id'];?>"><?php echo $event->get_title($row, $lang);?></a>
    </h1>                               
    <div class="triangle_inverse" style="border-top-color:<?php echo $rubrique_couleur;?>;"></div>
    <p class="date h5-like"><?php echo $horaires;?></p>
    <p>
        <?php $resumeFacebook = $event->affiche_resume($row, $lang);?>
        <a href="#" rel="" title="lire la suite" class="suite couleur_<?php echo $rubrique_couleur;?>" style="border-left-color:<?php echo $rubrique_couleur;?>;" id="lien_suite_<?php echo $row['evenement_id'];?>">
        </a>
    </p>
    
    <?php
        $organisateur = $event->get_organisateur($row, $lang);
        if($organisateur!=""){
    ?>
            <p class="organisateur bit_small">
                <span style="background-color:<?php echo $rubrique_couleur; ?>"></span><?php echo $organisateur;?>                                  
            </p>
    <?php
        }
    ?>

    <div class="reseaux">           
        <a href="http://www.facebook.com/dialog/feed?app_id=177352718976945&amp;link=<?php echo CHEMIN_FRONT_OFFICE; ?>index.php?id=<?php echo $row['evenement_id']; ?>&amp;picture=<?php echo CHEMIN_IMAGES; ?>evenement_<?php echo $row['evenement_id']; ?>/mini-<?php echo $row['evenement_image'];?>&amp;name=<?php echo $row['evenement_titre_en']; ?>&amp;caption=<?php echo $horaires; ?>&amp;description=<?php echo $resumeFacebook; ?>&amp;message=Sciences Po | events&amp;redirect_uri=<?php echo CHEMIN_FRONT_OFFICE; ?>" class="reseaux facebook" style="background-color:<?php echo $rubrique_couleur; ?>"  target="_blank">
        </a>

        <a href="http://twitter.com/home?status=Je participe à cet événement Sciences Po :  <?php echo CHEMIN_FRONT_OFFICE; ?>index.php?id=<?php echo $row['evenement_id']; ?>&amp;lang=en" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');" target="_blank" onclick="javascript:pageTracker._trackPageview ('/outbound/twitter.com');" class="reseaux twitter" style="background-color:<?php echo $rubrique_couleur; ?>">
        </a>

        <a href="makeIcal.php?id=<?php echo $row['evenement_id']; ?>" target="_blank" class="reseaux ical" style="background-color:<?php echo $rubrique_couleur; ?>">
        </a>
    </div>
    <?php
        $rowSession = $session->get_session($row['evenement_id']);
        echo $session->affiche_statut_inscription($rowSession, $row, $sinscrire, $complet, $rubrique_couleur); 
    ?>

</div>