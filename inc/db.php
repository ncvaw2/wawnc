<?php
/*
 * bill_list - tab 1- 
 * candidates - tab 2-
 * table_office  - tab 3-
 * districts - tab 4
 * vote_data - tab 5-
 * links 	 - tab 6-
 * 
 * 
 * 
 */
include_once  $root.'/obj/tables.php';


class bill {
	public $nickname;
	public $stance;
	public $effect;
	public $official;
	public $svid;
	public $hvid;	
	public $desc;
	public $year;
	public $key;
	public $docname;	
	public $picture;
	public function __construct($d,$index)
	{
		
		$this->nickname =getj($d,'nickname');
		$this->stance =getj($d,'stance');
		if($this->stance == 'pro')
		{
			$this->effect= "Supports animal welfare";
			$this->picture='bill_good.png';
		}
		else 
		{
			$this->picture='bill_bad.gif';
			$this->effect= 	"Harmful to animal welfare";
		}
		$this->official =getj($d,'official');
		$this->svid =getj($d,'svid');
		$this->hvid =getj($d,'hvid');		
		$this->desc =getj($d,'desc');
		$this->year =getj($d,'year');		
		$this->link =getj($d,'link');		
		$this->key =getj($d,'key');		
		$this->docname =getj($d,'name');		
	}
	public function get_stance()
	{
		if($this->stance == 'pro')
		{
			return "Supports animal welfare";
		}
		
		return 	"Harmful to animal welfare";
		
	
	}
	public function print_tr()
	{
        if($this->year == 'Year')
            return;
		echo ("<div><h3><a href='/guide/billpage.php?key=$this->key'>$this->year - $this->docname - $this->nickname <img style='display:inline;width:40px' src='/img/$this->picture' /></a></h3>	
			
			<div>$this->official </div>
		<div>$this->effect </div>
		<div><a target='_blank' href='http://www.ncga.state.nc.us/gascripts/BillLookUp/BillLookUp.pl?Session=$this->year&BillID=$this->docname&submitButton=Go' >Link to $this->docname on ncga.state.nc.us</a> </div>
		<div>$this->desc </div></div>");
	}
	public function print_page()	
	{
		echo "<H1>$this->year - $this->docname -  $this->nickname</H1>";
		echo "<H2>$this->official </H2>";
		
		
		echo "<p><img style='display:inline;width:60px' src='/img/$this->picture' /> $this->effect </p>";		
		echo "<p><a target='_blank' href='http://www.ncga.state.nc.us/gascripts/BillLookUp/BillLookUp.pl?Session=$this->year&BillID=$this->docname&submitButton=Go' >Link to $this->docname on ncga.state.nc.us</a> </p>";
		echo "<p>$this->desc </p>";
	}	
}

class bill_list extends table_base
{
	
	function create_from_spreadsheet()
	{
		$this->create1('data_v1',1,'bill','key');
	}	
	public function get_bill($key)
	{
		return $this->list [$key];
	}
	public function print_bills()
	{
		foreach ( $this->list as $bill )
		{
			$bill->print_tr();
		}
	}	
}

class vote {
	public $vid;
	public $mkey;
	public $doc;
	public $vote;
	public $score;
	public $grade; /* -1 bad vote, 0 neutral, 1 good */
	public function __construct($d,$index)
	{
		$this->vid=getj($d,'vid');
		$this->mkey=getj($d,'key');		
		$this->doc= getj($d,'doc');
		$this->vote=getj($d,'vote');
		$this->grade=0;
	}
	public function get_score()
	{
		$bill=get_table("bill_list")->get_bill($this->doc);
		
		if($this->vid)
		{
			if(!(($this->vid==$bill->svid)||($this->vid==$bill->hvid)))
				return 0;
		}
		if(($this->vote=='Aye') xor($this->vote=='No'))
		{
			if(($this->vote=='Aye') xor ($bill->stance=='pro'))
				$this->grade=-1;
			else
				$this->grade=1;
			return $this->grade;
		}

		if(($this->vote=='psp') xor($this->vote=='sp'))
		{
			if($bill->stance=='anti')
				$this->grade=-1;
			else
				$this->grade=1;
			return $this->grade * 3;
		}
		return $this->grade;
	}

	public function print_vote_tr()
	{
		$vote=$this->vote;
		$doc=$this->doc;
		$bill=get_table("bill_list")->get_bill($doc);
		if($this->vid)
		{
			if(!(($this->vid==$bill->svid)||($this->vid==$bill->hvid)))
				return;
		}
		//echo "<img style='width:50px' src='/img/x.jpg'/>";

		echo "<tr>";
		//echo "<td>$vid</td>";

		$picture=0;
		$class="";
		$title="Voted ".($vote=='Aye'? "for" :"against")." bill that ". $bill->effect;
		if(($vote=='Aye') xor($vote=='No'))
		{
			if(($vote=='Aye') xor ($bill->stance=='pro'))
			{
				$class='votebad';
				$picture='bill_bad.gif';
			}
			else
			{
				$class='votegood';
				$picture='bill_good.png';
			}
		}

		if(($vote=='psp') xor($vote=='sp'))
		{
			$title='Bill ' . $bill->effect;
			if($bill->stance=='anti')
			{
				$class='votebad';
				$picture='bill_bad.gif';
			}
			else
			{
				$class='votegood';
				$picture='bill_good.png';
			}
		}
		if($vote=='psp')
			$vote='Primary Sponsor';
		if($vote=='sp')
			$vote='Sponsor';
		if($vote=='N/V')
			$vote='Did Not Vote';
		echo "<td><div class='$class'>$vote</div>";
		if($picture)
			echo "<img style='width:60px' title='$title' src='/img/$picture'/>";

		echo "</td>";
		echo "<td><div><a href='/guide/billpage.php?doc=$doc'>$doc - $bill->official</a></div>";
		echo "<div class='$class'>$title</div>";
		if($bill->desc)
			echo "<div>$bill->desc</div>";

		echo "</td></tr>";
	}
}

class vote_data extends table_base
{

	function create_from_spreadsheet()
	{
		$this->create1('data_v1',3,'vote');
	}	

	public function print_bill_votes($title,$doc,$vote,$vid) {

		$legs=array(); //local
		//echo "<table class='votes'>";
		$comma=0;
		$uid=$doc.$vote.$vid;
		$display='block';

		
		foreach ( $this->list as $v )
		{
			if($vid && ($vid!=$v->vid))
				continue;
			if(($doc == $v->doc)&&
					($vote==$v->vote)
			)
			{
				
				$leg=get_table("table_office")->get_leg_by_key($v->mkey);
				if($leg)
					$legs[]=$leg;
			}
		}
		$count=count($legs);
		if($count>6)
			$display='none';
		echo("<h5>$title:<a onclick='togglehide(\"$uid\")'>($count)</a></h5><div id='$uid' style='display:$display'>");



		foreach ( $legs as $leg )
		{

			if($comma)
				echo (", ");
			echo "<a href='/v2/bio.php?key=$leg->key'>$leg->name</a>";
			$comma=1;
		}
		echo("</div>");
	}
	public function get_votes($legid,&$count) {
		$score=0;
		$count=0;
        
		foreach ( $this->list as $v )
		{
			if($legid == $v->mkey)
			{

				$score+=$v->get_score();
				$count++;

			}
		}
		return $score;
	}
	public function print_list_votes($legid,$sponsors) {
		foreach ( $this->list as $v )
		{
			if($v->vid xor $sponsors)
				if($legid == $v->mkey)
				{
					$v->print_vote_tr();
				}
		}
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
	public $key;
	public $candidates;
	public $doc;
	public $date;	
	public $title;	
	public $note;
	public $link;
	public $image;
	public $text;	
	public $candidate_list;
	
	public function __construct($d,$index) {
		$this->key =getj($d,'key');
		$this->link =getj($d,'link');
		$this->doc =getj($d,'doc');
		$this->title =getj($d,'title');
		$this->date =getj($d,'date');
		$this->image =getj($d,'image');
		$this->text =getj($d,'text');
		$this->candidates =getj($d,'candidates');
		$this->candidate_list=array();
		if($this->candidates)
			$this->candidate_list=str_getcsv($this->candidates);
		
		//if($this->link)	$this->fetch();
	}
	
	public function print_panel()	
	{
		global $g_debug;
		echo("<div style='background-color:#eee;padding:10px 10px 0 10px '><div style='background-color:white;margin: 10px 10px 0 10px ;padding:10px'>");
		
		if($g_debug)
		{
			echo("<h4>$this->key</h4>");
		}
		echo("<h4>$this->date</h4>");
		echo ("<a target='_blank' href='$this->link'><img  style='max-width:300px;max-height:200px;' src='$this->image'/></a>");
		echo ("<a  target='_blank' href='$this->link'><h4>$this->title</h4></a>");
		echo ("<p>$this->text</p><div>Links: ");	
		$canlist=	get_table('table_person');
		$comma=false;
		foreach($this->candidate_list as $key )
		{
			
			$candidate=$canlist->getobj($key);
			if($candidate)
			{
				$url=$candidate->get_local_page_url();
				if($comma)
					echo(",");
				echo("<a href='$url'>$candidate->fullname</a>");
				
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

class exlinks extends table_base
{
	function create_from_spreadsheet()
	{
		$this->create1('data_v1',6,'exlink');
	}
	public function has_links($legid,$billid)
	{
		foreach ( $this->list as $row )
		{
			if($legid)
			{
				if(in_array($legid,$row->candidate_list))
					return true;;
			}
			if($billid)
			{
				if($row->doc ==$billid)
					return true;
	
			}
			
		}
	}	
	public function process()
	{
		global $root;
		$filename=$root."/data2/links2.csv";
		$vars=get_member_vars('exlink');
		
		$fp = fopen($filename, 'w');
		fputcsv($fp,$vars);
		fclose($fp);
		
		foreach ( $this->list as $row )
		{
			if(($row->text)&&($row->image)&&($row->title))
				continue;			
			$row->fetch();
			$array= (array)$row;
			$fp = fopen($filename, 'a');
			fputcsv($fp,$array);
			fclose($fp);
			
			
		}
	}	
	public function print_list($legid,$billid)
	{
		foreach ( $this->list as $row )
		{
			if($legid)
			{
				if(in_array($legid,$row->candidate_list)==false)
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

class survey_question 
{
	public $q;
    public function __construct($d,$index) {
        $this->q = getj($d,'question');
	}
}
class survey_questions extends table_base
{
 	function create_from_spreadsheet()
	{
		$this->create1('data_v1',7,'survey_question');
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
		if($this->comments)
		{
			echo("<div style='margin-top:30px' class='section_head'>Comments</div>");
			echo("<div>$this->comments</div>");;
		}

		for ($x=0; $x<5; $x++)
		{
			$qnum=$x+1;
			$q=get_table("survey_questions")->getquestion($x);
			if(!$q)
				$q="could not get question";
			$a=$this->answers[$x];
			if(!$a)
				$a="[blank]";			
			echo("<div style='margin-top:30px' class='section_head'>Question #$qnum</div>");
			echo("<div>$q</div>");
			echo("<div style='margin-top:10px' class='section_head'>Answer:</div>");
			echo("<div>$a</div>");
		}
        

	}	
}

class survey_data extends table_base
{
 	function create_from_spreadsheet()
	{
		$this->create1('survey1',1,'survey_resp','key');
	}   
	public function check($key)
	{
        return (array_key_exists ($key,$this->list));
 
	}	
	
	public function printresp($key)
	{
 		echo("<div  style='max-width:800px'><h3>Responses to animal welfare survey:</h3>");    
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
		ksort ( $this->list );
		global $g_debug;
		foreach ( $this->list as $row )
		{
			$key=$row->key;
			$office=get_table ( "table_office" )->getobj ( $key );
			if(!$office)
				continue;
			$bio=get_table ( "table_person" )->getobj ( $key );
			if($bio)
			{
				
				$bio->print_list_row();
			}
			else {
				
				if($g_debug)
				{
					echo("<H1>$key NOT FOUND</H1>");
				}
				
			}
		}
	}
}



?>

