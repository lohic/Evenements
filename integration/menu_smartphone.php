<section id="menu_smartphone" class="grand-hidden">
    <input type="text" name="mot_recherche_smartphone" id="mot_recherche_smartphone" value="Rechercher"/>
    <div id="options_smart" class="little_bigger">
    <?php
        if(count($rubriques_organisme)>0){
            $sql = "SELECT * FROM ".TB."rubriques WHERE rubrique_id IN (".implode(',',$rubriques_organisme).") ORDER BY rubrique_titre";
            $res = mysql_query($sql)or die(mysql_error());
        }
        else{
            $res=-1;
        }
        if($res!=-1){
    ?>
            <div id="filtre_categorie_smart" class="mb1 option-set" data-group="rubrique">
                <span>Catégories</span>
                <div class="conteneur_filtre">
                    <input type="checkbox" value=""        id="rubrique_toutes" class="all checkbox_smart" checked />
                    <label for="rubrique_toutes" class="all"><span class="" style="background:#fff;"></span>toutes</label>
                    <?php       
                        while($row = mysql_fetch_array($res)){
                    ?>
                            <input type="checkbox" value=".rubrique_<?php echo $row['rubrique_id'];?>" id="rubrique_<?php echo $row['rubrique_id'];?>" class="checkbox_smart" />
                            <label for="rubrique_<?php echo $row['rubrique_id'];?>"><span class="vide" style="background:<?php echo $row['rubrique_couleur'];?>; border-color:<?php echo $row['rubrique_couleur'];?>;"></span><?php echo utf8_encode($row['rubrique_titre']);?></label>
                    <?php
                        }
                    ?>
                </div>          
            </div>
    <?php
        }
    ?>

    <?php
        if(count($keywords_organisme)>0){
            $sql = "SELECT * FROM ".TB."keywords WHERE keyword_id IN (".implode(',',$keywords_organisme).") ORDER BY keyword_nom";
            $res = mysql_query($sql)or die(mysql_error());
        }
        else{
            $res=-1;
        }
        if($res!=-1){
    ?>
            <div id="filtre_mot_smart" class="mb1 option-set" data-group="mots">
                <span>Mots-clés</span>
                <div class="conteneur_filtre">
                    <input type="checkbox" value="" id="mots_tous" class="all checkbox_smart" checked />
                    <label for="mots_tous" class="all"><span class="" style="background:#fff;"></span>tous</label>
                    <?php       
                        while($row = mysql_fetch_array($res)){
                    ?>
                            <input type="checkbox" value=".mot_<?php echo $row['keyword_id'];?>" id="keyword_<?php echo $row['keyword_id'];?>" class="checkbox_smart" />
                            <label for="keyword_<?php echo $row['keyword_id'];?>"><span class="vide" style="background:#fff;"></span><?php echo $row['keyword_nom'];?></label>
                    <?php
                        }
                    ?>
                </div>                 
            </div>
    <?php
        }
    ?>

    <?php
        if(count($tableauMois)>0){
    ?>
            <div id="filtre_date_smart" class="option-set" data-group="dates">
                <span>Dates</span>
                <div class="conteneur_filtre">
                    <input type="checkbox" value="" id="dates_toutes" class="all checkbox_smart" checked />
                    <label for="dates_toutes" class="all"><span class="" style="background:#fff;"></span>toutes</label>
                    
        <?php
                    foreach($tableauMois as $mois){
                        if($lang=="fr"){
        ?>
                            <input type="checkbox" value=".mois_<?php echo $mois['unique'];?>" id="mois_<?php echo $mois['unique'];?>" class="checkbox_smart"/>
                            <label for="mois_<?php echo $mois['unique'];?>"><span class="vide" style="background:#fff;"></span><?php echo $nomMoisFrancais[$mois['mois']];?> <?php echo $mois['annee'];?></label>
        <?php
                        }
                        else{
        ?>
                            <input type="checkbox" value=".mois_<?php echo $mois['unique'];?>" id="mois_<?php echo $mois['unique'];?>" class="checkbox_smart"/>
                            <label for="mois_<?php echo $mois['unique'];?>"><span class="vide" style="background:#fff;"></span><?php echo $nomMoisAnglais[$mois['mois']];?> <?php echo $mois['annee'];?></label>
        <?php
                        }
                    }
        ?>      
                </div>
            </div>
    <?php       
        }
    ?>
        </div>

        <button id="validation_smart" class="very_small">Filtrer</button>
        <a href="soumettre.php" class="soumettre"><span class="icone"></span><span class="texte_icone">Proposer un événement</span></a>
</section>