function ncvaw_init_your_district_menu(cookie,menutext,id,ch) {
	
	var dist = getCookie(cookie);

	if (dist) {
		var i=$(id);
		i.removeClass('zs_menu_hide');
		i=$(id+">a");
		i.text(menutext + dist);
		i.attr('href',
				'/district.php?ch='+ch+'&dist=' + dist);
	} else {

		$(id).addClass('zs_menu_hide');
	}	
}

function ncvaw_init_your_district_menus() {

	ncvaw_init_your_district_menu('dist_state_senate','Your Senate District #','#z_mi_dist_senate','S')
	ncvaw_init_your_district_menu('dist_state_house','Your House District #','#z_mi_dist_house','H')
	

}
function name_filter_chage() {

	var a=$("#namefilter").val();
	a=a.toLowerCase();
	
	var select="[data-name*='"+a+ "']";
	if(a == "")
		$("div.leg_bio").show();
	else
		{
		
			$("div.leg_bio"+select).show();	
			$("div.leg_bio:not("+select+")").hide();
		}
}

function ncvaw_init() {

	ncvaw_init_your_district_menus();
}

function setCookie(c_name, value) {
	var exdays = 999;
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value = escape(value)
			+ ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
	document.cookie = c_name + "=" + c_value+"; path=/";
	if(console)
		{
		console.log("setcookie %s %s",c_name,value);
		console.log(" document.cookie %s", document.cookie);			
		}
}
function getCookie(c_name) 
{
	var val=null;
	var i, x, y, ARRcookies = document.cookie.split(";");
	for (i = 0; i < ARRcookies.length; i++) 
	{
		x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
		y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
		x = x.replace(/^\s+|\s+$/g, "");
		if (x == c_name) {
			var val = unescape(y);
			if (val == 'false')
				val = 0;
			if (val == 'true')
				val = 1;
			break;
		}
	}
	if(console)
		{
		console.log("getCookie %s %s",c_name,val);		
		console.log(" document.cookie %s", document.cookie);		
		}

	return val;
}
function call_zipo(request) {
	var url;
	var ref = document.referrer;
	var refs = ref.split('?');
	if(window.location.hostname=='localhost')
		return;
	url = "http://www.zipobase.com/cgi-bin/zb.cgi?T=logger&db=ncvaw&requested="
			+ escape(document.URL) + "&referer=" + escape(refs[0]);
	$.get(url);
	return;
}
function tabclick(tabselected) {

    tabselect(tabselected);
}
function tabselect(tabselected) {

	$('.tab').removeClass('tab_slected');
	$('.tabbody').css('display','none');
	$('#'+tabselected).css('display','block');
	$('#'+tabselected+'_top').addClass('tab_slected');
}
function togglehide(id) {
	var e=$('#'+id);
	if(e.css('display') =='none')
		e.css('display','block');
	else
		e.css('display','none');
}
function tabinit() {
    var tab=QueryString["tab"];

    //var hash = location.hash;
    if (tab) {
        //hash = hash.substr(1);
            tabselect(tab);

    }

    else {

    if ($('#tab_news_top').length)
        tabselect('tab_news');
    else if ($('#tab_survey16_top').length)
        tabselect('tab_survey16');
    else if ($('#tab_votes_top').length)
        tabselect('tab_votes');
    }
}
/*
var drag_startX = 0;
function dragstart(ev) {
	// ev.dataTransfer.setData("Text",ev.target.id);

	drag_startX = ev.offsetX;

	if (console)
		console.log("offsetX %d Y %d", ev.offsetX, ev.offsetY);
}

function dragend(ev) {
	var offset = Math.abs(drag_startX - ev.offsetX);
	var perc = offset * 100 / screen.width;

	// ev.dataTransfer.setData("Text",ev.target.id);
	if (console)
		console.log("offsetX %d  %d", offset, perc);
	if (perc > 20) {

		window.location = "/"
	}
}


*/
var QueryString = function() {
    // This function is anonymous, is executed immediately and
    // the return value is assigned to QueryString!
    var query_string = {};
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        // If first entry with this name
        if (typeof query_string[pair[0]] === "undefined") {
            query_string[pair[0]] = pair[1];
            // If second entry with this name
        } else if (typeof query_string[pair[0]] === "string") {
            var arr = [ query_string[pair[0]], pair[1] ];
            query_string[pair[0]] = arr;
            // If third or later entry with this name
        } else {
            query_string[pair[0]].push(pair[1]);
        }
    }

    return query_string;
}();