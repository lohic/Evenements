
<div id="header">
	<a href="list.php"><img src="img/logo_full.png" alt="Sciences-Po"/></a>
	<h1><a href="list.php">/ événements</a></h1>
	<a href="index.php?error=1" class="deconnecter">se déconnecter</a>
	<form id="select_group" action="" method="post">
    	<label for="id_actual_group">Vous utilisez le groupe : </label>        
        <?php echo createSelect($core->user_info->groups,	'id_actual_group', 	$_SESSION['id_actual_group'], 	"onchange=\"$('#select_group').submit();\"", false ); ?>    	
    </form>
    <p style="float:right;margin-top:-11px;margin-right:30px;">Logué en tant que : <?php echo $_SESSION['prenom']." ". $_SESSION['nom'];?></p>
	
</div>


