<?php
function nyarukoliveOptionsPageInit() {
	add_management_page('直播设置', 'wpNyaruko 直播选项', 'manage_options', NYARUKOLIVE_TEXT_DOMAIN.'-options', 'nyarukoliveOptionsPage');
}
add_action('admin_menu', 'nyarukoliveOptionsPageInit');
function nyarukoliveGetOptions() {
	$wpNyarukoLiveOption = get_option('nyarukoliveGetOptions');
	if (!is_array($wpNyarukoLiveOption)) {
		$wpNyarukoLiveOption['wpNyarukoTest'] = '此处可以任意填写一些笔记';
	}
	return $wpNyarukoLiveOption;
}
//获得正在进行中的直播
function nyarukoliveNowLive() {
	global $wpdb;
	$dbinfos = $wpdb->get_results("select * from `".$wpdb->prefix."live`;");
	$infos = [];
	$nonetext = "(未知)";
	foreach ($dbinfos as $dbinfo) {
		$info["live_id"] = isset($dbinfo->live_id) ? $dbinfo->live_id : $nonetext;
		$info["action"] = isset($dbinfo->action) ? $dbinfo->action : -1;
		$info["ip"] = isset($dbinfo->ip) ? $dbinfo->ip : $nonetext;
		$info["app"] = isset($dbinfo->app) ? $dbinfo->app : $nonetext;
		$info["appname"] = isset($dbinfo->appname) ? $dbinfo->appname : $nonetext;
		$info["id"] = isset($dbinfo->id) ? $dbinfo->id : $nonetext;
		$info["time"] = isset($dbinfo->time) ? $dbinfo->time : $nonetext;
		$info["usrargs"] = isset($dbinfo->usrargs) ? $dbinfo->usrargs : $nonetext;
		$info["node"] = isset($dbinfo->node) ? $dbinfo->node : $nonetext;
		$info["cmode"] = isset($dbinfo->cmode) ? $dbinfo->cmode : 0;
		array_push($infos,$info);
	}
	$infos = array_reverse($infos);
	foreach ($infos as $info) {
		echo '<tr><th scope="row">'.$info["live_id"].'</th><td>';
		if ($info["action"] == 0) {
			echo '已停播';
		} else if ($info["action"] == 0) {
			echo '推流中';
		}
		if ($info["cmode"] == 1) {
			echo '<br/>(手动视为播放)';
		} else if ($info["cmode"] == 2) {
			echo '<br/>(手动视为停播)';
		}
		echo '</td>';
		echo '<td>'.$info["node"].'</td>';
		echo '<td>'.$info["app"].'</td>';
		echo '<td>'.$info["appname"].'</td>';
		echo '<td>'.$info["id"].'</td>';
		echo '<td>'.$info["ip"].'</td>';
		echo '<td>'.str_replace(" ","<br/>",$info["time"]).'</td>';
		echo '<td><button type="button" onclick="prompt(\'更多额外参数：\',\''.$info["usrargs"].'\');">查看</button></td>';
		echo '<td><select name="cmode" id="cmode" onchange="wpNyarukoOptionCMgLiveMode(this.value,'.$info["live_id"].');"><option value="0"'.nyarukolivecmodeselect($info["cmode"],0).'>接口控制</option><option value="1"'.nyarukolivecmodeselect($info["cmode"],1).'>手动播放</option><option value="2"'.nyarukolivecmodeselect($info["cmode"],2).'>手动停播</option></select></td>';
		echo '<td><button type="button" onclick="wpNyarukoOptionCMgLiveDanmaku('.$info["live_id"].');">管理</button></td>';
		echo '<td><button type="button" onclick="wpNyarukoOptionCMgLiveDelete('.$info["live_id"].');">删除</button></td>';
	}
}
function nyarukolivecmodeselect($cmode,$tmode) {
	if ($cmode == $tmode) return ' selected="selected"';
	return '';
}
function urlb64encode($str) {
	return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}
function urlb64decode($str) {
	$d1 = str_replace(array('-','_'),array('+','/'),$str);
	$d4 = strlen($d1) % 4;
	if ($d4) $d1 .= substr('====', $d4);
	return base64_decode($d1);
}
function nyarukoliveOptionsPage() {
	if (isset($_GET["nyamode"])) {
		wpNyarukoCModeGet($_GET["nyamode"]);
		die('<div id="wpNyarukoInfo">正在应用设置...</div>');
	}
	if (isset($_GET["info"])) echo '<div id="wpNyarukoInfo">'.urlb64decode($_GET["info"]).'</div>';
?>
<div id="optionbox">
	<div id="wpNyarukoOptionTitle"><a title="版本升级日志" class="link" href="https://github.com/kagurazakayashi/wpNyarukoLive/commits/master" target="_blank"><div id="wpNyarukoPanelLogo"></div></a>&nbsp;视频直播（版本&nbsp;0.1）</div><hr>
	<!-- <form action="#" method="post" enctype="multipart/form-data" name="op_form" id="op_form">
		<table border="0" cellspacing="0" cellpadding="10">
		<tbody>
		<tr>
			<td>笔记(不呈现)</td>
			<td><input name="wpNyarukoTest" type="text" id="wpNyarukoTest" value="" size=64 maxlength=128 /></td>
			<td>信息：</td>
			<td>此插件目前不可用。</td>
		</tr>
		</tbody>
		</table>
		<hr><div id="wpNyarukoOptionBtnBar"><p><input id="submitoption" type="submit" name="nyarukolive_input_save" value="应用这些设定" /></p></div>
	</form>
	<hr> -->
	<div id="wpNyarukoOptionMenuBar">
		<p><div class="wpNyarukoOptionMenuBarItem" id="wpNyarukoOptionMenuBarItem1" onclick="wpNyarukoOptionChTab(1);">推流记录</div><div class="wpNyarukoOptionMenuBarItem" id="wpNyarukoOptionMenuBarItem2" onclick="wpNyarukoOptionChTab(2);">弹幕管理</div><div class="wpNyarukoOptionMenuBarItem" id="wpNyarukoOptionMenuBarItem3" onclick="wpNyarukoOptionChTab(3);">权限设置</div></p>
	</div>
	<div class="wpNyarukoOptionTab" id="wpNyarukoOptionTab1">
		<h2>推流记录</h2>
		<p>在回调接口收到参数以后，将会记录在这里。</p>
		<table width="100%" border="1" cellspacing="1" cellpadding="1" class="wpNyarukoOptionInfotable">
		<tbody>
			<tr>
			<th scope="col">序号</th>
			<th scope="col">状态</th>
			<th scope="col">节点</th>
			<th scope="col">资源</th>
			<th scope="col">应用</th>
			<th scope="col">名称</th>
			<th scope="col">推流IP</th>
			<th scope="col">更新时间</th>
			<th scope="col">参数</th>
			<th scope="col">播放控制</th>
			<th scope="col">弹幕</th>
			<th scope="col">记录</th>
			</tr>
			<?php nyarukoliveNowLive(); ?>
		</tbody>
		</table>
	</div>
	<div class="wpNyarukoOptionTab" id="wpNyarukoOptionTab2">
		<h2>弹幕管理</h2>
		<p>在「推流记录」中选择一个直播流后面的「弹幕管理」。</p>
	</div>
	<div class="wpNyarukoOptionTab" id="wpNyarukoOptionTab3">
		<h2>IP 地址屏蔽</h2>
		<p>以下设置同时生效于直播播放和弹幕。</p>
	</div>
</div>
<script>wpNyarukoOptionChTab(0,true);</script>
<?php
}
function wpNyarukoCModeGet($nyamode) {
	global $wpdb;
	$alertinfo = "已受理您的变更。";
	if ($nyamode == "mglive" && isset($_GET["live_id"]) && isset($_GET["cmode"])) {
		$live_id = intval($_GET["live_id"]);
		$cmode = intval($_GET["cmode"]);
		//UPDATE `racing_live` SET `cmode` = '1' WHERE `racing_live`.`live_id` = 16
		$dbinfos = $wpdb->get_results("UPDATE `".$wpdb->prefix."live` SET `cmode`=".$cmode." WHERE `".$wpdb->prefix."live`.`live_id`=".$live_id.";");
		$alertinfo = "将 ".$live_id." 号直播间播放状态设置为 ".$cmode;
	}
	echo "<script>window.location.href = 'tools.php?page=nyarukolive-options&info=".urlb64encode($alertinfo)."';</script>";
}
?>