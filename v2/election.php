<?php
include_once $root . '/obj/districts.php';

include $header;

$distobj=get_table('districts')->print_list();


include $footer; ?>