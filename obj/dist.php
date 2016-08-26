<?php
include_once  $root.'/obj/election.php';
include_once  $root.'/obj/person.php';





class district {
    public $counties;
    public $ch;
    public $dist;
	public function __construct($d,$index) {
    		$counties =getj($d,'counties');
    		$this->counties=str_replace(",",", ",$counties);
    		$this->ch =getj($d,'chamber');
    		$this->dist =getj($d,'district');
	}	
}

class districts extends table_base
{
	function create_from_spreadsheet()
	{
		$this->create1('data_v2','oknijni','district');
	}	
	function get_columns()
	{
		return ['chamber','district','counties'];
	}	
	public function get($ch,$dist)
	{
		foreach ( $this->list as $d)
		{
            if(($d->dist==$dist)&&($d->ch==$ch))
                return $d;
        }
        return null;
	}
	public function print_list()
	{
		global $g_electionMode;
        $leglist=get_table("table_office");
        $election=get_table("table_election");

		
		echo("<table class='votes' style='width:100%;text-align:left'><tr><th>District#</th>");
		echo("<th style=' max-width: 45px;'>Counties</th><th>Current Representative</th>");
		//echo("<th>2016 Primary</th>");
		echo("<th>2016 General Election</th>");
		echo("</tr>");
		
		foreach ( $this->list as $d )
		{
			$leg=$leglist->get_leg_by_district($d->ch,$d->dist);
			$chamber=($d->ch=='H'?'House':'Senate');
            $current = get_name_link($leg->key,true);
            $markup="";
			echo ("<tr><td style='width:90px; '><a href='/district.php?ch=$d->ch&dist=$d->dist'>$chamber #$d->dist</a></td>");
			echo ("<td width='20%'><div >$d->counties</div></td>");
            echo ("<td>$current</td>");

            // PRIMARY

            /*
            echo ("<td>");
            $set=$election->getlist($d->ch,$d->dist,"2016","pri","REP");
            if(count($set))
            {
                $markup.="<div>Republican Primary</div>";
                foreach ( $set as $x )
                    $markup .= $x->get_name_link(false);
            }

            $set=$election->getlist($d->ch,$d->dist,"2016","pri","DEM");
            if(count($set))
            {
                $markup.="<div style='margin-top: 10px;'>Democratic Primary</div>";
                foreach ( $set as $x )
                    $markup .= $x->get_name_link(false);
            }
            echo ($markup . "</td>");
            */
            echo ($markup . "<td>");

            $markup="";

            $set=$election->getlist($d->ch,$d->dist,"2016","gen");
            if(count($set))
            {
                $markup.="<div>General Election</div>";
                foreach ( $set as $x )
                    $markup .= $x->get_name_link(true);
            }


            echo ( $markup . "</td></tr>");
		}
		echo("</table>");
	}
	public function print_endorse()
	{
		$leglist=get_table("table_office");
		//$canlist=get_table('table_election');
		
		
		
		foreach ( $this->list as $d )
		{
			$leg=$leglist->get_leg_by_district($d->ch,$d->dist);
			$chamber=($d->ch=='H'?'House':'Senate');
		
			echo ("<h3><a href='/district.php?ch=$d->ch&dist=$d->dist'>$chamber District #$d->dist</a></h3>");
			echo ("<div>");
			$canlist->print_endorse($d->ch,$d->dist,"gen");
			echo ("</div>");
		
		}
	}	
}


?>

