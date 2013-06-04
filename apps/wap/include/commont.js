//开始JS
var t = new Date();
var mark = function(tid,pid,num){
	if(tid && pid){
		$.getJSON("apps.php?m=wap&a=ajax",{"option":"mymark","tid":tid,"pid":pid,"num":num,"t":t.getTime()},function(data){
			if(data.status){
				$("#list_"+pid).css({ backgroundColor: "#FFFF88" });
				}
			else{
				alert(data.message);
				}
			});
		}
	else{
		alert("系统操作有误");
		}
	}