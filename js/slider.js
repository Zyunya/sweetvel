$(document).ready(function(){

pic = new Image();
  pic2 = new Image();
  pic3 = new Image();
   var img1 =  pic.src="img/slider/25f.jpg";
   var img2 =  pic2.src="img/slider/35f.jpg";
   var img3   =pic3.src="img/slider/85f.jpg";
  $('#main_top').css('height',$('#middle').height()+130+'px');
  $(window).on('resize',function(){$('#main_top').css('height',$('#middle').height()+130+'px');})
    images = [img1,img2,img3];
var i = 0;
$('#main_top').css('backgroundImage',"url('"+images[0]+"')");
setInterval(photos,5000);

function photos() {
$('#main_top').animate({'height': 'show'},500,function() {
 $(this).css('backgroundImage',"url('"+images[i]+"')");
});
i++;
if(i>images.length -1 ) {
i=0;
}
}
})