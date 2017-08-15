<?php
//header('Access-Control-Allow-Origin:*');   

include_once '../template.php';
include_once '../core/main.php';
include_once '../core/live.php';
include_once '../core/timeline.php';
include_once '../admin/core/admincore.php';

$sb   = new shablonizer;
$adm  = new admin;
$live = new live;
$tl   = new timeline_class;

//$main = new main;

if($_SERVER['REQUEST_METHOD'] == 'POST' OR $_SERVER['REQUEST_METHOD'] == 'GET' ){

switch($_POST['ajax']){
 case 'fb_vk_signin'         : $sb->fb_vk_signin('account');                                  break;
 case 'sw_login'             : $sb->sw_login('account');                                      break;
 case 'sign_up'              : $sb->sign_up('account');                                       break;
 case 'token'                : $sb->token('account');                                         break;
 case 'add_to_friends'       : $sb->add_to_friends('friends');                                break;
 case 'check_friend_status'  : $sb->check_friend_status('friends');                           break;
 case 'create_new_label'     : $sb->create_new_label('label');                                break;
 case 'get_friends_followers': $sb->get_friends_followers('friends','account');               break;
 case 'confirm_friend'       : $sb->confirm_friend('friends');                                break;
 case 'delete_friend'        : $sb->delete_friend('friends');                                 break;
 case 'edit_account_profile' : $sb->edit_account_profile('account');                          break;
 case 'edit_account_profile_main' : $sb->edit_account_profile_main('account');                break;
 case 'set_status_self'      : $sb->set_status_self('account');                               break;
 case 'param'                : $sb->param("account");                                         break;
 case 'sketch_info'          : $sb->sketch_info("account");                                   break;
 case 'logout'               : $sb->logout();                                                 break;
 case 'checksession'         : $sb->checksession();                                           break;
 case 'returnvalue'          : $sb->returnvalue($_POST['table']);                             break;
 case 'getimgset'            : $sb->getimgset('imgsets',$_POST['imgset']);                    break;
 case 'getaudio'             : $sb->getaudio('audio')  ;                                      break;
 case 'getcard'              : $sb->getcard('cardbs');                                        break;
 case 'getitem'              : $sb->getitem('item');                                          break;
 case 'gettheme'             : $sb->gettheme('theme')  ;                                      break;
 case 'getlist'              : $sb->getlist($_POST['table'],$_POST['row']);                   break;
 case 'getimgset0'           : $sb->getimgset0('imgsets');                                    break;
 case 'getlabels_private'    : $sb->getlabels_private('label');                               break;
 case 'getlabels_common'     : $sb->getlabels_common( 'label',$_POST['quantity']);            break;
 case 'delete_label'         : $sb->delete_label( 'label');                                   break;
 case 'createitem'           : $sb->createitem('item','cardpersonal');                        break;
 case 'creategiftcardbs'     : $sb->creategiftcardbs('cardbs','cardpersonal');                break;
 case 'createlabel'          : $sb->createlabel('label');                                     break;
 case 'createpost'           : $sb->createpost('posts');                                      break;
 case 'getpost'              : $sb->getpost('posts','label');                                 break;
 case 'getpost_feed'         : $sb->getpost_feed('friends','timeline');                       break;
 case 'getobjectimages'      : $sb->getobjectimages();                                        break;
 case 'labelactivation'      : $sb->labelactivation('label');                                 break;
 case 'delete'               : $sb->delete($_POST['table']);                                  break;
 case 'deletepost'           : $sb->deletepost($_POST['table']);                              break;
 case 'deletecards'          : $sb->deletecards($_POST['table']);                             break;
 case 'delete_image'         : $sb->delete_image();                                           break;
 case 'likes'                : $sb->likes('likes');                                           break;
 case 'likestatus'           : $sb->likestatus('likes');                                      break;
// case 'labelstatistic'       : $sb->labelstatistic('label');                                  break;
 case 'personalcards'        : $sb->personalcards('cardbs');                                  break;
 case 'personalitems'        : $sb->personalitems('item');                                    break;
 case 'comment'              : $sb->comment('comment');                                       break;///////HOME PAGE CALL BACK COMMENT
 case 'sendtomail'           : $sb->sendtomail($_POST['email'],$_POST['message']);            break;
 case 'metrics'              : $sb->metrics('metrics');                                       break;
 case 'getmetrics'           : $sb->getmetrics('metrics');                                    break;
 case 'feed'                 : $sb->feed('celebration');                                      break;
 case 'save_video'           : $sb->save_video('video');                                      break;
 case 'get_video'            : $sb->get_video('video');                                       break;
 case 'delete_video'         : $sb->delete_video('video');                                    break;
 case 'uploadlabelimages'    : $sb->uploadlabelimages('labelimages');                         break;
 case 'uploadlabelimages_canvas' : $sb->uploadlabelimages_canvas('labelimages');              break;
 case 'upload_account_image' : $sb->upload_account_image('account_images');                   break;
 case 'upload_account_avatar': $sb->upload_account_avatar('account');                         break;
 case 'upload_account_canvas': $sb->upload_account_canvas('account_images');                  break;
 case 'save_img'             : $sb->save_img('account_images');                               break;
 case 'get_account_images'   : $sb->get_account_images('account_images');                     break;
 case 'labelimages'          : $sb->labelimages('labelimages',$_POST['status']);              break;
 case 'search'               : $sb->search('label','account');                                break;
 case 'searchcontact'        : $sb->search_contact('account');                                break;
 case 'statistic_persons'    : $sb->statistic_persons();                                      break;
 case 'statistic_followers'  : $sb->statistic_followers('follow','label');                    break;
 case 'views'                : $sb->views('views');                                           break;
 case 'online'               : $sb->online('account');                                        break;
 /////////////////////////STATUS_STATE AND STATUS_RELATIONS//////////////////////
 case 'set_state'            : $sb->set_state('account');                                     break;
 case 'set_status'           : $sb->set_status('account');                                    break;
 ////////////////////////////////MESSENGER///////////////////////////////
 case 'messenger'            : $sb->messenger('messenger');                                   break;
 case 'chat'                 : $sb->chat('messenger');                                        break;
 case 'chat_img'             : $sb->chat_img('messenger');                                    break;
 case 'sendmessage'          : $sb->sendmessage('messenger');                                 break;
 case 'sendmessage_preload'  : $sb->sendmessage_preload();                                    break;
 case 'sendmessage_preload_finish': $sb->sendmessage_preload_finish('messenger');             break;
 case 'sendmessage_og'       : $sb->sendmessage_og('messenger');                              break;
 case 'sendmessage_voice'    : $sb->sendmessage_voice('messenger');                           break;
 case 'delete_preloaded_image': $sb->delete_preloaded_image();                                break;
///////////////////////////////////TIMELINE//////////////////////////////
 case 'timeline'                   : $tl->timeline('timeline');                               break;
 case 'sendmessage_timeline'       : $tl->sendmessage_timeline('timeline');                   break;
 case 'sendmessage_timeline_voice' : $tl->sendmessage_timeline_voice('timeline');             break;
 case 'sendmessage_timeline_og'    : $tl->sendmessage_timeline_og('timeline');                break;
 case 'delete_message_timeline'    : $tl->delete_message_timeline('timeline');                break;
  ///////////////////////////////////////////////////////////////////////
 case 'send_comment'         : $sb->send_comment('comments');                                 break;
 case 'get_comments'         : $sb->get_comments('comments');                                 break;
 case 'delete_comment'       : $sb->delete_comment('comments');                               break;
 case 'deletemessage'        : $sb->delete_message('messenger');                              break;
 case 'myunreadedmessages'   : $sb->my_unreaded_messages('messenger');                        break;
 case 'activity'             : $sb->activity('activity');                                     break;
 case 'friends_activity'     : $sb->friends_activity('friends','activity');                   break;
 case 'friends_posts_activity': $sb->friends_posts_activity('friends','account_images');      break;
 case 'follow'               : $sb->follow('friends');                                        break;
 case 'checkfollow'          : $sb->checkfollow('friends');                                   break;
 case 'delete_profile'       : $sb->delete_profile();                                         break;
 case 'toggle_profile'       : $sb->toggle_profile('account');                                break;
 case 'card_existance'       : $sb->card_existance();                                         break;
 case 'user_card'            : $sb->user_card('label');                                       break;
///////////////////////////////////ADMIN////////////////////////////////////////////////////////////
 case 'autoriz'             : $adm->autoriz('admin');                                         break;
 case 'get_users'           : $adm->get_users('account');                                     break;
 case 'get_visitors'        : $adm->get_visitors('metrics');                                  break;
 case 'delete_profile_admin': $adm->delete_profile('account');                                break;
 case 'get_callback'        : $adm->get_callback('comment');                                  break;
 case 'check_session'       : $adm->check_session();                                          break;
 case 'log_out'             : $adm->log_out();                                                break;


 

 default: return false;break;
  }
}
else return false;

 ?>
