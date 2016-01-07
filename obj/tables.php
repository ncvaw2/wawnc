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
 $g_table_list=array(

		'survey_data',
		'candidates',
		'bill_list',
		'exlinks',
		'districts',
		'table_office',
		'bill_list',
		'survey_questions',
		'table_election',
		'table_office',
		'table_person',
		'vote_data',
);

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
	global $g_offline;
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
				if($g_offline)
					$table->create_offline();
				else
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
	return trim($row->{	'gsx$'.$id }->{'$t' });
}
function create_obj_from_json($obj,$data,$vars)
{
	foreach ($vars as $var )
	{
		$obj->$var=getj($data,$var);
	}

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
	function create_from_spreadsheet()	{	}
	function create_offline(){}
	function get_columns(){	}	
	function getobj($key)
	{
		if(array_key_exists ($key,$this->list))
			return  $this->list[$key];
		return null;
		
	}
	function create1($sheet_name,$tab,$objname,$keyname=null)
	{
		
		$jdata=$this->get_json_data($sheet_name,$tab);
		$this->create_from_json($jdata,$objname,$keyname);
	}
	function create($sheet_name,$tab,$objname,$keyname=null)
	{
	
		$jdata=$this->get_json_data($sheet_name,$tab);
		$this->create_from_json2($jdata,$objname,$keyname);
	}	
	function create_from_json($jdata,$objname,$keyname)
	{
	
		$index=2;
		foreach ($jdata as $row )
		{
			$obj=new $objname ( $row,$index );
			if(method_exists($obj,'pdata_init'))
				$obj->pdata_init();			
			if($keyname)
			{
				$key =getj($row,$keyname);
				$this->list [$key] = $obj;
			}
			else
				$this->list [] = $obj;
	
			$index++;
		}
	}
		
	function create_from_json2($jdata,$objname,$keyname)
	{

		$index=2;
		foreach ($jdata as $row )
		{
			$obj=new $objname();
			$columns=$this->get_columns();
			create_obj_from_json($obj,$row,$columns);
			
			if(method_exists($obj,'pdata_init'))
				$obj->pdata_init();
			if($keyname)
			{
				$key =getj($row,$keyname);
				$this->list [$key] = $obj;
			}
			else
				$this->list [] = $obj;
	
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
		
		$obj=reset($this->list);
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
	
	function output_csv_file($fileName,$columns)
	{
		//$columns=$this->get_columns();
		// output headers so that the file is downloaded rather than displayed

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$fileName}");
		header("Expires: 0");
		header("Pragma: public");		
		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');
	
	
		fputcsv($output, $columns);
		foreach ($this->list as $row )
		{
			$csv_row=[];
			foreach ($columns as $col )
			{
				$val=$row->$col;
				$csv_row[]=$row->$col;
			}
			fputcsv($output, $csv_row);
		}
	}	
}




?>

