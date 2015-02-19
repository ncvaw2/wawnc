<?php
$page_title='In The News';
include $header;
include $root.'/obj/person.php';





$exlinks=get_table("exlinks");

$exlinks->print_list (null,null);
?>

<?php include $footer; ?>