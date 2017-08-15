$(function(){

function str_replace ( search, replace, subject ) { // Replace all occurrences of the search string with the replacement string
    if(!(replace instanceof Array)){
        replace=new Array(replace);
        if(search instanceof Array){//If search is an array and replace is a string, then this replacement string is used for every value of search
            while(search.length>replace.length){
                replace[replace.length]=replace[0];
            }
        }
    }
    if(!(search instanceof Array))search=new Array(search);
    while(search.length>replace.length){//If replace has fewer values than search , then an empty string is used for the rest of replacement values
        replace[replace.length]='';
    }
    if(subject instanceof Array){//If subject is an array, then the search and replace is performed with every entry of subject , and the return value is an array as well.
        for(k in subject){
            subject[k]=str_replace(search,replace,subject[k]);
        }
        return subject;
    }
    for(var k=0; k<search.length; k++){
        var i = subject.indexOf(search[k]);
        while(i>-1){
            subject = subject.replace(search[k], replace[k]);
            i = subject.indexOf(search[k],i);
        }
    }
    return subject;
}

function get(parameterName) {
    var result = null,
        tmp = [];
    var items = location.search.substr(1).split("&");
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    }
    return result;
}

function xml(lang,name)
{
  var xml;
  $.ajax({
      url: 'lang/'+lang+'.xml',
      dataType: 'xml',
      async: false,
      success: function(data){
        //xml =  $('lang',data).find('string[name="test"] > value').text();
          xml =  $('lang',data).find('string[name='+name+'] > value').text();
      },
      error: function(data){
          console.log('Error loading XML data');
      }
  });
  return xml;
}
function xml2(lang,name){
  $.get('lang/ru.xml',function(result) { return $('lang',result).find('string[name='+name+'] > value').text(); });
}

function lang(word){
  var lang = get('lang') !== null ? get('lang') : navigator.language.substring(0,2);
  return xml(lang,word);
}

$("body").contents().each(function () {
    if (this.nodeType === 1) $(this).html(str_replace (
    Array(

      '%aboutgf%',       '%gfdesc1%',        '%namecom%'             //1

    ),
    Array(

      lang('aboutgc'),   lang('gcdesc'),     lang('namecom')         //1

    ), $(this).html() ))
})

})
