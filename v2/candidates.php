
<?php 
include_once $root . '/obj/person.php';
include $header;
$url = "/v2/candidates.php?";
?>
<h2>List of Canidates for 2014 General Election</h2>
<a style="margin: 20px" href='<?php echo("$url"); ?>&sort=dist'><button>Sort by District</button></a>
<a style="margin: 20px" href='<?php echo("$url"); ?>'><button>Sort by Name</button></a>

<?php 
/*
<a style="margin: 20px" href='<?php echo("$url"); ?>&sort=grade'><button>Sort by Grade</button></a>
*/
?>


<form style="display: inline" action="javascript:void(0);"
		autocomplete="on">
		<span>Search by last name: </span> <input style="width: 300px"
				type="search" value=""
						title="Enter an address or an intersection &hellip;" id="namefilter"
								onkeypress="this.onchange();" onpaste="this.onchange();"
										oninput="this.onchange();" onchange="name_filter_chage()" />

										</form>

										<div id='namelist'>

<?php





$biolist = get_table ( "table_election" );
$biolist->sort ( );
$biolist->print_list ( );
?>
</div>

<?php 
/*

$biolist->sort ();
$chamber = getParam ( "ch" );
$page_title = "Report Card";
$title = "Legislator Report Card";
$url = "/guide/leglist.html?";
$chamberName = "Senate";
if ($chamber == 'H')
{
	$chamberName = "House";
}
if ($chamber)
{
	
	$page_title = "$chamberName Report Card";
	$title = $page_title;
	$url = "/guide/leglist.html?ch=" . $chamber;
}

include $header;

?>
<h2><?php echo("$title"); ?></h2>
<p>Legislators are given points for voting for animal welfare bills,
	sponsoring animal welfare bills, completing the NCVAW survey, and
	responsiveness to animal advocates constituents. There have only been a
	few animal welfare bills up to this point. As bills are introduced and
	voted on NCVAW will adjust the formula for final rating.</p>

<a style="margin: 20px" href='<?php echo("$url"); ?>&sort=dist'><button>Sort by District</button></a>
<a style="margin: 20px" href='<?php echo("$url"); ?>'><button>Sort by Name</button></a>
<a style="margin: 20px" href='<?php echo("$url"); ?>&sort=grade'><button>Sort by Grade</button></a>


<form style="display: inline" action="javascript:void(0);"
	autocomplete="on">
	<span>Search by last name: </span> <input style="width: 300px"
		type="search" value=""
		title="Enter an address or an intersection &hellip;" id="namefilter"
		onkeypress="this.onchange();" onpaste="this.onchange();"
		oninput="this.onchange();" onchange="name_filter_chage()" />

</form>

<div id='namelist'>
<?php

$leglist->print_list ( $chamber );
?>
</div>
*/

include $footer;
?>
