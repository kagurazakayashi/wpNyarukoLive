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
		if (stristr($useragent,"iPhone") || stristr($useragent,"ios")) {
			$liveplayermode = 3;
		} else {
			$liveplayermode = 1;
		}
	}
	// echo '<script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'/eruda.js"></script><script>eruda.init();</script>';
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
	echo '<script>var nyarukolive_config={"pagetype":'.$pagetype.',"pageid":'.$livepageid.',"mode":'.$liveplayermode.',"pluginurl":"'.NYARUKOLIVE_PLUGIN_URL.'"';
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
		<td width="50" align="center"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-live_tv-24px.svg" /></td>
		<td align="left"><?php if (isset($attr["title"])) { echo $attr["title"]; } else { echo "L&nbsp;I&nbsp;V&nbsp;E"; } ?></td>
		<td id="nyarukolive_rtoolbox1" align="right"<?php 
		$showworldtime = (isset($attr["timezone"]) && isset($attr["zonename"]));
		if (!$showworldtime) echo ' style="width:70px;"';
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
				</tr>
			</tbody>
			</table>
		</td>
		</tr>
	</tbody>
	</table>
	<div id="nyarukolive_videobox">
		<video id="nyarukolive_video" class="video-js" x-webkit-airplay="allow" poster="" webkit-playsinline playsinline x5-video-player-type="h5" x5-video-player-fullscreen="true" preload="auto" style="width:100%;height:100%;position:relative;">
		<source id="nyarukolive_videosrc" src="" type="application/x-mpegURL">
		</video>
		<div id="nyarukolive_danmubox">弹幕预留位置</div>
		<div id="nyarukolive_pausebox" onclick="nyarukolive_playpausebtn();">
			<img id="nyarukolive_playbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline_play_circle_outline_white_48dp.png" alt="点击播放" />
		</div>
		<table id="nyarukolive_menu" width="100%">
		<tbody>
			<tr>
			<td>播放器设置</td>
			<td align="right"><a href="javascript:swmenu();" title="关闭设置菜单"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-close-24px.svg" alt="关" /></a></td>
			</tr>
			<tr>
			<td>线路</td>
			<td>
				<select>
					<option>默认线路</option>
				</select>
			</td>
			</tr>
			<tr>
			<td>分辨率</td>
			<td>
				<select>
					<option>原画</option>
				</select>
			</td>
			</tr>
			<tr>
			<td>传输方式</td>
			<td>
				<select onchange="changemodebtn(this.value);">
					<option id="nyarukolive_modeopt0" value=0<?php if ($liveplayermode==0) echo " selected"; ?>>Auto</option>
					<option id="nyarukolive_modeopt1" value=1<?php if ($liveplayermode==1) echo " selected"; ?>>RTMP</option>
					<option id="nyarukolive_modeopt2" value=2<?php if ($liveplayermode==2) echo " selected"; ?>>HLS</option>
					<option id="nyarukolive_modeopt3" value=3<?php if ($liveplayermode==3) echo " selected"; ?>>HLS+</option>
				</select>
			</td>
			</tr>
			<tr>
			<td>屏蔽弹幕</td>
			<td>
				<select>
					<option>不屏蔽</option>
					<option>屏蔽所有</option>
				</select>
			</td>
			</tr>
		</tbody>
		</table>
	</div>
	<table id="nyarukolive_footbar" border="0">
	<tbody>
		<tr>
		<td width="20">
			<a id="nyarukolive_btnplay" href="javascript:nyarukolive_playpausebtn();" title="播放/暂停"><img id="nyarukolive_btnplayi" class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-play_arrow-24px.svg" src2="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-play_arrow-24px.svg" src3="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-pause-24px.svg" /></a>
		</td>
		<td width="80"><input name="textfield" class="w100" type="text" id="nyarukolive_danmunick" value="昵称" maxlength="20"></td>
		<td><input name="textfield2" class="w100" type="text" id="nyarukolive_danmuchat" value="输入实时评论..." maxlength="100"></td>
		<td width="80" align="right">
			<a id="nyarukolive_btndanmusent" href="javascript:;" title="发送弹幕"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-send-24px.svg" alt="发"/></a>
			<a id="nyarukolive_btnsetting" href="javascript:swmenu();" title="播放器设置"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-settings-20px.svg" alt="设" /></a>
			<a id="nyarukolive_btnfullscreen" href="javascript:;" onclick="fullScreen();" title="全屏幕"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-fullscreen-24px.svg" alt="全" /></a>
		</td>
		</tr>
	</tbody>
	</table>
	</div>
	<?php
	echo '<script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'livescript.js"></script>';
}
add_shortcode('nyarukolive', 'nyarukoLiveShortcode');