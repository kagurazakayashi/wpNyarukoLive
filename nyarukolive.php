<?php
/**
 * @package wpNyarukoLive
 * @version 0.1
 */
/*
Plugin Name: wpNyarukoLive
Plugin URI: https://github.com/kagurazakayashi/wpNyarukoLive
Description: wpNyaruko 视频直播
Version: 0.1
Author: 神楽坂雅詩
Author URI: https://github.com/kagurazakayashi
Text Domain: wpNyarukoLive
*/
define("NYARUKOLIVE_PLUGIN_URL", plugin_dir_url( __FILE__ ));
define("NYARUKOLIVE_FULL_DIR", plugin_dir_path( __FILE__ ));
define("NYARUKOLIVE_TEXT_DOMAIN", "nyarukolive");
include_once NYARUKOLIVE_FULL_DIR."options.php";
function nyarukoLiveInit() {

}
function nyarukoLiveHead() {
    $plugindir = plugins_url('',__FILE__);
    echo '<link href="'.NYARUKOLIVE_PLUGIN_URL.'/style.css" rel="stylesheet">';
	echo '<script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'/script.js"></script>';
	echo '<style>#wpNyarukoPanelLogo{background-image:url("'.NYARUKOLIVE_PLUGIN_URL.'/wpNyaruko.gif");}#wpNyarukoPanelLogo:hover{background-image:url("'.NYARUKOLIVE_PLUGIN_URL.'/wpNyaruko2.gif");}</style>';
}
add_action("admin_head","nyarukoLiveHead");
add_action("admin_notices","nyarukoLiveInit");
function nyarukoLiveAdminlink($links){
	$links[] = '<a href="'.get_admin_url(null, 'tools.php?page='.NYARUKOLIVE_TEXT_DOMAIN.'-options').'">直播设置</a>';
	return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'nyarukoLiveAdminlink');
function nyarukoLiveShortcode($attr, $content) {
	$pagetype = "1";
	if (isset($_GET["page_id"])) {
		$pagetype = "2";
	}
	echo '<script>var pagetype='.$pagetype.';';
	echo 'var pageid='.get_the_ID().';';
	foreach($attr as $k => $v){
		echo 'var '.$k.'="'.$v.'";';
	}
	echo "</script>";
}
add_shortcode('nyarukolive', 'nyarukoLiveShortcode');