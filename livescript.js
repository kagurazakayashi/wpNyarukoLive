var video = document.getElementById('nyarukolive_video');
var videosrc = document.getElementById('nyarukolive_videosrc');
var pauseboxi = document.getElementById('nyarukolive_playbtn');
var player = null;
var ready = false;
var playing = false;
function selectmode(nmode) {
    mode = nmode;
    if (mode == 0) {
        autoselectmode();
    } else if (mode == 1) {
        console.log("flv mode");
        player = flvjs.createPlayer({
            type: 'flv',
            url: nyarukolive_flv
        });
        player.attachMediaElement(video);
        player.load();
        videoready();
    } else if (mode == 2) {
        console.log("hls mode");
        player = new Hls();
        player.loadSource(nyarukolive_hls);
        player.attachMedia(video);
        player.on(Hls.Events.MANIFEST_PARSED,function() {
            videoready();
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
            videoready();
        });
    }
}
function videoready() {
    ready = true;
    console.log("ready.");
    // playpausebtn();
}
function playpausebtn() {
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
selectmode(nyarukolive_playermode);
console.log("Loading Video ...OK");