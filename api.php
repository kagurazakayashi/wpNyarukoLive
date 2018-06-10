<?php
include "../../../wp-config.php";
define("NYARUKOLIVE_ERROR", "[NYA-L+ERR]");
nyarukoLiveAPI($table_prefix);
// api: 1=发送弹幕
function nyarukoLiveAPI($table_prefix) {
    if (isset($_POST["api"])) {
        header('Content-Type: application/json; charset=utf-8');
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
        die();
    }
}
function nyarukoLiveAPISendBarrage($table_prefix) {
    $bulletcomment = [];
    $userinfo = [];
    if (isset($_POST["liveid"])) {
        $bulletcomment["liveid"] = intval($_POST["liveid"]);
    } else {
        return showerror(array('code' => -2, 'msg' => 'liveid 配置错误'));
    }
    if (isset($_POST["name"]) && strlen($_POST["name"]) > 0 && mb_strlen($_POST["name"],'utf8') <= 16) {
        $bulletcomment["name"] = htmlentities($_POST["name"]);
        $userinfo["name"] = $bulletcomment["name"];
    } else {
        return showerror(array('code' => -3, 'msg' => '用户名不符合要求'));
    }
    if (isset($_POST["email"]) && strlen($_POST["email"]) > 0 && strlen($_POST["email"]) <= 32) {
        $bulletcomment["email"] = htmlentities($_POST["email"]);
        $userinfo["email"] = $bulletcomment["email"];
    } else {
        return showerror(array('code' => -4, 'msg' => '电子邮件输入不符合要求'));
    }
    if (isset($_POST["url"]) && strlen($_POST["url"]) > 0 && strlen($_POST["url"]) <= 64) {
        $bulletcomment["url"] = htmlentities($_POST["url"]);
        $userinfo["url"] = $bulletcomment["url"];
    } else {
        return showerror(array('code' => -5, 'msg' => '个人网址输入不符合要求'));
    }
    if (isset($_POST["content"]) && strlen($_POST["content"]) > 0 && mb_strlen($_POST["content"],'utf8') <= 32) {
        $bulletcomment["content"] = htmlentities($_POST["content"]);
        $userinfo["content"] = $bulletcomment["content"];
    } else {
        return showerror(array('code' => -6, 'msg' => '弹幕输入不符合要求'));
    }
    if (isset($_POST["style"]) && strlen($_POST["style"]) > 0 && strlen($_POST["style"]) < 16) {
        $bulletcomment["style"] = htmlentities($_POST["style"]);
        $userinfo["style"] = $bulletcomment["style"];
    } else {
        return showerror(array('code' => -7, 'msg' => 'style 配置错误'));
    }
    if (isset($_POST["token"]) && strlen($_POST["token"]) == 128) {
        $bulletcomment["token"] = htmlentities($_POST["token"]);
    } else {
        return showerror(array('code' => -8, 'msg' => 'token 配置错误'));
    }
    if (isset($_POST["browsertoken"]) && strlen($_POST["browsertoken"]) == 64) {
        $bulletcomment["browsertoken"] = htmlentities($_POST["browsertoken"]);
    } else {
        return showerror(array('code' => -9, 'msg' => 'browsertoken 配置错误'));
    }
    $tokenvifdbcmd = "SELECT `type`,`time` FROM `racing_live_audiences` WHERE (`token`='".$bulletcomment["token"]."') AND (`browsertoken`='".$bulletcomment["browsertoken"]."') AND (`type`='0') order by time desc;";
    $tokenvif = nyalivedb($tokenvifdbcmd);
    if ($tokenvif == NYARUKOLIVE_ERROR || count($tokenvif) < 4) {
        return showerror(array('code' => -10, 'msg' => 'token 验证失败'));
    }
    // ip 发送IPphp ua 浏览器UAPHP wpuserid WP用户IDphp
    // $current_user = wp_get_current_user();
    // 没有登录：if ( 0 == $current_user->ID )
    $bulletcomment["ua"] = htmlentities($_SERVER['HTTP_USER_AGENT']);
    $bulletcomment["wpuserid"] = 0;//$current_user->ID;
    $userinfo["wpuserid"] = $bulletcomment["wpuserid"];
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
    $dbcmd = "INSERT INTO `".$table_prefix."live_commenting` ".$sqlkeysstr." VALUES ".$sqlvalsstr.";";
    if (nyalivedb($dbcmd) == NYARUKOLIVE_ERROR) {
        return showerror(array('code' => -100, 'msg' => '数据库连接失败。'));
    }
    $jsonarr = array('code' => 0, 'msg' => "提交成功。");
    $returnarr = array_merge($jsonarr,$userinfo);
    return json_encode($returnarr);
}
function nyalivedb($sql) {
    $con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $con->query('set names utf8;');
    if($result = $con->query($sql)){
        $row = "";
        if (!is_bool($result)) {
            $row = $result->fetch_array();
        }
        // if (is_array($row))
        // echo "[RESULT]".print_r($row);
        return $row;
    }else{
        return NYARUKOLIVE_ERROR;
    }
    $con->close();
}
function showerror($errinfo) {
    // header('HTTP/1.1 403 Forbidden');
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