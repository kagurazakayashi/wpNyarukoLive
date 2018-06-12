// JavaScript Document
var nyarukolive_dmnum = 0;
var nyarukolive_dmW = 0;
var nyarukolive_dmH = 26;//防错弹幕初始高，根据字号，字体修改
var nyarukolive_dmTop = 10;//弹幕初始top
var nyarukolive_dmspacing = 5;//弹幕行距
var nyarukolive_dmObj = [];
var nyarukolive_dmtime = 5000;//弹幕速度

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
function nyarukolive_sendDanMu(textval){
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

		$("<span class='danmu' id='"+dmid+"'></span>").appendTo("#nyarukolive_danmubox").text(str).addClass("span").siblings().removeClass("span");
		nyarukolive_dmObj.push($("#"+dmid));
		nyarukolive_dmW = $("#"+dmid).width();
		nyarukolive_dmH = $("#"+dmid).height()
		$("#"+dmid).css({left: backW+'px'});
		$("#"+dmid).css({top: dmT+'px'});
		if($("#"+dmid) > backW){
			$("#"+dmid).css({left: backW+'px'});
		}
		$("#"+dmid).css({top: dmT+'px'});
		$('.danmu').animate({left:-nyarukolive_dmW},nyarukolive_dmtime,'linear',function(){
			$(this).remove();
			nyarukolive_dmObj.remove($(this));
		});
}
