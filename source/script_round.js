/*
var tips = new Array();
//条目内容
tips[0] = '山有木兮木有枝，心悦君兮君不知。';
tips[1] = '花红易衰似郎意，水流无限似侬愁。';
tips[2] = '天涯地角有穷时，只有相思无尽处。';
tips[3] = '只愿君心似我心，定不负相思意。';
tips[4] = '路过是一瞬，错过是永恒';
tips[5] = '若你决定灿烂，山无遮海无拦';
tips[6] = '爱心一片,真情永远。';
tips[7] = '聆听心声,实现愿望。';
tips[8] = '天涯海角，惟愿君安';
tips[9] = '一念放下，万般自在';
tips[10] = '陪君醉笑三千场，不诉离殇';
tips[11] = '世界以痛吻我，要我报之以歌';
tips[12] = '用力爱，哪怕之后洪水滔天';
tips[13] = '我想牵你的手，从心动到古稀';
tips[14] = '君记我一瞬，我念君半生';
tips[15] = '长不过执念，短不过善变';
tips[16] = '我花掉一整幅青春，用来寻你';
tips[17] = '爱，就是不顾一切和忍受一切';
tips[18] = '遇见你，就是我最好的时光';
tips[19] = '你还没来，我还在等';
tips[20] = '只要你在，我就心安';
tips[22] = '寄君一曲，不问曲终人聚散';
tips[21] = '给我一杯时光，让我忘掉忧伤';
tips[23] = '此生，遇见你，已很美';
tips[24] = '他来过一阵子，你怀念一辈子';
tips[25] = '有些爱，只能止于唇齿，掩于岁月';
tips[26] = '爱就是你等我，等我，还等我';
tips[27] = '妈妈是个美人，岁月你别伤害她';
tips[28] = '天不老，情难绝';
tips[29] = '最初的天堂，最终的荒唐';
tips[30] = '一见倾心，何其有幸';
tips[31] = '回忆若能下酒，往事一场宿醉';
tips[32]= '海水尚有涯，相思渺无畔';
tips[33]='繁华落尽，愿与君老';
tips[34]='缘来天注定，缘去人自夺';
tips[35]='因为爱情，可以生，可以死';
tips[36]='只要自觉心安，东西南北都好';
tips[37]='有一种感情一辈子都不会输给时间';


var ask = jQuery.cookie("zhibotie_ask");
if(ask){
	index = ask ;
	}
else{
	index = Math.floor(Math.random() * tips.length);
	jQuery.cookie("zhibotie_ask",index);
	}
*/

var img = "http://www-zhibotie-net.b0.upaiyun.com/alimama/20120816_NYWFQ.gif",
	link = "http://s.click.taobao.com/t_8?e=7HZ6jHSTbIcervns0DXRHPWvPrg8vSPUjhKJJuJ01BLQ1Q%3D%3D&p=mm_11751545_0_0",
	name = "那一舞风情",
	html = "<a href=\""+link+"\" target=\"_blank\"><img src=\""+img+"\" border=\"0\" alt=\""+name+"\"></a>";
$(function(){
	//$("#OSC_Slogon").html("[今天是父亲节] ");
	$("#show_ad_alimama").html(html );
	});