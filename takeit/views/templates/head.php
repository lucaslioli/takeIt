
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta charset="utf-8">
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/menu.css">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/footer.css">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/<?= (isset($css) ? $css : "")?>">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/<?= (isset($css2) ? $css2 : "")?>">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="<?= base_url();?>assets/css/bootstrap.css.map">
		<!-- FAVICON -->
		<link rel="icon" type="image/png" href="<?= base_url();?>assets/img/favicon.png">
		<?php if(isset($dataTable)): ?>
			<link rel="stylesheet" href="<?= base_url();?>assets/css/jquery.dataTables.min.css">
			<link rel="stylesheet" href="<?= base_url();?>assets/css/buttons.dataTables.min.css">
			<link rel="stylesheet" href="<?= base_url();?>assets/css/dataTables.bootstrap.css">
			<link rel="stylesheet" href="<?= base_url();?>assets/css/dataTables.fontAwesome.css">

		<?php endif;?>
		<title>
			<?= (isset($titulo) ? $titulo . " - takeIt" : "takeIt")?>
		</title>
	</head>
	<body>