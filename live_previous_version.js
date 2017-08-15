
////////////////////////////LIVE_FOLLOWERS//////////////////////////

$rootScope.who_follows_label = function(){
$http({method:'POST',url:'ajax/ajaxlive.php?ajax=who_follows'})
.then(function(response){
       $('#live_popup3').fadeIn(1000).delay(4000).fadeOut(500);
     
        $rootScope.who_follows  = response.data.record;
        ringer.play();
    })
}


$rootScope.live_followers = function(){
var last_data = "";
    if(typeof(EventSource) !== "undefined"){
var source = new EventSource("ajax/ajaxlive.php?ajax=live_followers");
source.onmessage = function(event) {
    if(event.data !== last_data && last_data && event.data > last_data){
    $rootScope.who_follows_label();
    console.log(event.data);
}
      last_data = event.data;
     }} 
    }
    $rootScope.live_followers();

        ///////////////////////////////LIVE_POST_LIKE////////////////////
$rootScope.live_post_like = function(){
var last_data = "";
    if(typeof(EventSource) !== "undefined"){
var source = new EventSource("ajax/ajaxlive.php?ajax=live_post_like");
source.onmessage = function(event) {
    if(event.data !== last_data && last_data && event.data > last_data){
   
    $rootScope.wholiked_post();
    //console.log(event.data);
}
      last_data = event.data;
     }} 
    }
$rootScope.live_post_like();

$rootScope.live_like = function(){
var last_data = "";
    if(typeof(EventSource) !== "undefined"){
var source = new EventSource("ajax/ajaxlive.php?ajax=live_like");
source.onmessage = function(event) {
    if(event.data !== last_data && last_data && event.data > last_data){
    ringer.play();$rootScope.wholiked();
}
      last_data = event.data;
     }} 
    }

    ///////LIVE_FRIENDS
    $rootScope.live_friends = function(){
    var last_data    = "";
    var last_confirm = "";
    if(typeof(EventSource) !== "undefined"){
    var source = new EventSource("ajax/ajaxlive.php?ajax=live_friends");
    source.onmessage = function(event) {
    var datar           = JSON.parse(event.data);
    var request         = datar.request;
    var confirmation    = datar.confirmation;
    //console.log("requests - " + request + " confirmation - " + confirmation);
    if(request !== last_data && last_data && request > last_data  ){
         console.log("last data = "+ last_data + " request = "+request);
         $rootScope.live_friends_new_request();
         $rootScope.get_friends_followers();
        } 
    if(confirmation !== last_confirm && last_confirm && confirmation > last_confirm){
        $rootScope.live_friends_request_confirmed();
         $rootScope.get_friends_followers();
        } 
      last_data    = request;
      last_confirm = confirmation;
     }} 
    }

////////////////////////////////////LIVE_FRIENDS /////////////////////

///////UPDATE FRIENDS BLOCK////////
$rootScope.get_friends_followers = function(){
    $http({method : 'POST',url : 'ajax/ajax.php' ,data : $.param({ajax : 'get_friends_followers'})
   }).then(function(response) {
   
    $rootScope.$$childHead.followers       = response.data.record;
    $rootScope.$$childHead.friends         = response.data.record2;
    $rootScope.$$childHead.mefollow        = response.data.record3;
    $rootScope.$$childHead.followers_count = response.data.record.length;
    $rootScope.$$childHead.friends_count   = response.data.record2.length;
    $rootScope.$$childHead.mefollow_count  = response.data.record3.length;
   
   });
}
        ////////CHECK FOR NEW REQUEST////
    $rootScope.live_friends_new_request = function(){
    $http({method:'POST',url:'ajax/ajaxlive.php?ajax=live_friends_new_request'})
   .then(function(response){
     $rootScope.activity_param = response.data;
     $('#live_popup3').fadeIn(1000);
    })
} 
    //CHECK IF SOMEONE CONFIRMED YOUR REQUEST//
    $rootScope.live_friends_request_confirmed = function(){
    $http({method:'POST',url:'ajax/ajaxlive.php?ajax=live_friends_request_confirmed'})
   .then(function(response){
     $rootScope.activity_param = response.data;
     $('#live_popup3').fadeIn(1000);
    })
} 

    $rootScope.live_friends();
    