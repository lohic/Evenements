<?php
include_once('../vars/config.php');
include('cookie.php');

// connection to data base
include('connect.php');

include('variables.php');

$sql3 ="SELECT * FROM sp_sessions WHERE session_id = '".$_POST['id']."'";
$res3 = mysql_query($sql3) or die(mysql_error());
$row3 = mysql_fetch_array($res3);

?>

	<p class="none">Langue de la conférence :
			<?php
				foreach($langues_evenement as $cle => $valeur){
					if($row3['session_langue']==$valeur){
						echo $cle;
					}
				}
			?>
	</p>
	
	<p class="none">lieu de l'événement : <?php echo $row3['session_lieu']; ?></p>
	<p>code du bâtiment : 
		<?php 
			foreach($batiments as $cle => $valeur){
				if($row3['session_code_batiment']==$cle){
					echo $valeur;
				}
			}
		?>
	</p>

	<p  class="none">Texte du lien : <?php echo $row3['session_texte_lien']; ?></p>
	<p>Lien : <?php echo $row3['session_lien']; ?></p>

	<p class="none">Type d'inscription : <?php echo $row3['session_type_inscription']; ?></p>

	<p class="none">Inscriptions ouvertes : <?php if($row3['session_statut_inscription']==1){echo "Oui";}else{echo "Non";}?></p>

	<p>Interne : <?php echo $row3['session_places_internes_prises']."/".$row3['session_places_internes_totales']; ?></p>
	<p class="externe">Externe : <?php echo $row3['session_places_externes_prises']."/".$row3['session_places_externes_totales']; ?></p>

	<p class="none">Visioconférence ouverte : <?php if($row3['session_statut_visio']==1){echo "Oui";}else{echo "Non";}?></p>
	
	<p>Interne : <?php echo $row3['session_places_internes_prises_visio']."/".$row3['session_places_internes_totales_visio']; ?></p>
	<p class="externe">Externe : <?php echo $row3['session_places_externes_prises_visio']."/".$row3['session_places_externes_totales_visio']; ?></p>

	<p class="none"><strong>Adresse de l'événement qui sera inscrit sur le ticket envoyé dans le mail : </strong></p>
	<p class="none"><?php echo $row3['session_adresse1']; ?></p>
	<p class="none"><?php echo $row3['session_adresse2']; ?></p>

	
