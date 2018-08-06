console.log("[wpNyarukoLive] Loading...");
var video = document.getElementById('nyarukolive_video');
var videosrc = document.getElementById('nyarukolive_videosrc');
var pauseboxi = document.getElementById('nyarukolive_playbtn');
var pauseboxi2 = document.getElementById('nyarukolive_btnplayi');
var fsiconbtn = document.getElementById('nyarukolive_fsiconbtna');
var nyarukolivediv = document.getElementById('nyarukolive');
var sendbtn = document.getElementById('nyarukolive_sendbtn');
var sendwaittimer = document.getElementById('nyarukolive_sendbtn');
var btndanmusentwait = document.getElementById('nyarukolive_btndanmusentwait');
var btndanmusent = document.getElementById('nyarukolive_btndanmusent');
var player = null;
var ready = false;
var playing = false;
var serplaying = 0;
var nyarukolive_debug = 0;
var nyarukolive_playermode = 0;
var nyarukolive_flv = "";
var nyarukolive_hls = "";
var nyarukolive_protocol = "";
var nyarukolive_timezone = 10000;
var nyarukolive_lconf = 0;
var isfullScreen = false;
var nyarukolive_barragecache = [];
var nyarukolive_updatebarragespeed = 300;
var nyarukolive_updatestatusspeed = 5000;
var nyarukolive_oldbarrageid = 0;
var nyarukolive_update_frequency = 5;
var nyarukolive_dmnum = 0;
var nyarukolive_dmW = 0;
var nyarukolive_dmH = 26;//防错弹幕初始高，根据字号，字体修改
var nyarukolive_dmTop = 10;//弹幕初始top
var nyarukolive_dmspacing = 5;//弹幕行距
var nyarukolive_dmObj = [];
var nyarukolive_dmtime = 5000;//弹幕速度
function nyarukolive_loadconfig(config) {
    if (config["pcode"] && config["pinfo"]) {
        console.log("[wpNyarukoLive]", config["pcode"], config["pinfo"]);
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
    if (config["timedst"]) nyarukolive_timedst = parseInt(config["timedst"]);
    return 0;
}
function nyarukolive_selectmode(nmode) {
    nyarukolive_playermode = nmode;
    if (nyarukolive_playermode == 0) {
        autoselectmode();
    } else if (nyarukolive_playermode == 1) {
        console.log("[wpNyarukoLive] flv mode",nyarukolive_flv);
        player = flvjs.createPlayer({
            type: 'flv',
            url: nyarukolive_flv
        });
        player.attachMediaElement(video);
        player.load();
        nyarukolive_videoready();
    } else if (nyarukolive_playermode == 2) {
        console.log("[wpNyarukoLive] hls mode");
        player = new Hls();
        player.loadSource(nyarukolive_hls);
        player.attachMedia(video);
        player.on(Hls.Events.MANIFEST_PARSED,function() {
            nyarukolive_videoready();
        });
    } else if (nyarukolive_playermode == 3) {
        nyarukolive_playermode = 3;
        console.log("[wpNyarukoLive] hls+ mode");
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
    console.log("[wpNyarukoLive] Ready.");
    // playpausebtn();
}
function nyarukolive_playpausebtn() {
    // if (ready) {
        if (playing) {
            console.log("[wpNyarukoLive] Pause.");
            // pauseboxi.style.display='block';
            if (nyarukolive_playermode == 2) {
                video.pause();
            } else {
                player.pause();
            }
            pauseboxi.style.display='block';
            if (isfullScreen) fsiconbtn.style.display='block';
            pauseboxi2.src = nyarukolive_pluginurl+"lib/baseline-play_arrow-24px.svg";
        } else {
            console.log("play");
            // pauseboxi.style.display='none';
            if (nyarukolive_playermode == 2) {
                video.play();
            } else {
                player.play();
            }
            pauseboxi.style.display='none';
            if (isfullScreen) fsiconbtn.style.display='none';
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
function nyarukolive_showalert(text) {
    if ($("#nyarukolive_alert").length > 0) {
        $("#nyarukolive_alert").stop();
        $("#nyarukolive_alert").remove();
    }
    var nyarukolive_alertbox = document.getElementById("nyarukolive_alertbox");
    nyarukolive_alertbox.innerHTML = '<div id="nyarukolive_alert">'+text+'</div>';
    $("#nyarukolive_alert").fadeOut(3000,function() {
        $("#nyarukolive_alert").remove();
    });
}
function updatetime() {
    if (btndanmusent.style.display == "none") {
        var newsec = parseInt(btndanmusentwait.innerText) - 1;
        if (newsec <= 0) {
            btndanmusentwait.innerText = nyarukolive_update_frequency+" ";
            btndanmusentwait.style.display = "none";
            btndanmusent.style.display = "inline-block";
        } else {
            btndanmusentwait.innerText = newsec+" ";
        }
    }
    var dt = new Date();
    var localtimestr = (updatetimezero(dt.getHours()) + ":" + updatetimezero(dt.getMinutes()) + ":" + updatetimezero(dt.getSeconds()));
    if (document.getElementById('nyarukolive_ltime')) document.getElementById('nyarukolive_ltime').innerText = localtimestr;
    if (nyarukolive_timezone != 10000) {
        var worldtimea = worldtime(nyarukolive_timezone,false,nyarukolive_timedst);
        var wtimestr = updatetimezero(worldtimea[0])+":"+updatetimezero(worldtimea[1])+":"+updatetimezero(worldtimea[2]);
        if (document.getElementById('nyarukolive_wtime')) document.getElementById('nyarukolive_wtime').innerText = wtimestr;
    }
}
function worldtime(tposition,isDate = false,dst = 0) {
	var myDate = new Date();
	var timeoff = new Date().getTimezoneOffset();
	timeoff = timeoff / 60;//获取时区
	var zerotime = myDate.getHours() + timeoff;
	var wordtimehours = zerotime + tposition;
	var wordtime = [];
    wordtimehours += dst;
	if(wordtimehours < 0){
		wordtimehours += 24;
	}else if(wordtimehours > 24){
		wordtimehours -= 24;
    }
	if(isDate){
		wordtime.push(myDate.getFullYear());
		wordtime.push((myDate.getMonth()+1));
		wordtime.push(myDate.getDate());
	}
	wordtime.push(wordtimehours);
	wordtime.push(myDate.getMinutes());
	wordtime.push(myDate.getSeconds());
	return wordtime;
}
function updatetimezero(num) {
    return ((num <= 9) ? ("0" + num) : num);
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
        nyarukolive_showalert("用户名和电子邮件均不能为空。");
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
        nyarukolive_showalert("请先输入用户信息，点对勾保存");
        swmenu(1,true);
    }
}
function changemodebtn(tomode) {
    var locationhref = window.location.href;
    for (let i = 0; i <= 3; i++) {
        locationhref = locationhref.replace(("&liveplayermode="+i), "");
        locationhref = locationhref.replace(("?liveplayermode="+i), "");
    }
    var linkchar = "?";
    if (locationhref.indexOf(linkchar) > -1) {
        linkchar = "&";
    }
    window.location.href = locationhref + linkchar + "liveplayermode=" + tomode;
}
function getStatus() {
    if (nyarukolive_debug > 1) {
        var ndate = new Date();
        console.log("[wpNyarukoLive] "+ndate.toLocaleTimeString());
    }
    if (nyarukolive_config["liveid"] <= 0) return;
    var guestinfo = loadguestname(true);
    var blockbullet = 1;
    if (nyarukolive_lconf == 0) blockbullet = document.getElementById("nyarukolive_blockbullet").value;
    var gstatus = {
        "api":2,
        "liveid":nyarukolive_config["liveid"],
        "blockbullet":blockbullet,
        "oldbarrageid":nyarukolive_oldbarrageid,
        "frequency":nyarukolive_update_frequency,
        "limit":50,
        "token":nyarukolive_config["token"],
        "email":guestinfo[1],
        "browsertoken":nyarukolive_config["browsertoken"]
    };
    if (nyarukolive_debug > 1) console.log("↑",gstatus);
    $.post(nyarukolive_config["api"],gstatus,function(result){
        if (nyarukolive_debug > 1) console.log("↓",result);
        if (result && result != "") {
            var dmjson = null;
            if (typeof(result) == "object") {
                dmjson = result;
            } else {
                dmjson = $.parseJSON(result);
            }
            if (serplaying == 0) {
                serplaying = dmjson.isplaying;
            } else if ((serplaying < 0 && dmjson.isplaying > 0) || (serplaying > 0 && dmjson.isplaying < 0)) {
                console.log("[wpNyarukoLive] Reloading...");
                if (nyarukolive_debug > 0) {
                    nyarukolive_config["liveid"] = -2;
                    var ndate = new Date();
                    var alertxt = "[wpNyarukoLive] 【△】直播状态发生改变！ "+ndate.toLocaleTimeString();
                    document.title = alertxt;
                    alert(alertxt);
                }
                location.reload();
            }
            if (dmjson.code == 0 && dmjson.isplaying > 0 && dmjson.liveid == nyarukolive_config["liveid"]) {
                dmjson.barrages.forEach(nowdm => {
                    var nowbarrageid = parseInt(nowdm[0]);
                    if (nowbarrageid > nyarukolive_oldbarrageid) {
                        nyarukolive_oldbarrageid = nowbarrageid;
                    }
                });
                nyarukolive_barragecache = dmjson.barrages;
            }
        } else {
            console.log("状态检查失败！");
        }
    }).error(function(err){
        console.log("状态检查失败。");
        if (nyarukolive_debug > 0) console.log(err.responseText);
    });
}
function addbarrage() {
    if (nyarukolive_barragecache.length > 0) {
        nyarukolive_sendDanMu(nyarukolive_barragecache[0][5]);
        nyarukolive_barragecache.splice(0,1);
    }
}
function sendBulletComment(iskey=false) {
    if (iskey) {
        if(event.keyCode != 13){
            return;
        }
    }
    var guestinfo = loadguestname(true);
    var danmuchat = document.getElementById("nyarukolive_danmuchat");
    var content = danmuchat.value;
    if (!cleartext(danmuchat,false,true,true)) {
        nyarukolive_showalert("输入中包括不支持的符号");
        return;
    }
    if (content == "") {
        nyarukolive_showalert("弹幕内容不能为空");
        return;
    }
    if (btndanmusent.style.display == "none") {
        nyarukolive_showalert("发送太频繁了，休息一下");
        return;
    }
    if (btndanmusent.style.display == "none") {
        nyarukolive_showalert("发送太频繁了，休息一下");
        return;
    }
    if (guestinfo[0].length < 1 && guestinfo[1].length < 1) {
        nyarukolive_showalert("请输入用户名和邮箱");
        swmenu(1,true);
        return;
    }
    if (guestinfo[0].length < 3) {
        nyarukolive_showalert("用户名至少三位");
        swmenu(1,true);
        return;
    }
    if (guestinfo[1].length < 5 || guestinfo[1].indexOf("@") == -1 || guestinfo[1].indexOf(".") == -1) {
        nyarukolive_showalert("请输入有效邮件地址");
        swmenu(1,true);
        return;
    }
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
    if (nyarukolive_debug > 1) console.log("↑",bulletcomment);
    $.post(nyarukolive_config["api"],bulletcomment,function(result){
        if (nyarukolive_debug > 1) console.log("↓",result);
        if (result && result != "") {
            var nrjson = null;
            if (typeof(result) == "object") {
                nrjson = result;
            } else {
                nrjson = $.parseJSON(result);
            }
            var jcode = nrjson.code;
            var jmsg = nrjson.msg;
            if (nrjson && typeof(nrjson) == "object") {
                if (jcode == 0) {
                    //nyarukolive_showalert(jmsg); //OK
                    nyarukolive_sendDanMu(nrjson.content,true);
                    danmuchat.value = "";
                } else {
                    sendBulletCommentFail(jmsg);
                }
            } else {
                sendBulletCommentFail("");
            }
        } else {
            sendBulletCommentFail("");
        }
    }).error(function(err){
        console.log("弹幕提交失败。");
        if (nyarukolive_debug > 0) console.log(err.responseText);
    });
    btndanmusent.style.display = "none";
    btndanmusentwait.innerText = nyarukolive_update_frequency+" ";
    btndanmusentwait.style.display = "inline-block";
}
function sendBulletCommentFail(errinfo) {
    var einfo = "弹幕发送失败。";
    if (errinfo != "") {
        einfo += errinfo;
    }
    nyarukolive_showalert(einfo);
}

function cleartext(thistbox,isstring = false,usefullchar = false,norevalue=true) {
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
        if (norevalue) {
            if (thistbox.value != rs) {
                thistbox.style.color = '#F00';
                return false;
            } else {
                thistbox.style.color = '';
                return true;
            }
        } else {
            thistbox.value = rs;
        }
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
    nyarukolivediv.className = "nyarukolivefullpage";
    var nyarukolive_titlebar = document.getElementById('nyarukolive_titlebar');
    var nyarukolive_footbar = document.getElementById('nyarukolive_footbar');
    // if (nyarukolive_videobox.style.height > getClientHeight() - 90) {}
    nyarukolive_footbar.style.display = "none";
    nyarukolive_titlebar.style.display = "none";
    fsiconbtn.style.display = pauseboxi.style.display;
    if (element.requestFullscreen) {
        element.requestFullscreen();
    } else if (element.mozRequestFullScreen) {
        element.mozRequestFullScreen();
    } else if (element.webkitRequestFullScreen) {
        element.webkitRequestFullScreen();
    }
    isfullScreen = true;
}
function exitFullscreen(element) {
    nyarukolivediv.className = "";
    var nyarukolive_titlebar = document.getElementById('nyarukolive_titlebar');
    var nyarukolive_footbar = document.getElementById('nyarukolive_footbar');
    nyarukolive_footbar.style.display = "table";
    nyarukolive_titlebar.style.display = "table";
    fsiconbtn.style.display = "none";
    if (element.exitFullscreen) {
        element.exitFullscreen();
    } else if (element.mozCancelFullScreen) {
        element.mozCancelFullScreen();
    } else if (element.webkitCancelFullScreen) {
        element.webkitCancelFullScreen();
    }
    isfullScreen = false;
}
function getClientHeight()
{
  var clientHeight=0;
  if(document.body.clientHeight&&document.documentElement.clientHeight)
  {
  var clientHeight = (document.body.clientHeight<document.documentElement.clientHeight)?document.body.clientHeight:document.documentElement.clientHeight;
  }
  else
  {
  var clientHeight = (document.body.clientHeight>document.documentElement.clientHeight)?document.body.clientHeight:document.documentElement.clientHeight;
  }
  return clientHeight;
}
function fullScreen() {
    if (isfullScreen) {
        exitFullscreen(document);
    } else {
        requestFullScreen(nyarukolivediv);
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
    console.log("[wpNyarukoLive] Loading Video ...OK");
    setbrowsertoken();
    if (typeof(nyarukolive_config) == "undefined") nyarukolive_error(1);
    if (nyarukolive_lconf == 0) {
        if (chkhttps()) {
            getStatus();
            setInterval("getStatus()",nyarukolive_updatestatusspeed);
            nyarukolive_selectmode(nyarukolive_playermode);
            updatetime();
            setInterval("updatetime()",1000);
            addbarrage();
            setInterval("addbarrage()",nyarukolive_updatebarragespeed);
        }
    } else if (nyarukolive_lconf == 1) {
        getStatus();
        setInterval("getStatus()",nyarukolive_updatestatusspeed);
    } else {
        nyarukolive_error(nyarukolive_lconf);
    }
    console.log("[wpNyarukoLive] Loading Video ...OK");
    removeWpNyarukoNPlayer();
}
nyarukolive_lconf = nyarukolive_loadconfig(nyarukolive_config);
if (typeof(yashitheme) != "undefined" && yashitheme == "wpnyarukof" && nyarukolive_playermode == 3) {
    if (wpnyarukolive_ready) {
        wpnyarukolive_ready = false;
    } else {
        wpnyarukoliveinit();
    }
} else {
    wpnyarukoliveinit();
}
//弹幕
Array.prototype.remove = function(val) {
	var index = this.indexOf(val);
	if (index > -1) {
		this.splice(index, 1);
	}
};
function nyarukolive_sortNumber(a,b) 
{
	return a - b;
}
function nyarukolive_sendDanMu(textval,selfsend=false) {
	var backW = $("#nyarukolive_danmubox").width();
	var dmid = 'dm' + nyarukolive_dmnum++;
    var str = textval;
    var dmT = nyarukolive_dmTop;
    var dmTarr = [];
    if(nyarukolive_dmObj.length > 0){
        nyarukolive_dmObj.forEach(function(obj,i){
            var isif = backW - obj.position().left;
            // console.log($(this).position().left,backW,isif,nyarukolive_dmW);
            if(isif < (obj.width()*2)){//控制同行两弹幕的间距
                dmTarr.push(obj.position().top);
            }else{
                nyarukolive_dmObj.remove(obj);
            }
        });
    }
    // console.log('-----------------');
    dmTarr.sort(nyarukolive_sortNumber);
    // console.log(dmTarr);
    if(dmTarr.length > 0){
        dmTarr.forEach(function(obi,i){
            if(obi == dmT){
                dmT += nyarukolive_dmspacing + nyarukolive_dmH;
            }
        });
    }
    var addnewclass = 'span';
    if(selfsend){
        addnewclass += ' nyarukolive_selfdanmu'
    }
    $("<span class='nyarukolive_danmu' id='"+dmid+"'></span>").appendTo("#nyarukolive_danmubox").text(str).addClass(addnewclass).siblings().removeClass("span");
    nyarukolive_dmObj.push($("#"+dmid));
    nyarukolive_dmW = $("#"+dmid).width();
    nyarukolive_dmH = $("#"+dmid).height()
    $("#"+dmid).css({left: backW+'px'});
    $("#"+dmid).css({top: dmT+'px'});
    if($("#"+dmid) > backW){
        $("#"+dmid).css({left: backW+'px'});
    }
    $("#"+dmid).css({top: dmT+'px'});
    $('.nyarukolive_danmu').animate({left:-nyarukolive_dmW},nyarukolive_dmtime,'linear',function(){
        $(this).remove();
        nyarukolive_dmObj.remove($(this));
    });
}
function livedebug(level=1) {
    var nyarukolive_livetitle = document.getElementById("nyarukolive_livetitle");
    nyarukolive_livetitle.innerText += " (调试模式)"
    nyarukolive_debug = level;
}
console.log("[wpNyarukoLive] Loaded.");