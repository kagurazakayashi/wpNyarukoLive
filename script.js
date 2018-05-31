function wpNyarukoOptionChTab(tabid) {
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
    var urlsh = window.location.href.split("#");
    if (urlsh.length == 2) {
        window.location.href = urlsh[0] + "#" + tabid;
    }
}