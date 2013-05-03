<?php
/**********************************************************************************
| Software Name:              SUNCORE VOTE SCRIPT                                 |
| Software by:                Janis Blaus (Glorificus @ http://www.suncore.lv/)   |
| Copyright 2010-2013 by:     Suncore Ltd.                                        |
| Support, News, Updates at:  http://www.suncore.lv/                              |
| Version:                    3.6                                                 |
| ------------------------------------------------------------------------------- |
| Visit www.suncore.lv for details and information about this software.           |
**********************************************************************************/

/*
	[!] Šī nav bezmaksas atvērtā koda aplikācija.
	[!] Šo failu un kodu tajā ir aizliegts izplatīt un/vai kopēt.
	[!] Šo failu un kodu tajā drīkst izmantot tikai iegādājoties no SUNCORE SIA.
*/

include './includes/boot.php';
include ROOT . '/includes/config.php';

define( 'IN_APP', 1 );

if( !isset( $config ) OR empty( $config ) )
{
	//header('Location: ' . URL . '/install/' );
	//exit;
}

$response = array();


/* pārbaude vai ir izveidota nepieciešamā tabula, ja nav - tādu mēģina izveidot */
if( mysql::get( "SHOW TABLES LIKE '" . $config['mysql.table'] . "'" ) == false )
{
	$response['state'] = 'success';
	$response['text']  = 'Tabula tika veiksmīgi izveidota.';
	
	$query = "CREATE TABLE `" . $config['mysql.table'] . "` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `site_key` varchar(50) NOT NULL,
			  `ip` varchar(50) NOT NULL,
			  `username` varchar(50) NOT NULL,
			  `time_added` int(11) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
	
	$install = mysql::q( $query );
	
	/* Tabulu neizdevās izveidot */
	if( !$install )
	{
		$response['state'] = 'error';
		$response['text']  = 'MySQL Tabulas instalācija neizdevās.<br /> <b>Kļūdas ziņojums:</b> ' . mysql_error();
	}
}

/* pārbaude vai ir izveidota nepieciešamā tabula, ja nav - tādu mēģina izveidot */
if( mysql::get( "SHOW TABLES LIKE '" . $config['mysql.table'] . "_top'" ) == false )
{
	$query = "CREATE TABLE `" . $config['mysql.table'] . "_top` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `username` varchar(50) NOT NULL,
			  `vote_count` int(11) NOT NULL,
			    PRIMARY KEY (`id`),
				UNIQUE KEY `username` (`username`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
	
	$install = mysql::q( $query );
}


/* veco balsojumu tīrīšana */
if( !isset( $_GET['target'] ) )
{
	mysql::q( "DELETE FROM `" . $config['mysql.table'] . "` WHERE `time_added` < " . strtotime( '-24 hours' ) );
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo URL?>/assets/voter.css" media="all" />

<script type="text/javascript" src="<?php echo URL?>/assets/voter.js"></script>
<script type="text/javascript">
if ( typeof jQuery == 'undefined' ) {
	alert('Voter requires jQuery lib included in your page header.');
}
if ( typeof voter == 'undefined' ) {
	alert('Voter requires voter.js lib included in your page header.');
}
voter.url = '<?php echo URL?>';
</script>

<?php
try
{
	$Rcon = new MinecraftRcon;
	$query = new MinecraftQuery($config['rcon.ip'], $config['query.port']);
	
	$Rcon->Connect( $config['rcon.ip'], $config['rcon.port'], $config['rcon.password'], 1 );
	
}
catch( MinecraftRconException $e )
{
	$rcon_connection_failed = true;
	
	$response['state'] = 'error';
	$response['text']  = '<b>Kļūdas ziņojums: ' . $e->getMessage() . '</b><br /> Serveris ir Offline, diemžēl ingame bonusus<br /> var saņemt tikai kad serveris ir online.';
}


if( isset( $_GET['target'] ) )
{
	$target = $_GET['target'];
	
	/* Pārbaudam vai bannera links ir pareizs */
	if( isset( $config['links'][ $target ] ) AND !empty( $config['links'][ $target ] ) )
	{
		/* Pārbaudam vai nav jau balsots */
		$if_already_voted = mysql::get_all("SELECT * FROM `" . $config['mysql.table'] . "` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `site_key` = %s", $_GET['target'] );
		
		/* Rezultāts ir atrasts, tātad jau ir nobalsojis šajā lapā. */
		if( $if_already_voted )
		{
			echo '<div class="alert alert-error" style="margin:40px;">Tu šodien jau esi balsojis šeit. Mēģini atkal rīt.</div>';
		}
		else
		{
			/* Ja lietotājs ir uzspiedis uz zaļās pogas */
			if( isset( $_POST['target_site'] ) AND !empty( $_POST['target_site'] ) )
			{
				$if_ip_changed = false;
				
				$response['state'] = 'error';
				
				$username = trim(str_replace(' ','',$_POST['username']));
				$credits  = $config['links'][ $target ][ 'credits' ];
				
				/* Pārbaudīsim vai lietotājs nav mainījis savu IP adresi */
				if( !empty( $username ) )
				{
					$if_ip_changed = mysql::get_all("SELECT * FROM `" . $config['mysql.table'] . "` WHERE `username` = %s AND `site_key` = %s", array( $username, $target ) );
				}

				if( !$if_ip_changed )
				{
					if( !empty( $username ) )
					{
						mysql::insert( $config['mysql.table'], array(
							'site_key'    => $target,
							'username'    => $username,
							'ip'          => $_SERVER['REMOTE_ADDR'],
							'time_added'  => time()
						));
						
						mysql::q(
							'INSERT INTO `'.$config['mysql.table'].'_top`' . 
							'(username,vote_count) VALUES(%s,1)' . 
							'ON DUPLICATE KEY UPDATE' . 
							'`vote_count` = `vote_count` + 1'
						, array( $username ) );

						/* Bonusu pievienošana -------------- */
						
						//pievieno bonusus uzreiz tikai ja nav wos.lv vai 8d.lv
						if( !in_array($target, array('wos','8d') ) )
						{
							include ROOT . '/includes/bonus.php';
						}
						
						/* EOF Bonusu pievienošana   -------------- */
					}
					
					$response['state'] = 'success';
					$response['text']  = 'Paldies, ka mūs atbalsti. Tagad tik vēl jānobalso';

					echo '<script type="text/javascript">' . "\n";
					
					if( !empty( $username ) )
					{
						echo "voter.setCookie('voter_username','" . $username . "',30);\n";
						
						$_POST['target_site'] = str_replace( '&user=NoName', '&user=' . $username, $_POST['target_site'] );
					}

					echo 'document.location = "' . addslashes($_POST['target_site']) . '";</script>';
				}
				else
				{
					$response['text']  = 'Tu šodien jau esi balsojis.';
				}
				
				echo '<div class="alert alert-' . $response['state'] . '" style="margin:40px;">' . $response['text'] . '</div>';
				
			}
			else
			{
				$players_online = $query->get_mc_info();
				$players_online = $players_online->players_list;
			?>
				<div class="well voter-target">
					<form action="" method="post">
						<div class="alert alert-info">
							<strong>Ievēro:</strong> pēc sava nika ievadīšanas Tevi pārmetīs uz lapu, kur būs jānobalso.
							<p>Lai saņemtu bonusus <span style="color:#D81414">Tev jābūt online spēlē.</span></p>
						</div>
						
						<?php
						
						$vote_button_value = 'Balsot un saņemt bonusus';
						
						if( isset( $rcon_connection_failed ) )
						{
							$vote_button_value = 'Balsot par mums';
						}

						if( isset( $config['links'][ $target ][ 'wos' ] ) )
						{
							$wos_banner_script = false;
							
							/*
								wos.lv error codes:
									already_voted_1 	= wrong session, already voted, wrong country
									already_voted_3 	= passed all checks, but already voted
									already_voted_2 	= session timeout
									already_voted_69 	= wrong browser hash [p] or passed good hash, but wrong country
									voice_accepted 		= vote OK
							*/

							$hackers = @file_get_contents( 'http://ip2country.hackers.lv/api/ip2country?ip=' . $_SERVER['SERVER_ADDR'] , FALSE, NULL, 0, 240);
							
							//$bcv = URL . '/bcv.php&user=Alex';
							$bcv = '&back_connect_url=' . urlencode(URL) . '/bcv.php&user=NoName';
							
							if( $hackers )
							{
								$hackers = json_decode( $hackers, true );

								if( strtolower( $hackers['c'][ $_SERVER['SERVER_ADDR'] ] ) == 'lv' )
								{
									$wos_banner_script = file_get_contents( 'http://wos.lv/v.php?' . $config['links'][ $target ][ 'wos' ] . $bcv . '&wos_f=1' );
								}
								else
								{
									//7074l H4X0r
									$wos_banner_script = file_get_contents( 'http://api.suncore.lv/voter/?request_bcv=' . $config['links'][ $target ][ 'wos' ] . $bcv . '&wos_f=1' );
									
									if( $wos_banner_script == 'banned' )
									{
										echo 'Serviss pieejams tikai suncore klientiem.';
										exit;
									}
								}

							}
							
							if( !$wos_banner_script )
							{
								$response['state'] = 'error';
								$response['text']  = 'Nevaram iegūt savienojumu ar attālinātu serveri.';
							}
							
							if( strpos( $wos_banner_script, 'already_voted' ) !== false )
							{
								$response['state'] = 'error';
								$response['text']  = 'No Tavas IP jau ir balsots @ wos.lv';
							}
							else
							{
								preg_match( '/_0xfe24=(.*);document.write/', $wos_banner_script, $wos_protect );

								if( !empty( $wos_protect[ 0 ] ) ) 
								{
									$wos_protect = str_replace( 'document.write', '', $wos_protect[0] );

									preg_match( '/http(.*)3D/', $wos_banner_script, $link );
									
									if( empty( $link[0] ) )
									{
										echo 'wos 0x3 error ';
									}
									else
									{
									?>
									<div>
									<?php
									if( isset( $players_online ) AND !empty( $players_online ) )
									{
										echo '<select name="username" id="username">';
										foreach( $players_online as $player )
										{
											echo '<option value="' . $player . '"'.( (isset( $_COOKIE['voter_username'] ) AND $_COOKIE['voter_username'] == $player ) ? ' selected="selected"' : '' ).'>' . $player . '</option>';
										}
										echo '</select>';
									}
									?>
									</div>
									<div><input type="submit" class="btn btn-success" style="width:220px;margin-top:3px;" value="<?php echo $vote_button_value?>" /></div>
									<?php
									}
								}
								else
								{
									$response['state'] = 'error';
									$response['text']  = 'Nevaram iegūt wos.lv linku.';
								}
							}
						?>
						<input type="hidden" name="target_site" id="voter-wos-hack" value="" />
						<script type="text/javascript">
							var <?php echo $wos_protect?>
							$(function(){
								$('#voter-wos-hack').val( '<?php echo $link[ 0 ]; ?>&p=' + decodeURI(wos_js_protect()) + '<?php echo $bcv?>'
									.replace(/'/gi, "%27")
									.replace(/\(/gi, "%28")
									.replace(/\)/gi, "%29")
									.replace(/\*/gi, "%2A")
									.replace(/\!/gi, "%21")
									.replace(/\]/gi, "%5D")
									.replace(/\</gi, "%3C")
									.replace(/\>/gi, "%3E")
								);
						
							})
						</script>
						<?php
						}
						else
						{
						
							if( $target == '8d' )
							{
								$config['links'][ $target ][ 'link' ] = $config['links'][ $target ][ 'link' ] . '&url=' . urlencode(URL) . '/bcv.php&user=NoName';
							}
						
						?>
						
						<div>
						<?php
							if( isset( $players_online ) AND !empty( $players_online ) )
							{
								echo '<select name="username" id="username">';
								foreach( $players_online as $player )
								{
									echo '<option value="' . $player . '"'.( (isset( $_COOKIE['voter_username'] ) AND $_COOKIE['voter_username'] == $player ) ? ' selected="selected"' : '' ).'>' . $player . '</option>';
								}
								echo '</select>';
							}
						?>
						</div>
									
						<div><input type="submit" class="btn btn-success" style="width:220px;margin-top:3px;margin-bottom:5px;" value="<?php echo $vote_button_value?>" /></div>
						<input type="hidden" name="target_site" value="<?php echo $config['links'][ $target ][ 'link' ]?>" />
						<?php
						}
						if( !empty( $response ) )
						{
							echo '<div class="alert alert-' . $response['state'] . '">' . $response['text'] . '</div>';
						}
						?>
					</form>
					
					<?php 
					if( isset( $config['random_prizes'] ) AND !empty( $config['random_prizes']  ) )
					{
						if( isset( $config['links'][ $target ]['prizes'] ) AND $config['links'][ $target ]['prizes'] )
						{
					?>
						<div class="well" style="margin-top:10px;">
							<div class="alert alert-info">Par vienu balsojumu Tev ir iespēja saņemt <?php echo $config['links'][ $target ]['credits']?> Kredītus un vienu no zemāk esošajiem priekšmetiem pēc nejaušības principa.</div>
							<img src="http://static.suncore.lv/minecraft/gold.png" class="tooltip1" alt="" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo $config['links'][ $target ]['credits']?> Kredīti" /> + 
							
							<?php
							foreach( $config['random_prizes'] as $prize )
							{
								echo '<div class="item tooltip1" style="background: url(\'http://static.suncore.lv/minecraft/32/' . $prize['item_id'] . '.png\') center center no-repeat"';
								echo ' data-toggle="tooltip" data-placement="top" data-original-title="'.$prize['title'].'"></div>';
							}
							?>
							
						</div>
						<style type="text/css">
						.item{
							width:32px !important;
							height:32px!important;
							padding:3px;
							border:1px solid #E4E1E1;
							margin-right:10px;
							display:inline-block;
							vertical-align:middle
							
						}
						.item:hover{
							border:1px solid #ccc;
						}
						</style>
					<?php
						}
					}
					?>
					<script type="text/javascript">
					$(document).ready(function(){
						$('.tooltip1').tooltip();
					});
					</script>
				</div>
				<div class="voter-copyright">
					<?php
					/*
					* izņemsi bannerus - aizvainosi autorus.
					*/
					?>
					<a href="http://www.suncore.lv/" target="_blank">
						<img src="http://static.suncore.lv/img/suncore-small.jpg" alt="" /> <img src="http://static.suncore.lv/img/suncore-100x15-hosting.png" alt="" />
					</a>
				</div>
			<?php
			}
		}
	}
	else
	{
		echo '<div class="alert alert-error">Un kā Tu te tiki?</div>';
	}
}
else
{

if( !isset( $config['links']['8d'] ) OR empty( $config['links']['8d'] ) )
{
?>
<div class="alert alert-error">
Kāpēc Tu neesi pievienojies 8d.lv topsite?
</div>
<?php
}
?>
<div id="voter-html-codes">
	<?php
	$voted = array();
	
	if( isset( $_COOKIE['voter_username'] ) )
	{
		$select_voted = mysql::get_all('SELECT `site_key` FROM `'. $config['mysql.table'] . '` WHERE `username` = %s', $_COOKIE['voter_username']);
		
		if( $select_voted )
		{
			foreach( $select_voted as $_voted )
			{
				$voted[] = $_voted->site_key;
			}

		}
	}
	
	foreach( $config['links'] as $name => $link )
	{
		echo '<div class="voter-link'.(in_array($name,$voted) ? ' voted' : '').'">';
		echo '<a href="javascript:;" data-site="' . $name . '"><img src="' . $link['image'] . '" alt="" /></a>';

		if( $config['show.credits'] )
		{
			echo '<div class="vote-credits">1 balsojums <br /> ' . $link['credits'] . ' ' . $config['credit.name'] . '</div>';
		}
		
		echo '</div>';
	}
	?>
</div>
<?php
}
?>
<!--!! --------------------------->
<!--!! Voter 3.6                -->
<!--!! Created @ www.suncore.lv -->
<!--!! --------------------------->