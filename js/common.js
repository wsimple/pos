(function($){
	$(document).on('click','.linkBack',function(){
		history.back(1);
		return false;
	}).on('click','.linkPrint',function(){ 
		window.print();
		return false;
	});
	//All Title attributes (tooltips)
	$('body').find('*:not(#chat)').on('mouseenter','[title]',function(e){
		//mouse over (hover)
		var title=this.title||$(this).data('title');
		if(!title) return;
		this.title='';
		$(this).data('title',title);
		var $tooltip=$('<p class="tooltip"></p>');
		$(this).data('tooltip',$tooltip);
		$tooltip.text(title).appendTo('body').fadeIn('fast');
	}).on('mousemove','[title]',function(e){
		var $tooltip=$(this).data('tooltip');
		if(!$tooltip) return;
		var mousex=e.pageX+20;//Get X coordinates
		var mousey=e.pageY+10;//Get Y coordinates
		if($tooltip.outerWidth()+mousex>=$(window).width()-5) mousex=e.pageX-10-$tooltip.outerWidth();
		if($tooltip.outerHeight()+mousey>=$(window).height()-5) mousey=e.pageY-$tooltip.outerHeight();
		$tooltip.css({top:mousey,left:mousex});
	}).on('mouseleave','[title]',function(){
		//mouse out
		var $tooltip=$(this).data('tooltip');
		if(!$tooltip) return;
		$(this).data('tooltip',null);
		$tooltip.remove();
	});
})(jQueryNew);

function get_dimensions() 
{
	var dims={width:0,height:0};
	
  if( typeof( window.innerWidth ) == 'number' ) {
	//Non-IE
	dims.width=window.innerWidth;
	dims.height=window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
	//IE 6+ in 'standards compliant mode'
	dims.width=document.documentElement.clientWidth;
	dims.height=document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
	//IE 4 compatible
	dims.width=document.body.clientWidth;
	dims.height=document.body.clientHeight;
  }
  
  return dims;
}

function set_feedback(text, classname, keep_displayed)
{
	if(text!='')
	{
		$('#feedback_bar').removeClass();
		$('#feedback_bar').addClass(classname);
		$('#feedback_bar').text(text);
		$('#feedback_bar').css('opacity','1');

		if(!keep_displayed)
		{
			$('#feedback_bar').fadeTo(5000, 1);
			$('#feedback_bar').fadeTo("fast",0);
		}
	}
	else
	{
		$('#feedback_bar').css('opacity','0');
	}
}

//keylisteners
$(window).jkey('f1,f2,f3,f4,f6,f7,f8,f9,f10,f11',function(k){
	switch(k){
		case 'f1':
			window.location=BASE_URL+'/customers/index';
		break;
		case 'f2':
			window.location=BASE_URL+'/items/index';
		break;
		case 'f3':
			window.location=BASE_URL+'/item_kits/index';
		break;
		case 'f4':
			window.location=BASE_URL+'/suppliers/index';
		break;
		case 'f5':
		break;
		case 'f6':
			window.location=BASE_URL+'/receivings/index';
		break;
		case 'f7':
			window.location=BASE_URL+'/sales/index';
		break;
		case 'f8':
			window.location=BASE_URL+'/employees/index';
		break;
		case 'f9':
			window.location=BASE_URL+'/giftcards/index';
		break;
		case 'f10':
			window.location=BASE_URL+'/config/index';
		break;
		case 'f11':
			window.location=BASE_URL+'/employees/assistance';
		break;
	}
});

if(!document.location.host.match(/^(localhost|192\.|127\.)/i))
$(window).jkey('f5',function(){
window.location=BASE_URL+'/reports/index';
});
