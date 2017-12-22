
module.exports = {
  
    date: function() {
       var d = new Date();
        var hours   = d.getHours();
        var date    = d.getDate();
        var month   = d.getMonth() + 1;
        var minutes = d.getMinutes();
        var dayweek = d.getDay();
        var year    = d.getFullYear().toString().substring(2,4);
        if(year    < 10){year    = "0" + year};
        if(date    < 10){date    = "0" + date};
        if(month   < 10){month   = "0" + month};
        if(minutes < 10){minutes = "0" + minutes;}
        if(hours   < 10){hours   = "0" + hours;}
        var time =date+"."+ month+"."+year+"."+ hours + ":"+minutes+"."+dayweek;
        return time;
    },
    datejson: function() {
       var d = new Date();
        var hours   = d.getHours();
        var date    = d.getDate();
        var month   = d.getMonth() + 1;
        var minutes = d.getMinutes();
        var weekday = d.getDay();
        var year = d.getFullYear().toString().substring(2,4);
        if(year < 10){year = "0" + year};
        if(date < 10){date = "0" + date};
        if(month < 10){month = "0" + month};
        if(minutes < 10 ){  minutes = "0" + minutes;}
        if(hours < 10 ){  hours = "0" + hours;}
        var time =JSON.stringify({date : date,month : month,year : year, hours : hours, minutes : minutes,weekday : weekday});
        return time;
    },
    mobilecheck: function(userAgent){
 if(userAgent.match(/Android/i)
 || userAgent.match(/webOS/i)
 || userAgent.match(/iPhone/i)
 || userAgent.match(/iPad/i)
 || userAgent.match(/iPod/i)
 || userAgent.match(/BlackBerry/i)
 || userAgent.match(/Windows Phone/i)
 ){
    return true;
  }
 else {
    return false;
     }
    },

    online_status: function(host,request,cook,useragent,status){

         request.post({
         headers :   {'Cookie' : cook },
         url     :     host + 'ajax/ajax.php',
         form    :   {
                       ajax    : 'online',
                       device  : this.mobilecheck(useragent) ? 'mobile' : 'desktop',
                       status  : status ,
                       date    : this.datejson()
                      }
           }, function(error, response, body){   console.log(body);  });
    }
};

