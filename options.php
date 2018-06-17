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
//获取屏蔽列表
function nyarukoliveGetBan() {
	global $wpdb;
	$infos = [];
	$dbinfos = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."live_ban` ORDER BY `start` DESC LIMIT 1000;");
	if (count($dbinfos) == 0) {
		echo "<center><h4>目前没有被屏蔽的来源</h4></center>";
	}
	foreach ($dbinfos as $dbinfo) {
		$info["id"] = isset($dbinfo->id) ? $dbinfo->id : -1;
		$info["type"] = isset($dbinfo->type) ? $dbinfo->type : 0;
		if ($info["type"] == 0) {
			$info["type"] = "IP地址";
		}
		$info["ban"] = isset($dbinfo->ban) ? $dbinfo->ban : "";
		$info["start"] = isset($dbinfo->start) ? $dbinfo->start : "";
		$info["end"] = isset($dbinfo->end) ? $dbinfo->end : "";
		$info["enable"] = isset($dbinfo->enable) ? $dbinfo->enable : 1;
		$info["note"] = isset($dbinfo->note) ? $dbinfo->note : "无";
		array_push($infos,$info);
	}
	foreach ($infos as $info) {
		echo '<tr><th scope="row">'.$info["id"].'</th>';
		echo '<td>'.$info["type"].'</td>';
		echo '<td>'.$info["ban"].'</td>';
		echo '<td>'.$info["start"].'</td>';
		echo '<td>'.$info["end"].'</td>';
		$banenable = "O N";
		if ($info["enable"] == 0) $banenable = "OFF";
		echo '<td><button type="button" onclick="">'.$banenable.'</button></td>';
		echo '<td>'.$info["note"].'</td>';
		echo '<td><button type="button" onclick="">删除</button></td>';
	}
}
//获得当前弹幕
function nyarukoliveGetDanmaku() {
	global $wpdb;
	$infos = [];
	if (isset($_GET["nyamode"]) && $_GET["nyamode"] == "mddmmgr" && isset($_GET["liveid"])) {
		$liveid = intval($_GET["liveid"]);
		$dbinfos = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."live_commenting` WHERE `liveid`=".$liveid." ORDER BY `date` DESC LIMIT 1000;");
		if (count($dbinfos) == 0) {
			echo "<center><h4>ID为 ".$liveid." 的直播间目前还没有弹幕</h4></center>";
		} else {
			echo "<center><h4>正在管理直播ID为 ".$liveid." 的弹幕列表</h4></center>";
		}
		echo '直播间弹幕开关：<button type="button" onclick="">O N</button>';
		foreach ($dbinfos as $dbinfo) {
			$info["id"] = isset($dbinfo->id) ? $dbinfo->id : -1;
			$info["liveid"] = isset($dbinfo->liveid) ? $dbinfo->liveid : -1;
			$info["browsertoken"] = isset($dbinfo->browsertoken) ? $dbinfo->browsertoken : "";
			$info["token"] = isset($dbinfo->token) ? $dbinfo->token : "";
			$info["name"] = isset($dbinfo->name) ? $dbinfo->name : "";
			$info["email"] = isset($dbinfo->email) ? $dbinfo->email : "没有邮箱";
			$info["url"] = isset($dbinfo->url) ? $dbinfo->url : "没有网址";
			$info["ip"] = isset($dbinfo->ip) ? $dbinfo->ip : "";
			$info["date"] = isset($dbinfo->date) ? $dbinfo->date : "";
			$info["content"] = isset($dbinfo->content) ? $dbinfo->content : "";
			$info["style"] = isset($dbinfo->style) ? $dbinfo->style : "0:0";
			if ($info["style"] == "0:0") {
				$info["style"] = "普通";
			}
			$info["ua"] = isset($dbinfo->ua) ? $dbinfo->ua : "";
			$info["wpuserid"] = isset($dbinfo->wpuserid) ? $dbinfo->wpuserid : 0;
			if ($info["wpuserid"] == 0) {
				$info["wpuserid"] = "游客";
			}
			array_push($infos,$info);
		}
		foreach ($infos as $info) {
			echo '<tr><th scope="row">'.$info["id"].'</th>';
			echo '<td><button type="button" onclick="prompt(\'当前会话\',\''.$info["token"].'\');">当前</button><br/><button type="button" onclick="prompt(\'浏览器会话\',\''.$info["browsertoken"].'\');">浏览器</button></td>';
			echo '<td>'.$info["name"].'<br/>'.$info["email"].'<br/>'.$info["url"].'</td>';
			echo '<td>'.$info["ip"].'</td>';
			echo '<td>'.str_replace(" ","<br/>",$info["date"]).'</td>';
			echo '<td>'.$info["content"].'</td>';
			echo '<td>'.$info["style"].'</td>';
			echo '<td><button type="button" onclick="prompt(\'浏览器 UA\',\''.$info["ua"].'\');">查看</button></td>';
			echo '<td>'.$info["wpuserid"].'</td>';
			echo '<td><button type="button" onclick="">删除</button></td>';
		}
	} else {
		echo "<center><h4>请在「推流记录」中选择一个直播流后面的弹幕「管理」按钮</h4></center>";
	}
}
//获得正在进行中的直播
function nyarukoliveGetNowLive() {
    global $wpdb;
    $infos = [];
	$dbinfos = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."live_channels` ORDER BY `time` DESC LIMIT 1000;");
	if (count($dbinfos) == 0) {
		echo "<center><h4>目前还没有正在进行的直播记录，请通过回调接口注册直播</h4></center>";
	}
	$nonetext = "(未知)";
	foreach ($dbinfos as $dbinfo) {
		$info["liveid"] = isset($dbinfo->liveid) ? $dbinfo->liveid : $nonetext;
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
	foreach ($infos as $info) {
		echo '<tr><th scope="row">'.$info["liveid"].'</th><td>';
		if ($info["action"] == 0) {
			echo '已停播';
		} else if ($info["action"] == 1) {
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
		echo '<td><select name="cmode" id="cmode" onchange="wpNyarukoOptionCMgLiveMode(this.value,'.$info["liveid"].');"><option value="0"'.nyarukolivecmodeselect($info["cmode"],0).'>接口控制</option><option value="1"'.nyarukolivecmodeselect($info["cmode"],1).'>手动播放</option><option value="2"'.nyarukolivecmodeselect($info["cmode"],2).'>手动停播</option></select></td>';
		echo '<td><button type="button" onclick="wpNyarukoOptionCMgLiveDanmaku('.$info["liveid"].');">管理</button></td>';
		echo '<td><button type="button" onclick="wpNyarukoOptionCMgLiveDelete('.$info["liveid"].');">删除</button></td>';
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
		if (wpNyarukoCModeGet($_GET["nyamode"])) die('<div id="wpNyarukoInfo">正在应用设置...</div>');
	}
	if (isset($_GET["info"])) echo '<div id="wpNyarukoInfo">'.urlb64decode($_GET["info"]).'</div>';
?>
<div id="optionbox">
	<div id="wpNyarukoOptionTitle"><a title="版本升级日志" class="link" href="https://github.com/kagurazakayashi/wpNyarukoLive/commits/master" target="_blank"><div id="wpNyarukoPanelLogo"></div></a>&nbsp;视频直播（版本&nbsp;0.1）</div><hr>
	<div id="wpNyarukoOptionMenuBar">
		<p><div class="wpNyarukoOptionMenuBarItem" id="wpNyarukoOptionMenuBarItem1" onclick="wpNyarukoOptionChTab(1);">基本设置</div><div class="wpNyarukoOptionMenuBarItem" id="wpNyarukoOptionMenuBarItem2" onclick="wpNyarukoOptionChTab(2);">推流记录</div><div class="wpNyarukoOptionMenuBarItem" id="wpNyarukoOptionMenuBarItem3" onclick="wpNyarukoOptionChTab(3);">弹幕管理</div><div class="wpNyarukoOptionMenuBarItem" id="wpNyarukoOptionMenuBarItem4" onclick="wpNyarukoOptionChTab(4);">权限设置</div></p>
	</div>
	<div class="wpNyarukoOptionTab" id="wpNyarukoOptionTab1">
		<form action="#" method="post" enctype="multipart/form-data" name="op_form" id="op_form">
			<table border="0" cellspacing="0" cellpadding="10">
			<tbody>
			<tr>
				<td>笔记(不呈现)</td>
				<td><input name="wpNyarukoTest" type="text" id="wpNyarukoTest" value="" size=64 maxlength=128 /></td>
			</tr>
			<tr>
				<td>弹幕发送权限</td>
				<td><label>
					<input type="radio" name="wpNyarukoLimit" value="0" id="wpNyarukoLimit_0">
					禁止任何人发送弹幕</label>
				<br>
				<label>
					<input type="radio" name="wpNyarukoLimit" value="1" id="wpNyarukoLimit_1">
					只允许实名注册认证用户发送弹幕</label>
				<br>
				<label>
					<input type="radio" name="wpNyarukoLimit" value="2" id="wpNyarukoLimit_2">
					允许所有注册用户发送弹幕</label>
				<br>
				<label>
					<input type="radio" name="wpNyarukoLimit" value="3" id="wpNyarukoLimit_3" checked="checked">
					允许所有人发送弹幕</label>
				<br></td>
			</tr>
			<tr>
				<td>前端播放器<br/>状态刷新时间</td>
				<td>每隔 <input name="wpNyarukoStatusTime" type="number" id="wpNyarukoStatusTime" max="999" min="1" step="1" value="5" size="3"> 秒检查一次直播间状态和新弹幕</td>
			</tr>
			</tbody>
			</table>
			<hr><div id="wpNyarukoOptionBtnBar"><p><input id="submitoption" type="submit" name="nyarukolive_input_save" value="应用这些设定" /></p></div>
		</form>
	</div>
	<div class="wpNyarukoOptionTab" id="wpNyarukoOptionTab2">
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
			<th scope="col">回调时间↓</th>
			<th scope="col">参数</th>
			<th scope="col">播放控制</th>
			<th scope="col">弹幕</th>
			<th scope="col">记录</th>
			</tr>
			<?php nyarukoliveGetNowLive(); ?>
		</tbody>
		</table>
	</div>
	<div class="wpNyarukoOptionTab" id="wpNyarukoOptionTab3">
		<h2>弹幕管理</h2>
		<table width="100%" border="1" cellspacing="1" cellpadding="1" class="wpNyarukoOptionInfotable">
		<tbody>
			<tr>
			<th scope="col">弹幕序号</th>
			<th scope="col">会话编码</th>
			<th scope="col">发送者<br/>用户信息</th>
			<th scope="col">发送者IP</th>
			<th scope="col">发送时间↓</th>
			<th scope="col">弹幕内容</th>
			<th scope="col">弹幕样式</th>
			<th scope="col">浏览器<br/>UA</th>
			<th scope="col">WP<br/>用户ID</th>
			<th scope="col">弹幕</th>
			</tr>
			<?php nyarukoliveGetDanmaku(); ?>
		</tbody>
		</table>
	</div>
	<div class="wpNyarukoOptionTab" id="wpNyarukoOptionTab4">
		<h2>IP 地址屏蔽</h2>
		<p>以下设置同时生效于直播播放和弹幕。</p>
		<table width="100%" border="1" cellspacing="1" cellpadding="1" class="wpNyarukoOptionInfotable">
		<tbody>
			<tr>
			<th scope="col">序号</th>
			<th scope="col">类型</th>
			<th scope="col">条件</th>
			<th scope="col">起始时间↓</th>
			<th scope="col">结束时间</th>
			<th scope="col">执行</th>
			<th scope="col">原因</th>
			<th scope="col">记录</th>
			</tr>
			<?php nyarukoliveGetBan(); ?>
		</tbody>
		</table>
	</div>
</div>
<script>wpNyarukoOptionChTab(0,true);</script>
<?php
}
function wpNyarukoCModeGet($nyamode) {
	global $wpdb;
	$alertinfo = "已受理您的变更。";
	$isalert = true;
	if ($nyamode == "mglive" && isset($_GET["liveid"]) && isset($_GET["cmode"])) {
		$liveid = intval($_GET["liveid"]);
		$cmode = intval($_GET["cmode"]);
		$dbinfos = $wpdb->get_results("UPDATE `".$wpdb->prefix."live_channels` SET `cmode`=".$cmode." WHERE `".$wpdb->prefix."live_channels`.`liveid`=".$liveid.";");
		$alertinfo = "将 ".$liveid." 号直播间播放状态设置为 ".$cmode;
    } else if ($nyamode == "mddmmgr" && isset($_GET["liveid"])) {
		$isalert = false;
	}
	$tabid = "";
	if ($isalert) {
		if (isset($_GET["tabid"])) $tabid = "#".$_GET["tabid"];
		echo "<script>window.location.href = 'tools.php?page=nyarukolive-options&info=".urlb64encode($alertinfo).$tabid."';</script>";
	}
	return $isalert;
}
?>