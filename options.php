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
		echo '<tr><th scope="row">'.$info["live_id"].'</th>';
		if ($info["action"] == 0) {
			echo '<td>已停止</td>';
		} else if ($info["action"] == 0) {
			echo '<td>推流中</td>';
		}
		echo '<td>'.$info["node"].'</td>';
		echo '<td>'.$info["app"].'</td>';
		echo '<td>'.$info["appname"].'</td>';
		echo '<td>'.$info["id"].'</td>';
		echo '<td>'.$info["ip"].'</td>';
		echo '<td>'.$info["time"].'</td>';
		echo '<td><a href="javascript:alert(\''.$info["usrargs"].'\')" title="'.$info["usrargs"].'">其他参数</a></td>';
		echo '<td><select name="cmode" id="cmode" onchange="wpNyarukoOptionCCMode(this.value,'.$info["live_id"].');"><option value="0"'.nyarukolivecmodeselect($info["cmode"],0).'>接口控制</option><option value="1"'.nyarukolivecmodeselect($info["cmode"],1).'>手动播放</option><option value="2"'.nyarukolivecmodeselect($info["cmode"],2).'>阻止播放</option></select></td>';
		echo '<td>弹幕管理</td></tr>';
	}
}
function nyarukolivecmodeselect($cmode,$tmode) {
	if ($cmode == $tmode) return ' selected="selected"';
	return '';
}
function nyarukoliveOptionsPage() {
	if (isset($_GET["nyamode"])) {
		echo '<div id="wpNyarukoInfo">'.wpNyarukoCModeGet($_GET["nyamode"]).'</div>';
	}
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
			<th scope="col">其他参数</th>
			<th scope="col">播放控制</th>
			<th scope="col">弹幕管理</th>
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
<script>wpNyarukoOptionChTab(0);</script>
<?php
}
?>