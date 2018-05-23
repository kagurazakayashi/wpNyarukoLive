var video = document.getElementById('nyarukolive_video');
var videosrc = document.getElementById('nyarukolive_videosrc');
var pauseboxi = document.getElementById('nyarukolive_playbtn');
var player = null;
var ready = false;
var playing = false;
var nyarukolive_playermode = 0;
var nyarukolive_flv = "";
var nyarukolive_hls = "";
var nyarukolive_protocol = "";
function nyarukolive_loadconfig(config) {
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
        player = videojs('nyarukolivevideo',{
            bigPlayButton : false,
            textTrackDisplay : true,
            posterImage: true,
            errorDisplay : true,
            controlBar : true
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
        } else {
            console.log("play");
            // pauseboxi.style.display='none';
            if (mode == 2) {
                video.play();
            } else {
                player.play();
            }
            pauseboxi.style.display='none';
        }
        playing = !playing;
    // } else {
    //     console.log("no ready");
    // }
}
function nyarukolive_error(err) {
    $("#nyarukolive").html('<div id="nyarukolive_errorinfo" class="nyarukolive_errordig"><p><b>直播播放器加载失败</b></p><p>错误代码：'+err+'</p></div>');
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
        $("#nyarukolive").html('<div id="nyarukolive_errorinfo" class="nyarukolive_warndig"><p><b>正在配置传输协议</b></p><p>正在配置 HTTP'+newprotocol+' ...</p></div>');
        setTimeout("window.location.href='"+newurl+"';",1000);
        return false;
    }
    return true;
}
if (typeof(nyarukolive_config) == "undefined") nyarukolive_error(1);
nyarukolive_lconf = nyarukolive_loadconfig(nyarukolive_config);
if (nyarukolive_lconf == 0) {
    if (chkhttps()) nyarukolive_selectmode(nyarukolive_playermode);
} else {
    nyarukolive_error(nyarukolive_lconf);
}
console.log("Loading Video ...OK");