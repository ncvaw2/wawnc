<?php
include_once $root . '/obj/person.php';

$key = getParam ( "key" );


if ($key) 
	include $root . '/v2/bio_page.php';
else 
	include $root . '/v2/bio_list.php';

include $footer; ?>