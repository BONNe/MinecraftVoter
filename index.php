<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Vote 4 us</title>
	
	<?php
	if( isset( $_GET['target'] ) )
	{
		echo '<link rel="stylesheet" type="text/css" href="http://static.suncore.lv/css/bootstrap.min.css" media="screen" />';
	}
	?>
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	
	<?php
	if( isset( $_GET['target'] ) )
	{
		echo '<script type="text/javascript" src="http://static.suncore.lv/js/bootstrap.min.js"></script>';
	}
	?>
	
</head>
<body>
	<?php
		include 'voter.php';
		include 'top.php';
	?>
</body>
</html>