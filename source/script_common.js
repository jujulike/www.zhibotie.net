/*

	[UCenter Home] (C) 2007-2008 Comsenz Inc.

	$Id: script_common.js 13191 2009-08-18 03:14:55Z xupeng $

*/

var userAgent=navigator.userAgent.toLowerCase(),is_opera=-1!=userAgent.indexOf("opera")&&opera.version(),is_moz="Gecko"==navigator.product&&userAgent.substr(userAgent.indexOf("firefox")+8,3),is_ie=-1!=userAgent.indexOf("msie")&&!is_opera&&userAgent.substr(userAgent.indexOf("msie")+5,3),is_safari=-1!=userAgent.indexOf("webkit")||-1!=userAgent.indexOf("safari"),note_step=0,note_oldtitle=document.title,note_timer,regemail=/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/,dialog=art.dialog;top.location!=location&&(top.location.href=location.href);function $(a){return typeof jQuery=="undefined"||typeof a=="string"&&document.getElementById(a)?document.getElementById(a):typeof a=="object"||!/^\w*$/.exec(a)||/^(body|div|span|a|input|textarea|button|img|ul|li|ol|table|tr|th|td)$/.exec(a)?jQuery(a):null}
function prompt(string,v){dialog.prompt(string,function(data){},v);}
function addSort(a){if(a.value=="addoption"){var c=document.createElement("div");c.id=a.id+"_menu";c.innerHTML="<h1>添加</h1><a href=\"javascript:;\" onclick=\"addOption('newsort', '"+a.id+'\')" class="float_del">删除</a><div class="popupmenu_inner" style="text-align: center;">名称：<input type="text" name="newsort" size="10" id="newsort" class="t_input" /><input type="button" name="addSubmit" value="创建" onclick="addOption(\'newsort\', \''+a.id+'\')" class="button" /></div>';c.className="popupmenu_centerbox";c.style.cssText="position: absolute; left: 50%; top: 200px; width: 400px; margin-left: -200px;";document.body.appendChild(c);$("newsort").focus()}}
function addOption(a,c){var b=$(c),d=$(a).value;$(a).value="";if(d!=null&&d!=""){var g=document.createElement("option");g.text=d;g.value="new:"+d;try{b.add(g,b.options[0])}catch(h){b.add(g,b.selecedIndex)}
b.value="new:"+d}else b.value=b.options[0].value;b=document.getElementById(c+"_menu");b.parentNode.removeChild(b)}
function checkAll(a,c){for(var b=0;b<a.elements.length;b++){var d=a.elements[b];if(d.name.match(c))d.checked=a.elements.chkall.checked}}
function cnCode(a){return is_ie&&document.charset=="utf-8"?encodeURIComponent(a):a}
function isUndefined(a){return typeof a=="undefined"?true:false}
function in_array(a,c){if(typeof a=="string"||typeof a=="number")for(var b in c)if(c[b]==a)return true;return false}
function strlen(a){return is_ie&&a.indexOf("\n")!=-1?a.replace(/\r?\n/g,"_").length:a.length}
function getExt(a){return a.lastIndexOf(".")==-1?"":a.substr(a.lastIndexOf(".")+1,a.length).toLowerCase()}
function doane(a){e=a?a:window.event;if(is_ie){e.returnValue=false;e.cancelBubble=true}else if(e){e.stopPropagation();e.preventDefault()}}
function seccode(){var a="do.php?ac=seccode&rand="+Math.random();document.writeln('<img id="img_seccode" src="'+a+'" align="absmiddle">')}
function updateseccode(){var a="do.php?ac=seccode&rand="+Math.random();if($("img_seccode"))$("img_seccode").src=a}
function resizeImg(a,c){var b=$(a).getElementsByTagName("img");for(i=0;i<b.length;i++)b[i].onload=function(){if(this.width>c){this.style.width=c+"px";if(this.parentNode.tagName.toLowerCase()!="a"){var a=document.createElement("div");this.parentNode.insertBefore(a,this);a.appendChild(this);a.style.position="relative";a.style.cursor="pointer";this.title="点击图片，在新窗口显示原始尺寸";var b=document.createElement("img");b.src="image/zoom.gif";b.style.position="absolute";b.style.marginLeft=c-28+"px";b.style.marginTop="5px";this.parentNode.insertBefore(b,this);a.onmouseover=function(){b.src="image/zoom_h.gif"};a.onmouseout=function(){b.src="image/zoom.gif"};a.onclick=function(){window.open(this.childNodes[1].src)}}}}}
function ctrlEnter(a,c,b){isUndefined(b)&&(b=0);if((a.ctrlKey||b)&&a.keyCode==13){$(c).click();return false}
return true}
function zoomTextarea(a,c){zoomSize=c?10:-10;obj=$(a);if(obj.rows+zoomSize>0&&obj.cols+zoomSize*3>0){obj.rows=obj.rows+zoomSize;obj.cols=obj.cols+zoomSize*3}}
function setCopy(a){if(is_ie){clipboardData.setData("Text",a);alert("网址<font color=\"red\">“"+a+"”</font><br>已经复制到您的剪贴板中\n您可以使用Ctrl+V快捷键粘贴到需要的地方")}else prompt("请复制网站地址:",a)}
function ischeck(a,c){form=document.getElementById(a);for(var b=0;b<form.elements.length;b++){var d=form.elements[b];if(d.name.match(c)&&d.checked)return confirm("您确定要执行本操作吗？")?true:false}
alert("请选择要操作的对象");return false}
function showPreview(a,c){var b=$(c);if(typeof b=="object")b.innerHTML=a.replace(/\n/ig,"<br />")}
function getEvent(){if(document.all)return window.event;for(func=getEvent.caller;func!=null;){var a=func.arguments[0];if(a&&(a.constructor==Event||a.constructor==MouseEvent||typeof a=="object"&&a.preventDefault&&a.stopPropagation))return a;func=func.caller}
return null}
function copyRow(a){var c=false,b;if($(a).rows.length==1&&$(a).rows[0].style.display=="none"){$(a).rows[0].style.display="";b=$(a).rows[0]}else{b=$(a).rows[0].cloneNode(true);c=true}
tags=b.getElementsByTagName("input");for(i in tags)if(tags[i].name=="pics[]")tags[i].value="http://";c&&$(a).appendChild(b)}
function delRow(a,c){if($(c).rows.length==1){var b=a.parentNode.parentNode;tags=b.getElementsByTagName("input");for(i in tags)if(tags[i].name=="pics[]")tags[i].value="http://";b.style.display="none"}else $(c).removeChild(a.parentNode.parentNode)}
function insertWebImg(a){if(checkImage(a.value)){insertImage(a.value);a.value="http://"}else alert("图片地址不正确")}
function checkFocus(a){a=$(a);a.hasfocus||a.focus()}
function insertImage(a){insertContent("message","\n[img]"+a+"[/img]\n")}
function insertContent(a,c){var b=$(a);selection=document.selection;checkFocus(a);if(isUndefined(b.selectionStart))if(selection&&selection.createRange){b=selection.createRange();b.text=c;b.moveStart("character",-strlen(c))}else b.value=b.value+c;else b.value=b.value.substr(0,b.selectionStart)+c+b.value.substr(b.selectionEnd)}
function checkImage(a){return a.match(/^http\:\/\/.{5,200}\.(jpg|gif|png)$/i)}
function quick_validate(a){if($("seccode")){var c=$("seccode").value;(new Ajax).get("cp.php?ac=common&op=seccode&code="+c,function(b){b=trim(b);if(b!="succeed"){alert(b);$("seccode").focus();return false}
a.form.submit();return true})}else{a.form.submit();return true}}
function trim(a){/\s*(\S[^\0]*\S)\s*/.exec(a);return RegExp.$1}
function stopMusic(a,c){var b=a.toString()+"_"+c.toString();$(b)&&$(b).SetVariable("closePlayer",1)}
function showFlash(host,flashvar,obj,shareid){var flashAddr={'youku.com':'http://player.youku.com/player.php/sid/FLASHVAR=/v.swf','ku6.com':'http://player.ku6.com/refer/FLASHVAR/v.swf','youtube.com':'http://www.youtube.com/v/FLASHVAR','5show.com':'http://www.5show.com/swf/5show_player.swf?flv_id=FLASHVAR','sina.com.cn':'http://vhead.blog.sina.com.cn/player/outer_player.swf?vid=FLASHVAR','sohu.com':'http://v.blog.sohu.com/fo/v4/FLASHVAR','mofile.com':'http://tv.mofile.com/cn/xplayer.swf?v=FLASHVAR','tudou.com':'http://www.tudou.com/v/FLASHVAR','music':'FLASHVAR','flash':'FLASHVAR'};var flash='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="380" height="300">'
+'<param name="movie" value="FLASHADDR" />'
+'<param name="quality" value="high" />'
+'<param name="bgcolor" value="#FFFFFF" />'
+'<embed width="380" height="300" menu="false" quality="high" src="FLASHADDR" type="application/x-shockwave-flash" />'
+'</object>';var videoFlash='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="380" height="350">'
+'<param value="transparent" name="wmode"/>'
+'<param value="FLASHADDR" name="movie" />'
+'<embed src="FLASHADDR" wmode="transparent" allowfullscreen="true" type="application/x-shockwave-flash" width="380" height="350"></embed>'
+'</object>';var musicFlash='<object id="audioplayer_SHAREID" height="24" width="290" data="image/player.swf" type="application/x-shockwave-flash">'
+'<param value="image/player.swf" name="movie"/>'
+'<param value="autostart=yes&bg=0xCDDFF3&leftbg=0x357DCE&lefticon=0xF2F2F2&rightbg=0xF06A51&rightbghover=0xAF2910&righticon=0xF2F2F2&righticonhover=0xFFFFFF&text=0x357DCE&slider=0x357DCE&track=0xFFFFFF&border=0xFFFFFF&loader=0xAF2910&soundFile=FLASHADDR" name="FlashVars"/>'
+'<param value="high" name="quality"/>'
+'<param value="false" name="menu"/>'
+'<param value="#FFFFFF" name="bgcolor"/>'
+'</object>';var musicMedia='<object height="64" width="290" data="FLASHADDR" type="audio/x-ms-wma">'
+'<param value="FLASHADDR" name="src"/>'
+'<param value="1" name="autostart"/>'
+'<param value="true" name="controller"/>'
+'</object>';var flashHtml=videoFlash;var videoMp3=true;if(''==flashvar){alert('音乐地址错误，不能为空');return false;}
if('music'==host){var mp3Reg=new RegExp('.mp3$','ig');var flashReg=new RegExp('.swf$','ig');flashHtml=musicMedia;videoMp3=false
if(mp3Reg.test(flashvar)){videoMp3=true;flashHtml=musicFlash;}else if(flashReg.test(flashvar)){videoMp3=true;flashHtml=flash;}}
flashvar=encodeURI(flashvar);if(flashAddr[host]){var flash=flashAddr[host].replace('FLASHVAR',flashvar);flashHtml=flashHtml.replace(/FLASHADDR/g,flash);flashHtml=flashHtml.replace(/SHAREID/g,shareid);}
if(!obj){$('flash_div_'+shareid).innerHTML=flashHtml;return true;}
if($('flash_div_'+shareid)){$('flash_div_'+shareid).style.display='';$('flash_hide_'+shareid).style.display='';obj.style.display='none';return true;}
if(flashAddr[host]){var flashObj=document.createElement('div');flashObj.id='flash_div_'+shareid;obj.parentNode.insertBefore(flashObj,obj);flashObj.innerHTML=flashHtml;obj.style.display='none';var hideObj=document.createElement('div');hideObj.id='flash_hide_'+shareid;var nodetxt=document.createTextNode("收起");hideObj.appendChild(nodetxt);obj.parentNode.insertBefore(hideObj,obj);hideObj.style.cursor='pointer';if('music'!=host&&'flash'!=host){$('icon_id_'+shareid).style.display='none';$('media_id_'+shareid).style.display='none';}
hideObj.onclick=function(){if(true==videoMp3){stopMusic('audioplayer',shareid);flashObj.parentNode.removeChild(flashObj);hideObj.parentNode.removeChild(hideObj);}else{flashObj.style.display='none';hideObj.style.display='none';}
obj.style.display='';if('music'!=host&&'flash'!=host){$('icon_id_'+shareid).style.display='';$('media_id_'+shareid).style.display='';}}}}
function userapp_open(){(new Ajax).get("cp.php?ac=common&op=getuserapp",function(a){$("my_userapp").innerHTML=a;$("a_app_more").className="on";$("a_app_more").innerHTML="收起";$("a_app_more").onclick=function(){userapp_close()}})}
function userapp_close(){(new Ajax).get("cp.php?ac=common&op=getuserapp&subop=off",function(a){$("my_userapp").innerHTML=a;$("a_app_more").className="off";$("a_app_more").innerHTML="展开";$("a_app_more").onclick=function(){userapp_open()}})}
function startMarquee(a,c,b,d){function g(){j=setInterval(h,c);if(!l)f.scrollTop=f.scrollTop+2}
function h(){if(!l)if(f.scrollTop%a!=0){f.scrollTop=f.scrollTop+2;if(f.scrollTop>=f.scrollHeight/2)f.scrollTop=0}else{clearInterval(j);setTimeout(g,b)}}
var j=null,l=false,f=$(d);f.innerHTML=f.innerHTML+f.innerHTML;f.onmouseover=function(){l=true};f.onmouseout=function(){l=false};f.scrollTop=0;setTimeout(g,b)}
function readfeed(a,c){if(Cookie.get("read_feed_ids"))var b=Cookie.get("read_feed_ids"),b=c+","+b;else b=c;Cookie.set("read_feed_ids",b,48);a.className="feedread"}
function showreward(){if(Cookie.get("reward_notice_disable"))return false;(new Ajax).get("do.php?ac=ajax&op=getreward",function(a){a&&msgwin(a,2E3)})}
function msgwin(a,c){var b=$("msgwin");if(!b){b=document.createElement("div");b.id="msgwin";b.style.display="none";b.style.position="absolute";b.style.zIndex="100000";$("append_parent").appendChild(b)}
b.innerHTML=a;b.style.display="";b.style.filter="progid:DXImageTransform.Microsoft.Alpha(opacity=0)";b.style.opacity=0;var d=document.documentElement&&document.documentElement.scrollTop?document.documentElement.scrollTop:document.body.scrollTop;pbegin=d+document.documentElement.clientHeight/2;pend=d+document.documentElement.clientHeight/5;setTimeout(function(){showmsgwin(pbegin,pend,0,c)},10);b.style.left=(document.documentElement.clientWidth-b.clientWidth)/2+"px";b.style.top=pbegin+"px"}
function showmsgwin(a,c,b,d){step=(a-c)/10;var g=$("msgwin");newp=parseInt(g.style.top)-step;if(newp>c){g.style.filter="progid:DXImageTransform.Microsoft.Alpha(opacity="+b+")";g.style.opacity=b/100;g.style.top=newp+"px";setTimeout(function(){showmsgwin(a,c,b=b+10,d)},10)}else{g.style.filter="progid:DXImageTransform.Microsoft.Alpha(opacity=100)";g.style.opacity=1;setTimeout("displayOpacity('msgwin', 100)",d)}}
function displayOpacity(a,c){if($(a))if(c>=0){c=c-10;$(a).style.filter="progid:DXImageTransform.Microsoft.Alpha(opacity="+c+")";$(a).style.opacity=c/100;setTimeout("displayOpacity('"+a+"',"+c+")",50)}else{$(a).style.display="none";$(a).style.filter="progid:DXImageTransform.Microsoft.Alpha(opacity=100)";$(a).style.opacity=1}}
function display(a){a=$(a);a.style.display=a.style.display==""?"none":""}
function urlto(a){window.location.href=a}
function explode(a,c){return c.split(a)}
function selector(a,c){var b=RegExp("([a-z]*)([.#:]*)(.*|$)","ig").exec(a),d=[];b[2]=="#"?d.push(["id",b[3]]):b[2]=="."?d.push(["className",b[3]]):b[2]==":"&&d.push(["type",b[3]]);for(var g=b[3].replace(/\[(.*)\]/g,"$1").split("@"),h=0;h<g.length;h++){var j=null;(j=/([\w]+)([=^%!$~]+)(.*)$/.exec(g[h]))&&d.push([j[1],j[2],j[3]])}
b=(c||document).getElementsByTagName(b[1]||"*");if(d){g=[];j={"for":"htmlFor","class":"className"};for(h=0;h<b.length;h++){for(var l=true,f=0;f<d.length;f++){var m=j[d[f][0]]||d[f][0],m=b[h][m]||(b[h].getAttribute?b[h].getAttribute(m):""),a=null;d[f][1]=="="?a=RegExp("^"+d[f][2]+"$","i"):d[f][1]=="^="?a=RegExp("^"+d[f][2],"i"):d[f][1]=="$="?a=RegExp(d[f][2]+"$","i"):d[f][1]=="%="?a=RegExp(d[f][2],"i"):d[f][1]=="~="&&(a=RegExp("(^|[ ])"+d[f][2]+"([ ]|$)","i"));if(a&&!a.test(m)){l=false;break}}
l&&g.push(b[h])}
return g}
return b}
function checkeURL(a){return RegExp(/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/).test(a)==true?true:false}
function escape2(a){return escape(a).replace(/\+/g,"%2b").replace(/\./g,"%2e")}
function service(){dialog.open("/apps.php?m=service",{title:"给我们提意见",width:470});return false;jQuery.get("/apps.php?m=service",function(a){dialog({content:a,width:450,id:"service_show",button:[{value:" 提交 ",focus:true,callback:function(){if($("#login-form-username").val()==""){show_error("show_error","难道你想学雷锋做好事不留名么？");$("#login-form-username").focus();return false}
if($("#login-form-mail").val()!=""&&!regemail.test($("#login-form-mail").val())){show_error("show_error","你搞什么嘛，邮件地址也写错了 ):");$("#login-form-mail").focus();return false}
if($("#login-form-content").val().length<=0){show_error("show_error","提意见\\建议什么的总得写点什么是吧");$("#login-form-content").focus();return false}
if($("#login-form-content").val().length>=500){show_error("show_error","写那么多，直接通过邮件发给我们吧:no-reply@zhibotie.net");$("#login-form-content").focus()}else jQuery.getJSON("/apps.php?m=service&a=index&option=submit","author="+escape2($("#login-form-username").val())+"&subject="+escape2($("#login-form-content").val())+"&email="+escape2($("#login-form-mail").val())+"&t="+(new Date).getTime(),function(a){if(a.status){dialog({content:a.message,id:"service_show_show"});setTimeout(function(){dialog.get("service_show").close();dialog.get("service_show_show").close()},2E3)}else alert("抱歉，系统错误")});return false}},{value:" 关闭 "}]})})}
function show_error(a,c){$("#"+a).css("display","block");$("#"+a).html(c)}
(function(a){if(!a.fn.textbox)a.fn.textbox=function(c){function b(a){var b=0;if(a.selectionStart||a.selectionStart=="0")b=a.selectionStart;else if(document.selection){a.focus();var c=document.selection.createRange(),d=c.duplicate();for(d.moveToElementText(a);c.compareEndPoints("StartToStart",d)>0;){c.moveStart("character",-1);b++}}
return b}
function d(a,b){return f.maxLength>0?a.replace(/\r/g,"").length-(b?b.replace(/\r/g,"").length:0)>f.maxLength:false}
function g(a){if(f.maxLength>0){var b=a.value.replace(/\r/g,"");if(d(b))a.value=b.substr(0,f.maxLength)}}
function h(c){var k=this.value.replace(/\r/g,"");if(d(k)){var g=this.scrollTop,h=b(this)-(k.length-f.maxLength);this.value=k.substr(0,h)+k.substr(b(this));this.focus();if(this.selectionStart||this.selectionStart=="0")this.selectionEnd=this.selectionStart=h;else if(document.selection){k=this.createTextRange();k.moveStart("character",h);k.collapse(true);k.select()}
this.scrollTop=g}
a.isFunction(f.onInput)&&f.onInput.call(this,c,{maxLength:f.maxLength,leftLength:f.maxLength-this.value.replace(/\r/g,"").length})}
function j(b,c){a(b).unbind("paste",c._pasteHandler).unbind("cut",c._cutHandler).unbind("keyup",c._keyupHandler).unbind("blur",c._blurHandler)}
function l(b,c){var d=a(b);if(c.maxLength<0)d.bind("keyup",c._keyupHandler);else{d.bind("paste",c._pasteHandler).bind("cut",c._cutHandler).bind("keyup",c._keyupHandler).bind("blur",c._blurHandler);g(b)}}
var f=a.extend({maxLength:-1,onInput:null,_pasteHandler:function(a){var b=this;window.setTimeout(function(){h.call(b,a)},0)},_cutHandler:function(a){var b=this;window.setTimeout(function(){h.call(b,a)},0)},_keyupHandler:function(b){f.maxLength<0?a.isFunction(f.onInput)&&f.onInput.call(this,b,{maxLength:f.maxLength,leftLength:-1}):h.call(this,b)},_blurHandler:function(){g(this)}},c);this.maxLength=function(b){if(b){f.maxLength=b;return this.filter("textarea").each(function(){j(this,a(this).data("textbox-opts"));a(this).data("textbox-opts",f);l(this,f)}).end()}
return f.maxLength};this.input=function(b){if(a.isFunction(b)){f.onInput=b;return this.filter("textarea").each(function(){a(this).data("textbox-opts",f)}).end()}
return this};this.fixLength=function(){return this.filter("textarea").each(function(){g(this)}).end()};this.insertText=function(b){b=b.replace(/\r/g,"");return this.filter("textarea").each(function(){var c=this.value+b,g;g="";g=this.selectionStart||this.selectionStart=="0"?this.value.substring(this.selectionStart,this.selectionEnd):document.selection.createRange().text;g=g.replace(/\r/g,"");if(!d(c,g)){if(this.selectionStart||this.selectionStart=="0"){g=this.selectionStart;var h=this.selectionEnd,c=this.scrollTop;this.value=this.value.substring(0,g)+b+this.value.substring(h,this.value.length);this.focus();this.selectionEnd=this.selectionStart=g=g+b.length;this.scrollTop=c}else if(document.selection){this.focus();c=document.selection.createRange();c.text=b;c.collapse(true);c.select()}
a.isFunction(f.onInput)&&f.onInput.call(this,{type:"insert"},{maxLength:f.maxLength,leftLength:f.maxLength-this.value.replace(/\r/g,"").length})}}).end()};return this.filter("textarea").each(function(){var b=a(this);if(c){b.data("textbox-opts")&&j(this,b.data("textbox-opts"));b.data("textbox-opts",f);l(this,f)}else b.data("textbox-opts")&&(f=b.data("textbox-opts"))}).end()}})(jQuery);jQuery.cookie=function(a,c,b){if(typeof c!="undefined"){b=b||{};if(c===null){c="";b.expires=-1}
var d="";if(b.expires&&(typeof b.expires=="number"||b.expires.toUTCString)){if(typeof b.expires=="number"){d=new Date;d.setTime(d.getTime()+b.expires*864E5)}else d=b.expires;d="; expires="+d.toUTCString()}
var g=b.path?"; path="+b.path:"",h=b.domain?"; domain="+b.domain:"",b=b.secure?"; secure":"";document.cookie=[a,"=",encodeURIComponent(c),d,g,h,b].join("")}else{c=null;if(document.cookie&&document.cookie!=""){b=document.cookie.split(";");for(d=0;d<b.length;d++){g=jQuery.trim(b[d]);if(g.substring(0,a.length+1)==a+"="){c=decodeURIComponent(g.substring(a.length+1));break}}}
return c}};function sendDoumail(a,c){var b;b='<div class="quickpost" id="postform"><div style="padding:5px;">直播贴是不会保留你的豆油内容，请放心使用 (*^◎^*) </div><form method="post" action="" class="quickpost" id="sendDoumail" name=""><table><tr><td>主题：<input name="title" value="" class="t_input" id="sendmail_title" type="text"></td></tr><tr><td>内容：<a href="###" id="sendmail_face" onclick="showText(this.id, \'sendmail_message\');return false;" title="文字表情">(⊙_⊙)</a><br /><textarea id="sendmail_message" name="message" col="50" rows="6" style="width: 430px; height: 6em;"></textarea></td></tr><tr><td><input type="hidden" name="api" value="http://api.douban.com/people/'+a+'" /><input type="hidden" name="postsubmit" value="true" /><input  id="postsubmit_btn" onclick="return check_snedDoumail();" name="postsubmit_btn" type="submit" class="submit" value="回复"/></td></tr></table></form></div>';a&&dialog({title:"给【"+decodeURIComponent(c)+"】发送豆油",content:b})}
function add_follow(a){if(a){$("#bm_hover_card_add_follow_btn").removeClass("add_follow");$("#bm_hover_card_add_follow_btn").addClass("btn_loading");jQuery.post("cp.php?ac=friend&op=friends_add","uid="+a,function(a){if(a){if(eval("("+a+")").status){$("#bm_hover_card_add_follow_btn").removeClass("btn_loading");$("#bm_hover_card_add_follow_btn").addClass("had_follow")}}else alert("系统错误")})}
return false}
function had_follow(a){if(a){$("#bm_hover_card_had_follow_btn").removeClass("had_follow");$("#bm_hover_card_had_follow_btn").addClass("btn_loading");jQuery.post("cp.php?ac=friend&op=del_friends","uid="+a,function(a){if(a)if(eval("("+a+")").status){$("#bm_hover_card_had_follow_btn").removeClass("btn_loading");$("#bm_hover_card_had_follow_btn").addClass("add_follow")}else alert("系统错误");return false})}
return false};function check_snedDoumail(){var title=$("#sendDoumail #sendmail_title"),message=$("#sendDoumail #sendmail_message");if(title.val()==""){alert("输入点什么主题吧：）");title.focus();return false;}else if(message.val()==""){alert("给对方写点什么，你说好不好呢？");message.focus();return false;}else{jQuery.post("/apps.php?m=oauth&a=index&op=live_sendmail","",function(data){});return false;}}
function getTie(){dialog.prompt("请输入豆瓣小组/天涯社区/百度贴吧帖子连接",function(data){check(data);},"http://");}
function check(data){if(!checkeURL(data)){dialog.tips("这不是一个网址,请输入豆瓣小组/天涯社区/百度贴吧帖子连接");setTimeout("getTie();",1000);return false}else{if(data.search(/douban\.com\/group\/topic/)>0){dialog({content:'人家害羞啦，在后台运行，等下你就可以看到结果啦。',id:'douban_show',lock:true});jQuery.post("/live.php?do=search","url="+escape2(data)+"&fid=0&t="+new Date().getTime(),function(data){var json=eval("("+data+")");if(json.status){dialog.get('douban_show').close();dialog.tips("魂淡，又消耗我一点体力去下载豆娘的东西~~~");setTimeout(function(){window.location="/live-view-tid-"+json.tid+".html"},2000);return false}else{if(json.tid){dialog.get('douban_show').close();dialog.tips("这帖子已经被获取，我们为你跳转");setTimeout(function(){window.location="/live-view-tid-"+json.tid+".html"},2000);return false}else{dialog.get('douban_show').close();dialog.tips("抱歉，可能豆瓣这个小组是私密的，我们闯入不进去");setTimeout("getTie();",1000);return false}}});return false}if(data.search(/tianya\.cn\/publicforum\/content/)>0||data.search(/tianya\.cn\/techforum\/content/)>0||data.search(/bbs\.tianya\.cn\/post-/)>0){dialog({content:'人家害羞啦，在后台运行，等下你就可以看到结果啦。',id:'tianya_show',lock:true});jQuery.post("/live.php?do=get_tianya","url="+escape2(data)+"&fid=2&t="+new Date().getTime(),function(data){var json=eval("("+data+")");if(json.status){dialog.get('tianya_show').close();dialog.tips("操作成功，正在转向");setTimeout(function(){window.location="/live-view-tid-"+json.tid+".html"},2000);return false}else{if(json.tid){dialog.get('tianya_show').close();dialog.tips("这帖子已经被获取，我们为你跳转");setTimeout(function(){window.location="/live-view-tid-"+json.tid+".html"},2000);return false}else{dialog.get('tianya_show').close();dialog.tips("抱歉，天涯估计抽风了，我们获取不到了呢~~~");setTimeout("getTie();",1000);return false}}});return false}else if(data.search(/tieba\.baidu\.com\/p\//)>0||data.search(/tieba.baidu.com\/f\?kz/)>0){var uri;if(data.indexOf("f?kz=")>0){uri="http://tieba.baidu.com/p/"+data.split("=")[1]}else{uri=data}dialog({content:'人家害羞啦，在后台运行，等下你就可以看到结果啦。',id:'baidu_show',lock:true});jQuery.post("/live.php?do=get_baidu","url="+escape2(uri)+"&fid=1&t="+new Date().getTime(),function(data){var json=eval("("+data+")");if(json.status){dialog.get('baidu_show').close();dialog.tips("操作成功，正在转向");setTimeout(function(){window.location="/live-view-tid-"+json.tid+".html"},2000);return false}else{if(json.tid){dialog.get('baidu_show').close();dialog.tips("这帖子已经被获取，我们为你跳转");setTimeout(function(){window.location="/live-view-tid-"+json.tid+".html"},2000);return false}else{dialog.get('baidu_show').close();dialog.tips("抱歉，度娘估计抽风了，我们获取不到了呢~~~");setTimeout("getTie();",1000);return false}}});return false}else{dialog.tips("奥~系统大姨妈来了:(");getTie();return false}}}
function check_sum(id){var a=parseFloat($("#wordCheckNum_"+id).html());var leng=160;var cont=$("div#TFormTitle").find("em");var value=$("#do_message_"+id).val().length;var font=$("div#TFormTitle").find("font");if(value==0){$("#wordCheckNum_"+id).html(160);$("#docommform_btn_"+id).attr("disabled",false);}else if(value<leng){$("#wordCheckNum_"+id).html(leng-value);$("#docommform_btn_"+id).attr("disabled",false);}else{$("#wordCheckNum_"+id).html(0);$("#do_message_"+id).val($("#do_message_"+id).val().substring(0,leng));}}
(function($){$.fn.autoTextarea=function(options){var defaults={maxHeight:null,minHeight:$(this).height()};var opts=$.extend({},defaults,options);return $(this).each(function(){$(this).bind("paste cut keydown keyup focus blur",function(){var height,style=this.style;this.style.height=opts.minHeight+'px';if(this.scrollHeight>opts.minHeight){if(opts.maxHeight&&this.scrollHeight>opts.maxHeight){height=opts.maxHeight;style.overflowY='scroll';}else{height=this.scrollHeight;style.overflowY='hidden';}
style.height=height+'px';}});});};})(jQuery);function setCaretPosition(ctrl,pos){if(ctrl.setSelectionRange){ctrl.focus();ctrl.setSelectionRange(pos,pos);}else if(ctrl.createTextRange){var range=ctrl.createTextRange();range.collapse(true);range.moveEnd('character',pos);range.moveStart('character',pos);range.select();}}

/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: script_cookie.js 10737 2008-12-17 01:41:36Z zhengqingpeng $
*/


Cookie = {	

	/** Get a cookie's value
	 *
	 *  @param integer	key		The token used to create the cookie
	 *  @return void
	 */
	get: function(key) {
		// Still not sure that "[a-zA-Z0-9.()=|%/]+($|;)" match *all* allowed characters in cookies
		tmp =  document.cookie.match((new RegExp(key +'=[a-zA-Z0-9.()=|%/]+($|;)','g')));
		if(!tmp || !tmp[0]) return null;
		else return unescape(tmp[0].substring(key.length+1,tmp[0].length).replace(';','')) || null;
		
	},	
	
	/** Set a cookie
	 *
	 *  @param integer	key		The token that will be used to retrieve the cookie
	 *  @param string	value	The string to be stored
	 *  @param integer	ttl		Time To Live (hours)
	 *  @param string	path	Path in which the cookie is effective, default is "/" (optional)
	 *  @param string	domain	Domain where the cookie is effective, default is window.location.hostname (optional)
	 *  @param boolean 	secure	Use SSL or not, default false (optional)
	 * 
	 *  @return setted cookie
	 */
	set: function(key, value, ttl, path, domain, secure) {
		cookie = [key+'='+    escape(value),
		 		  'path='+    ((!path   || path=='')  ? '/' : path),
		 		  'domain='+  ((!domain || domain=='')?  window.location.hostname : domain)];
		
		if (ttl)         cookie.push('expires='+Cookie.hoursToExpireDate(ttl));
		if (secure)      cookie.push('secure');
		return document.cookie = cookie.join('; ');
	},
	
	/** Unset a cookie
	 *
	 *  @param integer	key		The token that will be used to retrieve the cookie
	 *  @param string	path	Path used to create the cookie (optional)
	 *  @param string	domain	Domain used to create the cookie, default is null (optional)
	 *  @return void
	 */
	unset: function(key, path, domain) {
		path   = (!path   || typeof path   != 'string') ? '' : path;
        domain = (!domain || typeof domain != 'string') ? '' : domain;
		if (Cookie.get(key)) Cookie.set(key, '', 'Thu, 01-Jan-70 00:00:01 GMT', path, domain);
	},

	/** Return GTM date string of "now" + time to live
	 *
	 *  @param integer	ttl		Time To Live (hours)
	 *  @return string
	 */
	hoursToExpireDate: function(ttl) {
		if (parseInt(ttl) == 'NaN' ) return '';
		else {
			now = new Date();
			now.setTime(now.getTime() + (parseInt(ttl) * 60 * 60 * 1000));
			return now.toGMTString();			
		}
	},

	/** Return true if cookie functionnalities are available
	 *
	 *  @return boolean
	 */
	test: function() {
		Cookie.set('b49f729efde9b2578ea9f00563d06e57', 'true');
		if (Cookie.get('b49f729efde9b2578ea9f00563d06e57') == 'true') {
			Cookie.unset('b49f729efde9b2578ea9f00563d06e57');
			return true;
		}
		return false;
	},
	
	/** If Firebug JavaScript console is present, it will dump cookie string to console.
	 * 
	 *  @return void
	 */
	dump: function() {
		if (typeof console != 'undefined') {
			console.log(document.cookie.split(';'));
		}
	}
}

/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: script_menu.js 12767 2009-07-20 06:01:49Z zhengqingpeng $
*/

var jsmenu = new Array();
var ctrlobjclassName;
jsmenu['active'] = new Array();
jsmenu['timer'] = new Array();
jsmenu['iframe'] = new Array();

function initCtrl(ctrlobj, click, duration, timeout, layer) {
	if(ctrlobj && !ctrlobj.initialized) {
		ctrlobj.initialized = true;
		ctrlobj.unselectable = true;

		ctrlobj.outfunc = typeof ctrlobj.onmouseout == 'function' ? ctrlobj.onmouseout : null;
		ctrlobj.onmouseout = function() {
			if(this.outfunc) this.outfunc();
			if(duration < 3) jsmenu['timer'][ctrlobj.id] = setTimeout('hideMenu(' + layer + ')', timeout);
		}

		ctrlobj.overfunc = typeof ctrlobj.onmouseover == 'function' ? ctrlobj.onmouseover : null;
		ctrlobj.onmouseover = function(e) {
			doane(e);
			if(this.overfunc) this.overfunc();
			if(click) {
				clearTimeout(jsmenu['timer'][this.id]);
			} else {
				for(var id in jsmenu['timer']) {
					if(jsmenu['timer'][id]) clearTimeout(jsmenu['timer'][id]);
				}
			}
		}
	}
}

function initMenu(ctrlid, menuobj, duration, timeout, layer, drag) {
	if(menuobj && !menuobj.initialized) {
		menuobj.initialized = true;
		menuobj.ctrlkey = ctrlid;
		menuobj.onclick = ebygum;
		menuobj.style.position = 'absolute';
		if(duration < 3) {
			if(duration > 1) {
				menuobj.onmouseover = function() {
					clearTimeout(jsmenu['timer'][ctrlid]);
				}
			}
			if(duration != 1) {
				menuobj.onmouseout = function() {
					jsmenu['timer'][ctrlid] = setTimeout('hideMenu(' + layer + ')', timeout);
				}
			}
		}
		menuobj.style.zIndex = 50;
		if(is_ie) {
			menuobj.style.filter += "progid:DXImageTransform.Microsoft.shadow(direction=135,color=#CCCCCC,strength=2)";
		}
		if(drag) {
			menuobj.onmousedown = function(event) {try{menudrag(menuobj, event, 1);}catch(e){}};
			document.body.onmousemove = function(event) {try{menudrag(menuobj, event, 2);}catch(e){}};
			menuobj.onmouseup = function(event) {try{menudrag(menuobj, event, 3);}catch(e){}};
		}
	}
}

var menudragstart = new Array();
function menudrag(menuobj, e, op) {
	if(op == 1) {
		if(in_array(is_ie ? event.srcElement.tagName : e.target.tagName, ['TEXTAREA', 'INPUT', 'BUTTON', 'SELECT'])) {
			return;
		}
		menudragstart = is_ie ? [event.clientX, event.clientY] : [e.clientX, e.clientY];
		menudragstart[2] = parseInt(menuobj.style.left);
		menudragstart[3] = parseInt(menuobj.style.top);
		doane(e);
	} else if(op == 2 && menudragstart[0]) {
		var menudragnow = is_ie ? [event.clientX, event.clientY] : [e.clientX, e.clientY];
		menuobj.style.left = (menudragstart[2] + menudragnow[0] - menudragstart[0]) + 'px';
		menuobj.style.top = (menudragstart[3] + menudragnow[1] - menudragstart[1]) + 'px';
		doane(e);
	} else if(op == 3) {
		menudragstart = [];
		doane(e);
	}
}

function showMenu(ctrlid, click, offset, duration, timeout, layer, showid, maxh, drag) {
	var ctrlobj = $(ctrlid);
	if(!ctrlobj) return;
	if(isUndefined(click)) click = false;
	if(isUndefined(offset)) offset = 0;
	if(isUndefined(duration)) duration = 2;
	if(isUndefined(timeout)) timeout = 250;
	if(isUndefined(layer)) layer = 0;
	if(isUndefined(showid)) showid = ctrlid;
	var showobj = $(showid);
	var menuobj = $(showid + '_menu');
	if(!showobj|| !menuobj) return;
	if(isUndefined(maxh)) maxh = 1000;
	if(isUndefined(drag)) drag = false;

	if(click && jsmenu['active'][layer] == menuobj) {
		hideMenu(layer);
		return;
	} else {
		hideMenu(layer);
	}

	var len = jsmenu['timer'].length;
	if(len > 0) {
		for(var i=0; i<len; i++) {
			if(jsmenu['timer'][i]) clearTimeout(jsmenu['timer'][i]);
		}
	}

	initCtrl(ctrlobj, click, duration, timeout, layer);
	ctrlobjclassName = ctrlobj.className;
	ctrlobj.className += ' hover';
	initMenu(ctrlid, menuobj, duration, timeout, layer, drag);

	menuobj.style.display = '';
	if(!is_opera) {
		menuobj.style.clip = 'rect(auto, auto, auto, auto)';
	}

	setMenuPosition(showid, offset);

	if(maxh && menuobj.scrollHeight > maxh) {
		menuobj.style.height = maxh + 'px';
		if(is_opera) {
			menuobj.style.overflow = 'auto';
		} else {
			menuobj.style.overflowY = 'auto';
		}
	}

	if(!duration) {
		setTimeout('hideMenu(' + layer + ')', timeout);
	}

	jsmenu['active'][layer] = menuobj;
}

function setMenuPosition(showid, offset) {
	var showobj = $(showid);
	var menuobj = $(showid + '_menu');
	if(isUndefined(offset)) offset = 0;
	if(showobj) {
		showobj.pos = fetchOffset(showobj);
		showobj.X = showobj.pos['left'];
		showobj.Y = showobj.pos['top'];
		showobj.w = showobj.offsetWidth;
		showobj.h = showobj.offsetHeight;
		menuobj.w = menuobj.offsetWidth;
		menuobj.h = menuobj.offsetHeight;
		var sTop = document.documentElement && document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
		if(offset != -1) {
			menuobj.style.left = (showobj.X + menuobj.w > document.body.clientWidth) && (showobj.X + showobj.w - menuobj.w >= 0) ? showobj.X + showobj.w - menuobj.w + 'px' : showobj.X + 'px';
			menuobj.style.top = offset == 1 ? showobj.Y + 'px' : (offset == 2 || ((showobj.Y + showobj.h + menuobj.h > sTop + document.documentElement.clientHeight) && (showobj.Y - menuobj.h >= 0)) ? (showobj.Y - menuobj.h) + 'px' : showobj.Y + showobj.h + 'px');
		} else if(offset == -1) {
			menuobj.style.left = (document.body.clientWidth-menuobj.w)/2 + 'px';
			var divtop = sTop + (document.documentElement.clientHeight-menuobj.h)/2;
			if(divtop > 100) divtop = divtop - 100;
			menuobj.style.top = divtop + 'px';
		}
		if(menuobj.style.clip && !is_opera) {
			menuobj.style.clip = 'rect(auto, auto, auto, auto)';
		}
	}
}

function hideMenu(layer) {
	if(isUndefined(layer)) layer = 0;
	if(jsmenu['active'][layer]) {
		try {
			$(jsmenu['active'][layer].ctrlkey).className = ctrlobjclassName;
		} catch(e) {}
		clearTimeout(jsmenu['timer'][jsmenu['active'][layer].ctrlkey]);
		jsmenu['active'][layer].style.display = 'none';
		if(is_ie && is_ie < 7 && jsmenu['iframe'][layer]) {
			jsmenu['iframe'][layer].style.display = 'none';
		}
		jsmenu['active'][layer] = null;
	}
}

function fetchOffset(obj) {
	var left_offset = obj.offsetLeft;
	var top_offset = obj.offsetTop;
	while((obj = obj.offsetParent) != null) {
		left_offset += obj.offsetLeft;
		top_offset += obj.offsetTop;
	}
	return { 'left' : left_offset, 'top' : top_offset };
}

function ebygum(eventobj) {
	if(!eventobj || is_ie) {
		window.event.cancelBubble = true;
		return window.event;
	} else {
		if(eventobj.target.type == 'submit') {
			eventobj.target.form.submit();
		}
		eventobj.stopPropagation();
		return eventobj;
	}
}


/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: script_ajax.js 12670 2009-07-14 07:43:56Z liguode $
*/

var Ajaxs = new Array();
var AjaxStacks = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
var ajaxpostHandle = 0;
var evalscripts = new Array();
var ajaxpostresult = 0;

function Ajax(recvType, waitId) {

	for(var stackId = 0; stackId < AjaxStacks.length && AjaxStacks[stackId] != 0; stackId++);
	AjaxStacks[stackId] = 1;

	var aj = new Object();

	aj.loading = 'Loading...';//public
	aj.recvType = recvType ? recvType : 'XML';//public
	aj.waitId = waitId ? $(waitId) : null;//public

	aj.resultHandle = null;//private
	aj.sendString = '';//private
	aj.targetUrl = '';//private
	aj.stackId = 0;
	aj.stackId = stackId;

	aj.setLoading = function(loading) {
		if(typeof loading !== 'undefined' && loading !== null) aj.loading = loading;
	}

	aj.setRecvType = function(recvtype) {
		aj.recvType = recvtype;
	}

	aj.setWaitId = function(waitid) {
		aj.waitId = typeof waitid == 'object' ? waitid : $(waitid);
	}

	aj.createXMLHttpRequest = function() {
		var request = false;
		if(window.XMLHttpRequest) {
			request = new XMLHttpRequest();
			if(request.overrideMimeType) {
				request.overrideMimeType('text/xml');
			}
		} else if(window.ActiveXObject) {
			var versions = ['Microsoft.XMLHTTP', 'MSXML.XMLHTTP', 'Microsoft.XMLHTTP', 'Msxml2.XMLHTTP.7.0', 'Msxml2.XMLHTTP.6.0', 'Msxml2.XMLHTTP.5.0', 'Msxml2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP'];
			for(var i=0, icount=versions.length; i<icount; i++) {
				try {
					request = new ActiveXObject(versions[i]);
					if(request) {
						return request;
					}
				} catch(e) {}
			}
		}
		return request;
	}

	aj.XMLHttpRequest = aj.createXMLHttpRequest();
	aj.showLoading = function() {
		if(aj.waitId && (aj.XMLHttpRequest.readyState != 4 || aj.XMLHttpRequest.status != 200)) {
			changedisplay(aj.waitId, '');
			aj.waitId.innerHTML = '<span><img src="image/loading.gif"> ' + aj.loading + '</span>';
		}
	}

	aj.processHandle = function() {
		if(aj.XMLHttpRequest.readyState == 4 && aj.XMLHttpRequest.status == 200) {
			for(k in Ajaxs) {
				if(Ajaxs[k] == aj.targetUrl) {
					Ajaxs[k] = null;
				}
			}
			if(aj.waitId) changedisplay(aj.waitId, 'none');
			if(aj.recvType == 'HTML') {
				aj.resultHandle(aj.XMLHttpRequest.responseText, aj);
			} else if(aj.recvType == 'XML') {
				try {
					aj.resultHandle(aj.XMLHttpRequest.responseXML.lastChild.firstChild.nodeValue, aj);
				} catch(e) {
					aj.resultHandle('', aj);
				}
			}
			AjaxStacks[aj.stackId] = 0;
		}
	}

	aj.get = function(targetUrl, resultHandle) {	
		if(targetUrl.indexOf('?') != -1) {
			targetUrl = targetUrl + '&inajax=1';
		} else {
			targetUrl = targetUrl + '?inajax=1';
		}
		setTimeout(function(){aj.showLoading()}, 500);
		if(in_array(targetUrl, Ajaxs)) {
			return false;
		} else {
			Ajaxs.push(targetUrl);
		}
		aj.targetUrl = targetUrl;
		aj.XMLHttpRequest.onreadystatechange = aj.processHandle;
		aj.resultHandle = resultHandle;
		var delay = 100;
		if(window.XMLHttpRequest) {
			setTimeout(function(){
			aj.XMLHttpRequest.open('GET', aj.targetUrl);
			aj.XMLHttpRequest.send(null);}, delay);
		} else {
			setTimeout(function(){
			aj.XMLHttpRequest.open("GET", targetUrl, true);
			aj.XMLHttpRequest.send();}, delay);
		}

	}
	aj.post = function(targetUrl, sendString, resultHandle) {
		if(targetUrl.indexOf('?') != -1) {
			targetUrl = targetUrl + '&inajax=1';
		} else {
			targetUrl = targetUrl + '?inajax=1';
		}
		setTimeout(function(){aj.showLoading()}, 500);
		if(in_array(targetUrl, Ajaxs)) {
			return false;
		} else {
			Ajaxs.push(targetUrl);
		}
		aj.targetUrl = targetUrl;
		aj.sendString = sendString;
		aj.XMLHttpRequest.onreadystatechange = aj.processHandle;
		aj.resultHandle = resultHandle;
		aj.XMLHttpRequest.open('POST', targetUrl);
		aj.XMLHttpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		aj.XMLHttpRequest.send(aj.sendString);
	}
	return aj;
}

function newfunction(func){
	var args = new Array();
	for(var i=1; i<arguments.length; i++) args.push(arguments[i]);
	return function(event){
		doane(event);
		window[func].apply(window, args);
		return false;
	}
}

function changedisplay(obj, display) {
	if(display == 'auto') {
		obj.style.display = obj.style.display == '' ? 'none' : '';
	} else {
		obj.style.display = display;
	}
	return false;
}

function evalscript(s) {
	if(s.indexOf('<script') == -1) return s;
	var p = /<script[^\>]*?>([^\x00]*?)<\/script>/ig;
	var arr = new Array();
	while(arr = p.exec(s)) {
		var p1 = /<script[^\>]*?src=\"([^\>]*?)\"[^\>]*?(reload=\"1\")?(?:charset=\"([\w\-]+?)\")?><\/script>/i;
		var arr1 = new Array();
		arr1 = p1.exec(arr[0]);
		if(arr1) {
			appendscript(arr1[1], '', arr1[2], arr1[3]);
		} else {
			p1 = /<script(.*?)>([^\x00]+?)<\/script>/i;
			arr1 = p1.exec(arr[0]);
			//获取字符集
			var re = /charset=\"([\w\-]+?)\"/i;
			var charsetarr = re.exec(arr1[1]);
			appendscript('', arr1[2], arr1[1].indexOf('reload=') != -1, charsetarr[1]);
		}
	}
	return s;
}

function appendscript(src, text, reload, charset) {
	var id = hash(src + text);
	if(!reload && in_array(id, evalscripts)) return;
	if(reload && $(id)) {
		$(id).parentNode.removeChild($(id));
	}

	evalscripts.push(id);
	var scriptNode = document.createElement("script");
	scriptNode.type = "text/javascript";
	scriptNode.id = id;
	scriptNode.charset = charset;
	try {
		if(src) {
			scriptNode.src = src;
		} else if(text){
			scriptNode.text = text;
		}
		$('append_parent').appendChild(scriptNode);
	} catch(e) {}
}

function stripscript(s) {
	return s.replace(/<script.*?>.*?<\/script>/ig, '');
}

function ajaxupdateevents(obj, tagName) {
	tagName = tagName ? tagName : 'A';
	var objs = obj.getElementsByTagName(tagName);
	for(k in objs) {
		var o = objs[k];
		ajaxupdateevent(o);
	}
}

function ajaxupdateevent(o) {
	if(typeof o == 'object' && o.getAttribute) {
		if(o.getAttribute('ajaxtarget')) {
			if(!o.id) o.id = Math.random();
			var ajaxevent = o.getAttribute('ajaxevent') ? o.getAttribute('ajaxevent') : 'click';
			var ajaxurl = o.getAttribute('ajaxurl') ? o.getAttribute('ajaxurl') : o.href;
			_attachEvent(o, ajaxevent, newfunction('ajaxget', ajaxurl, o.getAttribute('ajaxtarget'), o.getAttribute('ajaxwaitid'), o.getAttribute('ajaxloading'), o.getAttribute('ajaxdisplay')));
			if(o.getAttribute('ajaxfunc')) {
				o.getAttribute('ajaxfunc').match(/(\w+)\((.+?)\)/);
				_attachEvent(o, ajaxevent, newfunction(RegExp.$1, RegExp.$2));
			}
		}
	}
}

function ajaxget(url, showid, waitid) {
	waitid = typeof waitid == 'undefined' || waitid === null ? showid : waitid;
	var x = new Ajax();
	x.setLoading();
	x.setWaitId(waitid);
	x.display = '';
	x.showId = $(showid);
	if(x.showId) x.showId.orgdisplay = typeof x.showId.orgdisplay === 'undefined' ? x.showId.style.display : x.showId.orgdisplay;

	if(url.substr(strlen(url) - 1) == '#') {
		url = url.substr(0, strlen(url) - 1);
		x.autogoto = 1;
	}

	var url = url + '&inajax=1&ajaxtarget=' + showid;
	x.get(url, function(s, x) {
		evaled = false;
		if(s.indexOf('ajaxerror') != -1) {
			evalscript(s);
			evaled = true;
		}
		if(!evaled) {
			if(x.showId) {
				changedisplay(x.showId, x.showId.orgdisplay);
				changedisplay(x.showId, x.display);
				x.showId.orgdisplay = x.showId.style.display;
				ajaxinnerhtml(x.showId, s);
				ajaxupdateevents(x.showId);
				if(x.autogoto) scroll(0, x.showId.offsetTop);
			}
		}
		if(!evaled)evalscript(s);
	});
}

function ajaxpost(formid, func, timeout) {
	showloading();
	
	if(ajaxpostHandle != 0) {
		return false;
	}
	var ajaxframeid = 'ajaxframe';
	var ajaxframe = $(ajaxframeid);
	if(ajaxframe == null) {
		if (is_ie && !is_opera) {
			ajaxframe = document.createElement("<iframe name='" + ajaxframeid + "' id='" + ajaxframeid + "'></iframe>");
		} else {
			ajaxframe = document.createElement("iframe");
			ajaxframe.name = ajaxframeid;
			ajaxframe.id = ajaxframeid;
		}
		ajaxframe.style.display = 'none';
		$('append_parent').appendChild(ajaxframe);
	}
	$(formid).target = ajaxframeid;
	$(formid).action = $(formid).action + '&inajax=1';
	
	ajaxpostHandle = [formid, func, timeout];
	
	if(ajaxframe.attachEvent) {
		ajaxframe.detachEvent ('onload', ajaxpost_load);
		ajaxframe.attachEvent('onload', ajaxpost_load);
	} else {
		document.removeEventListener('load', ajaxpost_load, true);
		ajaxframe.addEventListener('load', ajaxpost_load, false);
	}
	$(formid).submit();
	return false;
}

function ajaxpost_load() {
	
	var formid = ajaxpostHandle[0];
	var func = ajaxpostHandle[1];
	var timeout = ajaxpostHandle[2];
	
	var formstatus = '__' + formid;
	
	showloading('none');
	
	if(is_ie) {
		var s = $('ajaxframe').contentWindow.document.XMLDocument.text;
	} else {
		var s = $('ajaxframe').contentWindow.document.documentElement.firstChild.nodeValue;
	}
	evaled = false;
	if(s.indexOf('ajaxerror') != -1) {
		evalscript(s);
		evaled = true;
	}
	if(s.indexOf('ajaxok') != -1) {
		ajaxpostresult = 1;
	} else {
		ajaxpostresult = 0;
	}
	//function
	if(func) {
		setTimeout(func + '(\'' + formid + '\',' + ajaxpostresult + ')', 10);
	}
	if(!evaled && $(formstatus)) {
		$(formstatus).style.display = '';		
		ajaxinnerhtml($(formstatus), s);
		evalscript(s);
	}

	//层消失
	if(timeout && ajaxpostresult) jsmenu['timer'][formid] = setTimeout("hideMenu()", timeout);

	formid.target = 'ajaxframe';
	ajaxpostHandle = 0;
}

function ajaxmenu(e, ctrlid, isbox, timeout, func) {
	
	var offset = 0;
	var duration = 3;
	
	if(isUndefined(timeout)) timeout = 0;
	if(isUndefined(isbox)) isbox = 0;
	if(timeout>0) duration = 0;
	
	showloading();
	if(jsmenu['active'][0] && jsmenu['active'][0].ctrlkey == ctrlid) {
		hideMenu();
		doane(e);
		return;
	} else if(is_ie && is_ie < 7 && document.readyState.toLowerCase() != 'complete') {
		return;
	}
	
	if(isbox) {
		divclass = 'popupmenu_centerbox';
		offset = -1;
	} else {
		divclass = 'popupmenu_popup';
	}
	
	var div = $(ctrlid + '_menu');
	if(!div) {
		div = document.createElement('div');
		div.ctrlid = ctrlid;
		div.id = ctrlid + '_menu';
		div.style.display = 'none';
		div.className = divclass;
		$('append_parent').appendChild(div);
	}

	var x = new Ajax();
	var href = !isUndefined($(ctrlid).href) ? $(ctrlid).href : $(ctrlid).attributes['href'].value;
	x.div = div;
	x.etype = e.type;

	x.get(href, function(s) {
		evaled = false;
		if(s.indexOf('ajaxerror') != -1) {
			evaled = true;
		}
		if(s.indexOf('hideMenu()') == -1) {//添加关闭
			s = '<h1>消息</h1><a href="javascript:hideMenu();" class="float_del" title="关闭">关闭</a><div class="popupmenu_inner">' + s + '<div>';
		}
		if(!evaled) {
			if(x.div) x.div.innerHTML = s;
			showMenu(ctrlid, x.etype == 'click', offset, duration, timeout, 0, ctrlid, 1000, true);
			//function
			if(func) {
				setTimeout(func + '(\'' + ctrlid + '\')', 10);
			}
		}
		evalscript(s);
	});

	showloading('none');
	doane(e);
}
//得到一个定长的hash值,依赖于 stringxor()
function hash(string, length) {
	var length = length ? length : 32;
	var start = 0;
	var i = 0;
	var result = '';
	filllen = length - string.length % length;
	for(i = 0; i < filllen; i++){
		string += "0";
	}
	while(start < string.length) {
		result = stringxor(result, string.substr(start, length));
		start += length;
	}
	return result;
}

function stringxor(s1, s2) {
	var s = '';
	var hash = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var max = Math.max(s1.length, s2.length);
	for(var i=0; i<max; i++) {
		var k = s1.charCodeAt(i) ^ s2.charCodeAt(i);
		s += hash.charAt(k % 52);
	}
	return s;
}

function showloading(display, wating) {
	var display = display ? display : 'block';
	var wating = wating ? wating : 'Loading...';
	$('ajaxwaitid').innerHTML = wating;
	$('ajaxwaitid').style.display = display;
}

function ajaxinnerhtml(showid, s) {
	if(showid.tagName != 'TBODY') {
		showid.innerHTML = s;
	} else {
		while(showid.firstChild) {
			showid.firstChild.parentNode.removeChild(showid.firstChild);
		}
		var div1 = document.createElement('DIV');
		div1.id = showid.id+'_div';
		div1.innerHTML = '<table><tbody id="'+showid.id+'_tbody">'+s+'</tbody></table>';
		$('append_parent').appendChild(div1);
		var trs = div1.getElementsByTagName('TR');
		var l = trs.length;
		for(var i=0; i<l; i++) {
			showid.appendChild(trs[0]);
		}
		var inputs = div1.getElementsByTagName('INPUT');
		var l = inputs.length;
		for(var i=0; i<l; i++) {
			showid.appendChild(inputs[0]);
		}		
		div1.parentNode.removeChild(div1);
	}
}


//显示表情菜单
function showFace(showid, target) {
	var div = $('uchome_face_bg');
	if(div) {
		div.parentNode.removeChild(div);
	}
	div = document.createElement('div');
	div.id = 'uchome_face_bg';
	div.style.position = 'absolute';
	div.style.left = div.style.top = '0px';
	div.style.width = '100%';
	div.style.height = document.body.scrollHeight + 'px';
	div.style.backgroundColor = '#FFFFFF';
	div.style.zIndex = 10000;
	div.style.display = 'none';
	div.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=0,finishOpacity=100,style=0)';
	div.style.opacity = 0;
	div.onclick = function() {
		$(showid+'_menu').style.display = 'none';
		$('uchome_face_bg').style.display = 'none';
	}
	$('append_parent').appendChild(div);
	
	if($(showid + '_menu') != null) {
		$(showid+'_menu').style.display = '';
	} else {
		var faceDiv = document.createElement("div");
		faceDiv.id = showid+'_menu';
		faceDiv.className = 'facebox';
		faceDiv.style.position = 'absolute';
		var faceul = document.createElement("ul");
		for(i=1; i<31; i++) {
			var faceli = document.createElement("li");
			faceli.innerHTML = '<img src="image/face/'+i+'.gif" onclick="insertFace(\''+showid+'\','+i+', \''+ target +'\')" style="cursor:pointer; position:relative;" />';
			faceul.appendChild(faceli);
		}
		faceDiv.appendChild(faceul);
		$('append_parent').appendChild(faceDiv)
	}
	//定位菜单
	setMenuPosition(showid, 0);
	div.style.display = '';
}
//插入表情
function insertFace(showid, id, target) {
	var faceText = '[em:'+id+':]';
	if($(target) != null) {
		insertContent(target, faceText);
	}
	$(showid+'_menu').style.display = 'none';
	$('uchome_face_bg').style.display = 'none';
}

//插入文字表情
function insertText(showid, id, target){
	var newtext = new Array();
	newtext = getTextFace();
	var faceText = newtext[id];
	if($(target) != null) {
		insertContent(target, faceText);
	}
	$(showid+'_menu').style.display = 'none';
	$('uchome_face_bg').style.display = 'none';	
	}
function getTextFace(){
	var text = ":）№:-P№:-(№~~~^_^~~~ №-_-!№=_=№-_-#№$_$№?_?№T^T№+_+№(#‵′)凸№╭∩╮№⊙﹏⊙№o(>﹏<)o№O(∩_∩)O~№(*^◎^*)№o(≧v≦)o~~№{{{(>_<)}}}№╭(╯^╰)╮№(ˉ(∞)ˉ)№(～ o ～)~zZ№↖(^ω^)↗№o(╯□╰)o№~(@^_^@)~№（¯﹃¯）№(+﹏+)~№~~o(>_<)o ~~№/(ㄒoㄒ)/~~№-_-|||";
	var newtext = text.split("№");
	return newtext;
	}
	
function showText(showid, target){
	var div = $('uchome_face_bg');
	if(div) {
		div.parentNode.removeChild(div);
	}
	div = document.createElement('div');
	div.id = 'uchome_face_bg';
	div.style.position = 'absolute';
	div.style.left = div.style.top = '0px';
	div.style.width = '100%';
	div.style.height = document.body.scrollHeight + 'px';
	div.style.backgroundColor = '#FFFFFF';
	div.style.zIndex = 10000;
	div.style.display = 'none';
	div.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=0,finishOpacity=100,style=0)';
	div.style.opacity = 0;
	div.onclick = function() {
		$(showid+'_menu').style.display = 'none';
		$('uchome_face_bg').style.display = 'none';
	}
	$('append_parent').appendChild(div);
	
	if($(showid + '_menu') != null) {
		$(showid+'_menu').style.display = '';
	} else {
		var faceDiv = document.createElement("div");
		faceDiv.id = showid+'_menu';
		faceDiv.className = 'facebox';
		faceDiv.style.width = '420px';
		faceDiv.style.position = 'absolute';
		var faceul = document.createElement("ul");
		var newtext = new Array();
		newtext = getTextFace();
		for(i=0; i<newtext.length; i++) {
			var faceli = document.createElement("li");
			faceli.style.width = '100px';
			faceli.innerHTML = '<a href="javascript:void(0);" onclick="insertText(\''+showid+'\','+i+',\''+ target +'\')" style="cursor:pointer; position:relative;">'+newtext[i]+'</a>';
			//faceli.innerHTML = '<img src="image/face/'+i+'.gif" onclick="insertFace(\''+showid+'\','+i+', \''+ target +'\')" style="cursor:pointer; position:relative;" />';
			faceul.appendChild(faceli);
		}
		faceDiv.appendChild(faceul);
		$('append_parent').appendChild(faceDiv)
	}
	//定位菜单
	setMenuPosition(showid, 0);
	div.style.display = '';
	}

function textCounter(obj, showid, maxlimit) {
	var len = strLen(obj.value);
	var showobj = $(showid);
	if(len > maxlimit) {
		obj.value = getStrbylen(obj.value, maxlimit);
		showobj.innerHTML = '0';
	} else {
		showobj.innerHTML = maxlimit - len;
	}
	if(maxlimit - len > 0) {
		showobj.parentNode.style.color = "";
	} else {
		showobj.parentNode.style.color = "red";
	}
	
}
function getStrbylen(str, len) {
	var num = 0;
	var strlen = 0;
	var newstr = "";
	var obj_value_arr = str.split("");
	for(var i = 0; i < obj_value_arr.length; i ++) {
		if(i < len && num + byteLength(obj_value_arr[i]) <= len) {
			num += byteLength(obj_value_arr[i]);
			strlen = i + 1;
		}
	}
	if(str.length > strlen) {
		newstr = str.substr(0, strlen);
	} else {
		newstr = str;
	}
	return newstr;
}
function byteLength (sStr) {
	aMatch = sStr.match(/[^\x00-\x80]/g);
	return (sStr.length + (! aMatch ? 0 : aMatch.length));
}
function strLen(str) {
	var charset = document.charset; 
	var len = 0;
	for(var i = 0; i < str.length; i++) {
		len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? (charset == "utf-8" ? 3 : 2) : 1;
	}
	return len;
}


/*
	[UCenter Home] (C) 2007-2008 Comsenz Inc.
	$Id: script_manage.js 13178 2009-08-17 02:36:39Z liguode $
*/

//添加留言
function wall_add(cid, result) {
	if(result) {
		var obj = $('comment_ul');
		var newli = document.createElement("div");
		var x = new Ajax();
		x.get('do.php?ac=ajax&op=comment', function(s){
			newli.innerHTML = s;
		});
		obj.insertBefore(newli, obj.firstChild);
		if($('comment_message')) {
			$('comment_message').value= '';
		}
		//提示获得积分
		showreward();
	}
}

//添加分享
function share_add(sid, result) {
	if(result) {
		var obj = $('share_ul');
		var newli = document.createElement("div");
		var x = new Ajax();
		x.get('do.php?ac=ajax&op=share', function(s){
			newli.innerHTML = s;
		});
		obj.insertBefore(newli, obj.firstChild);
		$('share_link').value = 'http://';
		$('share_general').value = '';
		//提示获得积分
		showreward();
	}
}
//添加评论
function comment_add(id, result) {
	if(result) {
		var obj = $('comment_ul');
		var newli = document.createElement("div");
		var x = new Ajax();
		x.get('do.php?ac=ajax&op=comment', function(s){
			newli.innerHTML = s;
		});
		if($('comment_prepend')){
			obj = obj.firstChild;
			while (obj && obj.nodeType != 1){
				obj = obj.nextSibling;
			}
			obj.parentNode.insertBefore(newli, obj);
		} else {
			obj.appendChild(newli);
		}
		if($('comment_message')) {
			$('comment_message').value= '';
		}
		if($('comment_replynum')) {
			var a = parseInt($('comment_replynum').innerHTML);
			var b = a + 1;
			$('comment_replynum').innerHTML = b + '';
		}
		//提示获得积分
		showreward();
	}
}
//编辑
function comment_edit(id, result) {
	if(result) {
		var ids = explode('_', id);
		var cid = ids[1];
		var obj = $('comment_'+ cid +'_li');
		var x = new Ajax();
		x.get('do.php?ac=ajax&op=comment&cid='+ cid, function(s){
			obj.innerHTML = s;
		});
	}
}
//删除
function comment_delete(id, result) {
	if(result) {
		var ids = explode('_', id);
		var cid = ids[1];
		var obj = $('comment_'+ cid +'_li');
		obj.style.display = "none";
		if($('comment_replynum')) {
			var a = parseInt($('comment_replynum').innerHTML);
			var b = a - 1;
			$('comment_replynum').innerHTML = b + '';
		}
	}
}
//删除feed
function feed_delete(id, result) {
	if(result) {
		var ids = explode('_', id);
		var feedid = ids[1];
		var obj = $('feed_'+ feedid +'_li');
		obj.style.display = "none";
	}
}

//删除分享
function share_delete(id, result) {
	if(result) {
		var ids = explode('_', id);
		var sid = ids[1];
		var obj = $('share_'+ sid +'_li');
		obj.style.display = "none";
	}
}
//删除好友
function friend_delete(id, result) {
	if(result) {
		var ids = explode('_', id);
		var uid = ids[1];
		var obj = $('friend_'+ uid +'_li');
		if(obj != null) obj.style.display = "none";
		var obj2 = $('friend_tbody_'+uid);
		if(obj2 != null) obj2.style.display = "none";
	}
}
//更改分组
function friend_changegroup(id, result) {
	if(result) {
		var ids = explode('_', id);
		var uid = ids[1];
		var obj = $('friend_group_'+ uid);
		var x = new Ajax();
		x.get('do.php?ac=ajax&op=getfriendgroup&uid='+uid, function(s){
			obj.innerHTML = s;
		});
	}
}
//更改分组名
function friend_changegroupname(id, result) {
	if(result) {
		var ids = explode('_', id);
		var group = ids[1];
		var obj = $('friend_groupname_'+ group);
		var x = new Ajax();
		x.get('do.php?ac=ajax&op=getfriendname&group='+group, function(s){
			obj.innerHTML = s;
		});
	}
}
//添加回帖
function post_add(pid, result) {
	if(result) {
		var obj = $('post_ul');
		var newli = document.createElement("div");
		var x = new Ajax();
		x.get('do.php?ac=ajax&op=post', function(s){
			newli.innerHTML = s;
		});
		obj.appendChild(newli);
		if($('message')) {
			$('message').value= '';
			newnode = $('quickpostimg').rows[0].cloneNode(true);
			tags = newnode.getElementsByTagName('input');
			for(i in tags) {
				if(tags[i].name == 'pics[]') {
					tags[i].value = 'http://';
				}
			}
			var allRows = $('quickpostimg').rows;
			while(allRows.length) {
				$('quickpostimg').removeChild(allRows[0]);
			}
			$('quickpostimg').appendChild(newnode);
		}
		if($('post_replynum')) {
			var a = parseInt($('post_replynum').innerHTML);
			var b = a + 1;
			$('post_replynum').innerHTML = b + '';
		}
		//提示获得积分
		showreward();
	}
}
//编辑回帖
function post_edit(id, result) {
	if(result) {
		var ids = explode('_', id);
		var pid = ids[1];

		var obj = $('post_'+ pid +'_li');
		var x = new Ajax();
		x.get('do.php?ac=ajax&op=post&pid='+ pid, function(s){
			obj.innerHTML = s;
		});
	}
}
//删除回帖
function post_delete(id, result) {
	if(result) {
		var ids = explode('_', id);
		var pid = ids[1];
		
		var obj = $('post_'+ pid +'_li');
		obj.style.display = "none";
		if($('post_replynum')) {
			var a = parseInt($('post_replynum').innerHTML);
			var b = a - 1;
			$('post_replynum').innerHTML = b + '';
		}
	}
}
//打招呼
function poke_send(id, result) {
	if(result) {
		var ids = explode('_', id);
		var uid = ids[1];

		if($('poke_'+ uid)) {
			$('poke_'+ uid).style.display = "none";
		}
		//提示获得积分
		showreward();
	}
}
//好友请求
function myfriend_post(id, result) {
	if(result) {
		var ids = explode('_', id);
		var uid = ids[1];
		$('friend_'+uid).innerHTML = '<p>你们现在是好友了，接下来，您还可以：<a href="space.php?uid='+uid+'#comment" target="_blank">给TA留言</a> ，或者 <a href="cp.php?ac=poke&op=send&uid='+uid+'" id="a_poke_'+uid+'" onclick="ajaxmenu(event, this.id, 1)">打个招呼</a></p>';
	}
}
//删除好友请求
function myfriend_ignore(id) {
	var ids = explode('_', id);
	var uid = ids[1];
	$('friend_tbody_'+uid).style.display = "none";
}

//加入群组
function mtag_join(tagid, result) {
	if(result) {
		location.reload();
	}
}

//选择图片
function picView(albumid) {
	if(albumid == 'none') {
		$('albumpic_body').innerHTML = '';
	} else {
		ajaxget('do.php?ac=ajax&op=album&id='+albumid+'&ajaxdiv=albumpic_body', 'albumpic_body');
	}
}
//删除重发邮件
function resend_mail(id, result) {
	if(result) {
		var ids = explode('_', id);
		var mid = ids[1];
		var obj = $('sendmail_'+ mid +'_li');
		obj.style.display = "none";
	}
}

//设置应用不可见
function userapp_delete(id, result) {
	if(result) {
		var ids = explode('_', id);
		var appid = ids[1];
		$('space_app_'+appid).style.display = "none";
	}
}

//do评论
function docomment_get(id, result,num) {
	if(result) {
		var ids = explode('_', id);
		var doid = ids[1];
		var showid = 'docomment_'+doid;
		var opid = 'do_a_op_'+doid;

		$(showid).style.display = '';
		$(showid).className = 'sub_doing';
		ajaxget('cp.php?ac=doing&op=getcomment&doid='+doid, showid);

		if($(opid)) {
			$(opid).innerHTML = '收起';
			$(opid).onclick = function() {
				docomment_colse(doid,num);
			}
		}
		//提示获得积分
		showreward();
	}
}

function docomment_colse(doid,num) {
	var showid = 'docomment_'+doid;
	var opid = 'do_a_op_'+doid;

	$(showid).style.display = 'none';
	$(showid).style.className = '';

	$(opid).innerHTML = num+'回复';
	$(opid).onclick = function() {
		docomment_get(showid, 1,num);
	}
}

function docomment_form(doid, id) {
	var showid = 'docomment_form_'+doid+'_'+id;
	var divid = 'docomment_' + doid;
	ajaxget('cp.php?ac=doing&op=docomment&doid='+doid+'&id='+id, showid);
	if($(divid)) {
		$(divid).style.display = '';
	}
}

function docomment_form_close(doid, id) {
	var showid = 'docomment_form_'+doid+'_'+id;
	$(showid).innerHTML = '';
}

//feed评论
function feedcomment_get(feedid, result) {
	var showid = 'feedcomment_'+feedid;
	var opid = 'feedcomment_a_op_'+feedid;

	$(showid).style.display = '';
	$(showid).className = 'fcomment';
	ajaxget('cp.php?ac=feed&op=getcomment&feedid='+feedid, showid);
	if($(opid) != null) {
		$(opid).innerHTML = '收起';
		$(opid).onclick = function() {
			feedcomment_close(feedid);
		}
	}
}

function feedcomment_add(id, result) {
	if(result) {
		var ids = explode('_', id);
		var cid = ids[1];

		var obj = $('comment_ol_'+cid);
		var newli = document.createElement("div");
		var x = new Ajax();
		x.get('do.php?ac=ajax&op=comment', function(s){
			newli.innerHTML = s;
		});
		obj.appendChild(newli);

		$('feedmessage_'+cid).value= '';
		//提示获得积分
		showreward();
	}
}

//关闭评论
function feedcomment_close(feedid) {
	var showid = 'feedcomment_'+feedid;
	var opid = 'feedcomment_a_op_'+feedid;

	$(showid).style.display = 'none';
	$(showid).style.className = '';

	$(opid).innerHTML = '评论';
	$(opid).onclick = function() {
		feedcomment_get(feedid);
	}
}

//分享完成
function feed_post_result(feedid, result) {
	if(result) {
		location.reload();
	}
}

//显示更多动态
function feed_more_show(feedid) {
	var showid = 'feed_more_'+feedid;
	var opid = 'feed_a_more_'+feedid;

	$(showid).style.display = '';
	$(showid).className = 'sub_doing';

	$(opid).innerHTML = '&laquo; 收起列表';
	$(opid).onclick = function() {
		feed_more_close(feedid);
	}
}

function feed_more_close(feedid) {
	var showid = 'feed_more_'+feedid;
	var opid = 'feed_a_more_'+feedid;

	$(showid).style.display = 'none';

	$(opid).innerHTML = '&raquo; 更多动态';
	$(opid).onclick = function() {
		feed_more_show(feedid);
	}
}

//发布投票
function poll_post_result(id, result) {
	if(result) {
		var aObj = $('__'+id).getElementsByTagName("a");
		window.location.href = aObj[0].href;
	}
}

//点评之后
function show_click(id) {
	var ids = id.split('_');
	var idtype = ids[1];
	var id = ids[2];
	var clickid = ids[3];
	ajaxget('cp.php?ac=click&op=show&clickid='+clickid+'&idtype='+idtype+'&id='+id, 'click_div');
	//提示获得积分
	showreward();
}

//feed菜单
function feed_menu(feedid, show) {
	var obj = $('a_feed_menu_'+feedid);
	if(obj) {
		if(show) {
			obj.style.display='block';
		} else {
			obj.style.display='none';
		}
	}
	var obj = $('feedmagic_'+feedid);
	if(obj) {
		if(show) {
			obj.style.display='block';
		} else {
			obj.style.display='none';
		}
	}
}

//填写生日
function showbirthday(){
	$('birthday').length=0;
	for(var i=0;i<28;i++){
		$('birthday').options.add(new Option(i+1, i+1));
	}
	if($('birthmonth').value!="2"){
		$('birthday').options.add(new Option(29, 29));
		$('birthday').options.add(new Option(30, 30));
		switch($('birthmonth').value){
			case "1":
			case "3":
			case "5":
			case "7":
			case "8":
			case "10":
			case "12":{
				$('birthday').options.add(new Option(31, 31));
			}
		}
	} else if($('birthyear').value!="") {
		var nbirthyear=$('birthyear').value;
		if(nbirthyear%400==0 || nbirthyear%4==0 && nbirthyear%100!=0) $('birthday').options.add(new Option(29, 29));
	}
}
/**
 * 插入涂鸦
 * @param String fid: 要关闭的层ID
 * @param String oid: 要插入到对象的目标ID
 * @param String url: 涂鸦文件的地址
 * @param String tid: 切换标签ID
 * @param String from: 涂鸦从哪来的
 * @return 没有返回值
 */
function setDoodle(fid, oid, url, tid, from) {
	if(tid == null) {
		hideMenu();
	} else {
		//用于两标签切换用
		$(tid).style.display = '';
		$(fid).style.display = 'none';
	}
	var doodleText = '[img]'+url+'[/img]';
	if($(oid) != null) {
		if(from == "editor") {
			insertImage(url);
		} else {
			insertContent(oid, doodleText);
		}
	}
}

function selCommentTab(hid, sid) {
	$(hid).style.display = 'none';
	$(sid).style.display = '';
}


//文字闪烁
function magicColor(elem, t) {
	t = t || 10;//最多尝试
	elem = (elem && elem.constructor == String) ? $(elem) : elem;
	if(!elem){
		setTimeout(function(){magicColor(elem, t-1);}, 400);//如果没有加载完成，推迟
		return;
	}
	if(window.mcHandler == undefined) {
		window.mcHandler = {elements:[]};
		window.mcHandler.colorIndex = 0;
		window.mcHandler.run = function(){
			var color = ["#CC0000","#CC6D00","#CCCC00","#00CC00","#0000CC","#00CCCC","#CC00CC"][(window.mcHandler.colorIndex++) % 7];
			for(var i = 0, L=window.mcHandler.elements.length; i<L; i++)
				window.mcHandler.elements[i].style.color = color;
		}
	}
	window.mcHandler.elements.push(elem);
	if(window.mcHandler.timer == undefined) {
		window.mcHandler.timer = setInterval(window.mcHandler.run, 500);
	}
}

//隐私密码
function passwordShow(value) {
	if(value==4) {
		$('span_password').style.display = '';
		$('tb_selectgroup').style.display = 'none';
	} else if(value==2) {
		$('span_password').style.display = 'none';
		$('tb_selectgroup').style.display = '';
	} else {
		$('span_password').style.display = 'none';
		$('tb_selectgroup').style.display = 'none';
	}
}

//隐私特定好友
function getgroup(gid) {
	if(gid) {
		var x = new Ajax();
		x.get('cp.php?ac=privacy&op=getgroup&gid='+gid, function(s){
			s = s + ' ';
			$('target_names').innerHTML += s;
		});
	}
}