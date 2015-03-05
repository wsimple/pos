var showlogs=false;
var nolog=/friendslist|heartbeat/gi;
var chatcontrol='index.php/chat/';
var play=true;
var minChatHeartbeat=2000;
var maxChatHeartbeat=20000;
var windowFocus=true;
var chatHeartbeatCount=0;
var originalTitle;
var blinkOrder=0;
var chatpos=175;

(function($){
if(window.chatStarted) return;
var chat=$('body>#chat')[0];
$(chat).html(
	'<div id="chatmsgs"></div>'+
	'<div id="chatMainContainer">'+
		'<div class="chatListContainer"></div>'+
		'<div class="chatConfig">'+
			'<input type="button" id="enable" value="Show" style="display:none;"/>'+
			'<input type="button" id="disable" value="Hide" style="display:none;"/>'+
			'<span id="hideChat" style="display:none;"></span>'+
			'<span id="showChat" style="display:none;"></span>'+
		'</div>'+
	'</div>'
).on('click','.listUserChat',function(){
	if(this.id!=userid){
		var chatbox=createChatBox(this.id,0,this.dataset.name,1);
		$('textarea',chatbox).focus();
	}
}).on('keyup','#chatmsgs textarea',function(event){
	checkChatBoxInputKey.call(this,event);
}).on('click','.chatbox',function(){
	if($('.chatboxcontent',this).is(':visible')){
		$('textarea',this).focus();
	}
}).on('click','.chatbox .minimize',function(){
	toggleChatBoxGrowth($(this).parents('.chatbox')[0].id);
}).on('click','.chatboxoptions #close',function(){
	var chatbox=$(this).parents('.chatbox'),
		chatboxusr=chatbox[0].id;
	chatbox.css('display','none');
	//chatbox.remove();
	$('textarea',chatbox).val('');
	restructureChatBoxes();
	typing[chatboxusr]=false;
	chat_ajax(chatcontrol+'stoptyping',{to:chatboxusr});
	chat_ajax(chatcontrol+'closechat',{chatbox:chatboxusr});
}).on('click','.chatConfig #hideChat',function(){
	$('#showChat',chat).show();
	$('#hideChat',chat).hide();
	$('.chatListContainer',chat).slideUp();
	$.local('minchat_'+userid,true);
}).on('click','.chatConfig #showChat',function(){
	$('#showChat',chat).hide();
	$('#hideChat',chat).show();
	$('.chatListContainer',chat).slideDown();
	$.local('minchat_'+userid,null);
}).on('click','.chatConfig #enable,.chatConfig #disable',function(){
	showChat(this.id);
	//changeStatus(this.id,this);
});

var userid='';
var username='';
var chatHeartbeatTime=minChatHeartbeat;
var typing=new Array();
var chatBoxes=new Array();
var chatboxFocus=new Array();
var newMessages=new Array();
var newMessagesWin=new Array();

if(window.matchMedia('screen').matches)
	chat_init();
	// chat_ajax(chatcontrol+'islogged',chat_init);

function chat_init(data){
	window.chatStarted=true;
	$('#chatMainContainer',chat).show();
	startChatSession();
	
	originalTitle=document.title;
	$([window,document]).blur(function(){
		windowFocus=false;
	}).focus(function(){
		windowFocus=true;
		document.title=originalTitle;
	});
}

function startChatSession(){
	chat_ajax({
		url:chatcontrol+'startchatsession',
		success:function(data){
			if(showlogs) console.log(['startchatsession',data]);
			var chatbox,content;
			userid=data.userid;
			username=data.username;
			showChat();
			$.each(data.items,function(i,item){
				if(item){//fix strange ie bug
					chatbox=$('.chatbox#'+item.f);
					if(chatbox.length<=0){
						chatbox=createChatBox(item.f,1,item.u,3);
					}
					if(item.s==1){
						item.f=userid;
						item.u=username;
					}
					if(item.s==2){
						$('.chatboxcontent',chatbox).append('<div class="chatboxmessage"><span class="chatboxinfo">'+emoticons(item.m)+'</span></div>');
					}else{
						$('.chatboxcontent',chatbox).append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+item.u+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+emoticons(item.m)+'</span></div>');
					}
				}
			});
			for(i=0;i<chatBoxes.length;i++){
				content=$('.chatboxcontent',chatBoxes[i]);
				content.scrollTop(content[0].scrollHeight);
				setTimeout(function(){
					content.scrollTop(content[0].scrollHeight);
				},100);//yet another strange ie bug
			}
			chatHeartbeat(1);
		}
	});
}

function checkChatBoxInputKey(event){
	if(this.value!=''&&!typing[this.id]){
		typing[this.id]=true;
		chat_ajax(chatcontrol+'starttyping',{to:this.id});
	}
	if(this.value==''&&typing[this.id]){
		typing[this.id]=false;
		chat_ajax(chatcontrol+'stoptyping',{to:this.id});
	}
	if(event.keyCode==13&&event.shiftKey==0){
		message=this.value;
		message=message.replace(/^\s+|\s+$/g,"");
		this.value='';
		$(this).focus().css('height','44px');
		if(message!=''){
			typing[this.id]=false;
			chat_ajax(chatcontrol+'stoptyping',{to:this.id});
			message=message.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/\"/g,"&quot;").replace(/\'/g,"&apos;");
			message=linkify(message);
			var content=$(this).parents('.chatbox').find('.chatboxcontent');
			content.append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+username+': </span><span class="chatboxmessagecontent">'+emoticons(message)+'</span></div>')
				.scrollTop(content[0].scrollHeight);
			if(showlogs) console.log(message);
			chat_ajax(chatcontrol+'sendchat',{to:this.id,message:message});
		}
		chatHeartbeatTime=minChatHeartbeat;
		chatHeartbeatCount=1;
		return false;
	}
	var adjustedHeight=this.clientHeight;
	var maxHeight=94;
	if(maxHeight>adjustedHeight){
		adjustedHeight=Math.max(this.scrollHeight,adjustedHeight);
		if(maxHeight)
			adjustedHeight=Math.min(maxHeight,adjustedHeight);
		if(adjustedHeight > this.clientHeight)
			$(this).css('height',adjustedHeight+8+'px');
	}else{
		$(this).css('overflow','auto');
	}
}

function changeStatus(status,submit){
	if(submit) submit.disabled=true;
	chat_ajax(chatcontrol+'status/'+(status?1:0),function(){
		showChat(status);
		startChatSession();
	}).always(function(){
		if(submit) submit.disabled=false;
	});
}

function showChat(enable){
	var hide=false,min=false,slow=(enable!==undefined&&userid);
	if(userid){
		hide=$.local('hidechat_'+userid);
		min=$.local('minchat_'+userid);
	}
	if(enable=='enable'||enable=='disable') enable=(enable=='enable');
	if(enable!==undefined) hide=!enable;
	if(hide){
		$('#enable',chat).show();
		$('#disable,#showChat,#hideChat',chat).hide();
		$('.chatListContainer',chat)[slow?'slideUp':'hide']();
		$('#chatmsgs',chat)[slow?'fadeOut':'hide']();
		//play=false;
	}else{
		$('#enable',chat).hide();
		$('#disable',chat).show();
		$('#showChat',chat)[min?'show':'hide']();
		$('#hideChat',chat)[min?'hide':'show']();
		$('.chatListContainer',chat)[!min?(slow?'slideDown':'show'):(slow?'slideUp':'hide')]();
		$('#chatmsgs',chat)[slow?'fadeIn':'show']();
		//play=true;
		//startChatSession();
	}
	if(userid) $.local('hidechat_'+userid,hide||null);
}

var chbt;//chatheartbeat timeout var
function chatHeartbeat(p){
	if(chbt) return;
	var itemsfound=0,x;
	if(windowFocus==false){
		var blinkNumber=0;
		var titleChanged=0;
		for(x in newMessagesWin){
			if(newMessagesWin[x]==true){
				++blinkNumber;
				if(blinkNumber>=blinkOrder){
					document.title='(*)'+originalTitle;
					titleChanged=1;
					break;
				}
			}
		}
		if(titleChanged==0){
			document.title=originalTitle;
			blinkOrder=0;
		}else{
			++blinkOrder;
		}
	}else{
		for(x in newMessagesWin){
			newMessagesWin[x]=false;
		}
	}
	for(x in newMessages){
		if(newMessages[x]==true){
			if(chatboxFocus[x]==false){
				//FIXME: add toggle all or none policy,otherwise it looks funny
				$('.chatbox#'+x+' .chatboxhead').toggleClass('chatboxblink');
			}
		}
	}
	var blink=$('#chatmsgs .chatboxblink').length>0;
	$('#chat .chatConfig')[blink?'addClass':'removeClass']('chatboxblink');
	data=null;
	chbt=true;
	chat_ajax({
		url:chatcontrol+'chatheartbeat',
		success:function(data){
			if(data){
				var chatbox;
				$.each(data.items,function(i,item){
					if(item){//fix strange ie bug
						chatbox=$('.chatbox#'+item.f);
						if(chatbox.length<=0){	
							chatbox=createChatBox(item.f,0,item.u,2);
						}
						if(chatbox.css('display')=='none'){
							chatbox.css('display','block');
							restructureChatBoxes();
						}
						if(item.s==1){
							item.f=userid;
							item.u=username;
						}
						if(item.s==2){
							$('.chatboxcontent',chatbox).append('<div class="chatboxmessage"><span class="chatboxinfo">'+emoticons(item.m)+'</span></div>');
						}else{
							newMessages[item.f]=true;
							newMessagesWin[item.f]=true;
							$('.chatboxcontent',chatbox).append('<div class="chatboxmessage"><span class="chatboxmessagefrom">'+item.u+':&nbsp;&nbsp;</span><span class="chatboxmessagecontent">'+emoticons(item.m)+'</span></div>');
						}
						$('.chatboxcontent',chatbox).scrollTop($('.chatboxcontent',chatbox)[0].scrollHeight);
						itemsfound+=1;
					}
				});
				$.each(data.ty,function(i,item){
					if(item){// fix strange ie bug
						$('.chatbox#'+item.u+' .typing').css('visibility',item.s=='1'?'visible':'hidden');
					}
				});
			}
			chatHeartbeatCount++;
			if(itemsfound>0){
				chatHeartbeatTime=minChatHeartbeat;
				chatHeartbeatCount=1;
			}else if(chatHeartbeatCount>=10){
				chatHeartbeatTime+=minChatHeartbeat;
				chatHeartbeatCount=1;
				if(chatHeartbeatTime>maxChatHeartbeat){
					chatHeartbeatTime=maxChatHeartbeat;
				}
			}
			if(play){
				listFriendsChat(p);
				setTimeout(chatHeartbeat,chatHeartbeatTime);
			}
		},
		complete:function(){
			chbt=false;
		}
	});
}

var lfc;
function listFriendsChat(p){
	if(lfc&&!p) return;
	if(p==1){
		if($.trim($('.chatListContainer').html())==''){
			$('.chatListContainer').html('<div id="loading"></div>');
		}
	}else{
		p='';
	}
	lfc=true;
	chat_ajax({
		url:chatcontrol+'friendslist',
		data:{'update':p},
		success:function(data){
			if(data!=null){
				if(showlogs) console.log(data);
				switch(data.a*1){
				case 1:
					var el,me,salida='';
					$.each(data.f,function(i,item){
						if(item){
							me=(item.c==userid);
							el='<div id="'+item.c+'" data-name="'+item.u+'" data-status="'+item.t+'" '+(me?'':'title="'+item.t+'"')+' class="listUserChat'+(me?' me':'')+'"></div>';
							if(me)
								salida=el+salida;
							else
								salida+=el;
						}
					});
					$('.chatListContainer').html(salida);
				break;
				case 2:
					//execute code block 2
				break;
				default:
					//code to be executed if n is different from case 1 and 2
					$('.chatListContainer').html('<div style="text-align:center;">Empty</div>');
				}
			}
		},
		complete:function(){
			lfc=false;
		}
	});
}

function restructureChatBoxes(){
	align=0;
	for(var x in chatBoxes){
		chatbox=$(chatBoxes[x]);
		if(chatbox.css('display')!='none'){
			if(align==0){
				chatbox.css('right',chatpos+'px');
			}else{
				width=(align)*(225+7)+chatpos;
				chatbox.css('right',width+'px');
			}
			align++;
		}
	}
}

function createChatBox(chatboxusr,minimizeChatBox,chatboxname,tmp){
	if(showlogs) console.log([tmp,chatboxusr,chatboxname]);
	var chatbox=$('.chatbox#'+chatboxusr);
	if(chatbox.length > 0){
		if(chatbox.css('display')=='none'){
			chatbox.css('display','block');
			restructureChatBoxes();
		}
		$('textarea',chatbox).focus();
		return chatbox;
	}
	typing[chatboxusr]=false;
	chatbox=$('<div/>').addClass('chatbox').attr('id',chatboxusr)
	.html('<div class="chatboxhead"><div class="chatboxtitle">'+chatboxname+'</div><div class="minimize"></div><div class="chatboxoptions"> <a id="close" href="javascript:void(0)">X</a></div><br clear="all"/></div><div class="chatboxarea"><div class="chatboxcontent"></div><div class="typing">'+chatboxname+' is typing...</div></div><div class="chatboxinput"><textarea id="'+chatboxusr+'" data-name="'+chatboxname+'" class="chatboxtextarea"></textarea></div>')
	.appendTo('#chatmsgs');
	chatbox.css('bottom','0px');
	chatBoxeslength=0;
	for(x in chatBoxes){
		if($(chatBoxes[x]).css('display')!='none'){
			chatBoxeslength++;
		}
	}
	if(chatBoxeslength==0){
		chatbox.css('right',chatpos+'px');
	}else{
		width=(chatBoxeslength)*(225+7)+chatpos;
		chatbox.css('right',width+'px');
	}
	chatBoxes.push(chatbox[0]);
	if(minimizeChatBox!=0){
		minimizedChatBoxes=new Array();
		if($.local('chatbox_minimized')){
			minimizedChatBoxes=$.local('chatbox_minimized').split(/\|/);
		}
		minimize=0;
		for(j=0;j<minimizedChatBoxes.length;j++){
			if(minimizedChatBoxes[j]==chatboxusr){
				minimize=1;
			}
		}
		if(minimize==1){
			$('.chatboxarea,.chatboxinput',chatbox[0]).css('display','none');
		}
	}
	chatboxFocus[chatboxusr]=false;
	$('textarea',chatbox[0]).blur(function(){
		chatboxFocus[chatboxusr]=false;
		$(this).removeClass('chatboxtextareaselected');
	}).focus(function(){
		chatboxFocus[chatboxusr]=true;
		newMessages[chatboxusr]=false;
		$('.chatboxhead',chatbox[0]).removeClass('chatboxblink');
		$('.chatboxtextarea',chatbox[0]).addClass('chatboxtextareaselected');
	});
	chatbox.show();
	return chatbox;
}

function toggleChatBoxGrowth(chatboxusr){
	var chatbox=$('.chatbox#'+chatboxusr);
	if($('.chatboxarea',chatbox).css('display')=='none'){  
		var minimizedChatBoxes=new Array();
		if($.local('chatbox_minimized')){
			minimizedChatBoxes=$.local('chatbox_minimized').split(/\|/);
		}
		var newLocal='';
		for(i=0;i<minimizedChatBoxes.length;i++){
			if(minimizedChatBoxes[i]!=chatboxusr){
				newLocal+=chatboxusr+'|';
			}
		}
		newLocal=newLocal.slice(0,-1)
		$.local('chatbox_minimized',newLocal);
		$('.chatboxarea,.chatboxinput',chatbox).css('display','block');
		$('.chatboxcontent',chatbox).scrollTop($('.chatboxcontent',chatbox)[0].scrollHeight);
	}else{
		var newLocal=chatboxusr;
		if($.local('chatbox_minimized')){
			newLocal+='|'+$.local('chatbox_minimized');
		}
		$.local('chatbox_minimized',newLocal);
		$('.chatboxarea,.chatboxinput',chatbox).css('display','none');
	}
}

function linkify(text){
	var inputText=text;
	//URLs starting with http://,https://,or ftp://
	var replacePattern1=/(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
	var replacedText=inputText.replace(replacePattern1,"<a href=$1 target=_blank>$1</a>");
	//URLs starting with www. (without // before it,or it'd re-link the ones done above)
	var replacePattern2=/(^|[^\/])(www\.[\S]+(\b|$))/gim;
	replacedText=replacedText.replace(replacePattern2,"$1<a href=http://$2 target=_blank>$2</a>");
	//Change email addresses to mailto:: links
	var replacePattern3=/(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/gim;
	replacedText=replacedText.replace(replacePattern3,"<a href=mailto:$1>$1</a>");
	var replacePattern4=/(^|[^\/])(.*\.(net$|com$|org$))/gim;
	replacedText=replacedText.replace(replacePattern4,"$1<a href=http://$2 target=_blank>$2</a>");
	return replacedText;
}

function emoticons(text){
	var inputText=text||'';
	inputText=inputText.replace(/(^|\s):-?\)(\s|$)/gim,		'<div class="em smile"></div>');
	inputText=inputText.replace(/(^|\s):-?D(\s|$)/gim,		'<div class="em bigSmile"></div>');
	inputText=inputText.replace(/(^|\s):-?\((\s|$)/gim,		'<div class="em sad"></div>');
	inputText=inputText.replace(/(^|\s):-?P(\s|$)/gim,		'<div class="em tongeOut"></div>');
	inputText=inputText.replace(/(^|\s);-?\)(\s|$)/gim,		'<div class="em wink"></div>');
	inputText=inputText.replace(/(^|\s):-?o(\s|$)/gm,		'<div class="em surprise"></div>');
	inputText=inputText.replace(/(^|\s):-?O(\s|$)/gm,		'<div class="em surprise2"></div>');
	inputText=inputText.replace(/(^|\s):-?s(\s|$)/gim,		'<div class="em uneasy"></div>');
	inputText=inputText.replace(/(^|\s):'-?\((\s|$)/gim,	'<div class="em sob"></div>');
	inputText=inputText.replace(/(^|\s)8-?\)(\s|$)/gim,		'<div class="em hearteyes"></div>');
	inputText=inputText.replace(/(^|\s)t[-_]t(\s|$)/gim,	'<div class="em crying"></div>');
	inputText=inputText.replace(/(^|\s)(<|&lt;)3(\s|$)/gim,	'<div class="em heart"></div>');
	inputText=inputText.replace(/(^|\s)xd(\s|$)/gim,		'<div class="em xD"></div>');
	inputText=inputText.replace(/(^|\s):-?\$(\s|$)/gim,		'<div class="em shame"></div>');
	inputText=inputText.replace(/(^|\s):-?\*(\s|$)/gim,		'<div class="em kiss"></div>');
	return inputText;
}

function chat_ajax(url,data,success){
	var d={};
	if(url){
		if(typeof(url)=='string')//url
			d.url=url;
		else if(typeof(url)=='object')//objeto ajax
			d=url;
		if(typeof(data)=='object')//post data
			d.data=data;
		else if(typeof(data)=='function')//success
			d.success=data;
		if(success){
			if(!d.success)//success
				d.success=success;
			else//si ya existe success, se toma como error
				d.error=success;
		}
	}

	d.dataType='json';
	d.type=d.data?'post':'get';
	d.statusCode={
		500:function(){
			d.count=(d.count||0)+1;
			if(d.count<5) chat_ajax(d);
		}
	};
	var ajax=$.ajax(d);
	if(d.url.match(nolog)) return ajax;
	if(!showlogs) return ajax;
	return ajax.done(function(data){
		var D={'url':d.url,'success':data};
		if(d.data) D.post=d.data;
		console.log(D);
	}).fail(function(jqXHR,textStatus,errorThrown){
		var D={'url':d.url,'error':[jqXHR,textStatus,errorThrown]};
		if(d.data) D.post=d.data;
		console.log(D);
	});
}

})(window.jQueryNew||window.$||window.jQuery);
