var url = "/index.php";
$(function() {
	$('a').each(function() {
		if ($(this).attr("href").indexOf('java')<0) {/*
														 * $(this).attr("href") !=
														 * "/" ||
														 */
			$(this).attr("href", url + $(this).attr("href"));
		}
	});
});


$(function() {
	var date = getfulldate();
	$("#starttime").val(date);
})

function getfulldate() {
	var date = new Date();
	var year = date.getFullYear();
	var month = date.getMonth() + 1;
	var day = date.getDate();
	if (month * 1 < 10) {
		month = "0" + month;
	}
	if (day * 1 < 10) {
		day = "0" + day;
	}
	var full = year + "-" + month + "-" + day;
	return full;

}
function getQueryStringByName(name) {
    var result = window.location.search.match(new RegExp("[\?\&]" + name + "=([^\&]+)", "i"));
    if (result == null || result.length < 1) {
        return "";
    }
    return result[1];
}

function layercontent(content,fun1,title,fun2){	
	if(title==""||title==undefined){
		title="确认";		
	}
	if(fun2==""||fun2==undefined){
		fun2=function() {};	
	}
	$.layer({shade:[0],area:['auto','auto'],
		title:title,
		dialog:{
		msg:content,
		btns:2,
		type:4,
		btn:['确认','取消'],
		yes:fun1,no:fun2
		}
	});
}

function layeralert(content,fun,title){
	if(title==""||title==undefined){
		title="确认";		
	}
	$.layer({shade:[0],area:['auto','auto'],
		title:title,
		dialog:{
		msg:content,
		btns:1,
		type:4,
		btn:['确认'],
		yes:fun
		}
	});
}
$(document).on("mousemove",".spantopwork",function(){
	$(this).addClass("spandown");
	$(this).removeClass("spantop");
	$(".spandivwork").show();
});
$(document).on("mouseout",".spantopwork",function(){
	$(this).removeClass("spandown");
	$(this).addClass("spantop");
	$(".spandivwork").hide();
});


$(document).on("mousemove",".atopuser",function(){
	$(".atopuserinfo").show();
});
$(document).on("mouseout",".atopuser",function(){
	$(".atopuserinfo").hide();
});


$(document).on("mousemove",".shobackcolor",function(){
	$(this).css("background-color","#A3A3A3");
	//$($(this).find(".left")).css("background-color",$(this).find("span").attr("guid"));
});
$(document).on("mouseout",".shobackcolor",function(){
	$(this).css("background-color","#EFEFEF");
	//$($(this).find(".left")).css("background-color","#fff")
});


function showiframe(title, src) {
	$.layer( {
		type : 2,
		title : title,
		shadeClose : true,
		maxmin : false,
		fix : false,
		area : [ '800px', '500px' ],
		iframe : {
			src : src
		}
	});

}