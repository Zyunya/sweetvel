if (!Array.from) {
  Array.from = (function() {
    var toStr = Object.prototype.toString;
    var isCallable = function(fn) {
      return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
    };
    var toInteger = function (value) {
      var number = Number(value);
      if (isNaN(number)) { return 0; }
      if (number === 0 || !isFinite(number)) { return number; }
      return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
    };
    var maxSafeInteger = Math.pow(2, 53) - 1;
    var toLength = function (value) {
      var len = toInteger(value);
      return Math.min(Math.max(len, 0), maxSafeInteger);
    };

    // Свойство length метода from равно 1.
    return function from(arrayLike/*, mapFn, thisArg */) {
      // 1. Положим C равным значению this.
      var C = this;

      // 2. Положим items равным ToObject(arrayLike).
      var items = Object(arrayLike);

      // 3. ReturnIfAbrupt(items).
      if (arrayLike == null) {
        throw new TypeError('Array.from requires an array-like object - not null or undefined');
      }

      // 4. Если mapfn равен undefined, положим mapping равным false.
      var mapFn = arguments[1];
      if (typeof mapFn !== 'undefined') {
        mapFn = arguments.length > 1 ? arguments[1] : void undefined;
        // 5. иначе
        // 5. a. Если вызов IsCallable(mapfn) равен false, выкидываем исключение TypeError.
        if (!isCallable(mapFn)) {
          throw new TypeError('Array.from: when provided, the second argument must be a function');
        }

        // 5. b. Если thisArg присутствует, положим T равным thisArg; иначе положим T равным undefined.
        if (arguments.length > 2) {
          T = arguments[2];
        }
      }

      // 10. Положим lenValue равным Get(items, "length").
      // 11. Положим len равным ToLength(lenValue).
      var len = toLength(items.length);

      // 13. Если IsConstructor(C) равен true, то
      // 13. a. Положим A равным результату вызова внутреннего метода [[Construct]]
      //     объекта C со списком аргументов, содержащим единственный элемент len.
      // 14. a. Иначе, положим A равным ArrayCreate(len).
      var A = isCallable(C) ? Object(new C(len)) : new Array(len);

      // 16. Положим k равным 0.
      var k = 0;
      // 17. Пока k < len, будем повторять... (шаги с a по h)
      var kValue;
      while (k < len) {
        kValue = items[k];
        if (mapFn) {
          A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
        } else {
          A[k] = kValue;
        }
        k += 1;
      }
      // 18. Положим putStatus равным Put(A, "length", len, true).
      A.length = len;
      // 20. Вернём A.
      return A;
    };
  }());
}

function langlink(elem,link){
var lang = get('lang');
if(lang !== null) {elem.setAttribute('href',link+'?lang='+lang);}
else {elem.setAttribute('href',link);}
}

    function langimg(){
    jQuery('.langpointer_img').attr('src','img/media/'+ currentlang() +'.png');
    jQuery('.lang_pointer').text(currentlang().toUpperCase());
  }

try{
$(document).on('scroll',function(){
if ($('body').scrollTop() > 100){
  $('[id = header]').css({
    transition:'0.7s',
    background:'rgba(118, 188, 28, 0.8)',
    color:'white',
    boxShadow:'0 1px 2px #ff656565'});
    jQuery('#logo_div').css({color:'white'});
    jQuery('.langlist').css({background:'linear-gradient(to bottom, rgba(118, 188, 28, 0.8), rgba(118, 188, 28, 0.5))'});
}
else{$('[id = header]').css({
  background:'none',
  color:'white',
  boxShadow:'0 0 0 #ff656565'});
  jQuery('#logo_div').css({color:'rgba(118, 188, 28, 0.8)'});
  jQuery('.langlist').css({background:'linear-gradient(to bottom, rgba(0, 0, 0, 0.0), rgba(118, 188, 28, 0.5))'});
}
});
}
catch(err){
  console.log(err);
}


 jQuery(function(){




jQuery('[id=chooselangindex]').bind('click',function(){

jQuery(".langlist").fadeToggle(250);

})



jQuery('.open_menu').bind('click',function(){

  if(jQuery("#header_mobile").attr('data-bgcolor') == 'nocolor'){
    jQuery("#header_mobile").attr('data-bgcolor','color');
    if(ioscheck() == true){jQuery(".nav_menur").animate({'left':'0%'},500);}
    else{jQuery(".nav_menur").animate({'left':'0%'},500);}
    //jQuery("body").css('overflow','hidden');

  }
  else {
    jQuery("#header_mobile").attr('data-bgcolor','nocolor');
    if(ioscheck() == true){jQuery(".nav_menur").animate({'left':'-100%'},500);}
    else{jQuery(".nav_menur").animate({'left':'-100%'},500);}
    //jQuery("body").css('overflow','scroll');

  }
})
try{
var swiper =  new Hammer($('body')[0]);
var swiper2 = new Hammer($(".nav_menur")[0]);
swiper.on('swiperight', function(){
if(jQuery("#header_mobile").attr('data-bgcolor') == 'nocolor'){
    jQuery("#header_mobile").attr('data-bgcolor','color');
jQuery(".nav_menur").animate({'left':'0%'},400);
   document.getElementsByClassName('open_menu')[0].classList.toggle('change');

  }
})
}
catch(err){console.log(err);}
swiper2.on('swipeleft', function(){
 jQuery("#header_mobile").attr('data-bgcolor','nocolor');
   jQuery(".nav_menur").animate({'left':'-100%'},400);
document.getElementsByClassName('open_menu')[0].classList.toggle('change');
})
langimg();
 })
