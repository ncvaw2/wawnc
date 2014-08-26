<?php
$page_title='In The News';
include $header;
include $root.'/inc/db.php';





$exlinks=get_table("exlinks");

$exlinks->print_list (null,null);
?>

<?php include $footer; ?>