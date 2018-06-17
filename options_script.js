function wpNyarukoOptionChTab(tabid,first = false) {
    if (tabid == 0) {
        var urlsh = window.location.href.split("#");
        if (urlsh.length == 2) {
            tabid = parseInt(urlsh[1]);
        }
    }
    if (tabid <= 0 || tabid > 4) {
        tabid = 1;
    }
    var wpNyarukoOptionMenuBarItem = document.getElementsByClassName("wpNyarukoOptionMenuBarItem");
    for (let index = 0; index < wpNyarukoOptionMenuBarItem.length; index++) {
        const element = wpNyarukoOptionMenuBarItem[index];
        element.className = "wpNyarukoOptionMenuBarItem";
    }
    document.getElementById("wpNyarukoOptionMenuBarItem"+tabid).className = "wpNyarukoOptionMenuBarItem wpNyarukoOptionMenuBarItemSelected";
    var wpNyarukoOptionTabs = document.getElementsByClassName("wpNyarukoOptionTab")
    for (let index = 0; index < wpNyarukoOptionTabs.length; index++) {
        const element = wpNyarukoOptionTabs[index];
        element.style.display = "none";
    }
    document.getElementById("wpNyarukoOptionTab"+tabid).style.display = "inline";
    if (!first) window.location.href = 'tools.php?page=nyarukolive-options#'+tabid;
}
function wpNyarukoOptionCMgLiveMode(cmode,liveid) {
    window.location.href = 'tools.php?page=nyarukolive-options&nyamode=mglive&liveid='+liveid+'&cmode='+cmode+'&tabid=2';
}
function wpNyarukoOptionCMgLiveDelete(liveid) {
    if (window.confirm("将会永久删除这条记录和相关弹幕。\n如果后台接口再次收到目标为此条目的设置，将新建条目。\n确定吗？")) window.location.href = 'tools.php?page=nyarukolive-options&nyamode=mgdellive&liveid='+liveid+'&tabid=2';
}
function wpNyarukoOptionCMgDanmakuDelete(danmakuid) {
    if (window.confirm("将会永久删除这条弹幕，确定吗？")) window.location.href = 'tools.php?page=nyarukolive-options&nyamode=mgdeldanmaku&danmakuid='+danmakuid+'&tabid=3';
}
function wpNyarukoOptionCMgBanDelete(banid) {
    if (window.confirm("将不再对此项进行屏蔽，确定吗？")) window.location.href = 'tools.php?page=nyarukolive-options&nyamode=mgdelban&banid='+banid+'&tabid=4';
}
function wpNyarukoOptionCMgLiveDanmaku(liveid) {
    window.location.href = 'tools.php?page=nyarukolive-options&nyamode=mddmmgr&liveid='+liveid+'&tabid=3#3';
}