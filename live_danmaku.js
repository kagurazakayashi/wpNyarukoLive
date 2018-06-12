// JavaScript Document
var dmnum = 0;
var dmW = 0;
var dmH = 26;//防错弹幕初始高，根据字号，字体修改
var dmTop = 10;//弹幕初始top
var dmspacing = 5;//弹幕行距
var dmObj = [];
var dmtime = 5000;//弹幕速度

Array.prototype.remove = function(val) {
	var index = this.indexOf(val);
	if (index > -1) {
		this.splice(index, 1);
	}
};
function sortNumber(a,b) 
{ 
	return a - b;
}
function sendDanMu(textval){
	var backW = $(".back").width();
	var dmid = 'dm' + dmnum++;
		var str = textval;
		var dmT = dmTop;
		var dmTarr = [];
		if(dmObj.length > 0){
			dmObj.forEach(function(obj,i){
				var isif = backW - obj.position().left;
				// console.log($(this).position().left,backW,isif,dmW);
				if(isif < (obj.width()*2)){//控制同行两弹幕的间距
					dmTarr.push(obj.position().top);
				}else{
					dmObj.remove(obj);
				}
			});
		}
		// console.log('-----------------');
		dmTarr.sort(sortNumber);
		// console.log(dmTarr);
		if(dmTarr.length > 0){
			dmTarr.forEach(function(obi,i){
				if(obi == dmT){
					dmT += dmspacing + dmH;
				}
			});
		}

		$("<span class='danmu' id='"+dmid+"'></span>").appendTo(".back").text(str).addClass("span").siblings().removeClass("span");
		dmObj.push($("#"+dmid));
		dmW = $("#"+dmid).width();
		dmH = $("#"+dmid).height()
		$("#"+dmid).css({left: backW+'px'});
		$("#"+dmid).css({top: dmT+'px'});
		if($("#"+dmid) > backW){
			$("#"+dmid).css({left: backW+'px'});
		}
		$("#"+dmid).css({top: dmT+'px'});
		$('.danmu').animate({left:-dmW},dmtime,'linear',function(){
			$(this).remove();
			dmObj.remove($(this));
		});
}
