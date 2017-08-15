var events =   mobilecheck() == true ? "touchstart" : "mouseover";
var event2 =   mobilecheck() == true ? "touchstart" : "click";
var play = true;

function langlink(elem,link){
var lang = get('lang');
if(lang !== null) {elem.setAttribute('href',link+'?lang='+lang);}
else {elem.setAttribute('href',link);}
}
    function alertino(text){
         $('.mmalertino').fadeIn();
         $('#alertino_text').text(text);
      }
jQuery(function(){

  var emimg = "";
  jQuery(document).on('click','.themepreview',function(){
  //jQuery(this).find('.imgprev').attr('src','img/media/like6.png');
jQuery(this).find('.icon_theme').addClass('fa-star').fadeIn('800');
jQuery('.themepreview').not(this).find('.icon_theme').removeClass('fa-star')
  //jQuery('.themepreview').not(this).find('.imgprev').attr('src',emimg);
})
//////////////////////////
  jQuery(document).on('click','#eventlist',function(){
  jQuery('.eventsblock').fadeToggle(250);
})
/////////////////////////////////
  jQuery(document).on('click','#wishlist',function(){
  jQuery('.wishblock').fadeToggle(250);
})
///////////////////////////////////////////////
  jQuery('.chooselang').on('click',function(){
  jQuery(".langblock").fadeToggle(400);
  //jQuery(".langblock").sticky({ topSpacing: 0 });
  })
//////////////////////////////////////
  jQuery(".rotate").on('mouseover',function(){

    jQuery(this).rotate(90);
  })
  jQuery(".rotate").on('mouseout',function(){

  jQuery(this).rotate(180);
  })
/////////////////////////////////
    jQuery(document).on('click','.minilabel_event',function(){
    var eventval = jQuery(this).find('.hidden_event').val();
    jQuery('#titler').html(eventval);
    jQuery('#event').attr('value',eventval);
    jQuery('.eventsblock').slideUp(250);
    jQuery('#whatevent').text(eventval);
  	jQuery('.tooltip').fadeOut(350);
  })
   jQuery(document).on('click','.minilabel_wish',function(){
    var wishval = jQuery(this).find('.hidden_wish').val();
    jQuery('#desc_preview').html(wishval);
    jQuery('.wishblock').slideUp(250);
    jQuery('#wishes').val(wishval);
	  jQuery('.tooltip').fadeOut(350);
  })
  //////////////////////////////////
 jQuery(document).on('click','.themepreview',function(){
    var themeval = jQuery(this).attr('theme');
    jQuery('#theme').attr('value',themeval);
  })
 jQuery(document).on('click','.choose_label',function(){
     $(this).css('background','#ff5959');
     $('.choose_label').not(this).css('background','yellowgreen')
     $
    var label = "account#/privatelabel/"+jQuery(this).attr('data-label-id')
    jQuery('#label_id').attr('value',label);
  })




jQuery(document).on('click','.audiotable',function(){
var track = jQuery(this).find('.audiotrack').attr('src');
jQuery('#audiotrack').val(track);
jQuery(this).find('.choosedtrack').css({'background':'#ff5959','border-radius':'5px','color':'white'});
jQuery('.audiotable').not(this).find('.choosedtrack').css({'background':'white','color':'#656565'});

  var license       = jQuery(this).find('.audiotrack').attr('data-license').split('|')[0];
  var licenselink   = jQuery(this).find('.audiotrack').attr('data-license').split('|')[1];
  var author        = jQuery(this).find('.audiotrack').attr('data-author').split('|')[0];
  var authorlink    = jQuery(this).find('.audiotrack').attr('data-author').split('|')[1];
$('#audio_license').text("License - "+license);
$('#audio_author').text("Author - "+author);
$('.audio_license_link').attr('href',licenselink);
$('.audio_license_link').attr('target',licenselink);
$('.audio_author_link').attr('href',authorlink);
$('.audio_author_link').attr('target',authorlink);

})

function ogpreview(var1){

    $.ajax({
    type : 'POST',
    url  : 'core/og.php',
    //beforesend: jQuery('#content').empty().append("<img style = 'height:50%' src ='img/media/loader.gif'>"),
    data : 'ogpreview='+ encodeURIComponent(var1),
     success : function(data){
        $('#ogpreview').empty();
        $('#ogpreview').append(data);
        $('#ogpreview').css("background-image","none");
     
      },
     error   : function(data){
        console.log(data);
      }
})
}

jQuery(document).on('click','#sendtomail',function(){
  var email = jQuery('input[name=res_email]').val();
  var link  = jQuery('#gcresultlink').text();
  jQuery.ajax({
    type : 'POST',
    url  : 'ajax/ajax.php',
    data : 'ajax=sendtomail&&email='+email+"&&message="+link,
    success : function(data){
    jQuery('input[name=res_email]').val('');

    },
    error : function(){
      return false;
    }

  })
})
jQuery(document).on('click','.imgsetname2',function(){
  jQuery(this).css({'background': 'rgba(118, 188, 28, 0.8)'});
  jQuery('.imgsetname2').not(this).css({'background': '#ff5959'});
})

////////////////////////////////////////preview///////////////////////////////////////////
jQuery(document).on('click','.themepreview',function(){
  var colors   =   jQuery(this).attr('themestyle').split(":");
  var back     =   colors[0];
  var color    =   colors[1];
  var altcolor =   colors[2];
  var font     =   colors[3];
  var musicimg =   colors[4];

  jQuery('#preview_preview').css({'background':back,'color':color,'font-family': font,});

  jQuery('.icon_sw').css('color',altcolor);
  jQuery('#musicimg').attr('src',musicimg);
});
//////////////////////////////////
jQuery(document).ready(function(){
  setTimeout(function(){
for(var i = 0 ; i < 7; i++){jQuery("#img"+i).attr('src',jQuery('.imgset:eq('+i+')').attr('src'));}
  },1000)
})

jQuery(document).on('click','.imgsetlink',function(){
  setTimeout(function(){
  for(var i = 0 ; i < 7; i++){jQuery("#img"+i).attr('src',jQuery('.imgset:eq('+i+')').attr('src'));}
},500)
})
//jQuery('#sender').on('input',function(){jQuery('#sender_preview').html(langmixer('Отправитель','Sender')+" "+jQuery('#sender').val());})
jQuery('#titler').html(get('event'));
})



function audio_play(e){
  var audio = document.getElementsByClassName('audiotrack');
  var audio_pointer = document.getElementsByClassName('audio_play');
  var pause = document.getElementsByClassName('audiotrackimg2');
  var play  = document.getElementsByClassName('audiotrackimg');
if(mobilecheck2() == true && event.type !=='touchstart'){return false};
  for(var i = 0 ;i < audio.length ; i++){
     audio[i].pause();
     audio[i].currentTime    = 0;
     pause[i].style.display  = 'none';
      play[i].style.display  = 'block';
  }
  e.nextElementSibling.play();
  e.previousElementSibling.style.display = 'block';
  e.style.display = 'none';
  
}
function audio_pause(e){
var audio = document.getElementsByClassName('audiotrack');
if(mobilecheck2() == true && event.type !=='touchstart'){return false};
   for(var i = 0 ;i < audio.length ; i++){
if (audio[i].duration > 0 && !audio[i].paused) {
    audio[i].pause();
   } 
e.style.display = 'none';
e.nextElementSibling.style.display = 'block';


 }
}
////////////////////////////////////MOBILE////////////////////