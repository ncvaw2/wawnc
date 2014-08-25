<?php
$page_title='List Of Bills';
include $header;
include $root.'/inc/db.php';

$bill_list=get_table('bill_list');

$bill_list->print_bills();

?>

<?php include $footer; ?>