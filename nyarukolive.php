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
	//0.AUTO 1FLV 2HLS 3HLS+
	$liveplayermode = 0;
	if (isset($_GET["liveplayermode"])) {
		$liveplayermode = intval($_GET["liveplayermode"]);
	}
	if ($liveplayermode == 0) {
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (stristr($useragent,"ios")) {
			$liveplayermode = 3;
		} else {
			$liveplayermode = 1;
		}
	}
	if ($liveplayermode == 1) {
		echo '<script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'lib/flv.min.js"></script>';
	} else if ($liveplayermode == 2) {
		echo '<script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'lib/hls.min.js"></script>';
	} else if ($liveplayermode == 3) {
		echo '<link href="'.NYARUKOLIVE_PLUGIN_URL.'/lib/video.css" rel="stylesheet">';
		echo '<script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'lib/video.min.js"></script>';
		echo '<script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'lib/videojs-contrib-hls.js"></script>';
	}
	echo '<link href="'.NYARUKOLIVE_PLUGIN_URL.'livestyle.css" rel="stylesheet">';
	$nyarukoLivePlayerCssLoaded = true;
	$pagetype = "1";
	if (isset($_GET["page_id"])) {
		$pagetype = "2";
	}
	$livepageid = get_the_ID();
	echo '<script>var nyarukolive_config={"pagetype":'.$pagetype.',"pageid":'.$livepageid.',"mode":'.$liveplayermode;
	foreach($attr as $k => $v){
		echo ',"'.$k.'":"'.$v.'"';
	}
	echo "};</script>";
	?>
	<br/>
	<div id="nyarukolive">
	<table id="nyarukolive_titlebar" border="0">
	<tbody>
		<tr>
		<td align="left"><marquee><?php if (isset($attr["title"])) { echo $attr["title"]; } else { echo "L&nbsp;I&nbsp;V&nbsp;E"; } ?></marquee></td>
		<td id="nyarukolive_rtoolbox1" align="right"<?php 
		$showworldtime = (isset($attr["timezone"]) && isset($attr["zonename"]));
		if (!$showworldtime) echo ' style="width:170px;"';
		?>>
			<table id="nyarukolive_rtoolbox" border="0" cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
				<td>本地时间<br/><span id="nyarukolive_ltime" class="timev">--:--:--</span></td>
				<?php 
				if ($showworldtime) {
					echo '<td>'.$attr["zonename"].'<br/><span id="nyarukolive_wtime" class="timev">--:--:--</span></td>';
				}
				?>
				<td>传输方式<br/>
					<select onchange="changemodebtn(this.value);">
						<option id="nyarukolive_modeopt0" value=0<?php if ($liveplayermode==0) echo " selected"; ?>>Auto</option>
						<option id="nyarukolive_modeopt1" value=1<?php if ($liveplayermode==1) echo " selected"; ?>>RTMP</option>
						<option id="nyarukolive_modeopt2" value=2<?php if ($liveplayermode==2) echo " selected"; ?>>HLS</option>
						<option id="nyarukolive_modeopt3" value=3<?php if ($liveplayermode==3) echo " selected"; ?>>HLS+</option>
					</select></td>
				</tr>
			</tbody>
			</table>
		</td>
		</tr>
	</tbody>
	</table>
	<div id="nyarukolive_videobox">
		<video id="nyarukolive_video" class="video-js" x-webkit-airplay="allow" poster="" webkit-playsinline playsinline x5-video-player-type="h5" x5-video-player-fullscreen="true" preload="auto" controls="controls" style="width:100%;height:100%;position:relative;">
		<source id="nyarukolive_videosrc" src="" type="application/x-mpegURL">
		</video>
		<div id="nyarukolive_danmubox">弹幕预留位置</div>
		<div id="nyarukolive_pausebox" onclick="nyarukolive_playpausebtn();">
			<img id="nyarukolive_playbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline_play_circle_outline_white_48dp.png" alt="点击播放" />
		</div>
	</div>
	</div>
	<?php
	echo '<script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'livescript.js"></script>';
}
add_shortcode('nyarukolive', 'nyarukoLiveShortcode');