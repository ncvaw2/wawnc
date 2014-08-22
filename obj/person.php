<?php
include $root.'/inc/db.php';



class legislator{

	public $name;
	public $first;
	public $last;
	public $id;
	public $key;
	public $grade;
	public $score;
	public $phone;
	public $email;
	public $comment;
	public $grade_color;
	public $uid;
	public $party;
	public $chamberId;
	public $chamber;
	public $county;
    public $url;
	public $district;

	public function __construct($d,$index) {

		$this->party = getj($d,'party');
		$this->first = getj($d,'first');
		$this->phone = getj($d,'phone');
		$this->last = getj($d,'last');
		$this->key = getj($d,'key');
		$this->comment=getj($d,"comment");
		$this->email=getj($d,"email");
		$this->grade=getj($d,"grade");
		$this->note=getj($d,"note");
		
		$this->name = $this->first.' '.$this->last;
		$this->uid =getj($d,'uid');
		$this->id = getj($d,'id');
		$this->county = getj($d,'county');
		$this->chamberId = getj($d,'chamber');
        $this->district=getj($d,'district');
        $this->title=getj($d,'title') ;
		if($this->chamberId=='H')
			$this->chamber='House';
		else
			$this->chamber='Senate';
			
		$this->create_grade();
	}
	public function get_url() {
		return ("http://www.ncleg.net/gascripts/members/viewMember.pl?sChamber=$this->chamber&nUserID=$this->uid");
	}
	public function print_survey() {
		getobj("survey_data")->printresp($this->key);
	}
	public function print_list_votes()
	{
		getobj("vote_data")->print_list_votes($this->key,0);

	}
	public function print_list_sponsorship()
	{

		getobj("vote_data")->print_list_votes($this->key,1);

	}
	public function create_grade()
	{
		$count=0;
		$color="#000";

		$score=getobj("vote_data")->get_votes($this->key,$count);

		if( ($count==0) && (!$this->grade))
		{
			$this->grade='Not Graded';
			$this->comment='Has not been in office long enough to assign grade';
		}
		else
			get_grade($score,$this->grade,$color);


		$this->grade_color=$color;
		$this->score=$score;
			
	}

	public function print_table_row($label, $val,$color=null) {
		$style="";
		if($color)
			$style="style='color:$color;font-weight:bold'";
			
		echo "<tr><td class='leg_label'>$label: </td><td class='leg_val' $style>$val</td></tr>";
	}
	public function print_list_row() {
		global $isPhone;
		$canidate=getobj("canidates")->get_candiate($this->key);
		$data_key=$this->key;


		echo "<div class='leg_bio' data-name='$data_key'><hr/><div class='leg_thumb' >";
		echo "<a title='Click for voting record'  href='/guide/legpage.php?id=$this->key'>";


		echo "<img src='http://www.ncleg.net/$this->chamber/pictures/$this->uid.jpg'/></a>";
		
		echo "</div><div class='leg_info' ><a title='Click for voting record' href='/guide/legpage.php?id=$this->key'><h2>$this->title $this->name</h2></a><table>";

		/*
		 $district=
		*/
		
		$district_url="'/district.php?dist=". $this->district . "&ch=" . $this->chamberId . "'";
		$this->print_table_row ( 'District', "<a title='Show district race'  href=$district_url>$this->district</a>" );
		$running="Not running for re-election.";
		if($canidate)
		{
			$running=$canidate->get_running();

				
		}
		$running.="<div><a href=$district_url>Click here for 2014 district race</a></div>";
		$this->print_table_row ( '2014 Election', $running );
		$this->print_table_row ( 'Party', $this->party );
		$this->print_table_row ( 'Counties', $this->county );

		$email_link="<a  href='mailto:'" .$this->email . ">" . $this->email . "</a>";
		$this->print_table_row ( 'Email', $email_link );
		$this->print_table_row ( 'Phone', $this->phone );
        $responded="No";
        if(getobj("survey_data")->check($this->key))
            $responded="<a style='color:green;font-weight:bold;' href='/guide/legpage.php?id=$this->key'>Yes</a>";
        
     
        $this->print_table_row ( 'Responded to survey', $responded );


			$grade_link="<a title='Click for voting record'  style='font-weight:bold;color:" .$this->grade_color
			."' href='/guide/legpage.php?id=$this->key'>$this->grade</a>";
			$this->print_table_row ( 'Grade',$grade_link );
				
	

		if(!$this->comment)
		{
				
			$this->comment="Voting record, responsiveness to inquiries, and feedback from constituents";
			$this->print_table_row ( 'Ranking Created By', $this->comment );
		}
		else
			$this->print_table_row ( 'Reason For Grade', $this->comment );

		if($this->note)
		{
			$this->print_table_row ( 'Note', $this->note );
		}       



		echo '</table>';
        $url=$this->get_url();
		echo ("<a target='_blank' href='$url'>Link to page on NCGA website</a>&nbsp;&nbsp;&nbsp;&nbsp;<a  href='/guide/legpage.php?id=$this->key'>Link to voting record</a>");
		echo "</div></div><div style='clear:both'></div>";
	}
}



function sort_func_grade($a, $b) {
	if ($a->score == $b->score) {
		return 0;
	}
	return ($a->score < $b->score) ? 1 : -1;
}
function sort_func_dist($a, $b) {
	if ($a->district == $b->district) {
		return 0;
	}
	return ($a->district < $b->district) ? -1 : 1;
}

class leg_list extends data_source{
    function create_from_spreadsheet()
	{
		$this->get_json_data(2,'legislator','key');
	}
	public function print_list($chamber) {
		echo "<div class='tbl_leglist' >";
		foreach ( $this->list as $d )
        {
            if($chamber)
                if($chamber != $d->chamberId)
                    continue;
			$d->print_list_row ();
		}
		echo '</div>';
	}
   
	public function get_leg_by_key($key) {
        if(array_key_exists ($key,$this->list))
            return $this->list[$key];
        return null;
	}
	
	public function get_leg_by_district($chamber,$district) {
		foreach ( $this->list as $leg ) {
			if (($district == $leg->district)
                &&($chamber==$leg->chamberId))
				return $leg;
		}
		return 0;
	}
	public function sort() {
		
		$sort=getParam("sort");
		if($sort=='grade')
		{
			uasort($this->list, 'sort_func_grade');
		}
		else
		if($sort=='dist')
		{
			uasort($this->list, 'sort_func_dist');
		}	
		else
			ksort ( $this->list );
	
	}	
	
}

class canidate {
	public $displayname;
	public $key;
	public $party;
	public $party_id;
	public $first;
	public $last;

	public $id;
	public $uid;
	public $chamberId;
	public $chamber;
	public $district;
	public $url_cover_jpg;

	public $jpg_path;
	public $url;
	public $photo;
	public $website;
	public $phone;
	public $email;
	public $election;



	public function __construct($d,$index) {
		$this->photo =getj($d,'photo');
		$this->election=getj($d,'election');
		$this->displayname=getj($d,'nameonballot');
		$this->party_id = getj($d,'party');
		$this->phone = getj($d,'phone');
		$this->district = getj($d,'district');
		$this->chamberId = getj($d,'chamber');
		$this->key = getj($d,'key');
		$this->website = getj($d,'website');
		$this->party=$this->party_id;
        
        
		if($this->party_id=='DEM')
		{
			$this->party='Democratic';
		}
		if($this->party_id=='REP')
		{
			$this->party='Republican';
		}
		if($this->chamberId=='H')
			$this->chamber='House';
		else
			$this->chamber='Senate';
			
	}

	public function print_table_row($label, $val) {
		echo ("<tr><td class='leg_label'>$label: </td><td class='leg_val'>$val</td></tr>");
	}
	public function get_running() {
		$running="Running for re-election in the ";

		if($this->election=='gen')
		{
			$running.='general election 11/4/2014';
				
		}
		else
		{
			$running.=$this->party . ' primary election 5/6/2014';
		}
		return $running;
	}
	public function get_local_page_url() {	
		$url="/guide/canidate.php?key=$this->key";
		$leg=getobj("leg_list")->get_leg_by_key($this->key);
		if($leg)
		{
			$url="/guide/legpage.php?id=$this->key";
			
		}
		return $url;
	}
	public function print_list_row() {

		$leg=getobj("leg_list")->get_leg_by_key($this->key);
		if($leg)
		{
			$leg->print_list_row();
			return;
		}
		$data_key=$this->key;
		
		echo ("<div class='leg_bio' data-name='$data_key'><hr>");
		//thumbnail
		
		if($this->photo)
		{

			echo ("<div class='leg_thumb' ><a href='/guide/canidate.php?key=$this->key'>");
			echo ("<img src='$this->photo'/></a></div>");
		}
		else {
			echo ("<div class='leg_thumb' ><img src='/img/unknown.png'/></div>");
				
		}

		echo ("<div class='leg_info' ><a href='/guide/canidate.php?key=$this->key'><h2>$this->displayname</h2></a><table><tr><td/><td/></tr>");
		$district_url="'/district.php?dist=". $this->district . "&ch=" . $this->chamberId . "'";
		$this->print_table_row ( 'District', "<a href=$district_url>$this->district</a>" );

		$this->print_table_row ( 'Party', $this->party );
		$running="Challenger in the ";

		if($this->election=='gen')
		{
			$running.='general election 11/4/2014';
				
		}
		else
		{
			$running.=$this->party . ' primary election 5/6/2014';
		}
		
		$this->print_table_row ( '2014 Election', $running );
		
		if($this->website)
		{
			$link="<a href='".$this->website."' target='_blank'>".$this->website."</a>";
			$this->print_table_row ( 'Webiste', $link );
				
		}
		
		
		$this->print_table_row ( 'Email', $this->email );
		$this->print_table_row ( 'Phone', $this->phone );

		echo ('</table>');
			
		echo ("</div></div><div style='clear:both'></div>");
	}

}


class canidates extends data_source
{
	function create_from_spreadsheet()
	{
		$this->get_json_data(5,'canidate','key');
	}	

	public function get_candiate($key) {
        if(array_key_exists ($key,$this->list))
            return $this->list[$key];
        //person may not be running
        return null;
	}	
	
	public function getlist($ch,$num,$elect)
	{
		$set=array();
		foreach ( $this->list as $c )
		{
			if( 
            ($c->district==$num)&&
            ($c->chamberId==$ch)&&
            ($c->election==$elect))
			{
				$set [] =$c;
			}	
		}
		return $set;
	}
	public function get_candate_links($ch,$num,$elect)
	{
		$link_string="";
		$set=$this->getlist($ch,$num,$elect);

		foreach ( $set as $x )
		{

			$link_string.="<div>$x->party_id: <a href='/guide/canidate.php?key=$x->key'>$x->displayname</a></div>";

			
		}
		if(count($set)==1)
		{
			$link_string.="<div>(uncontested)</div>";
			
		}
		return $link_string;
	}	
	public function printlist($ch,$num,$elect)
	{
		
	    $set=$this->getlist($ch,$num,$elect);
		foreach ( $set as $x )
				$x->print_list_row();
	}	
}
function file_get_contents_curl($url)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	
	

	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}
class exlink {
	public $link;
	public $canidates;
	public $date;
	public $index;
	public $bill;
	public $image;
	public $title;	
	public $description;	
	public function __construct($d,$index) {
		$this->index =$index;
		$this->link =getj($d,'link');
		$this->doc =getj($d,'doc');
		$this->title =getj($d,'title');
		$this->date =getj($d,'date');
		$this->image =getj($d,'image');
		$this->text =getj($d,'text');
		$canidates=array();
		
		$this->canidates =str_getcsv (getj($d,'canidates'));
		if($this->link)
			$this->fetch();
	}
	
	public function print_panel()	
	{
		global $g_debug;
		echo("<div style='background-color:#eee;padding:10px 10px 0 10px '><div style='background-color:white;margin: 10px 10px 0 10px ;padding:10px'>");
		
		if($g_debug)
		{
			echo("<h4>$this->index</h4>");
			
			
		}
		echo ("<a target='_blank' href='$this->link'><img  style='max-width:300px;max-height:200px;' src='$this->image'/></a>");
		echo ("<a  target='_blank' href='$this->link'><h4>$this->title</h4></a>");
		echo ("<p>$this->text</p><div>Links: ");	
		$canlist=	getobj("canidates");
		$comma=false;
		if($this->canidates)
		foreach($this->canidates as $key )
		{
			
			$canidate=$canlist->get_candiate($key);
			if($canidate)
			{
				$url=$canidate->get_local_page_url();
				if($comma)
					echo(",");
				echo("<a href='$url'>$canidate->displayname</a>");
				
				$comma=true;
			}
			
		}
		echo("</div></div></div>");
		
		
		
	}
	public function fetch()
	{
		try {
		//parsing begins here:
			$html = file_get_contents_curl($this->link);
			
			$doc = new DOMDocument();
			@$doc->loadHTML($html);
			
			//get and display what you need:
			if(!$this->title)
			{
				$nodes = $doc->getElementsByTagName('title');
				$this->title = $nodes->item(0)->nodeValue;
			}
			
			$keywords="";
			$description="";
			$image="";
			
			$metas = $doc->getElementsByTagName('meta');
			
			for ($i = 0; $i < $metas->length; $i++)
			{
				$meta = $metas->item($i);
				if($description=="")
					if($meta->getAttribute('name') == 'description')
						$description = $meta->getAttribute('content');
				if($description=="")					
					if($meta->getAttribute('property') == 'og:description')
						$description = $meta->getAttribute('content');			
				if($image=="")
					if($meta->getAttribute('property') == 'og:image')
						$image = $meta->getAttribute('content');
			}
			if(!$this->image)
			{
				$this->image=$image;
			}		
			if(!$this->text)
			{
				$this->text=$description;
			}
	
		}
		catch (Exception $e) {
			$this->title=$e->getMessage();
  
			}
		
	
	}	

	
	
}

class exlinks extends data_source
{
	function create_from_spreadsheet()
	{
		$this->get_json_data(6,'exlink');
	}
	public function has_links($legid,$billid)
	{
		foreach ( $this->list as $row )
		{
			if($legid)
			{
				if(in_array($legid,$row->canidates))
					return true;;
			}
			if($billid)
			{
				if($row->doc ==$billid)
					return true;
	
			}
			
		}
	}	
	public function print_list($legid,$billid)
	{
		foreach ( $this->list as $row )
		{
			if($legid)
			{
				if(in_array($legid,$row->canidates)==false)
					continue;
			}
			if($billid)
			{
				if($row->doc !=$billid)
					continue;

			}			
			$row->print_panel();
		}
	}	

}
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

class districts extends data_source
{
	function create_from_spreadsheet()
	{
		$this->get_json_data(4,'district');
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
		$leglist=getobj("leg_list");
		$canlist=getobj("canidates");
		
		
		echo("<table class='votes' style='width:100%;text-align:left'><tr><th>District#</th><th>Canidates</th><th>Election</th>
				<th style=' max-width: 45px;'>Counties</th><th>Current Representative</th></tr>");
		foreach ( $this->list as $d )
		{
			$leg=$leglist->get_leg_by_district($d->ch,$d->dist);
			$chamber=($d->ch=='H'?'House':'Senate');
			$canidates=$canlist->get_candate_links($d->ch,$d->dist,"gen");
		
			echo ("<tr><td style='width:90px; '><a href='/district.php?ch=$d->ch&dist=$d->dist'>$chamber #$d->dist</a></td>");
			echo ("<td>$canidates</td>");
			echo ("<td><a href='/district.php?ch=$d->ch&dist=$d->dist'>Election Coverage</a></td>");
			echo ("<td width='20%'><div >$d->counties</div></td>");
			echo ("<td><a  href='/guide/legpage.php?id=$leg->key'>$leg->name</a></td></tr>");
		
		}
		echo("</table>");
	}	
}
class survey_question 
{
	public $q;
    public function __construct($d,$index) {
        $this->q = getj($d,'question');
	}
}
class survey_questions extends data_source
{
 	function create_from_spreadsheet()
	{
		$this->get_json_data(7,'survey_question');
	}     
	public function getquestion($num)
	{
		return  $this->list[$num]->q;
	}
}
class survey_resp 
{
	public $fistname;
	public $lastname;
	public $comments;
	public $answers;
	public $key;
    public function __construct($d,$index) {
            $this->answers=array();
		    $this->key = getj($d,'key');
		    $this->firstname = getj($d,'firstname');
		    $this->lastname = getj($d,'lastname');
		    $this->comments = getj($d,'comments');
            $this->answers[0]=getj($d,'a1');
            $this->answers[1]=getj($d,'a2');
            $this->answers[2]=getj($d,'a3');
            $this->answers[3]=getj($d,'a4');
            $this->answers[4]=getj($d,'a5');
        
            }
	public function printresp()
	{
		for ($x=0; $x<5; $x++)
		{
			$qnum=$x+1;
			$q=getobj("survey_questions")->getquestion($x);
			if(!$q)
				$q="could not get question";
			$a=$this->answers[$x];
			if(!$a)
				$a="could not get answer";			
			echo("<div style='margin-top:30px' class='section_head'>Question #$qnum</div>");
			echo("<div>$q</div>");
			echo("<div style='margin-top:10px' class='section_head'>Answer:</div>");
			echo("<div>$a</div>");
		}
        

	}	
}

class survey_data extends data_source
{
 	function create_from_spreadsheet()
	{
		$this->get_json_data(1,'survey_resp','key',"0AonA9tFgf4zjdE45M0MyZTR0UUYxXzNzRjBuNWFnMGc");
	}   
	public function check($key)
	{
        return (array_key_exists ($key,$this->list));
 
	}	
	
	public function printresp($key)
	{
 		echo("<div  style='max-width:800px'><h3>Resposes to animal welfare survey:</h3>");    
       $row=null;
        if(array_key_exists ($key,$this->list))
            $row= $this->list[$key];
		if(!$row)
        {
            echo("<div>Did not respond to our survey.</div>");    
        }
        else
       		$row->printresp();
        echo("</div>");  
	}

	public function printlist()
	{
		foreach ( $this->list as $row )
		{
			$key=$row->key;
				
			$leg=getobj("leg_list")->get_leg_by_key($key);
			if($leg)
			{
				
				$leg->print_list_row();
			}
			else {
				echo("<div>$key </div>");
				
			}
		}
	}
}



?>

