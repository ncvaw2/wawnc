<?php
include_once $root . '/obj/person.php';

$key = getParam ( "key" );


if ($key) {

    $person = get_table("table_person")->getobj($key);
    if(!$person)
    {
        include $header;
        echo("<h1>Person not found: $key</h1>");


    }
    else
    include $root . '/v2/bio_page.php';
}

else 
	include $root . '/v2/bio_list.php';

include $footer; ?>