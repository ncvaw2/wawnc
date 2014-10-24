<?php
include_once $root . '/obj/dist.php';

include $header;

$distobj=get_table('districts')->print_list();


include $footer; ?>