<?php
include_once $root . '/obj/person.php';

$key = getParam ( "id" );
include $header;

if ($key) 
	include $root . '/v2/bio_page.php';
else 
	include $root . '/v2/bio_list.php';

include $footer; ?>

