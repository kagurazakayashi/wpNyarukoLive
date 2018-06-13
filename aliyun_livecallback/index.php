<?php
/* 
JSON返回值：{"code":<状态码>,"msg":<状态描述>}
- 0：成功（新增）。
- 1：成功（更新）。
- -1：没有输入当前直播状态。
- -2：查询条目是否存在时出现数据库错误。
- -3：缺少 id, app, appname 其中之一。
*/
//DEBUG用 开始 可直接注释关闭
error_reporting(E_ALL); //打开全部错误监视
ini_set('display_errors', 0); //禁止把错误输出到页面
ini_set('log_errors', 1); //设置错误信息输出到文件
ini_set("error_log", 'phperr.log'); //指定错误日志文件名
logtofile("playing.log"); //记录回调内容到文件
//DEBUG用 结束

header('Content-Type: application/json; charset=utf-8');
header('X-Powered-By: wpNyarukoLive');
define("NYARUKOLIVE_ERROR", "[NYA-L+ERR]");
include "../wp-config.php";
nyalivealicb($table_prefix);
function nyalivealicb($table_prefix) {
    $sqlkeys = [];
    $sqlvals = [];
    $updateid = -1;
    if (isset($_GET["action"])) {
        array_push($sqlkeys,"action");
        if ($_GET["action"] == "publish_done") {
            array_push($sqlvals,0);
        } else if ($_GET["action"] == "publish") {
            array_push($sqlvals,1);
        } else {
            array_push($sqlvals,2);
        }
    } else {
        die(echoerror(-1,"no action"));
    }
    if (isset($_GET["ip"])) {
        array_push($sqlkeys,"ip");
        array_push($sqlvals,htmlentities($_GET["ip"]));
    }
    if (isset($_GET["id"]) && isset($_GET["app"]) && isset($_GET["appname"])) {
        $kid = htmlentities($_GET["id"]);
        $kapp = htmlentities($_GET["app"]);
        $appname = htmlentities($_GET["appname"]);
        $psql = "SELECT `liveid` FROM `".$table_prefix."live_channels` WHERE (app='".$kapp."' and appname='".$appname."' and id='".$kid."');";
        $aid = nyalivedb($psql);
        if ($aid == NYARUKOLIVE_ERROR) die(echoerror(-2,"db error"));
        if (isset($aid[0])) {
            $updateid = intval($aid[0]);
        }
        array_push($sqlkeys,"id","app","appname");
        array_push($sqlvals,$kid,$kapp,$appname);
    } else {
        die(echoerror(-3,"no liveid"));
    }
    if (isset($_GET["usrargs"])) {
        array_push($sqlkeys,"usrargs");
        array_push($sqlvals,htmlentities($_GET["usrargs"]));
    }
    if (isset($_GET["node"])) {
        array_push($sqlkeys,"node");
        array_push($sqlvals,htmlentities($_GET["node"]));
    }
    if (isset($_GET["time"])) {
        array_push($sqlkeys,"time");
        array_push($sqlvals,date('Y-m-d H:i:s', $_GET["time"]));
    }
    $dbcmd = "";
    $dbupdmode = [];
    if ($updateid > -1) {
        $sqlkvsstr = [];
        for ($i=0; $i < count($sqlkeys); $i++) {
            $sqlkvsstrv = "`".$sqlkeys[$i]."`='".$sqlvals[$i]."'";
            array_push($sqlkvsstr,$sqlkvsstrv);
        }
        $dbcmd = "UPDATE `".$table_prefix."live_channels` SET ".implode(",", $sqlkvsstr)." WHERE `".$table_prefix."live_channels`.`liveid`=".$updateid.";";
        $dbupdmode = [1,"update ok"];
    } else {
        $sqlkeysstr = "(`".implode("`,`", $sqlkeys)."`)";
        $sqlvalsstr = "('".implode("','", $sqlvals)."')";
        $dbcmd = "INSERT INTO `".$table_prefix."live_channels` ".$sqlkeysstr." VALUES ".$sqlvalsstr.";";
        $dbupdmode = [0,"insert ok"];
    }
    if (nyalivedb($dbcmd) == NYARUKOLIVE_ERROR) die(echoerror(2,"db error"));
    echo echoerror($dbupdmode[0],$dbupdmode[1]);
}
function nyalivedb($sql) {
    $con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $con->query('set names utf8;');
    if ($result = $con->query($sql)) {
        $row = "";
        if (!is_bool($result)) {
            $row = $result->fetch_array();
        }
        // if (is_array($row))
        // echo "[RESULT]".print_r($row);
        return $row;
    } else {
        return NYARUKOLIVE_ERROR;
    }
    $con->close();
}
function logtofile($logfilename) {
    $txt = "\n";
    foreach ($_GET as $key => $value) {
        $txt = $txt.$key.":".$value."|";
    }
    file_put_contents($logfilename,$txt,FILE_APPEND);
}
function echoerror($status,$info) {
    $jsonarr = array('code' => $status, 'msg' => $info);
    $json = json_encode($jsonarr);
    return $json;
}
?>