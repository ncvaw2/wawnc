
(function(zs, window, document, undefined) {
	var menu = zs.menu = zs.menu
			|| (function() {
				var option_set_callback = 0;
				var options = {};
				function show(elm, ev) {

					var lists = elm.getElementsByTagName('ul');
					if (lists.length > 0) {
						if (lists[0].className == "zs_menu_hide") {
							lists[0].className = "zs_menu_show";
							return;
						}
						if (lists[0].className == "zs_menu_bar zs_mb_hide")
							lists[0].className = "zs_menu_bar zs_mb_show";

					}
				}
				function hide(elm, ev) {

					if ((ev.target.className == 'zs_menutop')
							|| (ev.target.className == 'zs_menu_show')
							|| (ev.target.className == 'zs_menu_sub')
							|| (ev.target.className == 'zs_menu_item')
							|| (ev.target.className == 'zs_menu_all')
							|| (ev.target.className == 'zs_menu_link')
							|| (ev.target.className == 'zs_menu_content')) {
						var lists = elm.getElementsByTagName('ul');

						if (lists.length > 0) {
							if (lists[0].className == "zs_menu_bar zs_mb_show") {
								lists[0].className = "zs_menu_bar zs_mb_hide";
								return;
							}
							if (lists[0].className == "zs_menu_show") {
								lists[0].className = "zs_menu_hide";
								return;
							}
						} else {
							if (console)
								console.log("hide %s %s", ev.target,
										ev.target.className);
						}
					}
				}

				function hide_sub(elm, ev) {
					// elm.className = 'zs_menu_item_sub';
					hide(elm, ev);
				}
				function show_sub(elm, ev) {
					show(elm, ev);
					/*
					 * var lists = elm.getElementsByTagName('ul'); if
					 * (lists.length > 0) lists[0].style.display = "inline";
					 * lists = elm.getElementsByTagName('a'); if (lists.length >
					 * 0) lists[0].style.display = "inline-block";
					 */

				}
				function show_hide_elm(elm_id, val) {
					document.getElementById(elm_id).style.display = (val ? 'block'
							: 'none');
				}
				function show_hide_svg(elm_id, val) {
					document.getElementById(elm_id).setAttribute("visibility",
							(val ? 'visible' : 'hidden'));
				}
				function valset_bool(elm, val) {
					if (val == 'false')
						val = 0;
					elm.firstElementChild.style.display = (val ? "inline"
							: "none");
				}
				function valset_opt_sel(elm, val) {
					var opt = jQuery(elm).find("a[data-mi-val=" + val + "]");
					if (opt.length > 0) {
						elm.firstElementChild.innerHTML = opt[0].innerHTML;
					}

				}

				function onclk_bool(elm) {
					var boolval = ((elm.firstElementChild.style.display == "inline") ? 0
							: 1);
					valset_bool(elm, boolval);
					if (console)
						console.log("boolval=" + boolval);

					if (this.option_set_callback)
						this.option_set_callback(elm.id, boolval);
				}
				function onclk_opt(elm_opt) {
					var val = elm_opt.getAttribute("data-mi-val");
					var p = $(elm_opt).parents("[data-mi-type= 'opt_sel']")[0];
					if (p) {
						valset_opt_sel(p, val);

						if (this.option_set_callback)
							this.option_set_callback(p.id, val);
					}
				}
				return {
					option_set_callback : option_set_callback,
					show_hide_svg : show_hide_svg,
					show_hide_elm : show_hide_elm,
					valset_opt_sel : valset_opt_sel,
					valset_bool : valset_bool,
					onclk_bool : onclk_bool,
					onclk_opt : onclk_opt,
					hide_sub : hide_sub,
					show_sub : show_sub,
					options : options,
					hide : hide,
					show : show

				}
			}());
}(window.zs = window.zs || {}, window, document));
