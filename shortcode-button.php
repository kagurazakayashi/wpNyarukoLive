<?php
/*

**************************************************************************

Plugin Name:  Shortcode Button
Plugin URI:   http://www.arefly.com/shortcode-button/
Description:  Add Useful Buttons to your blog simply by shortcode. 在你的部落格中使用短代碼來加入實用的按鈕
Version:      1.1.9
Author:       Arefly
Author URI:   http://www.arefly.com/
Text Domain:  shortcode-button
Domain Path:  /lang/

**************************************************************************

	Copyright 2014  Arefly  (email : eflyjason@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

**************************************************************************/

define("SHORTCODE_BUTTON_PLUGIN_URL", plugin_dir_url( __FILE__ ));
define("SHORTCODE_BUTTON_FULL_DIR", plugin_dir_path( __FILE__ ));
define("SHORTCODE_BUTTON_TEXT_DOMAIN", "shortcode-button");

/* Plugin Localize */
function shortcode_button_load_plugin_textdomain() {
	load_plugin_textdomain(SHORTCODE_BUTTON_TEXT_DOMAIN, false, dirname(plugin_basename( __FILE__ )).'/lang/');
}
add_action('plugins_loaded', 'shortcode_button_load_plugin_textdomain');

include_once SHORTCODE_BUTTON_FULL_DIR."help.php";

/* Add Links to Plugins Management Page */
function shortcode_button_action_links($links){
	$links[] = '<a href="'.get_admin_url(null, 'tools.php?page='.SHORTCODE_BUTTON_TEXT_DOMAIN.'-help').'">'.__("Help", SHORTCODE_BUTTON_TEXT_DOMAIN).'</a>';
	return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'shortcode_button_action_links');

function shortcode_button_enqueue_styles(){
	wp_enqueue_style(SHORTCODE_BUTTON_TEXT_DOMAIN, SHORTCODE_BUTTON_PLUGIN_URL.'style.min.css');
}
add_action('wp_enqueue_scripts', 'shortcode_button_enqueue_styles');
add_action('admin_enqueue_scripts', 'shortcode_button_enqueue_styles');

if(!function_exists('shortcode_button')){
	function shortcode_button($atts, $content = null){
		$content = trim(do_shortcode(shortcode_unautop($content)));
		extract(shortcode_atts(array("mode" => 'link', "href" => 'http://'), $atts));
		switch ($mode) {
			case 'down':
				return '<span class="but-down"><a href="'.$href.'" target="_blank"><span>'.$content.'</span></a></span>';
			break;

			case 'heart':
				return '<span class="but-heart"><a href="'.$href.'" target="_blank"><span>'.$content.'</span></a></span>';
			break;

			case 'link':
				return '<span class="but-link"><a href="'.$href.'" target="_blank"><span>'.$content.'</span></a></span>';
			break;

			case 'doc':
				return '<span class="but-document"><a href="'.$href.'" target="_blank"><span>'.$content.'</span></a></span>';
			break;

			default:
				return '<a href="'.$href.'" target="_blank">'.$content.'</a>';
			break;
		}
	}
}
add_shortcode('button', 'shortcode_button');
