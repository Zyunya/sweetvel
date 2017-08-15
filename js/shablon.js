$(document).ready(function(){
setTimeout(function(){
$('.mattab').on('click',function(){
     $('.mattab').not(this).removeClass('mattab_active');
   $(this).addClass('mattab_active').fadeIn(2000);
  
})

},100)
})