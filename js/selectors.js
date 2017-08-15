$cls = function(cl){
  return document.getElementsByClassName(cl)[0];
}
var $id = {};

$id = function (el){
  return document.getElementById(el);
}
$name = function(name){
  return document.getElementsByName(name)[0];
}
$id.setatr = function (el,atr,value){
  return document.getElementById(el).setAttribute(atr,value);
}
$id.getatr = function (el,atr){
  return document.getElementById(el).getAttribute(atr);
}

var $class = {};

$class.get = function(clas,nm){
var num = nm;
return document.querySelectorAll(clas)[num];

  }
$class.setatr = function(elc,atrtibute,value){
  var gcnn= document.getElementsByClassName(elc);
  attr = [];
  for(var i = 0 ; i < gcnn.length;i++){
    attr[i] = gcnn[i].setAttribute(atrtibute,value);
  }
return attr;
  }
  $class.getatr = function(elc,atrtibute){
    var gcnn= document.getElementsByClassName(elc);
    attr = [];
    for(var i = 0 ; i < gcnn.length;i++){
      attr[i] = gcnn[i].getAttribute(atrtibute);
    }
  return attr;
    }
$class.css =  function(elc,option){
    var gcnn= document.getElementsByClassName(elc);
    attr = [];
    for(var i = 0 ; i < gcnn.length;i++){
      attr[i] = gcnn[i].setAttribute("style", option);
    }
  return attr;
    }
