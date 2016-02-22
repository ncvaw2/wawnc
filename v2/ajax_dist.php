<?php
// called via AJAX!! Not standalone
include $root.'/obj/person.php';
 


$chamber=0;
$distnum=0;
if (array_key_exists ( "dist", $_GET ))
    $distnum= $_GET ["dist"];
if (array_key_exists ( "ch", $_GET ))
	$chamber= $_GET ["ch"];


echo("<h3 style='margin:0;padding:0;'><a href='/district.php?ch=". $chamber . "&dist=" . $distnum  ."'>Your " . ($chamber=='H'? 'House' : 'Senate')." district: ". $distnum . "</a></h3>");

include $root. "/v2/district_race.php";

?>



