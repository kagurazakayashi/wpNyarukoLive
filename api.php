<?php
include "../../../wp-config.php";
nyarukoLiveAPI($table_prefix);
// api: 1=发送弹幕
function nyarukoLiveAPI($table_prefix) {
    if (isset($_POST["api"])) {
        // header('Content-Type: application/json; charset=utf-8');
        header('X-Powered-By: wpNyarukoLive');
        $api = intval($_POST["api"]);
        $array = null;
        switch ($api) {
            case 1:
                $array = nyarukoLiveAPISendBarrage($table_prefix);
                break;
            default:
                break;
        }
        if ($array == null) {
            //header('HTTP/1.1 403 Forbidden');
            //die();
            $array = array('code' => -1, 'msg' => '不可识别的接入方式。');
        }
        die(json_encode($array));
    } else {
        header('HTTP/1.1 403 Forbidden');
    }
}
function nyarukoLiveAPISendBarrage($table_prefix) {
    //TODO:限制长度,检查空白
    //id 弹幕序号DB	liveid 直播序号JS	name 昵称JS	email 邮件JS	url 主页JS	ip 发送IPphp	date 发送时间PHP	content 弹幕内容JS	style 弹幕样式JS	ua 浏览器UAPHP	wpuserid WP用户IDphp
    
    $bulletcomment = [];
    if (isset($_POST["liveid"])) {
        $bulletcomment["liveid"] = intval($_POST["liveid"]);
    } else {
        return showerror(array('code' => -2, 'msg' => 'liveid 配置错误'));
    }
    if (isset($_POST["name"]) && strlen($_POST["name"]) > 0 && mb_strlen($_POST["name"],'utf8') <= 16) {
        $bulletcomment["name"] = htmlentities($_POST["name"]);
    } else {
        return showerror(array('code' => -3, 'msg' => 'name 配置错误'));
    }
    if (isset($_POST["email"]) && strlen($_POST["email"]) > 0 && strlen($_POST["email"]) <= 32) {
        $bulletcomment["email"] = htmlentities($_POST["email"]);
    } else {
        return showerror(array('code' => -4, 'msg' => 'email 配置错误'));
    }
    if (isset($_POST["url"]) && strlen($_POST["url"]) > 0 && strlen($_POST["url"]) <= 64) {
        $bulletcomment["url"] = htmlentities($_POST["url"]);
    } else {
        return showerror(array('code' => -5, 'msg' => 'url 配置错误'));
    }
    if (isset($_POST["content"]) && strlen($_POST["content"]) > 0 && mb_strlen($_POST["content"],'utf8') <= 32) {
        $bulletcomment["content"] = htmlentities($_POST["content"]);
    } else {
        return showerror(array('code' => -6, 'msg' => 'content 配置错误'));
    }
    if (isset($_POST["style"]) && strlen($_POST["style"]) > 0 && strlen($_POST["style"]) < 16) {
        $bulletcomment["style"] = htmlentities($_POST["style"]);
    } else {
        return showerror(array('code' => -7, 'msg' => 'style 配置错误'));
    }
    if (isset($_POST["token"]) && strlen($_POST["token"]) == 128) {
        $bulletcomment["token"] = htmlentities($_POST["token"]);
    } else {
        return showerror(array('code' => -8, 'msg' => 'token 配置错误'));
    }
    // ip 发送IPphp ua 浏览器UAPHP wpuserid WP用户IDphp
    // $current_user = wp_get_current_user();
    // 没有登录：if ( 0 == $current_user->ID )
    $bulletcomment["ua"] = htmlentities($_SERVER['HTTP_USER_AGENT']);
    $bulletcomment["wpuserid"] = 0;//$current_user->ID;
    $cliip = getip();
    if (preg_match('/^[0-9a-zA-Z.:]+$/',$cliip)) {
        $bulletcomment["ip"] = strtoupper($cliip);
    }
    $sqlkeys = [];
    $sqlvals = [];
    foreach ($bulletcomment as $key => $value) {
        array_push($sqlkeys,$key);
        array_push($sqlvals,$value);
    }
    $sqlkeysstr = "(`".implode("`,`", $sqlkeys)."`)";
    $sqlvalsstr = "('".implode("','", $sqlvals)."')";
    //INSERT INTO `racing_live_commenting` (`id`, `liveid`, `token`, `name`, `email`, `url`, `ip`, `date`, `content`, `style`, `ua`, `wpuserid`) VALUES ('1', '2', '3', '4', '5', '6', '7', CURRENT_TIMESTAMP, '9', '10', '11', '12')
    $dbcmd = "INSERT INTO `".$table_prefix."live_commenting` ".$sqlkeysstr." VALUES ".$sqlvalsstr.";";
    echo $dbcmd;
    return "OK";
}
function showerror($errinfo) {
    header('HTTP/1.1 403 Forbidden');
    die(json_encode($errinfo));
}
function getip() {
    static $realip;
    if(isset($_SERVER)){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])){
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')){
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_CLIENT_IP')){
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    return $realip;
}
?>