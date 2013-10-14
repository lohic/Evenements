<html>
	<head>
		<title>FRONT OFFICE PAR DEFAUT</title>
		<link rel="stylesheet" href="<?php echo $template_css ?>" type="text/css" media="screen" />
		
	</head>

	<body>
		
		<h1>FRONT OFFICE PAR DEFAUT</h1>
		<p><?php echo !empty($_GET['test']) ? $_GET['test'] : ''; ?></p>

	</body>

</html>