function wpNyarukoOptionChTab(tabid,first = false) {
    if (tabid == 0) {
        var urlsh = window.location.href.split("#");
        if (urlsh.length == 2) {
            tabid = parseInt(urlsh[1]);
        }
    }
    if (tabid <= 0 || tabid > 3) {
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
function wpNyarukoOptionCMgLiveMode(cmode,live_id) {
    window.location.href = 'tools.php?page=nyarukolive-options&nyamode=mglive&live_id='+live_id+'&cmode='+cmode;
}
function wpNyarukoOptionCMgLiveDelete(live_id) {
    if (window.confirm("将会永久删除这条记录和相关弹幕。\n如果后台接口再次收到目标为此条目的设置，将新建条目。\n确定吗？")) window.location.href = 'tools.php?page=nyarukolive-options&nyamode=mgdellive&live_id='+live_id;
}
function wpNyarukoOptionCMgLiveDanmaku(live_id) {
    window.location.href = 'tools.php?page=nyarukolive-options&nyamode=mddmmgr&live_id='+live_id;
}