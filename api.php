<?php
// api: 1=发送弹幕
function nyarukoLiveAPI() {
    if (isset($_POST["api"])) {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Powered-By: wpNyarukoLive');
        $api = intval($_POST["api"]);
        $array = null;
        switch ($api) {
            case 1:
                $array = nyarukoLiveAPISendBarrage();
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
    }
}
function nyarukoLiveAPISendBarrage() {
    //TODO:限制长度,检查空白
    //id 弹幕序号DB	liveid 直播序号JS	name 昵称JS	email 邮件JS	url 主页JS	ip 发送IPphp	date 发送时间PHP	content 弹幕内容JS	style 弹幕样式JS	ua 浏览器UAPHP	wpuserid WP用户IDphp
    // mb_strlen($_POST["id"],'utf8')
    $bulletcomment = [];
    if (isset($_POST["liveid"])) {
        $bulletcomment["liveid"] = intval($_POST["liveid"]);
    } else {
        //ERR
    }
    if (isset($_POST["name"]) && strlen($_POST["name"]) > 0 && mb_strlen($_POST["name"],'utf8') <= 16) {
        $bulletcomment["name"] = htmlentities($_POST["name"]);
    } else {
        //ERR
    }
    if (isset($_POST["email"]) && strlen($_POST["email"]) > 0 && strlen($_POST["email"]) <= 32) {
        $bulletcomment["email"] = htmlentities($_POST["email"]);
    } else {
        //ERR
    }
    if (isset($_POST["url"]) && strlen($_POST["url"]) > 0 && strlen($_POST["url"]) <= 64) {
        $bulletcomment["url"] = htmlentities($_POST["url"]);
    } else {
        //ERR
    }
    if (isset($_POST["content"]) && strlen($_POST["content"]) > 0 && mb_strlen($_POST["content"],'utf8') <= 32) {
        $bulletcomment["content"] = htmlentities($_POST["content"]);
    } else {
        //ERR
    }
    if (isset($_POST["style"]) && strlen($_POST["style"]) > 0 && strlen($_POST["style"]) < 16) {
        $bulletcomment["style"] = htmlentities($_POST["style"]);
    } else {
        //ERR
    }
    if (isset($_POST["token"]) && strlen($_POST["token"]) == 128) {
        $bulletcomment["token"] = htmlentities($_POST["token"]);
    } else {
        //ERR
    }
}
?>