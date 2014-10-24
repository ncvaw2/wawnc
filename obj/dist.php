<?php
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
		$this->create1('data_v1',4,'district');
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
		$leglist=get_table("leg_list");
		$canlist=get_table('table_election');
		
		
		echo("<table class='votes' style='width:100%;text-align:left'><tr><th>District#</th><th>Candidates</th><th>Election</th>
				<th style=' max-width: 45px;'>Counties</th><th>Current Representative</th></tr>");
		foreach ( $this->list as $d )
		{
			$leg=$leglist->get_leg_by_district($d->ch,$d->dist);
			$chamber=($d->ch=='H'?'House':'Senate');
			$candidates=$canlist->get_candate_links($d->ch,$d->dist,"gen");
		
			echo ("<tr><td style='width:90px; '><a href='/district.php?ch=$d->ch&dist=$d->dist'>$chamber #$d->dist</a></td>");
			echo ("<td>$candidates</td>");
			echo ("<td><a href='/district.php?ch=$d->ch&dist=$d->dist'>Election Coverage</a></td>");
			echo ("<td width='20%'><div >$d->counties</div></td>");
			echo ("<td><a  href='/guide/legpage.php?id=$leg->key'>$leg->name</a></td></tr>");
		
		}
		echo("</table>");
	}
	
}


?>

