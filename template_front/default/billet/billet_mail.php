<html>
	<head>
		<title>Sciences Po événement | <?php echo $this->session_name ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, scale=1.0">
		<meta name="format-detection" content="telephone=no">
	</head>

	<body>
		<table width="300px;" style="margin:auto;background:#F8F8F8;font-family:helvetica,sans;font-size:14px;border-radius: 8px;-moz-border-radius: 8px;-webkit-border-radius: 8px;" cellpadding="10" border="0" cellspacing="0">
			<tr>
				<td colspan="3" height="25" style="background:<?php echo $this->code_couleur; ?> url(<?php echo $this->absoluteBilletFolder.'images/bordure-header.png'; ?>) center top;"></td>
			</tr>
			<tr>
				<td colspan="3" style="background:<?php echo $this->code_couleur; ?>;"><img src="<?php echo $this->absoluteBilletFolder.'images/logo.png'; ?>" width="280" alt="Sciences Po"></td>
			</tr>
			<tr>
				<td colspan="3" style="background:<?php echo $this->code_couleur; ?>;">
					<h1 style="font-size:22px;text-transform:uppercase;margin:0;"><?php echo $this->session_name ?></h1>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="20" style="background:<?php echo $this->code_couleur; ?> url(<?php echo $this->absoluteBilletFolder.'images/bordure-top.png'; ?>) center top;"></td>
			</tr>
			<tr>
				<td colspan="3">
					<h3 style="font-size:18px;font-weight:normal;margin:0;">Le <?php echo $this->date?> à <?php echo $this->horaire ?></h3>
					<p style="margin:0;"><?php echo $this->lieu ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<p style="margin:0;">Organisateur : <?php echo $this->organisateur ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="3"><p><?php echo $this->prenom .' <strong style="text-transform:uppercase;">'.  $this->nom .'</strong>'; ?></p></td>
			</tr>
			<tr>
				<td colspan="3" height="20" style="background:<?php echo $this->code_couleur; ?> url(<?php echo $this->absoluteBilletFolder.'images/bordure-bottom.png'; ?>) center bottom;"></td>
			</tr>
			<tr>
				<td colspan="3" height="20" style="background:<?php echo $this->code_couleur; ?>"></td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:center;background:<?php echo $this->code_couleur; ?>;"><?php echo $QRcode?></td>
			</tr>
			<tr>
				<td colspan="3" style="text-align:center;font-size:20px;background:<?php echo $this->code_couleur; ?>;"><strong style="color:#000;text-decoration:none;"><?php echo $this->presentUniqueID() ?></strong></td>
			</tr>
			<tr>
				<td width="33%" height="100px" valign="top" style="text-align:center;background:<?php echo $this->code_couleur; ?>;">
					<img src="<?php echo $this->absoluteBilletFolder.'images/picto-retransmission.png'; ?>" width="68" height="68">
					<p><?php echo $this->acces ?></p>
				</td>
				<td width="33%" valign="top" style="text-align:center;background:<?php echo $this->code_couleur; ?>;">
					<img src="<?php echo $this->ecouteurs? $this->absoluteBilletFolder.'images/ecouteurs.png' : $this->absoluteBilletFolder.'images/ecouteurs-no.png'; ?>" width="68" height="68">
					<p><?php echo $this->ecouteurs? 'Écouteurs réservés' : ''; ?></p>
				</td>
				<td width="33%" valign="top" style="text-align:center;background:<?php echo $this->code_couleur; ?>;">
					<p style="text-transform:uppercase;font-weight:normal;font-size:66px;color:#FFF;font-family:courier,sans;"><?php echo $this->lang; ?></p>
				</td>
			</tr>
			<tr>
				<td colspan="3" height="50" style="background:<?php echo $this->code_couleur; ?> url(<?php echo $this->absoluteBilletFolder.'images/bordure-footer.png'; ?>) center bottom;"></td>
			</tr>
		</table>
	</body>
</html>