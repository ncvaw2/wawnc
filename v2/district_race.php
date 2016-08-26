<?php
/*
INCLUDED BY DISTRICT FILES

$chamber = getParam("ch");
$distnum = getParam("dist");
*/
$election=get_table("table_election");
echo("<h2 class='bar'>Current Representative</h2>");
$current=   get_table('table_office')->get_leg_by_district($chamber, $distnum);
$person=get_table('table_person')->getobj($current->key);
$person->print_list_row();
/*
echo("<h2 class='bar'>Republican Primary 3/15/2016</h2>");
$set=$election->getlist($chamber,$distnum,"2016","pri","REP");
if(count($set))
{
    foreach ( $set as $x )
    {
        $person=get_table('table_person')->getobj($x->key);
        $person->print_list_row();
    }
}
else
    echo("<h4>Uncontested</h4>");
echo("<h2 class='bar'>Democratic Primary 3/15/2016</h2>");
$set=$election->getlist($chamber,$distnum,"2016","pri","DEM");
if(count($set))
{
    foreach ( $set as $x )
    {
        $person=get_table('table_person')->getobj($x->key);
        $person->print_list_row();
    }
}
else
    echo("<h4>Uncontested</h4>");
*/
echo("<h2 class='bar'>General Election 11/8/2016</h2>");
$set=$election->getlist($chamber,$distnum,"2016","gen");
if(count($set))
{
    foreach ( $set as $x )
    {
        $person=get_table('table_person')->getobj($x->key);
        $person->print_list_row();
    }
}
else
    echo("<h4>No candidates currently registered</h4>");

