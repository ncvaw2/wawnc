<?php
include_once  $root.'/obj/tables.php';
include_once  $root.'/obj/person.php';

$surveyq  =  [
    "Do you think NC needs to pass a law to regulate commercial dog breeding facilities? If so, will you be a sponsor or co-sponsor for a bill like this?",
    "In NC it is a felony to fight birds, but it is not illegal to possess the birds or the paraphernalia to fight the birds. This makes it difficult to shut down cock fighting rings because animal control must catch them in the act. Do you support a bill to make it a crime to possess the birds and paraphernalia with the intent to fight them?",
    "NC does not currently have a definition for adequate housing in place for companion animals living outside. Would you support legislation to add a definition to our laws?",
    "Do you support a bill that would ban private ownership of exotics such as big cats, bears, and primates kept as pets? This question is not geared towards conservation efforts by accredited organizations or rescue/rehabilitation groups.",
    "How do you feel about transparency when it comes to NC animal shelters?&nbsp; Do you support mandatory reporting for all facilities when it comes to reporting the disposition of all animals that enter the NC shelter system?",
    "If you have held a political seat before, what have you done to help animals in your district?",
    "Do you have any pets? We are requesting this information because we have received excellent feedback when we profile legislators through social networking and we would like to add a personal touch.",
];


class survey2_resp
{
    public $key;
    public function pdata_init()
    {

    }
    public function printresp()
    {
        global $surveyq;


        for ($x=0; $x<7; $x++)
        {
            $qnum=$x+1;
            $qvar='a'. $qnum;


            $q=$surveyq[$x];
            if(!$q)
                $q="could not get question";
            $a=$this->$qvar;
            if(!$a)
                $a="[blank]";
            echo("<div style='margin-top:30px' class='section_head'>Question #$qnum</div>");
            echo("<div>$surveyq[$x]</div>");
            echo("<div style='margin-top:10px' class='section_head'>Answer:</div>");
            echo("<div>$a</div>");
        }


    }
}

class table_survey extends table_base
{
    function get_columns()
    {
        return ['key','name','org','email','chamber','dist','a1','a2','a3','a4','a5','a6','a7'];
    }
    function create_from_spreadsheet()
    {
        $this->create('data_v2','oal5trp','survey2_resp','key');
    }

    public function check($key)
    {
        return (array_key_exists ($key,$this->list));

    }

    public function printresp($key)
    {
        echo("<div  style='max-width:800px'><h3>Responses to 2016 animal welfare survey:</h3>");
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