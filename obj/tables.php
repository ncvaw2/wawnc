<?php
/*
 * bill_list - tab 1- 
 * canidates - tab 2-
 * leg_list  - tab 3-
 * districts - tab 4
 * vote_data - tab 5-
 * links 	 - tab 6-
 * 
 * 
 * 
 */


function get_member_vars($classname)
{
	$rtn=array();
	$vars=get_class_vars ($classname);
	foreach ($vars as $varname => $val )
	{
		$rtn[]=$varname;
	}
	return $rtn;
}


$g_table_array=array();

function get_table($type) {
	global $root;
	global $g_table_array;
	global $g_refresh_data;
	$table=null;

	if(!array_key_exists($type,$g_table_array))
	{
		if (class_exists($type)) 
		{
			$filename=$root."/data2/$type.pdata";
			if( file_exists ( $filename ) && (!$g_refresh_data))
			{
				$data = file_get_contents($filename);
				$table = unserialize($data);
			}
			else {
				$table=new $type();
				$table->create_from_spreadsheet();
				$data=serialize($table);
				file_put_contents($filename, $data);				
			}
			$g_table_array[$type]=$table;
            
		}
		else {
			throw new Exception("unknown class:" .$type );
		}
	}
    
	return $g_table_array[$type];
}



function getj(&$row,$id)
{
	return $row->{	'gsx$'.$id }->{'$t' };
}
/*
 to get the feed id's 
 
 https://spreadsheets.google.com/feeds/worksheets/1B7d66Ggzayqzw2W2uu_n1Fa12uF9kwh4prhNvLUcxSc/public/basic
 
  
 */
$g_spreadsheets=array(

	'data_v1'=>'0AonA9tFgf4zjdHhNd1FIeFJzVWRrdDlUangxWUlkTXc',
	'data_v2'=>'1B7d66Ggzayqzw2W2uu_n1Fa12uF9kwh4prhNvLUcxSc',
	'survey1' => '0AonA9tFgf4zjdE45M0MyZTR0UUYxXzNzRjBuNWFnMGc'
	
);

class table_base
{

	public $list;
	function create_from_spreadsheet()	
	{
	
	}
	function get_columns()
	{
	
	}	
	function get_row($key)
	{
		if(array_key_exists ($key,$this->list))
			return  $this->list[$key];
		return null;
		
	}
	function create($sheet_name,$tab,$objname,$keyname=null)
	{
		
		$jdata=$this->get_json_data($sheet_name,$tab);
		$this->create_from_json($jdata,$objname,$keyname);
		
	}
	function create_from_json($jdata,$objname,$keyname)
	{

		$index=2;
		foreach ($jdata as $row )
		{
			
			 
			if($keyname)
			{
				$key =getj($row,$keyname);
				$this->list [$key] = new $objname ( $row,$index );
			}
			else
				$this->list [] = new $objname ( $row ,$index);
	
			$index++;
		}
	}	
	function get_json_data($sheet_name,$tab)
	{
		global $root;
		global $g_spreadsheets;
		global $g_refresh_data;
		$refresh_data=$g_refresh_data;
		
		$this->list=array();
		$cn=get_class($this);
		$filename=$root."/data2/$cn.json";
		
		$spreadsheetid=$g_spreadsheets[$sheet_name];

		//https://spreadsheets.google.com/feeds/list/0AonA9tFgf4zjdHhNd1FIeFJzVWRrdDlUangxWUlkTXc/10/public/values?alt=json"
		$url = "https://spreadsheets.google.com/feeds/list/$spreadsheetid/$tab/public/values?alt=json";
		if(! file_exists ( $filename ))
			$refresh_data=1;
		if($refresh_data)
		{
		
	        $file = file_get_contents ( $url );
			$fp = fopen ( $filename, 'w' );
			fwrite ( $fp, $file );
			fclose ( $fp );
		}
        $json = json_decode (  file_get_contents ( $filename ) );
        
		if($json)
			$jdata = $json->{'feed' }->{'entry' };
		else
		{
			throw new Exception("Could not get JSON data for $cn");
			return;
		}
		return $jdata;
		

	}
	function printtable2()
	{
		if(count($this->list)==0)
			return;
		
		$obj=$this->list[0];
		$vars=get_object_vars($obj);

		
		
		//$columns=$this->get_columns();
		echo "<table class='votes'>";
		echo "<tr>";
		foreach ($vars as $varname => $val )
		{
			
			echo "<th>$varname</th>";
		}
		echo "</tr>";	
		foreach ($this->list as $row )
		{
			echo "<tr>";
			foreach ($vars as $varname => $val )
			{
				$val=$row->$varname;
				echo "<td>$val</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
	function printtable()
	{
		$columns=$this->get_columns();
		echo "<table>";
	
		foreach ($this->list as $row )
		{
			echo "<tr>";
			foreach ($columns as $col )
			{
				$val=$row->$col;
				echo "<td>$val</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
	}
}

class testobj {
	public $key;
	public $legs;
	public function __construct($d,$index) {
		$this->key = getj($d,'key');
		$this->legs=getj($d,"legs");
	}
	function printrow()
	{
		echo "<tr><td>$this->key</td><td>$this->legs</td>";
		
	}
}

class table_test extends table_base
{
	function get_columns()
	{
		return ['key','legs'];
	}	
	function create_from_spreadsheet()
	{
		$this->create('data_v1',10,'testobj');
	}
	function printtable4()
	{
		echo "<table>";
		
		foreach ($this->list as $row )
		{
			$row->printrow();
			
		}
		echo "</table>";
	}
	public function print_list()
	{
		$leglist=get_table("leg_list");
		$canlist=get_table("canidates");


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



?>

