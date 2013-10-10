<?php
// security
include('cookie.php');

// connection to data base
include('connect.php');

// functions library
include('functions.php');

// feedcreator library
include('feedcreator.class.php');

include('variables.php');

$sql ="SELECT * FROM sp_evenements WHERE evenement_id = '".$_GET['id']."'";
$res = mysql_query($sql) or die(mysql_error());
$row = mysql_fetch_array($res);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>CMS</title>
	<link href="css/layout.css" rel="stylesheet" type="text/css" />
	<link href="jquery-ui/css/ui-lightness/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
	<script src="sample.js" type="text/javascript"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.5.custom.min.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery.ui.datepicker-fr.js"></script>
	<script type="text/javascript" src="tiny_mce/jquery.tinymce.js"></script>
	<link href="css/sample.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<div id="page">
	    <?php include("top.php"); ?>
    <div id="menu">
		<?php include("menu.php"); ?>
    </div>
    <div id="content">
			<p>
				Statut : 
				<?php if($row['evenement_statut']==1){echo "Brouillon";} ?>
				<?php if($row['evenement_statut']==2){echo "Caché";} ?>
				<?php if($row['evenement_statut']==3){echo "Publié";} ?>
				<?php if($row['evenement_statut']==4){echo "Soummission";} ?>	
			</p>
			<div class="colonne_gauche">
				<p>Nom de l'événement : <?php echo $row['evenement_titre']; ?></p>
				<p>Intro : <?php echo strip_tags($row['evenement_texte']); ?></p>
				<p>Description : <?php echo strip_tags($row['evenement_resume']); ?></p>
			</div>

			<div class="colonne_droite">
				<p>Nom de l'évenement (EN) : <?php echo $row['evenement_titre_en']; ?></p>
				<p>Intro (EN) : <?php echo strip_tags($row['evenement_texte_en']); ?></p>
				<p>Description (EN) : <?php echo strip_tags($row['evenement_resume_en']); ?></p>
			</div>

			<hr/>

		
			<div class="colonne_gauche">
				<p>organisateur : <?php echo $row['evenement_organisateur']; ?></p>
				<p>co-organisateur : <?php echo $row['evenement_coorganisateur']; ?></p>
			</div>

			<div class="colonne_droite">
				<p>organisateur (en) : <?php echo $row['evenement_organisateur_en']; ?></p>
				<p>co-organisateur (en) : <?php echo $row['evenement_coorganisateur_en']; ?></p>
			</div>



			<hr/>
			<p>
				Rubrique : 
					<?php
						$sqlrubriques ="SELECT * FROM sp_rubriques ORDER BY rubrique_titre ASC";
						$resrubriques = mysql_query($sqlrubriques) or die(mysql_error());
						while($rowrubrique = mysql_fetch_array($resrubriques)){
							if( $row['evenement_rubrique'] == $rowrubrique['rubrique_id'] ) {
								echo utf8_encode($rowrubrique['rubrique_titre']);
							} 							
						}
					?>
			</p>
			<hr/>
			<img src=<?php echo '"upload/photos/evenement_'.$_GET['id'].'/'.$row['evenement_image'].'"';?> alt=""/>
			<hr/><br/>

			<div class="parametres_inscriptions">
				<h3>Inscription :</h3>

				<div class="colonne_gauche">
					&nbsp;
				</div>

				<div class="colonne_droite">
					<p><strong>Adresse de l'événement qui sera inscrit sur le ticket envoyé dans le mail : </strong></p>
					<p><?php echo $row['evenement_adresse1']; ?></p>
					<p><?php echo $row['evenement_adresse2']; ?></p>
					<p><?php echo $row['evenement_adresse3']; ?></p>
				</div>

				<hr/>
			</div>
		</form>
		
		<hr/>
		<hr/>
		
		<p>Liste des tables rondes</p>
      	
		<?php
			$sql2 ="SELECT * FROM sp_sessions WHERE evenement_id = '".$_GET['id']."'";
			$res2 = mysql_query($sql2) or die(mysql_error());
			while($row2 = mysql_fetch_array($res2)){
				$jour_debut_session = date("d/m/Y H:i",$row2['session_debut']);
				$jour_fin_session = date("d/m/Y H:i",$row2['session_fin']);
				echo '<div class="listItemRubrique1">';
				echo '<div class="liens"><a href="#" id="lien_session_'.$row2['session_id'].'" class="lien_session">Voir</a> / ';?>
			
				<?php
				echo '</div>';
			?>
				
			<?php
				echo '<div class="infos" id="session_id_'.$row2['session_id'].'">Du '.$jour_debut_session.' au '.$jour_fin_session.'</div>';
				echo '</div>';
			?>
				<div id="bloc_modif_<?php echo $row2['session_id'];?>" class="bloc_modif">
					
				</div>
		<?php	
			}
		?>
	</div>
</div>
<script type="text/javascript">
	$(window).load(function(){
		var sauv_id="";
				
		$("a.lien_session").click(function(e){
			e.preventDefault();
			$("div.bloc_modif").css("display","none");
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			
			var tableau_id=$(this).attr("id").split('_');
			var identifiant = "bloc_modif_"+tableau_id[2];
			document.getElementById(identifiant).style.display="block";
			
			$.post("voirSessionAJAX.php", { id: tableau_id[2] },
			function(data){
				document.getElementById(identifiant).innerHTML=data;
			});
			
			sauv_id = identifiant;
		});
	});
</script>
</body>
</html>
