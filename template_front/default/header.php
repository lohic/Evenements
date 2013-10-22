<header>
    <div>
        <section id="pre_header">
            <a href="#" id="lien_menu_smartphone" class="grand-hidden"></a>
            <div class="small-hidden">
                <a href="soumettre.php" class="soumettre fancybox.iframe"><span class="icone"></span><span class="texte_icone">Proposer un événement</span></a>
            </div>
            <div>
            <?php
                if($lang=="fr"){
            ?>
                    <span class="francais"></span>
                    <a href="index.php?lang=en" title="anglais" class="anglais"><span class="anglais"></span></a>
            <?php
                }
                else{
            ?>
                    <a href="index.php?lang=fr" title="français" class="francais"><span class="francais"></span></a>
                    <span class="anglais"></span>
            <?php
                }
            ?>
                <form id="recherche_isotope" action="#" method="get">
                    <input type="text" name="mot_recherche" id="mot_recherche" placeholder="Rechercher" class="small-hidden"/>
                    <input type="hidden" name="langue_recherche" id="langue_recherche" value="<?php echo $lang;?>"/>
                    <input type="submit" value="OK" class="valider_recherche small-hidden"/>
                </form>
            <?php
                if($lang=="fr"){
            ?>
                    <a href="rss_events.php?lang=fr" class="rss"></a>
            <?php
                }
                else{
            ?>
                    <a href="rss_events.php?lang=en" class="rss"></a>
            <?php
                }
            ?>
            </div>
        </section>
        <section id="header">
            <h1><span class="invisible">Science po événements</span><a href="index.php"><img src="img/logo_spo.png" alt="sciences Po événements"/></a></h1>
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
                <div id="filtre_categorie" class="small-hidden">
                    <ul>
                        <li class="titre_filtre bit_small">
                            <span class="le_titre_filtre">Catégories</span>
                            <ul id="filtering-nav-categorie" class="filtre_isotope option-set" data-filter-group="rubrique">
                                <li class="" id="entre_cate_0">
                                    <a class="nom_du_filtre" href="#" data-filter-value=""><span class="" style="background:#666;"></span>Toutes</a>
                                </li>
                        <?php       
                                while($row = mysql_fetch_array($res)){
                        ?>
                                    <li class="" id="entre_cate_<?php echo $row['rubrique_id'];?>">
                                        <a class="nom_du_filtre" href="#" data-filter-value=".rubrique_<?php echo $row['rubrique_id'];?>"><span class="" style="background:<?php echo $row['rubrique_couleur'];?>; border-color:<?php echo $row['rubrique_couleur'];?>;"></span><?php echo utf8_encode($row['rubrique_titre']);?></a>
                                    </li>
                        <?php
                                }
                        ?>
                            </ul>
                        </li>
                    </ul>                   
                </div>
        <?php
            }
        ?>

        <?php
            if(count($tableauMois)>0){
        ?>
                <div id="filtre_date" class="small-hidden">
                    <ul>
                        <li class="titre_filtre bit_small">
                            <span class="le_titre_filtre">Dates</span>
                            <ul id="filtering-nav-date" class="filtre_isotope option-set" data-filter-group="month">
                                <li class="" id="entree_mois_0"><a class="nom_du_filtre" href="#" data-filter-value="">Toutes</a></li>
        <?php
                                foreach($tableauMois as $mois){
                                    if($lang=="fr"){
        ?>
                                        <li class="" id="entree_mois_<?php echo $mois['unique'];?>"><a class="nom_du_filtre" href="#" data-filter-value=".mois_<?php echo $mois['unique'];?>"><?php echo $nomMoisFrancais[$mois['mois']];?> <?php echo $mois['annee'];?></a></li>
        <?php
                                    }
                                    else{
        ?>
                                        <li class="" id="entree_mois_<?php echo $mois['unique'];?>"><a class="nom_du_filtre" href="#" data-filter-value=".mois_<?php echo $mois['unique'];?>"><?php echo $nomMoisAnglais[$mois['mois']];?> <?php echo $mois['annee'];?></a></li>
        <?php
                                    }
                                }
        ?>
                            </ul>
                        </li>
                    </ul>   
                </div>
        <?php       
            }
        ?>
        
        <?php
            if(count($keywords_organisme)>0){
                $sql = "SELECT * FROM ".TB."keywords WHERE keyword_id IN (".implode(',',$keywords_organisme).") ORDER BY keyword_nom";
                $res = mysql_query($sql) or die(mysql_error());
            }
            else{
                $res=-1;
            }
            if($res!=-1){
        ?>
                <div id="filtre_mot" class="small-hidden">
                    <ul>
                        <li class="titre_filtre bit_small">
                            <span class="le_titre_filtre">Mots-clés</span>
                            <ul id="filtering-nav-mot" class="filtre_isotope option-set" data-filter-group="mots">
                                <li class="" id="entree_mot_0"><a class="nom_du_filtre" href="#" data-filter-value="">Tous</a></li>
                        <?php       
                                while($row = mysql_fetch_array($res)){
                        ?>
                                    <li class="" id="entree_mot_<?php echo $row['keyword_id'];?>"><a class="nom_du_filtre" href="#" data-filter-value=".mot_<?php echo $row['keyword_id'];?>"><?php echo $row['keyword_nom'];?></a></li>
                        <?php
                                }
                        ?>
                            </ul>
                        </li>
                    </ul>                   
                </div>
        <?php
            }
        ?>
        </section>
    </div>
</header>
<div id="image" class="maxDivImg conteneur_banniere">
    <img src="admin/upload/banniere/banniere.jpg?cache=1375289621" alt="bannière" class="banniere" width="1280" height="128"/>
</div>
