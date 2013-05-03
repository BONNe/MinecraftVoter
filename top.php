<?php
if( !headers_sent() )
{
?>
	<!DOCTYPE HTML>
	<html lang="en-US">
	<head>
		<meta charset="UTF-8">
		<title>Top Balsotāji</title>
		<link rel="stylesheet" type="text/css" href="http://static.suncore.lv/css/bootstrap.min.css" media="screen" />
	</head>
	<body>
<?php
}
else
{
?>
	<link rel="stylesheet" type="text/css" href="http://static.suncore.lv/css/bootstrap.min.css" media="screen" />
<?php
}
?>
<style type="text/css">
body{
padding:10px;
}
</style>
<div class="clearfix"></div>
<table class="table" style="margin-top:20px;">
<tr><th>Top balsotāji</th> <th>Niks</th><th>Balsu skaits</th></tr>
<?php
$voters = mysql::get_all('SELECT * FROM `'.$config['mysql.table'].'_top` ORDER BY `vote_count` DESC LIMIT 0,100');
$i = 1;
foreach( $voters as $voter )
{
	echo '<tr><td>'.$i.'</td><td><img src="https://minotar.net/avatar/'.$voter->username.'/20.png"> '.$voter->username.'</td><td>'.$voter->vote_count.'</td></tr>';
	$i++;
}
?>
</table>
<?php
if( !headers_sent() )
{
?>	
	</body>
	</html>
<?php
}
?>