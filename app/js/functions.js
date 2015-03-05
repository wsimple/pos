function redir(url){
	var hash=document.location.pathname==''&&location.search.substring(1)=='';
	if(url.substr(0,1)=='#'){
		if(hash)
			document.location.hash=url.substr(1);
		else
			document.location='./'+url;
	}else
		document.location=url;
}

function redirect(url, op){
	if(op){
		window.open(url);
	}else if(url.substr(0,1)=='#'){
		document.location.hash=url.substr(1);
	}else{
		document.location=url;
	}
}

function goToScroll(anchor){
	$('html, body').animate({
    	scrollTop: $(anchor).offset().top-63
	}, 2000);
}

function deleteRecord(url, layer, title){
	$('#confirm-reveal h5').append('<strong>'+title+'</strong> ?');
	$('#confirm-reveal').foundation('reveal', 'open');
	$( "#delete" ).click(function() {
		$.ajax({
			url: url,
			type:  'POST',
			dataType: 'JSON',
			success:  function (data) {
				if (data['out']=='ok'){
					$(layer).fadeOut( "slow", function() {
				    	$('#confirm-reveal').foundation('reveal', 'close');
				    });
			    }
			}
		});
	});	
}

function showLayer(layer){
	
  		$(layer).fadeIn('slow');
	
}

function addRequiredAttribute(ids){
	var array = ids.split(',');
	for (var i = 0; i < array.length; i++) {
		$('#'+array[i]).html('required');
	};
}