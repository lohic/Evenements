<?php
	include('head.php');
	include('menu_smartphone.php');
?>
		<section id="contenu_principal">
<?php
			include('header.php');
?>
			<div id="conteneur_isotope">
				<div id="liste_evenements" class="masonry">
					<!-- attention data-sort doit être un multiple de 10-->
					<?php
						if(count($evenements_organisme)>0){
							$sql = "SELECT * FROM ".TB."evenements AS spe, ".TB."rubriques AS spr WHERE spe.evenement_rubrique=spr.rubrique_id AND evenement_id IN (".implode(',',$evenements_organisme).") ORDER BY spe.evenement_datetime";
							$res = mysql_query($sql)or die(mysql_error());
						}
						else{
							$res=-1;
						}
						if($res!=-1){
							$multiplicateur = 1;
							while($row = mysql_fetch_array($res)){
								include('event.php');
							}
						}
						else{
					?>
							<div id="pasderesultat"><p><?php echo $aucun;?></p></div>
					<?php
						}
					?>
				</div>
			</div>

			<?php
				include('detail.php');
				include('bloc_inscription.php');
				include('bloc_validation.php');
				include('bloc_validation_externe.php');
				include('bloc_inscription_multiple.php');
				include('bloc_validation_multiple.php');
				include('bloc_validation_externe_multiple.php');
				include('bloc_login_soumission.php');
				//include('bloc_soumission.php');
			?>
		</section>
	</body>
</html>