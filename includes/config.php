<?php
$config = array();

//------------------------------------------------------------------------------
//    Konfigurācija:                                              ------------
//==============================================================================
$config['rcon.ip']       = 'mc.fall.lv';
$config['rcon.password'] = 'TavaRconParole';
$config['rcon.port']     = 25575;
$config['query.port']    = 25565;

/*
* Kādu pluginu izmantosi savā serverī, kas nodrošinās spēles naudas pievienošanu?
* 'iconomy' vai 'essentials' vai arī 'boseconomy'
* Mēs iesakām izmantot 'essentials'.
*/

$config['minecraft.economy.type']  = 'essentials';
/**
* Vai parādīt ingame tekstu ar ziņu par to, ka spēlētājs balso mājaslapā?
*
* Piem.: /say "Player XXX tikko nobalsoja un sanema X kreditus."
*/
$config['minecraft.ingame.notifications'] = true;


$config['credit.name']   = 'Kredīti';
$config['show.credits']   = true;

$config['mysql.host']   = 'localhost';
$config['mysql.user']   = 'suncraft_voter';
$config['mysql.pass']   = 'W*Zs{RWRoF{s';
$config['mysql.base']   = 'suncraft_voter';

$config['mysql.table']  = 'voter_clicks';

$config['links']['8d'] = array(
	'link'    => 'http://www.8d.lv/vote.php?site=378',
	'image'   => 'http://www.bildites.lv/images/rbqg395swt5y0rvagm8h.png',
	'credits' => 400,
	'prizes'  => true, //vai piešķirt papildus balvas
);
$config['links']['wos'] = array(
	'wos'     => 10244, //wos ID no html koda (<script src=http://wos.lv/d.php?21061></script>)
	'image'   => 'http://wos.lv/top.gif',
	'credits' => 500,
	'prizes'  => true, //vai piešķirt papildus balvas
);
$config['links']['top'] = array(
	'link'    => 'http://top.ieej.lv/in/763/',
	'image'   => 'http://top.ieej.lv/button/763/',
	'credits' => 400,
	'prizes'  => false, //vai piešķirt papildus balvas
);


/*
Papildus balvas ieteicams piešķirt tikai www.8d.lv un www.wos.lv topsaitiem

Echanti jāatdala ar atstarpi, pēc dotā parauga:
'enchants' => 'ENCHANT_ID:LEVEL ENCHANT_ID:LEVEL ENCHANT_ID:LEVEL ENCHANT_ID:LEVEL'

*/
$config['random_prizes'][] = array(
	'item_id'   => 276,
	'title'     => '1 Dimanta zobens, Sharpness +5, Smite+5',
	'enchants'  => '16:5 17:5',
	'count'     => 1
);

$config['random_prizes'][] = array(
	'item_id'   => 384,
	'title'     => 'Bottle of Enchanting x64',
	'enchants'  => '',
	'count'     => 64
);

$config['random_prizes'][] = array(
	'item_id'   => 264,
	'title'     => 'x8 Diamond',
	'enchants'  => '',
	'count'     => 8
);

$config['random_prizes'][] = array(
	'item_id'   => 265,
	'title'     => 'x32 Iron',
	'enchants'  => '',
	'count'     => 32
);

$config['random_prizes'][] = array(
	'item_id'   => 266,
	'title'     => 'x16 Gold',
	'enchants'  => '',
	'count'     => 16
);

$config['random_prizes'][] = array(
	'item_id'   => 45,
	'title'     => 'x64 Brick',
	'enchants'  => '',
	'count'     => 64
);

$config['random_prizes'][] = array(
	'item_id'   => 47,
	'title'     => 'x32 Books',
	'enchants'  => '',
	'count'     => 32
);

$config['random_prizes'][] = array(
	'item_id'   => 49,
	'title'     => 'x16 Obsidian',
	'enchants'  => '',
	'count'     => 16
);

$config['random_prizes'][] = array(
	'item_id'   => 52,
	'title'     => 'x1 Spawner',
	'enchants'  => '',
	'count'     => 1
);

$config['random_prizes'][] = array(
	'item_id'   => 130,
	'title'     => 'x1 Enderman Chest',
	'enchants'  => '',
	'count'     => 1
);

$config['random_prizes'][] = array(
	'item_id'   => 276,
	'title'     => 'x1 Diamond Sword',
	'enchants'  => '',
	'count'     => 1
);

$config['random_prizes'][] = array(
	'item_id'   => 278,
	'title'     => 'x1 Diamond Pickaxe',
	'enchants'  => '',
	'count'     => 1
);




define( 'MYSQL_HOST', $config['mysql.host'] );
define( 'MYSQL_USER', $config['mysql.user'] );
define( 'MYSQL_PASS', $config['mysql.pass'] );
define( 'MYSQL_BASE', $config['mysql.base'] );

//define( 'INSTALLED', false );