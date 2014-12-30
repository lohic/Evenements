<div id="admin_bar">
	<p>Vous êtes connecté | <a href="admin/" target="_blank">Accéder à l'administration</a><?php if(MAINTENANCE){echo ' | Maintenance activée';} ?></p>
</div>
<style>
	#admin_bar{
		height:30px;
		background: #CB021A;
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		z-index: 30
	}

	#admin_bar p{
		margin : 5px 40px;
		text-align: center;
	}

	#admin_bar p a{
		color:#FFF;
	}
	
	#admin_bar p a:hover{
		text-decoration: underline;
	}
	
	body>header{
		top: 30px;
	}

</style>