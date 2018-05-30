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
	//Array ( [0] => stdClass Object ( [live_id] => 16 [action] => 0 [ip] => 192.168.10.1 [app] => dev.futureracing.com.cn [appname] => racing [id] => test [time] => 2018-05-24 15:21:01 [usrargs] => dev.futureracing.com.cn&alilive_streamidv2=p011171148040.nm89_15904_33284929_1527175159343 [node] => nm89 ) )
	$dbinfos = $wpdb->get_results("select * from `".$wpdb->prefix."live`;");
	// print_r($sentences[0]->live_id);
	$infos = [];
	$nonetext = "(未知)";
	foreach ($dbinfos as $dbinfo) {
		$info["live_id"] = isset($dbinfo->live_id) ? $dbinfo->live_id : $nonetext;
		$info["action"] = isset($dbinfo->action) ? $dbinfo->action : $nonetext;
		$info["ip"] = isset($dbinfo->ip) ? $dbinfo->ip : $nonetext;
		$info["app"] = isset($dbinfo->app) ? $dbinfo->app : $nonetext;
		$info["appname"] = isset($dbinfo->appname) ? $dbinfo->appname : $nonetext;
		$info["id"] = isset($dbinfo->id) ? $dbinfo->id : $nonetext;
		$info["time"] = isset($dbinfo->time) ? $dbinfo->time : $nonetext;
		$info["usrargs"] = isset($dbinfo->usrargs) ? $dbinfo->usrargs : $nonetext;
		$info["node"] = isset($dbinfo->node) ? $dbinfo->node : $nonetext;
		array_push($infos,$info);
	}
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
		echo '<td><select name="cmode" id="cmode"><option value="0" selected="selected">接口控制</option><option value="1">手动播放</option><option value="2">阻止播放</option></select></td>';
		echo '<td>弹幕管理</td></tr>';
	}
}
function nyarukoliveOptionsPage() {
?>
<div id="optionbox">
	<div id="wpNyarukoOptionTitle"><a title="版本升级日志" class="link" href="https://github.com/kagurazakayashi/wpNyarukoLive/commits/master" target="_blank"><div id="wpNyarukoPanelLogo"></div></a>&nbsp;视频直播（版本&nbsp;0.1）</div><hr>
	<form action="#" method="post" enctype="multipart/form-data" name="op_form" id="op_form">
	<table border="0" cellspacing="0" cellpadding="10">
	<tbody>
	<tr>
        <!-- <td>笔记(不呈现)</td>
		<td><input name="wpNyarukoTest" type="text" id="wpNyarukoTest" value="" size=64 maxlength=128 /></td> -->
		<td>信息：</td>
        <td>此插件目前不可用。</td>
    </tr>
	</tbody>
	</table>
	<hr>
	<h2>推流记录</h2>
	<div class="wpNyarukoOptionListbox">
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
	<hr>
	<h2>弹幕管理</h2>
	<div id="wpNyarukoOptionDanmakuMgr">请在上方「推流管理」中选择一个直播流。</div>
	<hr>
	<h2>IP 地址屏蔽</h2>
	<div id="wpNyarukoOptionBlockMgr">以下设置同时生效于直播播放和弹幕。</div>
	<hr><div id="wpNyarukoOptionBtnBar"><p><input id="submitoption" type="submit" name="nyarukolive_input_save" value="应用这些设定" /></p></div>
	</form>
</div>
<?php
}
?>