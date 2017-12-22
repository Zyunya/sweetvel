//////////////////////////////////////////////////////metrics///////////////////////

function metricsajax1(position){
	var device  = navigator.userAgent;
    var referer = document.referrer;
	var lat  = position.coords.latitude;
	var lon  = position.coords.longitude;	
	var coord = lat.toFixed(5)+","+lon.toFixed(5);
	var datar = "ajax=metrics&&time="+date()+"&&device="+device+"&&coord="+coord+"&&referer="+referer;
	$.ajax({
   type : "POST",
   url  : "ajax/ajax.php",
   data : datar,
   success: function(data){ },
   error  : function(err){console.log(err);}
   })
 }	
 try{				  
function showError(error){
	var device  = navigator.userAgent;
    var referer = document.referrer;
 var datar = "ajax=metrics&&time="+date()+"&&device="+device+"&&coord=undefined&&referer="+referer;
	$.ajax({
   type : "POST",
   url  : "ajax/ajax.php",
   data : datar,
   success: function(data){ },
   error  : function(err){console.log(err);}
   })
 }
}
catch(err){console.log(err)}
function  metrics() {if (navigator.geolocation) { navigator.geolocation.getCurrentPosition(metricsajax1,showError);} else {showError(error)}}	

$(function(){

  
  metrics();
  


})