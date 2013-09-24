<html>
	<head>
		<title>Sciences Po événement | <?php echo $this->session_name ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, scale=1.0">
	</head>

	<body>
		<table width="300px;" style="margin:auto;background:#DDD;font-family:helvetica,sans;font-size:14px;" cellspacing="20">
			<tr>
				<td>Sciences Po</td>
			</tr>
			<tr>
				<td><h1 style="font-size:22px;text-transform:uppercase;margin:0;"><?php echo $this->session_name ?></h1></td>
			</tr>
			<tr>
				<td>
					<h3 style="font-size:18px;font-weight:normal;margin:0;">Le <?php echo $this->date?> à <?php echo $this->horaire ?></h3>
					<p style="margin:0;"><?php echo $this->lieu ?></p>
				</td>
			</tr>
			<tr>
				<td>
					<p style="margin:0;">Organisateur : <?php echo $this->organisateur ?></p>
				</td>
			</tr>
			<tr>
				<td><p><?php echo $this->prenom .' <strong style="text-transform:uppercase;">'.  $this->nom .'</strong>'; ?></p></td>
			</tr>
			<tr>
				<td><p><?php echo $this->acces ?></p></td>
			</tr>
			<tr>
				<td><p><?php echo $this->ecouteurs? 'Écouteurs réservés' : ''; ?></p></td>
			</tr>
			<tr>
				<td style="text-align:center"><?php echo $QRcode?></td>
			</tr>
			<tr>
				<td style="text-align:center"><strong><?php echo $this->presentUniqueID() ?></strong></td>
			</tr>
		</table>
	</body>
</html>