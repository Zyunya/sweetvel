$scope.sendmessage = function(){
var socket          = io('http://192.168.0.107:4000');
var count_press     = 0;
var reverse  = function(a,b){return Math.min(a,b)+"-"+Math.max(a,b);}
var opponent = $location.search().sn  / hash;
console.log(socket);
socket.on('connect',function(data){
   //console.log(data);
   /////////////////////CREATE_ROOM///////////////
socket.emit('opponent', opponent);
})
  
////////////////LISTEN_FOR_NEW_MESSAGES_ON_SOCKET///
socket.on('chat_msg_client', function(data) {

$scope.$apply(function() { $scope.chater.push(data); });
$('#chat_cont').animate({ scrollTop: $('#chat_cont').prop('scrollHeight')}, 1000);

});
//////////////////////USER_TYPING_STATUS//////////////
$('#comment_text').on('keydown',function(e){
count_press +=1;
if(count_press <= 2)
{
socket.emit('user_typing');
setTimeout(function(){count_press = 0},3500);
}
})

socket.on('user_typing_emit', function(data) {

if(opponent == data.id)
{

$('#user_type_status').fadeIn(500).text(data.sender + " "+ " is typing..").append("<i class = 'ml_2 fa fa-keyboard-o fa-2x color5 '></i>").fadeOut(500);
}
})
//////////////SEND_MESSAGE_TO_SCOKET_AND_PHP_SERVER///////
$('#sender').off('click').on('click',function(){

var images = [];
//for(var i = 0; i <img_url.length;i++){images.push(img_url[i].style.backgroundImage.replace(/(url\(|\)|")/g, ''));}
$('.message_img_prev').each(function(index){images.push($(this).css('backgroundImage').replace(/(url\(|\)|")/g, ''))});

var msg       = $id('comment_text').value;
var node_data = {message : msg ,image : [images]};

socket.emit('chat_msg',node_data);
//$scope.clickenable = true;
var formdata = new FormData();
var file     = $id('message_file').files[0];
var files    = $id('message_file').files;
formdata.append('ajax'     ,'sendmessage');
formdata.append('message'  , he.encode($id('comment_text').value));
formdata.append('receiver' , $location.search().sn);
formdata.append('date'     , datejson());
for(var i = 0,len = filearr_message.length; i < len; i++){
formdata.append("file"+ i , filearr_message[i]);
}

$http({
method   : 'POST',
url      : 'ajax/ajax.php',
data     : formdata , 
headers  : {'Content-Type': undefined }
     })
     .then(function(response){
        //$scope.messengerer();
        $scope.messageval =  $id('comment_text').value;
        //$scope.$broadcast('chatptp',{ider:null,sound:false});
        if(response.data.status == '0')
        {
         $scope.alert_show(response.data.text);
        }
        $id('comment_text').value = "";
        //Make Empty input file and array with files
        $id('message_file').value = "";
        filearr_message = filearr_message.slice(0,0);

        $id('upload_chat_img_preview_block').classList.add('dp');
        $('.message_img_prev').remove();
        $('#chat_cont').animate({ scrollTop: $('#chat_cont').prop('scrollHeight')}, 1000)
    })

    })
} 


var filearr_message = [];
$scope.message_preview = function(elem)
{
     var block    = $id('upload_chat_img_preview_block');   
     var filer    =   $id('message_file').files;   

if(filearr_message.length < 3 && filer.length < 4 )
{

    var filearr0 = Array.from(filer);       ////////Files in input for One Operation
    filearr_message  = filearr_message.concat(filearr0);//////All files From input for all operations
    block.classList.remove('dp');

for (var i = 0; i < filearr0.length; i++) { 
    var url  = URL.createObjectURL(filearr0[i]);
    var div  = document.createElement('div');
    var itag = document.createElement('i');
    itag.className = "pull-right  fa fa-times-circle-o  fa-2x textshgrt color5 iconhov cursor";
    div.className  = 'message_img_prev';
    
    div.appendChild(itag);
    div.style.backgroundImage    = "url('"+url+"')";
    div.style.width              = '100px';
    div.style.height             = '100px';
    div.style.backgroundSize     = 'cover';
    div.style.backgroundPosition = 'center';
    div.style.display            = 'inline-block';
    div.style.margin             = '0.15%';
   if(elem.files[i].type.match("image/jpg|image/png|image/jpeg")){
     block.appendChild(div);
     
      if(filearr0.length == 1){
         itag.setAttribute('fileid',filearr_message.length - 1);  ////file_message = filearr_message.length + one new filearr0
      }
      if(filearr0.length  > 1){
         itag.setAttribute('fileid',filearr_message.length - filearr0.length + i);  
      }
      
     }
      else
      {
      $scope.alert_show('%action_type%');
      }
     filearr_message.onload = function() {   //img.onload
         URL.revokeObjectURL(filearr_message); //img.src
        } 
    itag.onclick = function(elem){
        var fileid = this.getAttribute('fileid');
        this.parentElement.style.display = 'none';
        delete filearr_message[fileid];
      
      }
      
    } 

 }

 else
 {
  $scope.alert_show('%3files%');
 }

}

$scope.message_preview_old = function(elem){
var block  = $id('upload_chat_img_preview_block');    
var file   = event.target.files;

 if(file.length < 4)
 {

 for(var i = 0 ;i < file.length; i++)
{

 if(file[i].type.match("image/jpg|image/png|image/jpeg"))
    {
var reader = new FileReader();

reader.onload = function(e)
    {
    var dataURL = e.target.result;
  
    $id('upload_chat_img_preview_block').classList.remove('dp');
    var image = new Image();
    image.src = dataURL;
    image.style.maxHeight = '100px';
    block.appendChild(image);
}
reader.readAsDataURL(file[i]);
    }

     else
    {
   $scope.alert_show("%action_filetype%");
       }

    }

  }

    else
    { 
   $scope.alert_show("%3files%");
    }
}


$scope.open_graph = function(elem){

setTimeout(function(){
$http({method:'POST',url:'core/og2.php',data : $.param({oglinkprev : elem.target.value})})
.then(function(response){
console.log(response.data.status);
if(response.data.status === '1')
{

$id('sender').classList.add('og_btn');
$scope.og_data = response.data;
$id('chat_og_preview').classList.remove('dp');
$scope.clickenable = true;


$('body').off('click').on('click','.og_btn',function(evt){

var receiver = $location.search().sn;
var title    = response.data.title;
var og_desc  = response.data.desc;
var url      = response.data.url;
var image    = response.data.image;
var desc     = he.encode($id('comment_text').value);
$http({
method : 'POST',
url    : 'ajax/ajax.php',
data   : $.param({ajax : 'sendmessage_og',receiver : receiver,title : title,desc : desc,og_desc : og_desc,url : url,image : image,date : datejson()}) 
     })
     .then(function(response2){
       
        $scope.messageval =  $id('comment_text').value;
        $scope.$broadcast('chatptp',{ider:null,sound:false});
        if(response2.data.status == '0')
        {
         $scope.alert_show(response2.data.text);
        }
        console.log(response2.data);
        $id('comment_text').value = "";
        $id('message_file').value = "";
        $id('sender').classList.remove('og_btn');
        $scope.clickenable = false;
       
       });
 
     });
     
    };

  });
 });

}

$scope.sendmessage_voice = function()
{
try{

var mediaConstraints = {
    audio: true
};

navigator.getUserMedia(mediaConstraints, onMediaSuccess, onMediaError);

function onMediaSuccess(stream) {
    var mediaRecorder = new MediaStreamRecorder(stream);
    mediaRecorder.mimeType = 'audio/wav'; // check this line for audio/wav
    mediaRecorder.ondataavailable = function (blob) {

    $id('chat_audio_preview').src = URL.createObjectURL(blob);

    document.getElementById('chat_audio_preview_play').addEventListener('click',function() {
    $id('chat_audio_preview').play();
});
  document.getElementById('chat_audio_preview_pause').addEventListener('click',function() {
    $id('chat_audio_preview').pause();
    })

       console.log(blob);
        $('#sendmessage_audio').off('click').on('click',function()  {
    var formdata = new FormData();
    formdata.append('ajax'     , 'sendmessage_voice');
    formdata.append('message'  , he.encode($id('comment_text').value));
    formdata.append('receiver' , $location.search().sn);
    formdata.append('voice'    , blob);
    formdata.append('date'     , datejson());
    $http({method   :'POST',
           url      :'ajax/ajax.php',
           data     : formdata , 
           headers  : {'Content-Type': undefined }
     }).then(function(response){
         $scope.$broadcast('chatptp',{ider:null,sound:false});
         console.log(response.data);
        });

    })
};
    $('body').on('click','.start_record',function()
    {
         
        $(this).removeClass('backsw3 start_record fa-microphone').addClass('back9 stop_record pulse fa-microphone-slash');
       mediaRecorder.start(10000);
    })
      $('body').on('click','.stop_record',function()
     {
         
         $(this).removeClass('back9 stop_record pulse pulse fa-microphone-slash').addClass('backsw3 start_record  fa-microphone');
        mediaRecorder.stop();
     })
}

function onMediaError(e) {
    console.error('media error', e);
}

} 

catch(err){
 console.log('recording is not supported in your browser');
  return false;
}
}
            
$scope.sendmessage_voice();