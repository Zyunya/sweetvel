
var adminers =  {
   constructor(){

   },
////////////////////////////////////////////////////////////////
autoriz:()=>{
    var login = $('#a_l').val();
    var pass  = $('#a_p').val();
$.ajax({
    
    type : 'POST',
    url  : '/ajax/ajax.php',
    data : {ajax : 'autoriz', log : login, pass : pass},

    success : (data)=>{
        if(data == 'true') document.location.href = 'personalpage.php'
        else{alertino(data);}
    },
    error   : (err) =>{
        console.log(err);
    }
 })

},
//////////////////////////////////////////////////////////////
setceleb:(e) => {
    let licensedata = $('#license').val(       ).split('|');
    let pointdate   = $('input[name=celebdate]'     ).val();
    let title       = $('input[name=celebtitle]'    ).val();
    let desc        = $('textarea[name=celebdesc]'  ).val();
    let author      = $('input[name=author]'        ).val();
    let authorlink  = $('input[name=authorlink]'    ).val();
    let alttitle    = $('input[name=alttitle]'      ).val();
    let language    = $('#language').val();
    let license     = licensedata[0];
    let licenselink = licensedata[1];
    if(pointdate !== '' && title !== '' && desc !== ''){
    var formdata = new FormData();
    formdata.append('celebimg', $('input[name=celebimg]')[0].files[0]);///celebimg указывает на celebimg в php
    formdata.append('title', title);
    formdata.append('ajax', 'setceleb');
    formdata.append('desc', desc); 
    formdata.append('pointdate', pointdate); 
    formdata.append('alttitle', alttitle); 
    formdata.append('author', author); 
    formdata.append('authorlink',authorlink); 
    formdata.append('language', language); 
    formdata.append('license', license);
    formdata.append('licenselink', licenselink); 
   
    $.ajax({
       type : "POST",
       url  : "/ajax/ajax.php",
       data : formdata,
       processData: false,
       contentType: false,
       success:(data)=>{
         alert(data);
       },
       error:(err)  =>{
           console.log(err);
       }

    })
}
else{ return false;}

},
//////////////////////////////////////////////////////////////////////////////
getpage:function(pos){
  switch(pos)  {
case 'addevent': $('.admin_block_main_2').hide();$('.admin_block_main_1').show();break;
case 'visitor' : $('.admin_block_main_1').hide();$('.admin_block_main_2').show();break;
  }
}

};
var interfacer =  {


authorization:(dom)    =>{ 
    $(dom).on('click',  () => adminers.autoriz());
   },
pagenavigation:(dom,pos)=>{
   $(document).on('click',dom,() => adminers.getpage(pos));
},
displaytoggle:(dom,elem)=>{
    $(dom).on('click',()=> $(elem).fadeToggle());
},
setceleb:(dom1) =>{ 
    $(document).on('submit',dom1, () => adminers.setceleb());
},
}

$(document).ready(()=>{

interfacer.authorization('#admin_comein');
interfacer.displaytoggle('#open_adminmenu','#adminmenu');
interfacer.setceleb('#addceleb');
$('input[name=celebimg]'  ).on('input',()=>{
$('.imglink_preview').attr('src',checkimg($('input[name=celebimg]').val()));

})

})
