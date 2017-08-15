<?php
header('Access-Control-Allow-Origin: *');  
include_once '../template.php';
include_once '../core/main.php';
include_once '../core/live.php';

$sb   = new shablonizer;
$live = new live;
//$main = new main;

if($_SERVER['REQUEST_METHOD'] == 'POST' OR $_SERVER['REQUEST_METHOD'] == 'GET' ){

switch($_GET['ajax']){
 case 'live_friends'                   : $live->live_friends('friends');                                        break;
 case 'live_friends_new_request'       : $live->live_friends_new_request('friends','account');                  break;
 case 'live_friends_request_confirmed' : $live->live_friends_request_confirmed('friends','account');            break;
 case 'live_like'                      : $live->live_like('likes','label');                                     break;
 case 'live_post_like'                 : $live->live_post_like('likes');                                        break;
 case 'wholiked'                       : $live->wholiked('likes','label');                                      break;
 case 'wholiked_post'                  : $live->wholiked_post('likes','posts');                                 break;
 case 'live_chat'                      : $live->live_chat('messenger');                                         break;
 case 'live_followers'                 : $live->live_followers('follow','label');                               break;
 case 'who_follows'                    : $live->who_follows('follow','label');                                  break;
 case 'live_common'                    : $live->live_common('activity');                                        break;
 case 'live_common_refresh'            : $live->live_common_refresh('activity');                                break;
 case 'live_common_checkupdates'       : $live->live_common_checkupdates('activity');                           break;

 

 
 

 default: return false;break;
  }
}
else return false;