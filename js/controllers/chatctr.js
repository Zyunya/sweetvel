com.controller('chatctr',function($scope,$http,$location,$filter){

$http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
var idparam = $location.search().sn;
$scope.arrows = true;


$scope.chat = function(ider){
    $scope.arrows     = false;
    $scope.preloader2 = true;
    $scope.chat_show  = false;
var chatid = $location.search().sn !== undefined ? $location.search().sn / hash : ider ;

$http({method:'POST',url:'ajax/ajax.php',data : $.param({ajax : 'chat',id : chatid,limit : 10})})
.then(function(response){
            $scope.arrows     = true;
            $scope.preloader2 = false;
            $scope.chat_show  = true;
            //$scope.messengerer();
            $scope.chater         = response.data.record;
            $scope.chater_img     = response.data.record2;
            console.log(response.data);
    })
} 
$scope.chat();

if($location.search().sn == undefined){alert('changed'); }

$scope.$watch(function(){ return $location.path(); }, function(value){ console.log(value); })
var count_press = 0;
//////////////////CONNECT_SOCKET_AND_ROOM/////////////////
var socket   = io('http://192.168.0.104:4000');

socket.on('connect',function(data){

if($location.search().sn !== undefined)
{
socket.emit('opponent', $location.search().sn  / hash);
}
else if($location.search().sn == undefined)//////????????????????????????????????????????????
{
socket.emit('forceDisconnect');
}
})
/////////////////////LISTEN_FOR_READED_MESSAGES///
socket.on('read_chat', function(data) {

$scope.$apply(function(){ for(let read of $scope.chater){  read['status'] = '1'; } })

})
//////////////////LISTEN FOR NEW MESSAGES/////////////
socket.on('chat_msg_client', function(data) {
var id = data.id;

$scope.$apply(function(){  $scope.chater.push(data); });
$('#chat_cont').animate({ scrollTop: $('#chat_cont').prop('scrollHeight')}, 1000);
console.log(data);

});

////////////////////USER_IS_TYPING///////////////
socket.on('user_typing_emit', function(data) {
if($location.search().sn  / hash == data.id)
{
$('#user_type_status').fadeIn(600).text(data.sender + " "+ " is typing..").append("<i class = 'ml_2 fa fa-keyboard-o fa-2x color5 '></i>").fadeOut(600);
}
})
/////////////////////EMIT_TYPING//////////
$('#comment_text').on('keydown',function(e){
count_press +=1;
if(count_press <= 2)
{
socket.emit('user_typing');
setTimeout(function(){count_press = 0},2500);
}
})

var imagesrel = [];
var images    = [];
/////////////////PRELOAD_FILES_AND_SEND_MESSAGE//////

$scope.sendmessage_preload = function(){


var opponent  = $location.search().sn  / hash;
var og_params = [];

$('#message_file').on('change',function(){

var formdata = new FormData();
var file     = $id('message_file').files[0];
var files    = $id('message_file').files;

formdata.append('ajax'     ,'sendmessage_preload');

for(var i = 0,len = files.length; i < len; i++){ formdata.append("file"+ i , files[i]); }

$http({
method   : 'POST',
url      : 'ajax/ajax.php',
data     : formdata , 
headers  : {'Content-Type': undefined }
     })
     .then(function(response){
         $id('upload_chat_img_preview_block').classList.remove('dp');
         $scope.message_img_preview = response.data.files;
         console.log(response.data);
       for(let val of response.data.files) {  images.push(val.file);imagesrel.push(val.filerel) }
      })
    
})


$('body').off('click').on('click','.common_message',function(){

$scope.message_status = 'message_load';

var node_data = {message : $id('comment_text').value ,image : new Array(images),audio : []};

socket.emit('chat_msg',node_data);

data = {ajax : 'sendmessage_preload_finish',files : imagesrel ,type : 'common',message : he.encode($id('comment_text').value),receiver : $location.search().sn,date  : datejson()};
$http({
method   : 'POST',
url      : 'ajax/ajax.php',
data     : $.param(data)
     })
     .then(function(response){
         console.log(response.data);
        $scope.messageval =  $id('comment_text').value;
        if(response.data.status === '0')
        {
         $scope.alert_show(response.data.text);
         $scope.message_status = 'message_error';
        }
        else if(response.data.status === '1')
        {
        $scope.message_status = 'message_sended';
        $id('comment_text').value = "";
        //Make Empty input file and array with files
        $id('message_file').value = "";
        images     = [];
        imagesrel  = [];
       
        $id('upload_chat_img_preview_block').classList.add('dp');
        $('.message_img_prev').remove();
        $('#chat_cont').animate({ scrollTop: $('#chat_cont').prop('scrollHeight')}, 1000);
                }
             })
         })

//////////////////////OPEN_GRAPH_MESSAGE////////////////////////////////////
$('#comment_text').on('paste',function(){

setTimeout(function(){
$http({method:'POST',url:'core/og2.php',data : $.param({oglinkprev : $('#comment_text').val()})})
.then(function(response){
console.log(response.data);
if(response.data.status === '1')
{
 $id('sender').classList.add('og_message');
 $id('sender').classList.remove('common_message');
$scope.og_data = response.data;
og_params.push(response.data);
//console.log(og_params);
$id('chat_og_preview').classList.remove('dp');


    $(document).off('click').on('click','.og_message',function(){

      $scope.message_status = 'message_load';

      $id('sender').classList.remove('og_message');
      $id('sender').classList.add('common_message');

    var node_data = {message  : $id('comment_text').value,og_params : og_params,audio : [] };
    socket.emit('chat_msg',node_data);
    og_params = [];
    $id('chat_og_preview').classList.add('dp');
    var receiver = $location.search().sn;
    var title    = response.data.title;
    var og_desc  = response.data.desc;
    var url      = response.data.url;
    var image    = response.data.image;
    var desc     = he.encode($id('comment_text').value);
    var data     = {ajax : 'sendmessage_og',receiver : receiver,title : title,desc : desc,og_desc : og_desc,url : url,image : image,date : datejson()}
    $http({
         method : 'POST',
         url    : 'ajax/ajax.php',
         data   : $.param(data) 
        })
     .then(function(response2){

        if(response2.data.status === '0')
        {
         $scope.alert_show(response2.data.text);
         $scope.message_status = 'message_error';
        }
        else if (response2.data.status === '1')
        {
        $scope.message_status = 'message_sended';
        $id('comment_text').value = "";
        $id('message_file').value = "";
        }
          });
          
        })
      
      }
    })
   })
  })
}

var audios    = [];
var audiosrel = [];
$scope.send_voice_preload = function()
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
        $('#sendmessage_audio').fadeIn();
        $('#deletemessage_audio').fadeIn();

    $id('chat_audio_preview').src = URL.createObjectURL(blob);

    document.getElementById('chat_audio_preview_play').addEventListener('click',function() {
    $id('chat_audio_preview').play();
});
  document.getElementById('chat_audio_preview_pause').addEventListener('click',function() {
    $id('chat_audio_preview').pause();
    })

console.log(blob);
var formdata = new FormData();

formdata.append('ajax'     ,'sendmessage_preload');
formdata.append('file'     ,blob);

$http({
method   : 'POST',
url      : 'ajax/ajax.php',
data     : formdata , 
headers  : {'Content-Type': undefined }
     })
     .then(function(response){
         console.log(response.data);
         $scope.preloaded_audio = response.data.files[0].filerel;
         for(let val of response.data.files) {  audios.push(val.file);audiosrel.push(val.filerel) }

         $('#sendmessage_audio').off('click').on('click',function(){
             $scope.message_status = 'message_load';

             var node_data = {audio : [new Array(response.data.files[0].file)],og_params : [],message : "",image : [] };
            socket.emit('chat_msg',node_data);
             
data = {ajax : 'sendmessage_preload_finish',files : audiosrel,type : 'audio',message : he.encode($id('comment_text').value),receiver : $location.search().sn,date  : datejson()};
$http({
method   : 'POST',
url      : 'ajax/ajax.php',
data     : $.param(data)
     })
     .then(function(response){
         console.log(response.data);
        $scope.messageval =  $id('comment_text').value;
        if(response.data.status === '0')
        {
         $scope.alert_show(response.data.text);
          $scope.message_status = 'message_error';
        }
        else if(response.data.status === '1')
        {
        $scope.message_status = 'message_sended';
        $id('comment_text').value = "";
        //Make Empty input file and array with files
        $id('message_file').value = "";
        $id('chat_audio_preview').src = "";
       
        $id('upload_chat_img_preview_block').classList.add('dp');
        $('.message_img_prev').remove();
        $('#chat_cont').animate({ scrollTop: $('#chat_cont').prop('scrollHeight')}, 1000);

             }

             })

         })
      })
          audios    = [];
         audiosrel = [];
}
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
catch(err)
{
    console.log(err);
}
}
$scope.send_voice_preload();
$scope.audio_recorder_option = function()
{
    if($scope.hasGetUserMedia())
    {
        $id('upload_chat_audio_preview').classList.remove('dp');
    }
    else
    {
        $scope.alert_show("%action_browser_record_no_support%");
    }
}   

$scope.delete_preloaded_file = function(item,img){
             
              $http({
                   method   : 'POST',
                   url      : 'ajax/ajax.php',
                   data     : $.param({ajax : 'delete_preloaded_image',file : img}) 
                    })
                 .then(function(response){
                    
                     if(response.data.status   === "1"){
                        if($scope.message_img_preview)
                        {   
                            $scope.message_img_preview.splice(item,1);
                            imagesrel.splice(item,1);
                            images.splice(item,1);
                            //console.log(imagesrel);
                            //console.log(images);
                        }
                     }
                        else if(response.data.status   === "0")
                        {
                            $scope.message_img_preview.splice(item,1);
                            imagesrel.splice(item,1);
                            images.splice(item,1);
                        }
                            audiosrel.splice(item,1);
                            console.log(audiosrel);
                     if(response.data.lastfile === "1"){
                     $id('upload_chat_img_preview_block').classList.add('dp');
                     }
                })  
             }




$scope.chat_play = function(elem)
{
  var audio = elem.target.nextElementSibling;
  var allaudio = document.getElementsByTagName('audio');
 
for(var i=0;i<allaudio.length;i++) {
    allaudio[i].pause();
    allaudio[i].previousElementSibling.classList.remove('fa-pause-circle');
   // allaudio[i].previousElementSibling.classList.add('fa-play-circle');
}
   if(audio.currentTime < 1)
   {
    audio.play();
    elem.target.classList.add('fa-pause-circle');
    elem.target.classList.remove('fa-play-circle');
  }
   else
   {
   audio.pause(); 
   elem.target.classList.add('fa-play-circle');
   elem.target.classList.remove('fa-pause-circle'); 
   audio.currentTime = 0;
   }
   audio.addEventListener("ended", function(){
   elem.target.classList.remove('fa-pause-circle');
   elem.target.classList.add('fa-play-circle');
   audio.currentTime = 0;
})
}



$scope.hasGetUserMedia = function () {
    try{
  
  return !!(navigator.mediaDevices.getUserMedia || navigator.mediaDevices.getUserMedia ||
            navigator.mediaDevices.getUserMedia || navigator.mediaDevices.getUserMedia);
    }
    catch(err)
    {
        console.log(err);
    }
}



})