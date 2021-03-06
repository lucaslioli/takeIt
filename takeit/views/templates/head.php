<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta charset="utf-8">
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/menu.css">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/footer.css">
		<? if(isset($css)): ?>
			<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/<?=$css?>">
		<? endif;
		if(isset($css2)): ?>
			<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/<?= $css2?>">
		<? endif; ?>
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/bootstrap.css.map">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/head.css">
		<!-- FAVICON -->
		<link rel="icon" type="image/png" href="<?= base_url();?>assets/img/favicon.png">
		<title>
			<?= (isset($titulo) ? $titulo . " - takeIt" : "takeIt")?>
		</title>
	</head>
	<body>