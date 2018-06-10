var video = document.getElementById('nyarukolive_video');
var videosrc = document.getElementById('nyarukolive_videosrc');
var pauseboxi = document.getElementById('nyarukolive_playbtn');
var pauseboxi2 = document.getElementById('nyarukolive_btnplayi');
var nyarukolivediv = document.getElementById('nyarukolive');
var player = null;
var ready = false;
var playing = false;
var nyarukolive_playermode = 0;
var nyarukolive_flv = "";
var nyarukolive_hls = "";
var nyarukolive_protocol = "";
var nyarukolive_timezone = 10000;
var isfullScreen = false;
function nyarukolive_loadconfig(config) {
    if (config["pcode"] && config["pinfo"]) {
        console.log("wpNyarukoLive Status", config["pcode"], config["pinfo"]);
        return 1;
    }
    if (config["mode"]) nyarukolive_playermode = parseInt(config["mode"]);
    if (config["flv"]) nyarukolive_flv = config["flv"];
    if (config["hls"]) nyarukolive_hls = config["hls"];
    if (nyarukolive_playermode == 0 && nyarukolive_flv == "" && nyarukolive_hls == "") {
        return 2;
    } else if (nyarukolive_playermode == 1 && nyarukolive_flv == "") {
        return 3;
    } else if ((nyarukolive_playermode == 2 || nyarukolive_playermode == 3) && nyarukolive_hls == "") {
        return 4;
    }
    if (config["protocol"]) nyarukolive_protocol = config["protocol"];
    if (config["pluginurl"]) nyarukolive_pluginurl = config["pluginurl"];
    if (config["timezone"]) nyarukolive_timezone = parseInt(config["timezone"]);
    return 0;
}
function nyarukolive_selectmode(nmode) {
    mode = nmode;
    if (mode == 0) {
        autoselectmode();
    } else if (mode == 1) {
        console.log("flv mode",nyarukolive_flv);
        player = flvjs.createPlayer({
            type: 'flv',
            url: nyarukolive_flv
        });
        player.attachMediaElement(video);
        player.load();
        nyarukolive_videoready();
    } else if (mode == 2) {
        console.log("hls mode");
        player = new Hls();
        player.loadSource(nyarukolive_hls);
        player.attachMedia(video);
        player.on(Hls.Events.MANIFEST_PARSED,function() {
            nyarukolive_videoready();
        });
    } else if (mode == 3) {
        mode = 3;
        console.log("hls+ mode");
        videosrc.src = nyarukolive_hls;
        player = videojs('nyarukolive_video',{
            bigPlayButton : false,
            textTrackDisplay : false,
            posterImage: false,
            errorDisplay : true,
            controlBar : false
        },function(){
            nyarukolive_videoready();
        });
    }
}
function nyarukolive_videoready() {
    ready = true;
    console.log("ready.");
    // playpausebtn();
}
function nyarukolive_playpausebtn() {
    // if (ready) {
        if (playing) {
            console.log("pause");
            // pauseboxi.style.display='block';
            if (mode == 2) {
                video.pause();
            } else {
                player.pause();
            }
            pauseboxi.style.display='block';
            pauseboxi2.src = nyarukolive_pluginurl+"lib/baseline-play_arrow-24px.svg";
        } else {
            console.log("play");
            // pauseboxi.style.display='none';
            if (mode == 2) {
                video.play();
            } else {
                player.play();
            }
            pauseboxi.style.display='none';
            pauseboxi2.src = nyarukolive_pluginurl+"lib/baseline-pause-24px.svg";
        }
        playing = !playing;
    // } else {
    //     console.log("no ready");
    // }
}
function nyarukolive_error(err) {
    document.getElementById('nyarukolive').innerHTML = '<div id="nyarukolive_errorinfo" class="nyarukolive_errordig"><p><b>直播播放器加载失败</b></p><p>错误代码：'+err+'</p></div>';
}
function chkhttps() {
    var newurl = "";
    var newprotocol = "";
    if (nyarukolive_protocol == "https" && document.location.protocol != "https:") {
        newurl = window.location.href.replace(/http:/, "https:");
        newprotocol = "S";
    } else if (nyarukolive_protocol == "http" && document.location.protocol != "http:") {
        newurl = window.location.href.replace(/https:/, "http:");
    }
    if (newurl != "") {
        document.getElementById('nyarukolive').innerHTML = '<div id="nyarukolive_errorinfo" class="nyarukolive_warndig"><p><b>正在配置传输协议</b></p><p>正在配置 HTTP'+newprotocol+' ...</p></div>';
        setTimeout("window.location.href='"+newurl+"';",1000);
        return false;
    }
    return true;
}
function updatetime() {
    var dt = new Date();
    var localtimestr = (updatetimezero(dt.getHours()) + ":" + updatetimezero(dt.getMinutes()) + ":" + updatetimezero(dt.getSeconds()));
    if (document.getElementById('nyarukolive_ltime')) document.getElementById('nyarukolive_ltime').innerText = localtimestr;
    if (nyarukolive_timezone != 10000) {
        var def = dt.getTimezoneOffset()/60;
        var gmt = (dt.getHours() + def);
        var ending = ":" + updatetimezero(dt.getMinutes()) + ":" + updatetimezero(dt.getSeconds());
        var gmtadd = gmt + nyarukolive_timezone;
        var wtime = updatetimecheck24((gmtadd > 24) ? (gmtadd - 24) : gmtadd,gmtadd);
        var wtimestr = (updatetimezero(wtime) + ending);
        if (document.getElementById('nyarukolive_wtime')) document.getElementById('nyarukolive_wtime').innerText = wtimestr;
    }
}
function updatetimezero(num) {
    return ((num <= 9) ? ("0" + num) : num);
}
function updatetimecheck24(hour) {
    var newhour = (hour >= 24) ? hour - 24 : hour;
    if (newhour < 0) { newhour += 24; }
    else if (newhour > 24) { newhour -= 24; }
    return newhour;
}
function swmenu(vmenuid,noclose = false) {
    var vmenuname = ["nyarukolive_menu","nyarukolive_usermenu"];
    var nyarukolivemenu = document.getElementById(vmenuname[vmenuid]);
    if (vmenuid == 1) document.getElementById("nyarukolive_danmunick").blur();
    if (nyarukolivemenu.style.display != "block") {
        nyarukolivemenu.style.display = "block";
        if (vmenuid == 1) document.getElementById("nyarukolive_dmuname").focus();
    } else {
        if (!noclose) nyarukolivemenu.style.display = "none";
    }
}
function saveguestname() {
    var guestname = document.getElementById("nyarukolive_dmuname").value;
    var guestmail = document.getElementById("nyarukolive_dmumail").value;
    var guesturl = document.getElementById("nyarukolive_dmuurl").value;
    if (guestname == "" || guestmail == "") {
        alert("用户名和电子邮件均不能为空。");
    } else {
        setCookie('nyarukolive_guestname',guestname,365);
        setCookie('nyarukolive_guestmail',guestmail,365);
        setCookie('nyarukolive_guesturl',guesturl.replace(":", ")"),365);
    }
    document.getElementById("nyarukolive_danmunick").value = guestname;
    swmenu(1);
}
function loadguestname($isonlyload = false) {
    var guestname = getCookie('nyarukolive_guestname');
    var guestmail = getCookie('nyarukolive_guestmail');
    var guesturl = getCookie('nyarukolive_guesturl').replace(")", ":");
    if ($isonlyload) {
        return [guestname,guestmail,guesturl];
    }
    document.getElementById("nyarukolive_dmuname").value = guestname;
    document.getElementById("nyarukolive_danmunick").value = guestname;
    document.getElementById("nyarukolive_dmumail").value = guestmail;
    document.getElementById("nyarukolive_dmuurl").value = guesturl;
    swmenu(1);
}
function sendBulletCommentChk() {
    var guestinfos = loadguestname(true);
    var isok = true;
    // for (var i = 0; i < guestinfos.length; i++) {
    //     if (guestinfos[i] != cleartext(guestinfos[i],true)) isok = false;
    // }
    if (guestinfos[0] == "" || guestinfos[1] == "") isok = false;
    if (!isok) {
        document.getElementById("nyarukolive_danmuchat").blur();
        swmenu(1,true);
    }
}
function sendBulletComment() {
    var guestinfo = loadguestname(true);
    var danmuchat = document.getElementById("nyarukolive_danmuchat");
    var content = danmuchat.value;
    if (!cleartext(danmuchat,false,true,true)) {
        alert("输入中包括不支持的符号，在输入时请保证文字不变成红色。");
        return;
    }
    if (content == "") return;
    var bulletcomment = {
        "api":1,
        "liveid":nyarukolive_config["liveid"],
        "name":guestinfo[0],
        "email":guestinfo[1],
        "url":guestinfo[2],
        "content":content,
        "style":"0:0",
        "token":nyarukolive_config["token"],
        "browsertoken":nyarukolive_config["browsertoken"]
    };
    $.post(nyarukolive_config["api"],bulletcomment,function(result){
        //content email msg name style url wpuserid
        if (result.length > 0) {
            var rjson = $.parseJSON(result);
            if (rjson) {
                if (rjson["code"] == 0) {
                    alert("弹幕发送成功。");
                    danmuchat.value = "";
                } else {
                    sendBulletCommentFail(rjson.msg);
                }
            } else {
                sendBulletCommentFail("");
            }
        } else {
            sendBulletCommentFail("");
        }
    });
}
function sendBulletCommentFail(errinfo) {
    var einfo = "弹幕发送失败。";
    if (errinfo != "") {
        einfo += errinfo;
    }
    alert(einfo);
}
function cleartext(thistbox,isstring = false,usefullchar = false,norevalue=false) {
    //new RegExp("[`~!@#$^&*()=|{}':;'\",\\[\\].<>/?~！@#￥……&*（）——|{}【】‘；：”“'。，、？%+_]")
    var pattern = new RegExp("[`~!@#^&*()|{}':;'\"\\[\\]<>/]");
    if (isstring) {
        if (thistbox == "") return thistbox;
        var svalue = thistbox;
        return svalue.replace(pattern, '');
    } else if (usefullchar && !isstring) {
        if (norevalue) {
            if (thistbox.value != thistbox.value.replace(pattern, '')) {
                thistbox.style.color = '#F00';
                return false;
            } else {
                thistbox.style.color = '';
                return true;
            }
        } else {
            thistbox.value = thistbox.value.replace(pattern, '');
        }
    } else {
        var svalue = thistbox.value;
        var sid = thistbox.id;
        // var keychar = ;
        var rs = "";
        var patterns = [new RegExp("[`~!#$^&*()=|{}';',\\[\\]<>?~！#￥……&*（）——|{}【】‘；：”“'。，、？]")];
        if (sid != "nyarukolive_dmumail") {
            patterns.push(new RegExp("[@]"));
        }
        if (sid != "nyarukolive_dmuurl") {
            patterns.push(new RegExp("[:/]"));
        }
        for (var i = 0; i < svalue.length; i++) {
            var sub = svalue.substr(i, 1);
            for (var j = 0; j < patterns.length; j++) {
                sub = sub.replace(patterns[j], '');
            }
            rs += sub;
        }
        thistbox.value = rs;
    }
}
function setCookie(c_name,value,expiredays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate()+expiredays);
    document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}
function getCookie(c_name)
{
    if (document.cookie.length>0)
    {
        c_start=document.cookie.indexOf(c_name + "=");
        if (c_start!=-1) { 
            c_start=c_start + c_name.length+1;
            c_end=document.cookie.indexOf(";",c_start);
            if (c_end==-1) c_end=document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end));
        }
    }
    return ""
}
function requestFullScreen(element) {
    if (element.requestFullscreen) {
        element.requestFullscreen();
        isfullScreen = true;
    } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
        isfullScreen = true;
    } else if (element.webkitRequestFullScreen) {
        element.webkitRequestFullScreen();
        isfullScreen = true;
    } else {
        console.log("未能进入全屏");
    }
}
function exitFullscreen(element) {
    if (element.exitFullscreen) {
        element.exitFullscreen();
        isfullScreen = false;
    } else if (element.mozCancelFullScreen) {
        element.mozCancelFullScreen();
        isfullScreen = false;
    } else if (element.webkitCancelFullScreen) {
        element.webkitCancelFullScreen();
        isfullScreen = false;
    } else {
        console.log("未能退出全屏");
    }
}
function fullScreen() {
    if (isfullScreen) {
        exitFullscreen(document);
        console.log("exitFullscreen");
    } else {
        requestFullScreen(nyarukolivediv);
        console.log("requestFullScreen");
    }
}
function removeWpNyarukoNPlayer() {
    if (typeof(yashitheme) != "undefined" && yashitheme == "wpnyarukof") {
        nyarukoplayer_stop();
        // $("#homepage_title1").remove();
        $("#homepage_title2").remove();
        // $("#homepage_titleb").remove();
        // $("#homepage_topimgbox").remove();
        // $("#homepage_title").remove();
        // $(".nyarukoplayer").remove();
    }
}
function setbrowsertoken() {
    var browsertoken = getCookie("nyarukolive_browsertoken");
    if (browsertoken != nyarukolive_config["browsertoken"]) {
        setCookie("nyarukolive_browsertoken",nyarukolive_config["browsertoken"],365);
    }
}
function wpnyarukoliveinit() {
    setbrowsertoken();
    if (typeof(nyarukolive_config) == "undefined") nyarukolive_error(1);
    nyarukolive_lconf = nyarukolive_loadconfig(nyarukolive_config);
    if (nyarukolive_lconf == 0) {
        if (chkhttps()) {
            nyarukolive_selectmode(nyarukolive_playermode);
            updatetime();
            setInterval("updatetime()","1000");
        }
    } else if (nyarukolive_lconf != 1) {
        nyarukolive_error(nyarukolive_lconf);
    }
    console.log("Loading Video ...OK");
    removeWpNyarukoNPlayer();
}
if (typeof(yashitheme) != "undefined" && yashitheme == "wpnyarukof") {
    if (wpnyarukolive_ready) {
        wpnyarukolive_ready = false;
    } else {
        wpnyarukoliveinit();
    }
} else {
    wpnyarukoliveinit();
}
