/*
 * Manejo de variables de uso local y de sesion
 * NOTA: Campo ce (ej: $.local.ce) debe ser una expresion regular que indique las excepciones de borrado.
 *		 Si no se indican excepciones, seran borrados todos los elementos.
 */
(function($,window,document,JSON){
	//localStorage
	if('localStorage' in window && window['localStorage'] !== null) {
		var ls=window['localStorage'];
		$.local=function(key,value){
			if(typeof key == 'string'){
				if(value!==undefined){
					if(value===null)
						ls.removeItem(key);
					else
						ls[key]=JSON.stringify(value);
				}
				value=ls[key];
				if(typeof value != 'string') value='null';
				return JSON.parse(value);
			}else
				return true;
		};
		$.local.get=function(key,value,options){
			return $.local(key,value,options);
		};
		$.local.set=function(values,options){
			for(var key in values) $.local(key,values[key],options||{});
			return $.local;
		};
		$.local.clear=function(){
			var i,key;
			for(i=ls.length;i>0;i--){
				key=ls.key(i-1);
				if( !$.local.ce || !key.match($.local.ce) ) ls.removeItem(key);
			}
			return $.local;
		};
	} else {
		$.local=function(){return null;};
	}
	//sessionStorage
	if('sessionStorage' in window && window['sessionStorage'] !== null) {
		var ss=window['sessionStorage'];
		$.session=function(key,value){
			if(typeof key == 'string'){
				if(value!==undefined){
					if(value===null)
						ss.removeItem(key);
					else
						ss[key]=JSON.stringify(value);
				}
				value=ss[key];
				if(typeof value != 'string') value='null';
				return JSON.parse(value);
			}else
				return true;
		};
		$.session.set=function(values,options){
			for(var key in values) $.session(key,values[key],options||{});
			return $.session;
		};
		$.session.clear=function(){
			var i,key;
			for(i=ss.length;i>0;i--){
				key=ss.key(i-1);
				if( !$.session.ce || !key.match($.session.ce) ) ss.removeItem(key);
			}
			return $.session;
		};
	}else{
		$.session=function(){return null;};
	}
})(jQuery,window,document,JSON);
