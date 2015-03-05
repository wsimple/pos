(function(w){
	var jq={};
	w.jQuerySwitch=function jQuerySwitch(name,val){
		if(val)	jq[name]=val;
		w.$=w.jQuery=jq[name];
		return jq[name];
	};
	w.jQ=function jQ(name){
		return jq[name]||w.jQuery;
	};
})(window);
