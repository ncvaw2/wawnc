<?php
include_once  $root.'/obj/survey.php';

class election
{
    //columns
    public $key;
    public $year;
    public $type;
    public $district;
    public $chamber;
    public $party;
    public $nameonballot;
    public $endorsements;
    function printbio()
    {
        echo("<table><tr><td/><td/></tr>");


        echo ('</table>');
    }

    public function get_local_page_url() {
        $url="/v2/bio.php?key=$this->key";
        $leg=get_table("table_office")->get_leg_by_key($this->key);
        if($leg)
        {
            $url="/guide/legpage.php?id=$this->key";

        }
        return $url;
    }

    function get_name_link($show_party)
    {
        $key=$this->key;
        $link_string="";
        $legs=get_table("table_office");
        $party="";
        $survey="";
        $endorse="";
        if($this->endorsements=='Y')
        {
            $endorse="<img title='Endorsed by NCVAW' class='endorsesmall' src='img/endorse_small.png'>";

        }
        if(get_table ( "table_survey" )->check($key))
        {
            $survey="<span style='font-size:small'>(survey)</span>";
        }


        if($show_party){
            $party="<span style='font-size:small'>($this->party)</span>";


        }
        $grade="";

        $leg=$legs->get_leg_by_key($key);
        if($leg)
        {
            $leg=get_table("table_person")->getobj($key);
            $f='normal';
            $c=get_grade_color($leg->grade,$f);
            $grade="<span style='font-weight:$f;color:" .toColor($c) . "'>" . $leg->grade . "</span>";

            $link_string.="<div><a href='/bio/$key'>$this->nameonballot $party $grade $survey $endorse</a></div>";

        }
        else
            $link_string.="<div><a href='/bio/$key'>$this->nameonballot $party $grade $survey $endorse</a></div>";

        return $link_string;

    }
}
class table_election  extends table_base
{
    function get_columns()
    {
        return ['key','year','type','district','chamber','party','nameonballot','endorsements'];
    }
    function create_from_spreadsheet()
    {
        $this->create('data_v2','oi0q51k','election','key');
    }

    public function print_list($year,$type) {
        global $g_debug;
        $g_debug=true;
        $biolist = get_table ( "table_person" );

        echo "<div class='tbl_leglist' >";
        foreach ( $this->list as $d )
        {
            if($d->year != $year)
                continue;
            if($d->type != $type)
                continue;
            $key=$d->key;
            $bio=	$biolist->getobj ( $key );
            if($bio)
                $bio->print_list_row ();
            else
            {
                if($g_debug)
                    echo "<H1>Could not find $key</H1>";
            }

        }
        echo '</div>';
    }

    function printtable()
    {
        $column="key";
        foreach ($this->list as $row )
        {
            $val=$row->$column;
            echo($val);
        }
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
    public function is_running($year,$type,$key)
    {
        foreach ( $this->list as $c ) {
            if($type && ($c->type!=$type))
                continue;
            if(($year == $c->year)&&($key == $c->key))
                return true;

        }
        return false;
    }
    public function getlist($ch,$district,$year,$type,$party=false,$key=false)
    {
        $set=array();
        foreach ( $this->list as $c )
        {
            if($district && ($c->district!=$district))
                continue;
            if($ch && ($c->chamber!=$ch))
                continue;
            if($type && ($c->type!=$type))
                continue;
            if($year && ($c->year!=$year))
                continue;
            if($party && ($c->party!=$party))
                continue;
            if($key && ($c->key!=$key))
                continue;
            $set [] =$c;
        }
        return $set;
    }
    public function print_people($ch,$num,$year,$type)
    {

        $set=$this->getlist($ch,$num,$year,$type);
        foreach ( $set as $x )
        {
            $leg=get_table("table_office")->get_leg_by_key($x->key);
            if($leg)
            {
                $leg->print_list_row();

            }
            else
            {
                $person = get_table ( "table_person" )->getobj ( $x->key );
                $person->print_list_row();
            }


        }
    }
    public function print_endorse($ch,$num,$year,$type)
    {

        $set=$this->getlist($ch,$num,$year,$type);
        foreach ( $set as $x )
        {
            $leg=get_table("table_office")->get_leg_by_key($x->key);
            if($leg)
            {
                $leg->print_short_bio();

            }
            else
            {
                $person = get_table ( "table_person" )->getobj ( $x->key );
                $person->print_short_bio();
            }


        }
    }


    public function get_endorsements($ch,$num,$year,$type)
    {
        $link_string="";
        $set=$this->getlist($ch,$num,$year,$type,"REP");
        foreach ( $set as $x )
        {
            $link_string .= print_name($x,true);


        }
        if(count($set)==1)
        {
            $link_string.="<div>(uncontested)</div>";

        }
        return $link_string;
    }
    public function get_candate_links($ch,$num,$year,$type)
    {
        $link_string="";
        $set=$this->getlist($ch,$num,$year,$type);
        $legs=get_table("table_office");
        foreach ( $set as $x )
        {
            $grade="";
            $endorse="";
            $leg=$legs->get_leg_by_key($x->key);
            if($x->endorsements=='Y')
            {
                $endorse="<img class='endorsesmall' src='img/endorse_small.png'>";

            }
            if($leg)
            {
                $grade="<span style='font-weight:bold;color:" .$leg->grade_color . "'>" . $leg->grade . $endorse . "</span>";

                $link_string.="<div>$x->party: <a href='/bio/$x->key'>$x->nameonballot $grade</a></div>";

            }
            else
                $link_string.="<div>$x->party: <a href='/bio/$x->key'>$x->nameonballot $grade </a>$endorse</div>";


        }
        if(count($set)==1)
        {
            $link_string.="<div>(uncontested)</div>";

        }
        return $link_string;
    }

}


?>

