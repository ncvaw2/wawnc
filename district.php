<?php

$chamber=getParam("ch");
$distnum=getParam("dist");

$page_title=($chamber=='H' ? 'House' : 'Senate') . ' District #' . $distnum;

array_push($zs_foot_jsfile,
	"http://www.google.com/jsapi",
	"http://maps.google.com/maps/api/js?sensor=false",

	"/inc/maps_lib2.js");

array_push($funcs_init,
	"map_init();map_show_district('$chamber','$distnum'	);");


include $header;
include $root.'/obj/person.php';

$distobj=get_table('districts')->get($chamber,$distnum);
$next="";
$counties="";
if($distobj)
{
	$counties=$distobj->counties;
	
	$nextdist=intval ($distnum)+1;
	$next="/district.php?ch=$chamber&dist=$nextdist";
}
?>


	<div id="page" >
	<a href='<?php print $next; ?>'>Next District</a>
	<div id="map_canvas"   ></div>
	<h1><?php 
	echo($page_title);
	

		?>
	</h1>
	<H4>Counties: <?php echo($counties);?>
	</h4>
	<div style='clear:both'></div>
<h3>General Election 11/4/2014</h3>
<?php	
	get_table('table_election')->print_people($chamber,$distnum);
	?>
	</div>


<?php include $footer; ?>
				
				