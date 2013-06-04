//2012.06.16加入显示被隐藏的
function show_hidden_posts(num){
	if(num){
		$("#detail_"+num+" #content_"+num).css("background","#FFFF88");
		$("#detail_"+num).toggle(200);
		}
	}

//初始化一个输入框
function input_ban() { 
	$("input, textarea").live("focus", function() {
		INPUT_FOCUS = this
	}).live("blur", function() {
		INPUT_FOCUS = null
	})
}


//删除重复的回复
function delete_reply(num,tid,l){
	if(num && tid){
		jQuery.getJSON("apps.php",{"m":"live","a":"ajax","option":"delete_posts","id":tid,"pid":num},function(data){
			if(data.status == 1){
				dialog.tips("操作成功，感谢你对直播贴做的贡献");
				}
			else if(data.status == 0){
				alert("非法操作，请勿尝试");
				return ;
				}
			else if(data.status == 2){
				dialog.tips("该楼已成功公投出局，感谢你对直播贴做的贡献");
				$("#detail_"+num).hide(200);
				}
			});
		return false;
		}
	else{
		alert("系统错误，请重试");
		}
	}


function gengxin_subject(){
	dialog({content:"亲，你的指令我已经收到，正在收集更新，更新好了会自动刷新...",icon:"face-smile",id:"gengxin_subject_div",lock:true});
	jQuery.post("live.php?do=gengxin_subject","tid=$subject['tid']&t="+new Date().getTime(),function(data){
		var json = eval("("+data+")");
		if(json.status){
			dialog.get("gengxin_subject_div").close()
			dialog({content:json.message,icon:"succeed",time:3});
			setTimeout(function(){
				window.location = "live.php?do=view&tid="+json.data.tid+"&only=author&page=<!--{eval echo $page + 1}-->";
				},2000);
			return false;
			}
		else{
			dialog.get("gengxin_subject_div").close()
			dialog({content:json.message,icon:"face-sad",time:3});
			return false;
		}
		});
	}

function ResetImageSize(e,mWidth)
	{
		var w=e.width;
		var h=e.height;
		if(w>mWidth)
		{
			e.width=mWidth;
			e.height=h*mWidth/w;
		}
	}


function join_ilike_submit_check(){
	var subject = $("#join_ilike_subject");
	if(subject.val() != "" && subject.val().length >= 200){
		alert("哎哟~~，节操掉了一地，别名太深了....");
		subject.focus();
		return false;
		}
	jQuery.post("live.php?do=join_ilike","tid=$_GET['tid']&subject="+ escape2(subject.val()) + "&t=" + new Date().getTime(),function(data){
		var json = eval("("+data+")");
		if(json.status){
			dialog.get("join_ilike_div").content(json.message);
			setTimeout(function(){
				dialog.get("join_ilike_div").close();
				},2000);
			return false;
			}
		else{
			dialog.get("join_ilike_div").content(json.message);
			setTimeout(function(){
				dialog.get("join_ilike_div").close();
				},2000);
			return false;
			}
		});
		return false;
	}

function zhibotie_submit(){
	dialog.get("show_zhibotie_div").content("加载操作中ing...");
	jQuery.post("live.php?do=zhibotie_share","tid="+$("#share_tid").val() + "&content=" + escape2($("#share_content").val()) + "&t="+new Date().getTime(),function(data){
		var json = eval("("+data+")");
		if(json.status){
			dialog.get("show_zhibotie_div").close();
			dialog({content:json.message,icon:"succeed",id:"show_zhibotie_div_l"});
			setTimeout(function(){
				dialog.get("show_zhibotie_div_l").close();
				},2000);
			return false;
			}
		else{
			dialog.get("show_zhibotie_div").close();
			dialog({content:json.message,icon:"error",id:"show_zhibotie_div_l"});
			setTimeout(function(){
				dialog.get("show_zhibotie_div_l").close();
				},2000);
			return false;
			}
		return false;
		});
	return false;
	}

function mark(num,tid){
	if(num && tid){
		jQuery.getJSON("live.php?do=mark&num="+num+"&tid="+tid,function(data){
			if(data.status){
				
				$("#detail_"+num+" #content_"+num).css("background","#FFFF88");
				//dialog.tips("操作成功：）");
				}
			else{
				alert("系统错误");
				}
			});
		return false;
		}
	}