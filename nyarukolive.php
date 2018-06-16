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
define("NYARUKOLIVE_RANDOM_CHAR", "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
include_once NYARUKOLIVE_FULL_DIR."options.php";
// include_once NYARUKOLIVE_FULL_DIR."api.php";
// nyarukoLiveAPI();
// function nyarukoLiveAlert() {
//     echo "还未完成初始设定";
// }
function nyarukoLiveHead() {
    $plugindir = plugins_url('',__FILE__);
    echo '<link href="'.NYARUKOLIVE_PLUGIN_URL.'/options_style.css" rel="stylesheet">';
    echo '<script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'/options_script.js"></script>';
    echo '<style>#wpNyarukoPanelLogo{background-image:url("'.NYARUKOLIVE_PLUGIN_URL.'/img/wpNyaruko.gif");}#wpNyarukoPanelLogo:hover{background-image:url("'.NYARUKOLIVE_PLUGIN_URL.'/img/wpNyaruko2.gif");}</style>';
}
add_action("admin_head","nyarukoLiveHead");
// add_action("admin_notices","nyarukoLiveAlert");
function nyarukoLiveAdminlink($links){
    $links[] = '<a href="'.get_admin_url(null, 'tools.php?page='.NYARUKOLIVE_TEXT_DOMAIN.'-options').'">直播设置</a>';
    return $links;
}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'nyarukoLiveAdminlink');
function randomstring($length = 16) {
    $mstr = "";
    for ( $i = 0; $i < $length; $i++ ) {
        $mstr .= NYARUKOLIVE_RANDOM_CHAR[mt_rand(0, strlen(NYARUKOLIVE_RANDOM_CHAR) - 1)];
    }
    return $mstr;
}
function nyarukoLiveShortcode($attr, $content) {
    $expguestreg = true; //TODO:简化游客信息填写
    //0.AUTO 1FLV 2HLS 3HLS+
    $errcode = [0,"ok"];
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
    echo '<link href="'.NYARUKOLIVE_PLUGIN_URL.'live_style.css" rel="stylesheet">';
    $nyarukoLivePlayerCssLoaded = true;
    $pagetype = "1";
    if (isset($_GET["page_id"])) {
        $pagetype = "2";
    }
    $livepageid = get_the_ID();
    global $wpdb;
    $livedbarr = [];
    if (isset($attr["res"])) array_push($livedbarr,"(app='".htmlentities($attr["res"])."')");
    if (isset($attr["app"])) array_push($livedbarr,"(appname='".htmlentities($attr["app"])."')");
    if (isset($attr["id"])) array_push($livedbarr,"(id='".htmlentities($attr["id"])."')");
    if (count($livedbarr) == 0) $errcode = [-2,"配置错误：缺少名称参数"];
    $dbinfos = [];
    if ($errcode[0] == 0) $dbinfos = $wpdb->get_results("SELECT `liveid`,`action`,`cmode`,`ip` FROM `".$wpdb->prefix."live_channels` WHERE ".implode(" AND ", $livedbarr)." ORDER BY liveid DESC;");
    $info = [];
    if (count($dbinfos) > 0) {
        $dbinfo = $dbinfos[0];
        $info["liveid"] = isset($dbinfo->liveid) ? $dbinfo->liveid : -1;
        $info["action"] = isset($dbinfo->action) ? $dbinfo->action : -1;
        $info["cmode"] = isset($dbinfo->cmode) ? $dbinfo->cmode : -1;
        $info["ip"] = isset($dbinfo->ip) ? $dbinfo->ip : "0.0.0.0";
        if (isban($info["ip"])[0]) {
            $errcode = [-5,"视频源被屏蔽"];
        } else if ($info["cmode"] == 2) {
            $errcode = [-4,"直播被中止"];
        } else if ($info["action"] != 1 && $info["cmode"] != 1) {
            $errcode = [-1,"目前尚未直播"];
        }
    } else if ($errcode[0] == 0) {
        $errcode = [-3,"配置错误：直播尚未登记"];
        $info["liveid"] = -1;
    }
    $time = strval(time());
    $timelen = strlen($time);
    $token = $time.randomstring(128-$timelen);
    $browsertoken = "";
    $newbrowsertoken = false;
    if (isset($_COOKIE["nyarukolive_browsertoken"])) {
        $browsertoken = $_COOKIE["nyarukolive_browsertoken"];
    } else {
        $browsertoken = $time.randomstring(64-$timelen);
        $newbrowsertoken = true;
        // setcookie("nyarukolive_browsertoken", $browsertoken, time()+31536000);
    }
    $dbinfos = $wpdb->get_results("SELECT `token` FROM `".$wpdb->prefix."live_audiences` WHERE `browsertoken`='".$browsertoken."';");
    if (count($dbinfos) > 0) {
        $wpdb->get_results("UPDATE `".$wpdb->prefix."live_audiences` SET `token`='".$token."', `time`=CURRENT_TIMESTAMP WHERE `".$wpdb->prefix."live_audiences`.`browsertoken`='".$browsertoken."';");
    } else {
        $wpdb->get_results("INSERT INTO `".$wpdb->prefix."live_audiences` (`token`, `browsertoken`, `type`, `time`) VALUES ('".$token."', '".$browsertoken."', '0', CURRENT_TIMESTAMP);");
    }
    echo '<script>var nyarukolive_config={"token":"'.$token.'","browsertoken":"'.$browsertoken.'","pcode":'.$errcode[0].',"pinfo":"'.$errcode[1].'","liveid":'.$info["liveid"].',"pagetype":'.$pagetype.',"pageid":'.$livepageid.',"mode":'.$liveplayermode.',"pluginurl":"'.NYARUKOLIVE_PLUGIN_URL.'","api":"'.WP_PLUGIN_URL.'/wpNyarukoLive/api.php"';
    foreach($attr as $k => $v){
        echo ',"'.$k.'":"'.$v.'"';
    }
    echo "};</script>";
    ?>
    <br/>
    <div id="nyarukolive" class="">
    <table id="nyarukolive_titlebar" border="0">
    <tbody>
        <tr>
        <td width="50" align="center"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-live_tv-24px.svg" /></td>
        <td align="left"><?php 
        if ($errcode[0] == 0) {
            if (isset($attr["title"])) {
                echo $attr["title"];
            } else {
                echo "L&nbsp;I&nbsp;V&nbsp;E";
            }
        } else {
            echo "直播暂停中";
        } ?></td>
        <td id="nyarukolive_rtoolbox1" align="right"<?php 
        if ($errcode[0] == 0) {
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
            </table><?php } ?>
        </td>
        </tr>
    </tbody>
    </table>
    <?php if ($errcode[0] == 0) { ?>
    <div id="nyarukolive_videobox">
        <video id="nyarukolive_video" class="video-js" x-webkit-airplay="allow" poster="" webkit-playsinline playsinline x5-video-player-type="h5" x5-video-player-fullscreen="true" preload="auto" style="width:100%;height:100%;position:relative;">
        <source id="nyarukolive_videosrc" src="" type="application/x-mpegURL">
        </video>
        <div id="nyarukolive_danmubox"></div>
        <div id="nyarukolive_pausebox" onclick="nyarukolive_playpausebtn();">
            <img id="nyarukolive_playbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline_play_circle_outline_white_48dp.png" alt="点击播放" />
        </div>
        <a id="nyarukolive_fsiconbtna" href="javascript:exitFullscreen(document);" title="退出全屏幕"><img id="nyarukolive_fsiconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-fullscreen-24px.svg" alt="全" />&nbsp;退出全屏幕</a>
        <table id="nyarukolive_usermenu"<?php if ($expguestreg) echo ' class="nyarukolive_usermenusmall"'; ?>>
        <tbody>
            <tr>
            <td>用户信息</td>
            <td align="right"><a href="javascript:saveguestname();" title="保存用户信息"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-done-24px.svg" alt="√" /></a>
            <!-- &emsp;<a href="javascript:loadguestname();" title="放弃更改"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-close-24px.svg" alt="×" /></a> -->
            </td>
            </tr>
            <tr><td colspan="2" width="100%">昵称（必须）：<br/><input name="nyarukolive_dmuname" class="nyarukolive_danmuinbox w100" type="text" id="nyarukolive_dmuname" placeholder="输入显示名称" value="" maxlength="16" oninput="cleartext(this,false,false,true);"></td></tr>
            <tr><td colspan="2" width="100%">电子邮件（必须）：<br/><input name="nyarukolive_dmumail" class="nyarukolive_danmuinbox w100" type="text" id="nyarukolive_dmumail" placeholder="输入电子邮件" value="" maxlength="32" oninput="cleartext(this);"></td></tr>
            <tr><td colspan="2" width="100%"<?php if ($expguestreg) echo " style='display:none;'"; ?>>网址（选填）：<br/><input name="nyarukolive_dmuurl" class="nyarukolive_danmuinbox w100" type="text" id="nyarukolive_dmuurl" placeholder="输入个人网址（选填）" value="" maxlength="64" oninput="cleartext(this);"></td></tr>
            <tr><td colspan="2" width="100%"<?php if ($expguestreg) echo " style='display:none;'"; ?>>使用我自己的账户：<br/><a>登录/注册(暂未开放)</a></td></tr>
        </tbody>
        </table>
        <script>swmenu(1);loadguestname();</script>
        <table id="nyarukolive_menu">
        <tbody>
            <tr>
            <td>播放器设置</td>
            <td align="right"><a href="javascript:swmenu(0);" title="关闭设置菜单"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-close-24px.svg" alt="×" /></a></td>
            </tr>
            <tr>
            <td>线路</td>
            <td><select onchange="window.location=this.value;"><?php 
            if (isset($attr["lines"])) {
                $lines = explode('|', $attr["lines"]);
                foreach ($lines as $line) {
                    $lineinfo = explode(',', $line);
                    $selected = "";
                    if (count($lineinfo) > 2 && $lineinfo[2] == "now") {
                        $selected = " selected";
                    }
                    echo '<option value="'.$lineinfo[1].'"'.$selected.'>'.$lineinfo[0].'</option>';
                }
            }
            ?></select></td>
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
                <select id="nyarukolive_blockbullet">
                    <option value="0">不屏蔽</option>
                    <option value="1">屏蔽所有</option>
                </select>
            </td>
            </tr>
        </tbody>
        </table>
        <div id="nyarukolive_alertbox"></div>
    </div>
    <table id="nyarukolive_footbar" border="0">
    <tbody>
        <tr>
        <td width="20">
            <a id="nyarukolive_btnplay" href="javascript:nyarukolive_playpausebtn();" title="播放/暂停"><img id="nyarukolive_btnplayi" class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-play_arrow-24px.svg" src2="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-play_arrow-24px.svg" src3="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-pause-24px.svg" /></a>
        </td>
        <td width="80"><input name="nyarukolive_danmunick" class="nyarukolive_danmuinbox w100" type="text" id="nyarukolive_danmunick" placeholder="请输入昵称" value="" maxlength="20" readonly="readonly" onclick="swmenu(1,true);"></td>
        <td><input name="nyarukolive_danmuchat" class="nyarukolive_danmuinbox w100" type="text" id="nyarukolive_danmuchat" placeholder="点这里输入弹幕内容（最多32个字）" value="" maxlength="32" oninput="cleartext(this,false,true,true);" onfocus="sendBulletCommentChk();" onkeyup="sendBulletComment(true);"></td>
        <td width="80" align="right">
            <span id="nyarukolive_btndanmusentwait">5</span>
            <a id="nyarukolive_btndanmusent" href="javascript:sendBulletComment();" title="发送弹幕"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-send-24px.svg" alt="发"/></a>
            <a id="nyarukolive_btnsetting" href="javascript:swmenu(0,true);" title="播放器设置"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-settings-20px.svg" alt="设" /></a>
            <a id="nyarukolive_btnfullscreen" href="javascript:;" onclick="fullScreen();" title="全屏幕"><img class="nyarukolive_footbariconbtn" src="<?php echo NYARUKOLIVE_PLUGIN_URL ?>lib/baseline-fullscreen-24px.svg" alt="全" /></a>
        </td>
        </tr>
    </tbody>
    </table>
    <?php } else if ($errcode[0] != -5) {
        if (!isset($attr["stoppic"]) || $attr["stoppic"] == "") $attr["stoppic"] = NYARUKOLIVE_PLUGIN_URL."/img/SMPTE_HD_1080P.png";
        echo '<img id="nyarukolive_stopalertimg" src="'.$attr["stoppic"].'" alt="目前尚未直播" />';
    } else {
        echo '<div id="nyarukolive_stopalert"><h1>&emsp;</h1><h1>暂时无法观看</h1><h2>'.$errcode[1].'</h2><h2>代码：'.$errcode[0].'</h2></div>';
    }
    echo '</div><script type="text/javascript" src="'.NYARUKOLIVE_PLUGIN_URL.'live_script.js"></script>';
}
function isban($ip) {
    global $wpdb;
    $dbinfos = $wpdb->get_results("SELECT `id`,`note` FROM `".$wpdb->prefix."live_banip` WHERE (`ban`='".$ip."') AND (`type`=0) AND (`start`<NOW()) AND (`end`>NOW()) AND (`enable`=1) ORDER BY `start`;");
    if (count($dbinfos) > 0) {
        $nowban = $dbinfos[0];
        return [true,$nowban->id,$nowban->note];
    } else {
        return [false];
    }
}
add_shortcode('nyarukolive', 'nyarukoLiveShortcode');