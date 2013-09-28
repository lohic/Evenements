<?php
session_start();
// security
include('cookie.php');

// connection to data base
include('connect.php');

// functions library
include('functions.php');

include_once('../vars/constantes_vars.php');
include_once('../vars/statics_vars.php');

include_once('../classe/classe_core_event.php');
include_once('../classe/classe_keyword.php');
include_once('../classe/fonctions.php');

$core = new core();

// if editing...

if( isset($_GET['deleterubrique']) ){
	// delete rubrique
	$sql="DELETE FROM sp_rubriques WHERE rubrique_id = '".$_GET['deleterubrique']."'";
	mysql_query($sql) or die(mysql_error());   
	
	$sql="DELETE FROM sp_rel_rubrique_groupe WHERE rubrique_id = '".$_GET['deleterubrique']."'";
	mysql_query($sql) or die(mysql_error());
}

if( isset($_GET['deletebatiment']) ){
	// delete batiment
	$sql="DELETE FROM sp_codes_batiments WHERE code_batiment_id = '".$_GET['deletebatiment']."'";
	mysql_query($sql) or die(mysql_error());
}

if( isset($_GET['deletelieu']) ){
	// delete lieu 
	$sql="DELETE FROM sp_lieux WHERE lieu_id = '".$_GET['deletelieu']."'";
	mysql_query($sql) or die(mysql_error());
}

if( isset($_GET['keyword_id']) ){
	// delete mot clé
	$keyword = new keyword();
	$keyword->updater($_GET['update'],$_GET['keyword_id']);
}

if( isset($_POST['rubrique_id'])){
	if($_POST['type_saisie']=="modification"){
		// query
		$sql ="UPDATE sp_rubriques SET
					rubrique_titre = '".addslashes(utf8_decode($_POST["rubrique_titre"]))."',
					rubrique_titre_en = '".addslashes(utf8_decode($_POST["rubrique_titre_en"]))."',
					rubrique_couleur = '".$_POST["rubrique_couleur"]."',
					rubrique_editeur_id = '".$_SESSION['id']."',
					rubrique_editeur_ip =  '".$_SERVER["REMOTE_ADDR"]."',
					rubrique_groupe_id = '".$_POST["rubrique_groupe_id"]."'
				WHERE rubrique_id = '".$_POST['rubrique_id']."'";
		mysql_query($sql) or die(mysql_error());
		
		$sql="DELETE FROM sp_rel_rubrique_groupe WHERE rubrique_id = '".$_POST['rubrique_id']."'";
		mysql_query($sql) or die(mysql_error());

		for ($i = 0; $i < count($_POST['groupes']); $i++) {
			$sqlinsert ="INSERT INTO sp_rel_rubrique_groupe VALUES ('', '".$_POST['rubrique_id']."', '".$_POST['groupes'][$i]."')";
			mysql_query($sqlinsert) or die(mysql_error());
		}

		$codecouleur = explode("#", $_POST["rubrique_couleur"]);
		
		triangle($codecouleur[1]);
		
		// reedirect
		header("Location:rubriques.php?r=".rand());
	}
	else{
		// query
		$sqlinsert ="INSERT INTO sp_rubriques VALUES ('', '".addslashes(utf8_decode($_POST["rubrique_titre"]))."', '".addslashes(utf8_decode($_POST["rubrique_titre_en"]))."', '".$_POST["rubrique_couleur"]."', '', '".$_SESSION['id']."', '".$_SERVER["REMOTE_ADDR"]."','".$_POST["rubrique_groupe_id"]."')";
		mysql_query($sqlinsert) or die(mysql_error());
		
		$lastIdInsert = mysql_insert_id();
		
		for ($i = 0; $i < count($_POST['groupes']); $i++) {
			$sqlinsert ="INSERT INTO sp_rel_rubrique_groupe VALUES ('', '".$lastIdInsert."', '".$_POST['groupes'][$i]."')";
			mysql_query($sqlinsert) or die(mysql_error());
		}

		$codecouleur = explode("#", $_POST["rubrique_couleur"]);
		
		triangle($codecouleur[1]);
		
		
		// reedirect
		header("Location:rubriques.php?menu_actif=rubriques");
	}
		
}

if(isset($_POST['keyword_id'])){
	$keyword = new keyword();
	if($_POST['update']=="update"){	
		$keyword->updater($_POST, $_POST['keyword_id']);
		header("Location:rubriques.php?menu_actif=rubriques");
	}
	else{
		$keyword->updater($_POST);
		header("Location:rubriques.php?menu_actif=rubriques");
	}	
}


if( isset($_POST['lieu_id'])){
	if($_POST['type_saisie']=="modification"){
		// query
		$sql ="UPDATE sp_lieux SET
					lieu_nom = '".addslashes(utf8_decode($_POST["lieu_nom"]))."',
					lieu_editeur_id = '".$_SESSION['id']."',
					lieu_editeur_ip =  '".$_SERVER["REMOTE_ADDR"]."'
				WHERE lieu_id = '".$_POST['lieu_id']."'";
		mysql_query($sql) or die(mysql_error());

		// reedirect
		header("Location:rubriques.php?menu_actif=rubriques");
	}
	else{
		// query
		$sqlinsert ="INSERT INTO sp_lieux VALUES ('', '".addslashes(utf8_decode($_POST["lieu_nom"]))."', '', '".$_SESSION['id']."', '".$_SERVER["REMOTE_ADDR"]."')";
		mysql_query($sqlinsert) or die(mysql_error());
		// reedirect
		header("Location:rubriques.php?menu_actif=rubriques");
	}		
}


if( isset($_POST['code_batiment_id'])){
	if($_POST['type_saisie']=="modification"){
		// query
		$sql ="UPDATE sp_codes_batiments SET
					code_batiment_nom = '".addslashes(utf8_decode($_POST["code_batiment_nom"]))."',
					code_batiment_adresse = '".addslashes(utf8_decode($_POST["code_batiment_adresse"]))."',
					code_batiment_editeur_id = '".$_SESSION['id']."',
				    code_batiment_editeur_ip =  '".$_SERVER["REMOTE_ADDR"]."'
				WHERE code_batiment_id = '".$_POST['code_batiment_id']."'";
		mysql_query($sql) or die(mysql_error());

		// reedirect
		header("Location:rubriques.php?menu_actif=rubriques");
	}
	else{
		// query
		$sqlinsert ="INSERT INTO sp_codes_batiments VALUES ('', '".addslashes(utf8_decode($_POST["code_batiment_nom"]))."', '".addslashes(utf8_decode($_POST["code_batiment_adresse"]))."', '', '".$_SESSION['id']."', '".$_SERVER["REMOTE_ADDR"]."')";
		mysql_query($sqlinsert) or die(mysql_error());
		// reedirect
		header("Location:rubriques.php?menu_actif=rubriques");
	}		
}

$sqlGetOrganisme ="SELECT organisme_id FROM sp_groupes as spg, sp_organismes as spo WHERE spg.groupe_organisme_id=spo.organisme_id AND groupe_id='".$_SESSION['id_actual_group']."'";
$resGetOrganisme= mysql_query($sqlGetOrganisme) or die(mysql_error());
$rowGetOrganisme = mysql_fetch_array($resGetOrganisme);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Sciences Po | Événements : administration</title>
	<link href="css/layout.css" rel="stylesheet" type="text/css" />
	<link href="css/couleur_<?php echo $rowGetOrganisme['organisme_id'];?>.css" rel="stylesheet" type="text/css" />
	<link href="jquery-ui/css/ui-lightness/jquery-ui-1.8.5.custom.css" rel="stylesheet" type="text/css" />
	
	<script language="JavaScript" src="tools.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery-1.4.2.min.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery-ui-1.8.5.custom.min.js"></script>
	<script type="text/javascript" src="jquery-ui/js/jquery.ui.datepicker-fr.js"></script>
</head>

<body>
<div id="page">
	    <?php include("top.php"); ?>
    <div id="menu">
		<?php include("menu.php"); ?>
    </div>
    <div id="content">
		<?php  
			if($core->isAdmin && $core->userLevel<=3){ 
		?>
	    		<h3>Liste des rubriques</h3>

				<p><a href="#" class="buttonenregistrer" id="creer_rubrique">Nouvelle rubrique</a></p>
				<div id="bloc_creation" class="bloc_modif">

				</div>
				<p>&nbsp;</p>
			 	<?php
                    
					if($core->isAdmin && $core->userLevel==1){
						$sql = "SELECT * FROM sp_rubriques";
					}
					else{
						if($core->isAdmin && $core->userLevel<=3){
							$idGroups= array();
							foreach($core->user_info->groups as $cle => $valeur) 
							{
								$idGroups[]=$cle;
							}
							$idGroups = implode(',',$idGroups); 
							$sql = "SELECT * FROM sp_rubriques WHERE rubrique_groupe_id IN ($idGroups)"; 
						}
					}

			 		$res = mysql_query($sql)or die(mysql_error());
					$iteration = 1;

					while($row = mysql_fetch_array($res)){
						if($iteration%2==1){
					?>
							<div class="listItemRubrique1">
					<?php
						}
						else{
					?>
							<div class="listItemRubrique2">
					<?php
						}
						$iteration++;

					?>
								<div class="infos_large" id="titre_rubrique_<?php echo $row['rubrique_id'];?>"><p><?php echo utf8_encode($row['rubrique_titre']);?></p></div>
								<div style="display:none" id="titre_en_rubrique_<?php echo $row['rubrique_id'];?>"><p><?php echo utf8_encode($row['rubrique_titre_en']);?></p></div>

								<div class="liens modif">
									<a href="#" id="lien_rubrique_<?php echo $row['rubrique_id'];?>" class="lien_rubrique" title="modifier"><img src="img/pencil.png" alt="modifier"/></a><a href="rubriques.php?deleterubrique=<?php echo $row['rubrique_id'];?>&amp;menu_actif=rubriques" onclick="confirmar('rubriques.php?deleterubrique=<?php echo $row['rubrique_id'];?>&amp;menu_actif=rubriques', 'Etes-vous sûr de vouloir supprimer cette rubrique? ')" title="supprimer"><img src="img/delete.png" alt="supprimer"/></a>								
									<div class="couleur" style="background:<?php echo $row['rubrique_couleur'];?>" id="couleur"><p id="couleur_rubrique_<?php echo $row['rubrique_id'];?>" style="display:none"><?php echo $row['rubrique_couleur'];?></p></div>
								</div>


								<div id="bloc_modif_<?php echo $row['rubrique_id'];?>" class="bloc_modif">

								</div>

							</div>
					<?php
					}
				?>  
		<?php
			}
		?>
	
		<div style="position:relative; margin-top:30px; float:left;">
			<h3>Liste des mots-clés</h3>
			<?php
				if($core->isAdmin && $core->userLevel<=3){
			?>
					<p><a href="#" class="buttonenregistrer" id="creer_keyword">Nouveau Mot clé</a></p>
					<div id="bloc_creation_keyword" class="bloc_modif">

					</div>	
			<?php
				}
			?>
			
			<p>&nbsp;</p>
		 	<?php
	
		 		$sql = "SELECT * FROM sp_keywords";
		 		$res = mysql_query($sql)or die(mysql_error());
				$iteration = 1;
	
				while($row = mysql_fetch_array($res)){
					if($iteration%2==1){
				?>
						<div class="listItemRubrique1">
				<?php
					}
					else{
				?>
						<div class="listItemRubrique2">
				<?php
					}
					$iteration++;
			
				?>
							<div class="infos_large" id="keyword_nom_<?php echo $row['keyword_id'];?>"><p><?php echo $row['keyword_nom'];?></p></div>
							<div class="liens modif">
								<?php
									if($core->isAdmin && $core->userLevel<=3){
								?>
										<a href="#" id="lien_keyword_<?php echo $row['keyword_id'];?>" class="lien_keyword" title="modifier"><img src="img/pencil.png" alt="modifier"/></a><a href="rubriques.php?id=<?php echo $row['keyword_id'];?>&amp;menu_actif=rubriques" onclick="confirmar('rubriques.php?update=delete&amp;keyword_id<?php echo $row['keyword_id'];?>&amp;menu_actif=rubriques', 'Etes-vous sûr de vouloir supprimer ce mot-clé? ')" title="supprimer"><img src="img/delete.png" alt="supprimer"/></a>	
								<?php
									}
								?>
																
							</div>
						
							<div id="bloc_modif_keyword_<?php echo $row['keyword_id'];?>" class="bloc_modif">

							</div>
						</div>
				<?php
				}
			?>
		</div>		
		
		<div style="position:relative; margin-top:30px; float:left;">
			<h3>Liste des bâtiments</h3>
	        		<?php
				if($core->isAdmin && $core->userLevel<=1){
			?>
					<p><a href="#" class="buttonenregistrer" id="creer_batiment">Nouveau Bâtiment</a></p>
					<div id="bloc_creation_batiment" class="bloc_modif">

					</div>	
			<?php
				}
			?>
			
			<p>&nbsp;</p>
		 	<?php
		 		$sql = "SELECT * FROM sp_codes_batiments";
		 		$res = mysql_query($sql)or die(mysql_error());
				$iteration = 1;
	
				while($row = mysql_fetch_array($res)){
					if($iteration%2==1){
				?>
						<div class="listItemRubrique1">
				<?php
					}
					else{
				?>
						<div class="listItemRubrique2">
				<?php
					}
					$iteration++;
			
				?>
							<div class="infos_large"><p id="code_batiment_nom_<?php echo $row['code_batiment_id'];?>" style="margin-right:25px;"><?php echo utf8_encode($row['code_batiment_nom']);?></p><p style="font-size:1em; padding-top:10px;"><?php echo utf8_encode($row['code_batiment_adresse']);?></p></div>
							<div class="infos_large" id="code_batiment_adresse_<?php echo $row['code_batiment_id'];?>" style="display:none;"><p><?php echo utf8_encode($row['code_batiment_adresse']);?></p></div>
							<div class="liens modif">
							<?php
								if($core->isAdmin && $core->userLevel<=1){
							?>
									<a href="#" id="lien_code_batiment_<?php echo $row['code_batiment_id'];?>" class="lien_code_batiment" title="modifier"><img src="img/pencil.png" alt="modifier"/></a><a href="rubriques.php?id=<?php echo $row['code_batiment_id'];?>&amp;menu_actif=rubriques" onclick="confirmar('rubriques.php?deletebatiment=<?php echo $row['code_batiment_id'];?>&amp;menu_actif=rubriques', 'Etes-vous sûr de vouloir supprimer ce code bâtiment? ')" title="supprimer"><img src="img/delete.png" alt="supprimer"/></a>	
							<?php
								}
							?>							
							</div>
						
						
							<div id="bloc_modif_batiment_<?php echo $row['code_batiment_id'];?>" class="bloc_modif">

							</div>
						
						</div>
				<?php
				}
			?>
		</div>
		
		<div style="position:relative; margin-top:30px; float:left;">
			<h3>Liste des lieux</h3>
	        
			<?php
				if($core->isAdmin && $core->userLevel<=1){
			?>
					<p><a href="#" class="buttonenregistrer" id="creer_lieu">Nouveau Lieu</a></p>
					<div id="bloc_creation_lieu" class="bloc_modif">

					</div>	
			<?php
				}
			?>
			
			<p>&nbsp;</p>
		 	<?php
		 		$sql = "SELECT * FROM sp_lieux";
		 		$res = mysql_query($sql)or die(mysql_error());
				$iteration = 1;
	
				while($row = mysql_fetch_array($res)){
					if($iteration%2==1){
				?>
						<div class="listItemRubrique1">
				<?php
					}
					else{
				?>
						<div class="listItemRubrique2">
				<?php
					}
					$iteration++;
			
				?>
							<div class="infos_large" id="lieu_nom_<?php echo $row['lieu_id'];?>"><p><?php echo utf8_encode($row['lieu_nom']);?></p></div>
							<div class="liens modif">
								<?php
									if($core->isAdmin && $core->userLevel<=1){
								?>
										<a href="#" id="lien_lieu_<?php echo $row['lieu_id'];?>" class="lien_lieu" title="modifier"><img src="img/pencil.png" alt="modifier"/></a><a href="rubriques.php?id=<?php echo $row['lieu_id'];?>&amp;menu_actif=rubriques" onclick="confirmar('rubriques.php?deletelieu=<?php echo $row['lieu_id'];?>&amp;menu_actif=rubriques', 'Etes-vous sûr de vouloir supprimer ce lieu? ')" title="supprimer"><img src="img/delete.png" alt="supprimer"/></a>	
								<?php
									}
								?>
																
							</div>
						
						
							<div id="bloc_modif_lieu_<?php echo $row['lieu_id'];?>" class="bloc_modif">

							</div>
						
						</div>
				<?php
				}
			?>
		</div>
	</div>	
</div>
<script type="text/javascript">
		
	$(window).load(function(){
		
		var sauv_id="";
		
		$("a.lien_rubrique").click(function(e){
			e.preventDefault();
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			
			var tableau_id=$(this).attr("id").split('_');
			var identifiant = "bloc_modif_"+tableau_id[2]; 
			$('.bloc_modif').removeClass('open');
			$('#bloc_modif_'+tableau_id[2]).addClass('open');
		 	$('.bloc_modif').not('.open').slideUp();
			
			var identifiant_rubrique = "titre_rubrique_"+tableau_id[2];
			var identifiant_rubrique_en = "titre_en_rubrique_"+tableau_id[2];
			
			var identifiant_couleur = "couleur_rubrique_"+tableau_id[2];
			
			var lacouleur = document.getElementById(identifiant_couleur).innerHTML;
			
			if($('#bloc_modif_'+tableau_id[2]).css("display")!="block"){
				$.post("modifRubriqueAJAX.php", { titre: document.getElementById(identifiant_rubrique).innerHTML, titre_en: document.getElementById(identifiant_rubrique_en).innerHTML, id: tableau_id[2], type:"modification", couleur:lacouleur },
				function(data){
					document.getElementById(identifiant).innerHTML=data;
					$('#bloc_modif_'+tableau_id[2]).slideToggle();
				});
			} 
			else{
				$('#bloc_modif_'+tableau_id[2]).slideToggle(); 
			}
						
			
			
			
			sauv_id = identifiant;
		});
		
		$("a#creer_rubrique").click(function(e){
			e.preventDefault();
			$("div.bloc_modif").css("display","none");
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			var identifiant = "bloc_creation";
			document.getElementById('bloc_creation').style.display="block";
						
			$.post("modifRubriqueAJAX.php", { titre: "", titre_en: "", id: "", type:"creation" },
			function(data){
				document.getElementById(identifiant).innerHTML=data;
			});
			
			sauv_id = identifiant;
		});
		
		$("a.lien_code_batiment").click(function(e){
			e.preventDefault();
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			
			var tableau_id=$(this).attr("id").split('_');
			var identifiant = "bloc_modif_batiment_"+tableau_id[3]; 
			$('.bloc_modif').removeClass('open');
			$('#bloc_modif_batiment_'+tableau_id[3]).addClass('open');
		 	$('.bloc_modif').not('.open').slideUp();
			
			var nom_batiment = "code_batiment_nom_"+tableau_id[3];
			var adresse_batiment = "code_batiment_adresse_"+tableau_id[3];
			
			if($('#bloc_modif_batiment_'+tableau_id[3]).css("display")!="block"){
				$.post("modifBatimentAJAX.php", { nom_batiment: document.getElementById(nom_batiment).innerHTML, adresse_batiment: document.getElementById(adresse_batiment).innerHTML, id: tableau_id[3], type:"modification" },
				function(data){
					document.getElementById(identifiant).innerHTML=data;
					$('#bloc_modif_batiment_'+tableau_id[3]).slideToggle();
				});
			} 
			else{
				$('#bloc_modif_batiment_'+tableau_id[3]).slideToggle(); 
			}   					
			
			
			
			sauv_id = identifiant;
		});
		
		$("a#creer_batiment").click(function(e){
			e.preventDefault();
			$("div.bloc_modif").css("display","none");
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			var identifiant = "bloc_creation_batiment";
			document.getElementById('bloc_creation_batiment').style.display="block";
						
			$.post("modifBatimentAJAX.php", { nom_batiment: "", adresse_batiment: "", id: "", type:"creation" },
			function(data){
				document.getElementById(identifiant).innerHTML=data;
			});
			
			sauv_id = identifiant;
		});
		
		
		
		$("a.lien_lieu").click(function(e){
			e.preventDefault();
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			
		   	var tableau_id=$(this).attr("id").split('_');
			var identifiant = "bloc_modif_lieu_"+tableau_id[2]; 
			$('.bloc_modif').removeClass('open');
			$('#bloc_modif_lieu_'+tableau_id[2]).addClass('open');
		 	$('.bloc_modif').not('.open').slideUp();
			
			var nom_lieu = "lieu_nom_"+tableau_id[2];
			
			if($('#bloc_modif_lieu_'+tableau_id[2]).css("display")!="block"){
				$.post("modifLieuAJAX.php", { nom_lieu: document.getElementById(nom_lieu).innerHTML, id: tableau_id[2], type:"modification" },
				function(data){
					document.getElementById(identifiant).innerHTML=data;
					$('#bloc_modif_lieu_'+tableau_id[2]).slideToggle();
				});
			} 
			else{
				$('#bloc_modif_lieu_'+tableau_id[2]).slideToggle(); 
			}						
			
			
			
			sauv_id = identifiant;
		});
		
		$("a#creer_lieu").click(function(e){
			e.preventDefault();
			$("div.bloc_modif").css("display","none");
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			var identifiant = "bloc_creation_lieu";
			document.getElementById('bloc_creation_lieu').style.display="block";
						
			$.post("modifLieuAJAX.php", { nom_lieu: "", id: "", type:"creation" },
			function(data){
				document.getElementById(identifiant).innerHTML=data;
			});
			
			sauv_id = identifiant;
		});


		$("a.lien_keyword").click(function(e){
			e.preventDefault();
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			
		   	var tableau_id=$(this).attr("id").split('_');
			var identifiant = "bloc_modif_keyword_"+tableau_id[2]; 
			$('.bloc_modif').removeClass('open');
			$('#bloc_modif_keyword_'+tableau_id[2]).addClass('open');
		 	$('.bloc_modif').not('.open').slideUp();
			
			var nom_keyword = "keyword_nom_"+tableau_id[2];
			
			if($('#bloc_modif_keyword_'+tableau_id[2]).css("display")!="block"){
				$.post("modifKeywordAJAX.php", { nom_keyword: document.getElementById(nom_keyword).innerHTML, id: tableau_id[2], update:"update" },
				function(data){
					document.getElementById(identifiant).innerHTML=data;
					$('#bloc_modif_keyword_'+tableau_id[2]).slideToggle();
				});
			} 
			else{
				$('#bloc_modif_keyword_'+tableau_id[2]).slideToggle(); 
			}						
			sauv_id = identifiant;
		});
		
		$("a#creer_keyword").click(function(e){
			e.preventDefault();
			$("div.bloc_modif").css("display","none");
			
			if(sauv_id!=""){
				document.getElementById(sauv_id).innerHTML="";
			}
			var identifiant = "bloc_creation_keyword";
			document.getElementById('bloc_creation_keyword').style.display="block";
						
			$.post("modifKeywordAJAX.php", { nom_keyword: "", id: "", update:"create" },
			function(data){
				document.getElementById(identifiant).innerHTML=data;
			});
			
			sauv_id = identifiant;
		});

		var actif = getParamValue('menu_actif');
		document.getElementById(actif).className = "actif";
		
	});
</script>

</body>
</html>
