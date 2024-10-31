 $(function(){
       	var now = new Date();
        var hours = now.getHours()+""+now.getMinutes();
        
        $('table tr').each(function(i,v){
        	if($(this).attr("value").replace(":","")>=hours)
            {    
				$(this).addClass("hightlight");
				return false;
			}			
        });
    
});