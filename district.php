<?php

$chamber = getParam("ch");
$distnum = getParam("dist");

$page_title = ($chamber == 'H' ? 'House' : 'Senate') . ' District #' . $distnum;

array_push($zs_foot_jsfile,
    "http://www.google.com/jsapi",
    "http://maps.google.com/maps/api/js?sensor=false",

    "/inc/maps_lib2.js");

array_push($funcs_init,
    "map_init();map_show_district('$chamber','$distnum'	);");


include $header;
include $root . '/obj/dist.php';

$distobj = get_table('districts')->get($chamber, $distnum);
$next = "";
$counties = "";
if ($distobj) {
    $counties = $distobj->counties;

    $nextdist = intval($distnum) + 1;
    $next = "/district.php?ch=$chamber&dist=$nextdist";
}
?>
<div id="page">


    <?php
    if ($g_debug)
        echo("<a href='$next'>Next District</a>"); ?>


    <h1><?php echo($page_title); ?></h1>

    <div id="map_canvas" class="map_small"></div>
    <H4>Counties: <?php echo($counties); ?>
    </h4>

    <div style='clear:both'></div>

    <?php include $root. "/v2/district_race.php"; ?>

</div>

<?php include $footer; ?>
				
				