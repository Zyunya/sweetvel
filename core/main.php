<?php
 header('Access-Control-Allow-Origin:*');   
abstract class main  {
  
public $mycon;
//hephaestus.ukrhost.biz:8443/smb/file-manager/code-editor?currentDir=%2Fsweetvel&file=fauth.php
//public  $host       = 'localhost';    public  $login    = 'schak_schakirrr';
//public  $password   = 'Mediamatrix16';public  $db       = 'schakirrr-29321_sweetvel';
public  $host     = 'localhost';     public  $login    = 'root';
public  $password = 'Mediamatrix16'; public  $db       = 'giftcard';
private $res;
public  $prefix   = 'gc_';
private $lang;
public  $outp     = "";
private $outp2    = "";
private $outp3    = "";
private $rateval1 = "280";
private $rateval2 = "300";
public  $limit    =   10;
public  $limit20  =   20;
public  $limittest = 2;
public  $validformats = array("image/jpg","image/png","image/jpeg","audio/wav","image/JPG","image/PNG","audio/WAV");
public  $maxsize      =  5096000; //4mb same as 1024 * 4000
public  $hash         = 95616;  
public  $host_prefix;
public  $path;
protected  $sesid;

public function __construct(){
session_name('global_sesid');

//$this->host_prefix  = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,strpos( $_SERVER["SERVER_PROTOCOL"],'/'))).'://';
$this->host_prefix  = $_SERVER['REQUEST_SCHEME'].'://';
$this->lang  = isset($_GET['lang'])? $_GET['lang']:"ru";
$this->path = "../globalimg/123.jpg";
$this->mycon = new mysqli($this->host,$this->login,$this->password);
$this->mycon->query("SET NAMES utf8");
if($this->mycon){
  $seldb = mysqli_select_db($this->mycon,$this->db);
  if($seldb){
     return true;
  }
  else{
   return false;
   }
  }

}


public function filter1($val){
$res = stripslashes(preg_replace("~(\*|\s+|\\$|\@|\%|\\\\|\&|\!|\:|\;|\=|\,|\-|\+|\<|\>|\^|\~|\`|\{|\}|\'|\[|\"|\]|\(|\))~","",$val));
return $res;
}

public function filter_outp($val){
$res =  trim(stripslashes(preg_replace("~(\*|\<|\>|\\\\|\_|\r\n|\r|\n|\t|\f|\^|\~|\'|\")~","`",$val)));
return $res;
}
public function filter_inp($val){
$res = $this->mycon->real_escape_string(addslashes(preg_replace("~(\*|\<|\>|\_|\^|\~|\'|\")~","`",$val)));
return $res;
}

public function path_abs($pp){
 return  $this->host_prefix.$_SERVER['HTTP_HOST'].substr($pp,2);
}
public function get_abs_path_array($img){
 $path =   array_map(array($this, 'path_abs'), $img);
 return $path;
}

public function json_true($json)
{
  $valid_json = $json !== null ? $json : "";
  return $valid_json;
}
protected function active_avatar($active,$avatar)
{
  $final_avatar   =  $active == '1'  ?  $avatar : $this->host_prefix.$_SERVER['HTTP_HOST']."/img/media/noavatar.png";
  return $final_avatar;
}
public  function create_new_label($table){
session_start();
$sesid        = $_SESSION['id'];
$social_start = json_encode(array('vk'=>'Vk','fb'=>'Fb','insta'=>'Insta','email'=>'E-Mail','phone'=>'Phone'));
$place_start  = json_encode(array('placename'=>'Location','placeadress'=>'Adress','placeid'=>'ChIJQ0yGC4oxxkARbBfyjOKPnxI'));
$label_title  = $_POST['title'];
$query = "INSERT INTO $this->prefix$table
(account,label_name,label_logo,description,social,place,date,timestamp,status)
VALUES
('".$sesid."','".$label_title."',' ','No Description ', '".$social_start."','".$place_start."',' ','".time()."','0')
";
if(!empty($label_title))
{
if($this->mycon->query($query)){
  echo '{"status" : "1"}';
}
else{die('{"status" : "1"}');}
}
else
{
  die('{"status" : "0","text":"%fill_all_fields%"}');
}

}
public function statistic_counter($table,$type,$id){
$query = " SELECT SUM(counter) as counter FROM $this->prefix$table WHERE object_id = '".$id."' AND object_type = '".$type."'";
$counter =$this->mycon->query($query)->fetch_array();
return $counter['counter'];
}
public function statistic_counter_followers($table,$id){
$query = " SELECT COUNT(*) as counter FROM $this->prefix$table WHERE object_followed = '".$id."'";
$counter =$this->mycon->query($query)->fetch_array();
return $counter['counter'];
}
public function statistic_counter_comments($table,$type,$id){
$query = " SELECT COUNT(*) as counter FROM $this->prefix$table WHERE object_type = '".$type."' AND object_id = '".$id."'";
$counter =$this->mycon->query($query)->fetch_array();
return $counter['counter'];
}
public function userinfo($id,$val){
$query = " SELECT id,firstname,lastname,avatar,gender,active,password,login FROM gc_account  WHERE id = '".$id."' ";
$user  = $this->mycon->query($query)->fetch_array();
return   $user[$val];
}

public function statistic_persons(){
$table        = $_POST['table'];
$tab1         = $this->prefix.$table;
$tab2         = $this->prefix.'account';
$id           = $_POST['id'];
$object_type  = $_POST['object_type'];
$limit        = !empty($_POST['limit']) ? $_POST['limit'] : 20;

$query = " SELECT $tab2.firstname,$tab2.lastname,$tab2.id,$tab2.avatar,$tab1.date FROM $tab1 INNER JOIN $tab2 ON $tab1.account = $tab2.id
WHERE $tab1.object_id = '".$id."' AND $tab1.object_type = '".$object_type."'ORDER BY $tab1.time DESC LIMIT ".$limit." ";
$counter =$this->mycon->query($query);
while($row = $counter->fetch_array()){
  if($this->outp !== ""){$this->outp .= ",";}
  $date = json_decode($row['date'],true);
  $this->outp .= '{"firstname"   :"'.$row['firstname'].      '",';
  $this->outp .= '"table"        :"'.$table.                 '",';
  $this->outp .= '"object_id"    :"'.$id.                    '",';
  $this->outp .= '"owner_id"     :"'.$row['id'].             '",';
  $this->outp .= '"type"         :"viewslikes"                 ,';
  $this->outp .= '"date"         :"'.$date['date'].          '",';
  $this->outp .= '"month"        :"'.$date['month'].         '",';
  $this->outp .= '"year"         :"'.$date['year'].          '",';
  $this->outp .= '"hours"        :"'.$date['hours'].         '",';
  $this->outp .= '"minutes"      :"'.$date['minutes'].       '",';
  $this->outp .= '"lastname"     :"'.$row['lastname'].       '",';
  $this->outp .= '"avatar"       :"'.$row['avatar'].         '"}';
}
$this->outp = '{"record":['.$this->outp.']}';
echo $this->outp;

}
public function statistic_followers($table,$table2){

$tab1             = $this->prefix.$table;
$tab2             = $this->prefix.$table2;
$object_followed  = $_POST['object_followed'];
$limit            = isset($_POST['limit']) ? $_POST['limit'] : 20;

$query = " SELECT $tab1.date,$tab1.account_follower,$tab1.object_followed FROM $tab2 INNER JOIN $tab1 ON $tab1.object_followed = $tab2.id
WHERE $tab1.object_followed = '".$object_followed."' ORDER BY $tab1.id DESC LIMIT ".$limit." ";
$counter =$this->mycon->query($query);
while($row = $counter->fetch_array()){
  if($this->outp !== ""){$this->outp .= ",";}
  $user_name   = $this->userinfo($row['account_follower'],'firstname')." ".$this->userinfo($row['account_follower'],'lastname');
  $user_avatar = $this->userinfo($row['account_follower'],'avatar');
  $date        = json_decode($row['date'],true);
 
  $this->outp .= '{"date"           :    "'.$date['date'].            '",';
  $this->outp .= '"month"           :    "'.$date['month'].           '",';
  $this->outp .= '"year"            :    "'.$date['year'].            '",';
  $this->outp .= '"hours"           :    "'.$date['hours'].           '",';
  $this->outp .= '"minutes"         :    "'.$date['minutes'].         '",';
  $this->outp .= '"owner_id"        :    "'.$row['account_follower']. '",';
  $this->outp .= '"object_followed" :    "'.$row['object_followed'].  '",';
  $this->outp .= '"firstname"       :    "'.$user_name.               '",';
  $this->outp .= '"avatar"          :    "'.$user_avatar.             '"}';
}
$this->outp = '{"record":['.$this->outp.']}';
echo $this->outp;


}

public function getlabels_private($table){
  session_start();
$query    = " SELECT * FROM $this->prefix$table WHERE account = '".$_SESSION['id']."' ";
$onelabel = $_POST['quantity'];
if($onelabel !== 'notone'){
  $query .= " AND id = '".$onelabel."' ";/////????????????????????
}
$query .= " ORDER BY id DESC ";
$query = $this->mycon->query($query);
while($row = $query->fetch_array()){
  $logo         = json_decode($row['label_logo'],true);
  $date         = json_decode($row['date'],true);
  $social       = json_decode($row['social'],true);
  $place        = json_decode($row['place'],true);
  $followers    = $this->statistic_counter_followers('follow',$row['id']);
  $views        = $this->statistic_counter('views','label',$row['id']) == "" ? 0 : $this->statistic_counter('views','label',$row['id']);
  $likes        = $this->statistic_counter('likes','label',$row['id']) == "" ? 0 : $this->statistic_counter('likes','label',$row['id']);
  $rating       = round($likes / $this->rateval1 + $views / $this->rateval2,2);
  
  if($this->outp !== ""){$this->outp .= ",";}
  $this->outp .= '{"id"          :"'.$row['id']. '          ",';
  $this->outp .= '"owner_id"     :"'.$row['account'].      '",';
  $this->outp .= '"status"       :"'.$row['status'].       '",';
  $this->outp .= '"name"         :"'.htmlspecialchars($row['label_name']).   '",';
  $this->outp .= '"themeid"      :"'.$logo['id'].          '",';
  $this->outp .= '"logoback"     :"'.$logo['background'].  '",';
  $this->outp .= '"logocolor"    :"'.$logo['color'].       '",';
  $this->outp .= '"logoaltcolor" :"'.$logo['altcolor'].    '",';
  $this->outp .= '"logofont"     :"'.$logo['font'].        '",';
  $this->outp .= '"description"  :"'.trim(htmlspecialchars($row['description'])).  '",';
  $this->outp .= '"tags"         :"'.htmlspecialchars($row['tags']).         '",';
  $this->outp .= '"placename"    :"'.htmlspecialchars($place['placename']).  '",';
  $this->outp .= '"placeadress"  :"'.htmlspecialchars($place['placeadress']).'",';
  $this->outp .= '"placeid"      :"'.$place['placeid'].    '",';
  $this->outp .= '"followers"    :"'.$followers.           '",';
  $this->outp .= '"likes"        :"'.$likes.               '",';
  $this->outp .= '"views"        :"'.$views.               '",';
  $this->outp .= '"rating"       :"'.$rating.              '",';
  $this->outp .= '"vk"           :"'.$social['vk'].        '",';
  $this->outp .= '"fb"           :"'.$social['fb'].        '",';
  $this->outp .= '"insta"        :"'.$social['insta'].     '",';
  $this->outp .= '"email"        :"'.$social['email'].     '",';
  $this->outp .= '"phone"        :"'.$social['phone'].     '",';
  $this->outp .= '"date"         :"'.$date['date'].        '"}';

}
$this->outp = '{"record":['.$this->outp.']}';
echo($this->outp);
}

public function getlabels_common($table,$onelabel){
  session_start();
$query = " SELECT * FROM $this->prefix$table ";

if($onelabel !== 'notone'){
  $query .= " WHERE id = '".$onelabel."' AND status = 1";/////????????????????????
}
$query = $this->mycon->query($query);
while($row = $query->fetch_array()){
  $logo   = json_decode($row['label_logo'],true);
  $date   = json_decode($row['date'],true);
  $social = json_decode($row['social'],true);
  $place  = json_decode($row['place'],true);

  $label_ownerfname  = $this->userinfo($row['account'],'firstname');
  $label_ownerlname  = $this->userinfo($row['account'],'lastname');
  $label_owneravatar = $this->userinfo($row['account'],'avatar');
  $followers         = $this->statistic_counter_followers('follow',$row['id']);
  $views             = $this->statistic_counter('views','label',$row['id']);
  $likes             = $this->statistic_counter('likes','label',$row['id']);
  $rating            = round($likes / $this->rateval1 + $views / $this->rateval2,2);
  
  if($this->outp !== ""){$this->outp .= ",";}
  $this->outp .= '{"id"              :"'.$row['id']. '                     ",';
  $this->outp .= ' "owner_id"        :"'.$row['account']. '                ",';
  $this->outp .= ' "labelownerfname" :"'.$label_ownerfname. '              ",';
  $this->outp .= ' "labelownerlname" :"'.$label_ownerlname. '              ",';
  $this->outp .= ' "labelowneravatar":"'.$label_owneravatar. '             ",';
  $this->outp .= '"name"         :"'.htmlspecialchars($row['label_name']).'",';
  $this->outp .= '"themeid"      :"'.$logo['id'].          '",';
  $this->outp .= '"logoback"     :"'.$logo['background'].  '",';
  $this->outp .= '"logocolor"    :"'.$logo['color'].       '",';
  $this->outp .= '"logoaltcolor" :"'.$logo['altcolor'].    '",';
  $this->outp .= '"logofont"     :"'.$logo['font'].        '",';
  $this->outp .= '"description"  :"'.htmlspecialchars($row['description']).  '",';
  $this->outp .= '"tags"         :"'.htmlspecialchars($row['tags']).         '",';
  $this->outp .= '"placename"    :"'.htmlspecialchars($place['placename']).  '",';
  $this->outp .= '"placeadress"  :"'.htmlspecialchars($place['placeadress']).'",';
  $this->outp .= '"placeid"      :"'.$place['placeid'].    '",';
  $this->outp .= '"vk"           :"'.$social['vk'].        '",';
  $this->outp .= '"fb"           :"'.$social['fb'].        '",';
  $this->outp .= '"insta"        :"'.$social['insta'].     '",';
  $this->outp .= '"email"        :"'.$social['email'].     '",';
  $this->outp .= '"phone"        :"'.$social['phone'].     '",';
  $this->outp .= '"followers"    :"'.$followers.           '",';
  $this->outp .= '"views"        :"'.$views.               '",';
  $this->outp .= '"rating"       :"'.$rating.              '",';
  $this->outp .= '"date"         :"'.$date['date'].        '"}';

}
$this->outp = '{"record":['.$this->outp.']}';
echo($this->outp);
}

public function delete($table){
  session_start();
  $user = $_SESSION['id'];
  $id   = $_POST['id'];
  $file = $_POST['file'];
  $query = "DELETE FROM $this->prefix$table WHERE account = '".$user."' AND id = '".$id."' ";
  $query = $this->mycon->query($query);
  $delete = unlink($file);
  if($query AND $delete){
  echo '{"record": "%action_success%"}';
  }
  else{
   die('{"record": "%action_error%"}');
  }
}

public function delete_image(){
session_start();
$table = $_POST['table'];
$file  = $_POST['file'];
$table = $this->prefix.$table;
$type  = $_POST['type'];
$id    = $_POST['id'];
$sesid = $_SESSION['id'];
$status;

if(isset($_SESSION['id'])){   
$image_delete             = " DELETE FROM  $table       WHERE id        = $id  AND account      = $sesid  ";    
$likes_delete             = " DELETE FROM `gc_likes`    WHERE object_id = '".$id."'  AND object_owner = '".$sesid."'      AND object_type = '".$type."' ";
$comments_delete          = " DELETE FROM `gc_comments` WHERE object_id = '".$id."'  AND object_type  = '".$type."' ";
$comments_activity_delete = " DELETE FROM `gc_activity` WHERE object_id IN (SELECT id FROM gc_comments WHERE object_id = '".$id."' AND object_type = '".$type."') ";
$likes_activity_delete    = " DELETE FROM `gc_activity` WHERE object_id = '".$id."'  AND object_type = '".$type."' ";


if($this->mycon->query($image_delete))
{
  if($this->mycon->affected_rows > 0)
    {
       if(@is_writable( $file) ) {  
          unlink($file);
           echo '{"status":"1","type":"'.$type.'","table":"'.$table.'","id":"'.$id.'","actions":"'.$this->mycon->affected_rows.'"}';
            }
       else
            {
           die('{"status" : "0","text":"file_not_exists"}');
            }

            try{$this->mycon->query($likes_activity_delete);}    catch (Exception $e) {echo $e->getMessage();}
            try{$this->mycon->query($comments_activity_delete);} catch (Exception $e) {echo $e->getMessage();}
            try{$this->mycon->query($likes_delete);}             catch (Exception $e) {echo $e->getMessage();}
            try{$this->mycon->query($comments_delete);}          catch (Exception $e) {echo $e->getMessage();}

        }

         else
             {
             die('{"status" : "0","text":"not_owner"}');
               }
     }

else
{
  die('{"status" : "0","text":"error"}');
}


 }
}
public function deletepost($table){
  session_start();
  $user           = $_SESSION['id'];
  $id             = $_POST['id'];
  $file           = $_POST['file'];
  $object_owner   = $_POST['object_owner'];
  $query_post     = " DELETE FROM $this->prefix$table     WHERE account   = '".$user."' AND id           = '".$id."' ";
  $query_like     = " DELETE FROM `gc_likes`              WHERE object_id = '".$id."' AND object_type  = 'post'      ";
  $query_comment  = " DELETE FROM `gc_comments`           WHERE object_id = '".$id."' AND object_type  = 'post'      ";
  $comments_activity_delete = " DELETE FROM `gc_activity` WHERE object_id IN (SELECT id FROM gc_comments WHERE object_id = '".$id."' AND object_type = 'post') ";
  $likes_activity_delete    = " DELETE FROM `gc_activity` WHERE object_id = '".$id."'  AND object_type = 'post'      ";
  
  $query = $this->mycon->query($comments_activity_delete);
  $query = $this->mycon->query($likes_activity_delete);
  $query = $this->mycon->query($query_post);
  $query = $this->mycon->query($query_like);
  $query = $this->mycon->query($query_comment);

  if($query){
  $this->deleteDir("../globalimg/post_images/".$file."/");
  echo '{"record": "%action_success%"}';
  }
  else{
   die('{"record": "%action_error%"}');
  }
}
public function deletecards($table){
  session_start();
  $user  = $_SESSION['id'];
  $key   = $_POST['key'];
  $path  = isset($_POST['path']) ? $_POST['path'] : "";

  $query  = "DELETE FROM $this->prefix$table WHERE   account = '".$user."' AND keyvalue = '".$key."' ";
  
  $query  = $this->mycon->query($query);

  if($table == 'item'){
    unlink($path);
  }
 

  if($query){
  echo '{"record": "%action_success%"}';
  }
  else{
   die('{"record": "%action_error%"}');
  }
}
public function deleteDir($path) {
  if(is_dir($path) === true) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
      if(in_array($file->getBasename(), array('.', '..')) !== true) {
        if($file->isDir() === true) rmdir($file->getPathName());
        else if (($file->isFile() === true) || ($file->isLink() === true)) unlink($file->getPathname());
      }
    }

    return rmdir($path);
  }
  else if((is_file($path) === true) || (is_link($path) === true)) return unlink($path);

  return false;
}

public function fb_vk_signin($table){

   $query = "SELECT * FROM $this->prefix$table WHERE login =  '".$_POST['login']."'";
   $result = $this->mycon->query($query);
   $row = $result->fetch_array();
	if(!empty($row['login'])){
		$queryupdate = "UPDATE $this->prefix$table SET
login       = '".$_POST['login']."',
ip          = '".$_SERVER['REMOTE_ADDR']."',
firstname   = '".$_POST['firstname']."',
lastname    = '".$_POST['lastname']."',
gender      = '".$_POST['gender']."',
avatar      = '".$_POST['avatar']."'
WHERE login = '".$_POST['login']."' AND regtype = '".$_POST['regtype']."'";
 $this->mycon->query($queryupdate);

session_start(); 
setcookie(session_name(),session_id(),time()+365*24*3600); 

    $_SESSION['id']        = $row['id'];
    $_SESSION['firstname'] = $row['firstname'];
    echo $row['id'];
    
	}
	   else{
		   $queryinsert = "INSERT INTO $this->prefix$table
SET 
login      = '".$_POST['login']."',
ip         = '".$_SERVER['REMOTE_ADDR']."',
firstname  = '".$_POST['firstname']."',
lastname   = '".$_POST['lastname']."',
gender     = '".$_POST['gender']."',
avatar     = '".$_POST['avatar']."',
active     = '1',
date       = '".$_POST['date']."',
regtype    = '".$_POST['regtype']."',
privacy    = '0'
";
		   $this->mycon->query($queryinsert);
		   $selectx = "SELECT * FROM $this->prefix$table WHERE login =  '".$_POST['login']."'";
       $result = $this->mycon->query($selectx);
       $rowx = $result->fetch_array(); 
       session_start(); 
	     setcookie(session_name(),session_id(),time()+365*24*3600);
         $_SESSION['id'] = $rowx['id'];
         $_SESSION['table'] = $table;
         echo $rowx['id'];
      
	   }
}
  public function sign_up($table){
  $login     = trim($this->filter_inp($_POST['login']));
  $password1 = trim($this->filter_inp($_POST['password1']));
  $password2 = trim($this->filter_inp($_POST['password2']));
  $fname     = trim($this->filter_inp($_POST['fname']));
  $lname     = trim($this->filter_inp($_POST['fname']));
  $gender    = trim($this->filter_inp($_POST['gender']));
  $ip        = $_SERVER['REMOTE_ADDR'];
  
  if($login !== "" AND $password1 !== "" AND $fname !== "" AND $lname !== "" AND $gender !== "")
  {
  $check = "SELECT login FROM $this->prefix$table WHERE  login = '".$login."' ";
  $check = $this->mycon->query($check)->fetch_array();

  if(!empty($check['login']))
  {
   die('{"status" : "0","text":"%action_login_exists%"}');
  }
  else if($password1 !== $password2)
  {
  die('{"status" : "0","text":"%action_password_notequals%"}');
  }
  else{
    $sign_up  = " INSERT INTO $this->prefix$table SET 
    login     = '".$login."',
    password  = '".sha1($password1)."',
    firstname = '".$fname."',
    regtype   = 'sw',
    ip        = '".$ip."',
    active    =  '1',
    lastname  = '".$lname."',
    gender    = '".$gender."'
    ";
    $res = $this->mycon->query($sign_up);
    if($res)
    {
      echo '{"status":"1","text":"%regestration_success%"}';
    }
      else
  {
    die('{"status":"0","text":"'.$this->mycon->error.'"}');
     }
   }

  }
  else
  {
   die('{"status":"0","text":"%action_fields%"}');
  }
  }
  public function sw_login($table){
  if(!empty($_POST['log']) AND !empty($_POST['pass'])){
  $login    = trim($this->filter_inp($_POST['log']));
  $password = trim($this->filter_inp($_POST['pass']));

  $query = "SELECT * FROM $this->prefix$table WHERE login = '".$login."'";
  $row   = $this->mycon->query($query)->fetch_array();

  if(!empty($row['login'])){
    if($row['password'] == sha1($password)){
     session_start(); 
     setcookie(session_name(),session_id(),time()+365*24*3600); 
     $_SESSION['id']   = $row['id'];
    

     echo '{"status":"1","text":"success","id" : "'.$_SESSION['id'].'"}';
     
    }
    else {die('{"status":"0","text":"%action_password%"}');}
  }
    else{
         die('{"status":"0","text":"%action_login%"}');
  }
  }
   else { 
        die('{"status":"0","text":"%action_fields%"}');
        }

  }
  public function edit_account_profile($table){
    session_start();
    $ip                = $_SERVER['REMOTE_ADDR'];
    $sesid             = $_SESSION['id'];
    $fname             = $this->filter_inp($_POST['fname']);
    $lname             = $this->filter_inp($_POST['lname']);
    $gender            = $this->filter_inp($_POST['gender']);
    $city              = $this->filter_inp($_POST['city']);
    $country           = $this->filter_inp($_POST['country']);
    $about_me          = $this->filter_inp($_POST['about_me']);
    $place_name        = $this->filter_inp($_POST['place_name']);
    $place_adress      = $this->filter_inp($_POST['place_adress']);
    $place_id          = $this->filter_inp($_POST['place_id']);
    $date              = $this->filter_inp($_POST['date']);
    $month             = $this->filter_inp($_POST['month']);
    $year              = $this->filter_inp($_POST['year']);
    $place = json_encode(array("place_name"=>$place_name,"place_adress"=>$place_adress,"place_id"=>$place_id ),JSON_UNESCAPED_UNICODE);
    $birth = json_encode(array("date"=>$date,"month"=>$month,"year"=>$year ),JSON_UNESCAPED_UNICODE);

    $edit_profile = " UPDATE $this->prefix$table SET 
    firstname        = '".$fname."',
    lastname         = '".$lname."',
    gender           = '".$gender."',
    country          = '".$country."',
    city             = '".$city."',
    ip               = '".$ip."',
    birth            = '".$birth."',
    about_me         = '".$about_me."',
    activity_place   = '".$place."'
    WHERE id         = '".$sesid."' ";
$res = $this->mycon->query($edit_profile);
if($res){

echo('{"record":"%action_success%"}');
return true;
}
else{
  die('{"record":"%action_error%"}');
  }

  }
    public function edit_account_profile_main($table){
    session_start();

    $sesid             = $_SESSION['id'];
    $login             = $this->filter_inp($_POST['login']);
    $password_old      = $this->filter_inp($_POST['password_old']);
    $password_new      = $this->filter_inp($_POST['password_new']);
    $check_login       = " SELECT login FROM $this->prefix$table WHERE login = '".$login."' AND id != '".$sesid."' ";
    $check_login       = $this->mycon->query($check_login)->fetch_array();
    $current_pass      = !empty($this->userinfo($sesid,'password')) ? $this->userinfo($sesid,'password') : sha1($this->userinfo($sesid,'password'));
    $edit_profile_full = " UPDATE $this->prefix$table SET 
    login              = '".$login."',
    password           = '".sha1($password_new)."'
    WHERE id           = '".$sesid."' ";
    $edit_profile_only_login = " UPDATE $this->prefix$table SET 
    login              = '".$login."'
    WHERE id           = '".$sesid."' ";

   if(empty($check_login['login']))
   {
    if(sha1($password_old) == $current_pass)
    {
      ////////////////////EDIT FULL PROFILE////////////////////
    $res = $this->mycon->query($edit_profile_full);
     if($res){

      echo('{"status":"1","text":"%action_success%"}');

       }
      else{
        die('{"status":"0","text":"%action_error%"}');
         }
       }
       ////////////////////////////////////////////////////
     else if($password_old == "" AND $password_new == "" )
     {
        ////////////////////EDIT PROFILE LOGIN////////////////////
         $res2 = $this->mycon->query($edit_profile_only_login);
         if($res2){
           echo('{"status":"1","text":"%action_success%"}');
             }
      else{
        die('{"status":"0","text":"%action_error%"}');
         }
       }
       
    //////////////////////////////////////////////////////
    else
    {
      die('{"status":"0","text":"%action_password%","pass":"'.$this->userinfo($sesid,'password').'","pass2":"'.sha1("").'"}');
    }

   }

    
        else
    {
      die('{"status":"0","text":"%action_login_exists%"}');
    }

    }

    
public function set_status_self($table){
 session_start();
$sesid             = $_SESSION['id'];
$statusaccount     = $this->filter_inp($_POST['statusaccount']);
$status = "UPDATE $this->prefix$table SET 
    status_account = '".$statusaccount."'
    WHERE id       = '".$sesid."' ";
$res = $this->mycon->query($status);
if($res){

echo('{"record":"%action_success%"}');
return true;
}
else{
  die('{"record":"%action_error%"}');
  }
}
public function logout(){
  session_start();
 
  if(isset($_SESSION['id'])){
   setcookie(session_name(),session_id(),time()-365*24*3600);	  
   session_destroy();
   echo '{"status":"1","text":"%action_success%"}';

}
else
{
  die('{"status":"0","text":"%action_error%"}');
}

}

public function checksession(){
  session_start();
  
  if(isset($_SESSION['id'])){
  $account = isset($_POST['sketch']) ? $_POST['sketch'] : $_SESSION['id'];
  $check   = "SELECT id,active,active_time FROM gc_account WHERE id = '".$account."'";
  $check   = $this->mycon->query($check)->fetch_array();
  if(!empty($check['id'])){
      
      $recover_period = date("F j, Y, g:i a",$check['active_time'] + 604800 * 4 * 6);
     echo '{"session":"1","active":"'.$check['active'].'","active_time":"'.$recover_period.'"}';
  }
  else{
    
    die('{"session":"1","active":"0","active_time":"00000"}');
     
  }
}
else {
  die('{"session":"0","active":"0"}');
  }
}

public function token($table){
 session_start();
 if(isset($_SESSION['id']))
 {
  $outp = "";
  if($outp !== ""){$outp .= ",";}
  $query = "SELECT * FROM $this->prefix$table WHERE id = '".$_SESSION['id']."'";
  $query = $this->mycon->query($query);
  $param = $query->fetch_array();
  $outp .= '{"firstname"        :"'.$this->filter_outp($param['firstname']). '",';
  $outp .= '"lastname"          :"'.$this->filter_outp($param['lastname']).  '",';
  $outp .= '"id"                :"'.$this->filter_outp($param['id']).        '",';
  $outp .= '"avatar"            :"'.$param['avatar'].                        '"}';

   $outp = $outp;

   echo $outp;
 }
 else{
   die('{"status" : "0"}');
 }
}
public function returnparam($table,$param,$what,$val){
  
  $this->query = "SELECT * FROM $this->prefix$table WHERE $what = '".$val."' ORDER BY id DESC";
  $this->query = $this->mycon->query($this->query);
  $this->param = $this->query->fetch_array();
   return $this->param[$param];
}
public function param($table){
  $outp = "";
  session_start();

  $query = "SELECT * FROM $this->prefix$table WHERE id = '".$_SESSION['id']."' AND active = 1 ";
  $query = $this->mycon->query($query);
  if($query AND $this->mycon->affected_rows > 0)
  {
  $param = $query->fetch_array();
  $placer = json_decode($param['activity_place'],true);
  $birth  = json_decode($param['birth'],true);
  $online = json_decode($param['online'],true);
  $online_status = $online['status'];
  $device        = $online['device'];
  $lastvisit     = $param['lastvisit'];
  //if($online['time'])
  if($outp !== ""){$outp .= ",";}

  $outp .= '{"firstname"        :"'.$this->filter_outp($param['firstname']). '",';
  $outp .= '"lastname"          :"'.$this->filter_outp($param['lastname']).  '",';
  $outp .= '"login"             :"'.$this->filter_outp($param['login']).     '",';
  $outp .= '"password"          :"'.$this->filter_outp($param['password']).  '",';
  $outp .= '"id"                :"'.$this->filter_outp($param['id']).        '",';
  $outp .= '"gender"            :"'.$this->filter_outp($param['gender']).    '",';
  $outp .= '"avatar"            :"'.$param['avatar'].                        '",';
  $outp .= '"online_status"     :"'.$online_status.                          '",';
  $outp .= '"device"            :"'.$device.                                 '",';
  $outp .= '"active_time"       :"'.$param['active_time'].                   '",';
  $outp .= '"lastvisit"         :['.$lastvisit.']                              ,';
  $outp .= '"state"             : "'.$this->state_exchange($param['state']).'" ,';
  $outp .= '"state_point"       :"'.$param['state'].'"                         ,';
  $outp .= '"status"            : "'.$this->status_exchange($param['status_relations']).'",';
  $outp .= '"status_point"      :"'.$param['status_relations'].'"                         ,';
  $outp .= '"city"              :"'.$this->filter_outp($param['city']).      '",';
  $outp .= '"country"           :"'.$this->filter_outp($param['country']).   '",';
  $outp .= '"birth_date"        :"'.$this->filter_outp($birth['date']).               '",';
  $outp .= '"birth_month"       :"'.$this->filter_outp($birth['month']).              '",';
  $outp .= '"birth_year"        :"'.$this->filter_outp($birth['year']).               '",';
  $outp .= '"status_account"    :"'.$this->filter_outp($param['status_account']).     '",';
  $outp .= '"about_me"          :"'.$this->filter_outp($param['about_me']).           '",';
  $outp .= '"place_id"          :"'.$this->filter_outp($placer['place_id']).          '",';
  $outp .= '"place_name"        :"'.$this->filter_outp($placer['place_name']).        '",';
  $outp .= '"place_adress"      :"'.$this->filter_outp($placer['place_adress']).      '"}';

   echo '{"status":"1","record":['.$outp.']}';
  }
  else
  {
    die('{"status":"0","record":[]}');
  }

}

public function sketch_info($table){
  $outp = "";
  $sketch = $_POST['sketch'];

  $this->query = "SELECT * FROM $this->prefix$table WHERE id = '".$sketch."' AND active = 1";
  $this->query = $this->mycon->query($this->query);
  $param       = $this->query->fetch_array();
  $placer        = json_decode($param['activity_place'],true);
  $birth         = json_decode($param['birth'],true);
  $online        = json_decode($param['online'],true);
  $online_status = intval($online['status']);
  $device        = $online['device'];
  $lastvisit     = $this->json_true($param['lastvisit']);
  //$avatar        = $param['active'] == '1' ? $param['avatar'] : $this->host_prefix.$_SERVER['HTTP_HOST']."/img/media/noavatar.png";
  if(!empty($param['id']))
  {
  if($outp !== ""){$outp .= ",";}

  $outp .= '{"firstname"        :"'.htmlspecialchars($param['firstname']). '",';
  $outp .= '"lastname"          :"'.htmlspecialchars($param['lastname']).  '",';
  $outp .= '"login"             :"'.htmlspecialchars($param['login']).     '",';
  $outp .= '"password"          :"'.htmlspecialchars($param['password']).  '",';
  $outp .= '"id"                :"'.htmlspecialchars($param['id']).        '",';
  $outp .= '"gender"            :"'.htmlspecialchars($param['gender']).    '",';
  $outp .= '"avatar"            :"'.htmlspecialchars($param['avatar']).    '",';
  $outp .= '"online_status"     :"'.$online_status.                        '",';
  $outp .= '"active"            :"'.$param['active'].                      '",';
  $outp .= '"device"            :"'.$device.                               '",';
  $outp .= '"lastvisit"         :['.$lastvisit.']                            ,';
  $outp .= '"state"             : "'.$this->state_exchange($param['state']).'" ,';
  $outp .= '"state_point"       :"'.$param['state'].'"                         ,';
  $outp .= '"status"            : "'.$this->status_exchange($param['status_relations']).'",';
  $outp .= '"status_point"      :"'.$param['status_relations'].'"                         ,';
  $outp .= '"city"              :"'.htmlspecialchars($param['city']).      '",';
  $outp .= '"country"           :"'.htmlspecialchars($param['country']).   '",';
  $outp .= '"birth_date"        :"'.htmlspecialchars($birth['date']).      '",';
  $outp .= '"birth_month"       :"'.htmlspecialchars($birth['month']).     '",';
  $outp .= '"birth_year"        :"'.htmlspecialchars($birth['year']).            '",';
  $outp .= '"status_relations"  :"'.htmlspecialchars($param['status_relations']).'",';
  $outp .= '"status_account"    :"'.$this->filter_outp($param['status_account']).'",';
  $outp .= '"about_me"          :"'.htmlspecialchars($param['about_me']).        '",';
  $outp .= '"place_id"          :"'.htmlspecialchars($placer['place_id']).       '",';
  $outp .= '"place_name"        :"'.htmlspecialchars($placer['place_name']).     '",';
  $outp .= '"place_adress"      :"'.htmlspecialchars($placer['place_adress']).   '"}';

  
  $outp = '{"record":['.$outp.'],"status":"1"}';

   echo $outp;
  }
  else
  {
   die('{"status":"0","text":"%no_data_availible%"}');
  }
}
public function personalcards($table){
  session_start();
  
  $this->query = "SELECT * FROM $this->prefix$table WHERE account = '".$_SESSION['id']."' ORDER BY id DESC";
  $this->query = $this->mycon->query($this->query);
  while($this->param = $this->query->fetch_array()){
  $this->style  = json_decode($this->param['style'] ,true);
  $this->place  = json_decode($this->param['place'] ,true);
  $this->img    = json_decode($this->param['img']   ,true);
   if ($this->outp != "") {$this->outp .= ",";}
  $this->outp .= '{"event"      : "'.htmlentities($this->param['event']).       '",';
  $this->outp .= '"keyvalue"    : "'.$this->param['keyvalue'].                  '",';
  $this->outp .= '"desc"        : "'.htmlentities($this->param['description']). '",';
  $this->outp .= '"receiver"    : "'.htmlentities($this->param['receiver']).    '",';
  $this->outp .= '"sender"      : "'.htmlentities($this->param['sender']).      '",';
  $this->outp .= '"back"        : "'.$this->style['background'].  '",';
  $this->outp .= '"color"       : "'.$this->style['color'].       '",';
  $this->outp .= '"altcolor"    : "'.$this->style['altcolor'].    '",';
  $this->outp .= '"font"        : "'.$this->style['font'].        '",';
  $this->outp .= '"musicimg"    : "'.$this->style['musicimg'].    '",';
  $this->outp .= '"img1"        : "'.$this->img['img1'].          '",';
  $this->outp .= '"img2"        : "'.$this->img['img2'].          '",';
  $this->outp .= '"img3"        : "'.$this->img['img3'].          '",';
  $this->outp .= '"img4"        : "'.$this->img['img4'].          '",';
  $this->outp .= '"img5"        : "'.$this->img['img5'].          '",';
  $this->outp .= '"img6"        : "'.$this->img['img6'].          '",';
  $this->outp .= '"audio"       : "'.$this->param['audio'].       '",';
  $this->outp .= '"placename"   : "'.$this->place['placename'].   '",';
  $this->outp .= '"placeadress" : "'.$this->place['placeadress']. '",';
  $this->outp .= '"placeid"     : "'.$this->place['placeid'].     '",';
  $this->outp .= '"proxy"       : "'.$this->param['proxy'].       '",';
  $this->outp .= '"link"        : "'.$this->param['link'].        '"}';

  }

  $this->outp = '{"record":['.$this->outp.']}';
  
  echo ($this->outp);
}

public function personalitems($table){
  session_start();
  
  $this->query = "SELECT * FROM $this->prefix$table WHERE account = '".$_SESSION['id']."' ORDER BY id DESC";
  $this->query = $this->mycon->query($this->query);
  while($this->param = $this->query->fetch_array()){
  $this->style = json_decode($this->param['style'],true);
  $this->place = json_decode($this->param['place'],true);
  $this->img   = json_decode($this->param['img']  ,true);
   if ($this->outp != "") {$this->outp .= ",";}
  $this->outp .= '{"title"      : "'.htmlentities($this->param['event']).       '",';
  $this->outp .= '"desc"        : "'.htmlentities($this->param['description']). '",';
  $this->outp .= '"keyvalue"    : "'.$this->param['keyvalue'].                  '",';
  $this->outp .= '"receiver"    : "'.htmlentities($this->param['receiver']).    '",';
  $this->outp .= '"sender"      : "'.htmlentities($this->param['sender']).      '",';
  $this->outp .= '"back"        : "'.$this->style['background'].  '",';
  $this->outp .= '"color"       : "'.$this->style['color'].       '",';
  $this->outp .= '"altcolor"    : "'.$this->style['altcolor'].    '",';
  $this->outp .= '"font"        : "'.$this->style['font'].        '",';
  $this->outp .= '"musicimg"    : "'.$this->style['musicimg'].    '",';
  $this->outp .= '"img1"        : "'.$this->img['img1'].          '",';
  $this->outp .= '"img2"        : "'.$this->img['img2'].          '",';
  $this->outp .= '"img3"        : "'.$this->img['img3'].          '",';
  $this->outp .= '"img4"        : "'.$this->img['img4'].          '",';
  $this->outp .= '"img5"        : "'.$this->img['img5'].          '",';
  $this->outp .= '"img6"        : "'.$this->img['img6'].          '",';
  $this->outp .= '"audio"       : "'.$this->param['audio'].       '",';
  $this->outp .= '"placename"   : "'.$this->place['placename'].   '",';
  $this->outp .= '"placeadress" : "'.$this->place['placeadress']. '",';
  $this->outp .= '"placeid"     : "'.$this->place['placeid'].     '",';
  $this->outp .= '"proxy"       : "'.$this->param['proxy'].       '",';
  $this->outp .= '"link"        : "'.$this->param['link'].        '"}';

  }

  $this->outp = '{"record":['.$this->outp.']}';
  
  echo ($this->outp);
}

public function getcard($table){

  $this->query = "SELECT * FROM $this->prefix$table  WHERE keyvalue = '".$_POST['keyvalue']."'";
  $this->query = $this->mycon->query($this->query);
  while($this->param = $this->query->fetch_array()){
  $this->style  = json_decode($this->param['style'],true);
  $this->place  = json_decode($this->param['place'],true);
  $this->social = json_decode($this->param['social'],true);
  $this->img    = json_decode($this->param['img'],true);
   if ($this->outp != "") {$this->outp .= ",";}
  $this->outp .= '{"event"      : "'.$this->param['event'].                     '",';
  $this->outp .= '"keyvalue"    : "'.htmlentities($this->param['keyvalue']).    '",';
  $this->outp .= '"desc"        : "'.htmlentities($this->param['description']). '",';
  $this->outp .= '"receiver"    : "'.htmlentities($this->param['receiver']).    '",';
  $this->outp .= '"sender"      : "'.htmlentities($this->param['sender']).      '",';
  $this->outp .= '"back"        : "'.$this->style['background'].                '",';
  $this->outp .= '"color"       : "'.$this->style['color'].                     '",';
  $this->outp .= '"altcolor"    : "'.$this->style['altcolor'].    '",';
  $this->outp .= '"font"        : "'.$this->style['font'].        '",';
  $this->outp .= '"musicimg"    : "'.$this->style['musicimg'].    '",';
  $this->outp .= '"img1"        : "'.$this->img['img1'].          '",';
  $this->outp .= '"img2"        : "'.$this->img['img2'].          '",';
  $this->outp .= '"img3"        : "'.$this->img['img3'].          '",';
  $this->outp .= '"img4"        : "'.$this->img['img4'].          '",';
  $this->outp .= '"img5"        : "'.$this->img['img5'].          '",';
  $this->outp .= '"img6"        : "'.$this->img['img6'].          '",';
  $this->outp .= '"audio"       : "'.$this->param['audio'].       '",';
  $this->outp .= '"kind"        : "'.$this->param['kind'].        '",';
  $this->outp .= '"placename"   : "'.$this->place['placename'].   '",';
  $this->outp .= '"placeadress" : "'.$this->place['placeadress']. '",';
  $this->outp .= '"placeid"     : "'.$this->place['placeid'].     '",';
  $this->outp .= '"phone"       : "'.$this->social['phone'].      '",';
  $this->outp .= '"email"       : "'.$this->social['email'].      '",';
  $this->outp .= '"vk"          : "'.$this->social['vk'].         '",';
  $this->outp .= '"fb"          : "'.$this->social['fb'].         '",';
  $this->outp .= '"insta"       : "'.$this->social['insta'].      '",';
  $this->outp .= '"label"       : "'.$this->social['label'].      '",';
  $this->outp .= '"proxy"       : "'.$this->param['proxy'].       '",';
  $this->outp .= '"link"        : "'.$this->param['link'].        '"}';

  }
  $this->outp = '{"record":['.$this->outp.']}';
  echo ($this->outp);
}

public function getitem($table){

  $this->query = "SELECT * FROM $this->prefix$table  WHERE keyvalue = '".$_POST['keyvalue']."'";
  $this->query = $this->mycon->query($this->query);
  while($this->param = $this->query->fetch_array()){
  $this->style  = json_decode($this->param['style'],true);
  $this->place  = json_decode($this->param['place'],true);
  $this->social = json_decode($this->param['social'],true);
  $this->img    = json_decode($this->param['img'],true);
   if ($this->outp != "") {$this->outp .= ",";}
  $this->outp .= '{"event"      : "'.$this->param['event'].                     '",';
  $this->outp .= '"desc"        : "'.htmlentities($this->param['description']). '",';
  $this->outp .= '"keyvalue"    : "'.htmlentities($this->param['keyvalue']).    '",';
  $this->outp .= '"receiver"    : "'.htmlentities($this->param['receiver']).    '",';
  $this->outp .= '"sender"      : "'.htmlentities($this->param['sender']).      '",';
  $this->outp .= '"back"        : "'.$this->style['background'].                '",';
  $this->outp .= '"color"       : "'.$this->style['color'].                     '",';
  $this->outp .= '"altcolor"    : "'.$this->style['altcolor'].    '",';
  $this->outp .= '"font"        : "'.$this->style['font'].        '",';
  $this->outp .= '"musicimg"    : "'.$this->style['musicimg'].    '",';
  $this->outp .= '"img"         : "'.$this->path_abs($this->param['img']).      '",';
  $this->outp .= '"audio"       : "'.$this->param['audio'].       '",';
  $this->outp .= '"placename"   : "'.$this->place['placename'].   '",';
  $this->outp .= '"placeadress" : "'.$this->place['placeadress']. '",';
  $this->outp .= '"placeid"     : "'.$this->place['placeid'].     '",';
  $this->outp .= '"phone"       : "'.$this->social['phone'].      '",';
  $this->outp .= '"email"       : "'.$this->social['email'].      '",';
  $this->outp .= '"vk"          : "'.$this->social['vk'].         '",';
  $this->outp .= '"fb"          : "'.$this->social['fb'].         '",';
  $this->outp .= '"insta"       : "'.$this->social['insta'].      '",';
  $this->outp .= '"label"       : "'.$this->social['label'].      '",';
  $this->outp .= '"proxy"       : "'.$this->param['proxy'].       '",';
  $this->outp .= '"link"        : "'.$this->param['link'].        '"}';

  }
  $this->outp = '{"record":['.$this->outp.']}';
  echo ($this->outp);
}


public function returnlang(){
  $this->langarray = array('ru','en');
  $this->serverlang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
  $this->autolang =(!in_array($this->serverlang,$this->langarray)) ? 'en' : $this->serverlang;
  return $this->autolang;
}
public function returnvalue($table){
$this->query = "SELECT * FROM $this->prefix$table WHERE keyvalue = '".$_GET['gc']."'";
$this->query = $this->mycon->query($this->query);

$this->stylerow = $this->query->fetch_array();
echo htmlspecialchars($this->stylerow[$_POST['val']],ENT_QUOTES);

}
public function getvalue($table,$val,$get){
$this->query = "SELECT * FROM $this->prefix$table WHERE keyvalue = '".$get."'";
$this->query = $this->mycon->query($this->query);
$this->stylerow = $this->query->fetch_array();
return $this->stylerow[$val];

}
public function getthemejson($table,$val){
$query = "SELECT * FROM $this->prefix$table WHERE id = '".$val."'";
$query = $this->mycon->query($query);
$res   = $query->fetch_array();
return json_encode(array("id"=>$val,
                         "background"=>$res['background'],
                         "color"     =>$res['color'],
                         "altcolor"  =>$res['altcolor'],
                         "font"      =>$res['font'],
                         "musicimg"  =>$res['musicimg']),JSON_UNESCAPED_UNICODE);
}
public function gettheme($table){
  $outp = '';
  $this->query = "SELECT * FROM $this->prefix$table";
  $this->query = $this->mycon->query($this->query);
  while($this->row = $this->query->fetch_array()){
    //$this->emptyimg = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D';
    if($outp != ''){$outp .= ",";}
    $outp .= '{"back":       "'.$this->row['background'].  '",';
    $outp .= '"color":       "'.$this->row['color'].       '",';
    $outp .= '"altcolor":    "'.$this->row['altcolor'].    '",';
    $outp .= '"font":        "'.$this->row['font'].        '",';
    $outp .= '"name":        "'.$this->row['name'].        '",';
    $outp .= '"id":          "'.$this->row['id'].          '",';
    $outp .= '"musicimg":    "'.$this->row['musicimg'].    '"}';

    
  }
  $outp = '{"record":['.$outp.']}';
  echo ($outp);
}

public function getimgset0($table){

  $outp = "";
  $this->query = "SELECT * FROM $this->prefix$table GROUP BY imgset ";
  $this->query = $this->mycon->query($this->query);
  while($this->row = $this->query->fetch_array()){
    if($outp != ""){$outp .= ",";}
    $outp .= '{"imgset": "'.$this->row['imgset'].'"}';

  }
     $outp = '{"record":['.$outp.']}';

   echo ($outp);
}

public function getimgset($table,$imgset){
  $outp = "";
  $this->query = "SELECT * FROM $this->prefix$table WHERE imgset = '".$imgset."'";
  $this->query = $this->mycon->query($this->query);
  while($this->row = $this->query->fetch_array()){
    if($outp != ""){$outp .= ",";}
    $this->author = explode('|',$this->row["author"]);
    $this->author[0] = !empty($this->author[0]) ? $this->author[0] : "";
    $this->author[1] = !empty($this->author[1]) ? $this->author[1] : "";
    $outp .= '{"author"    :"'.$this->author[0].          '",';
    $outp .= '"authorlink" :"'.$this->author[1].          '",';
    $outp .= '"link"       :"'.$this->row['link'].        '",';
    $outp .= '"imgset"     :"'.$this->row['imgset'].      '",';
    $outp .= '"licenselink":"'.$this->row['licenselink']. '",';
    $outp .= '"license"    :"'.$this->row['license'].     '"}';
  }
  $outp = '{"record":['.$outp.']}';
  echo ($outp);
}
public function returnimagesets($table,$imgset,$num){
  $this->query = "SELECT link,imgset FROM $this->prefix$table WHERE imgset = '".$imgset."'";
  $this->query = $this->mycon->query($this->query);
  while($this->row = $this->query->fetch_array()){
    $imgs[] = $this->row['link'];
  };
return $imgs[$num];

}

public function getlist($table,$row){
  $outp = '';
  $this->query = "SELECT * FROM $this->prefix$table WHERE lang = '".$_POST['lang']."' ORDER BY rand()";
  $this->query = $this->mycon->query($this->query);
  while($this->row = $this->query->fetch_array()){
   if($outp != ''){$outp .= ",";}
   $outp .= '{"row": "'.$this->row[$row].'"}';

    
  }
  $outp = '{"record":['.$outp.']}';
  echo ($outp);
}

public function getaudio($table){
  $i=0;
  $outp="";
  $this->query = "SELECT * FROM $this->prefix$table";
  $this->query = $this->mycon->query($this->query);
  while($this->row = $this->query->fetch_array()){
    $i++;
  if($outp != ''){$outp .=  ",";}
   $outp .= '{"audio":"'     .$this->row["audio"]     .   '",';
   $outp .= '"audioname":"'  .$this->row['audioname'].    '",';
   $outp .= '"license":"'    .$this->row['license'].      '",';
   $outp .= '"author":"'     .$this->row['author'].       '",';
   $outp .= '"number":"'     .$i.                         '"}';
  }
  $outp ='{"record":['.$outp.']}';

echo($outp);
}
public function createitem($table,$table2){
  @session_start();
  $this->host = $_SERVER['HTTP_HOST'];
  $keval  = uniqid();
  $time   = time();
  $link   = isset($_SESSION['id']) ? $this->host_prefix.$this->host."/switem/".$keval."/".$_SESSION['id'] : $this->host_prefix.$this->host."/switem/".$keval;
  $oglink = $this->host_prefix.$this->host."/swcard/".$table."/".$keyval;
  $img    = mysql_escape_string(main::file($table,'img',5048000,array('image/jpeg','image/png'),'/globalimg/item_images/'));

  $this->qmain = "INSERT INTO $this->prefix$table SET
  keyvalue     = '".$keval."',
  account      = '".$_SESSION['id']."',
  img          = '".$img."',
  event        = '".$_POST['title']."',
  description  = '".$this->mycon->real_escape_string($_POST['description'])."',
  style        = '".$this->getthemejson('theme',$_POST['style'])."',
  sender       = '".$this->mycon->real_escape_string($_POST['sender'])."',
  receiver     = '".$this->mycon->real_escape_string($_POST['receiver'])."',
  audio        = '".$_POST['audio']."',
  place        = '".$_POST['place']."',
  social       = '".$_POST['social']."',
  link         = '".$link."',
  proxy        = '".$oglink."',
  date         = '".$_POST['date']."',
  timestamp    = '".$time."'
  ";

  if($this->mycon->query($this->qmain)){
  echo "<a target = '".$oglink."' href   = '".$oglink."' >".$oglink."</a>";
  }

  
	else{
	   return false;  
   }

}

public function file($table,$file,$size,$type,$dir){
      @session_start();
      $dir2  = $dir;
     if(!empty($_FILES[$file]['name'])){
       $uniqname  = uniqid();
       $extension = pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION);
      $dir = '..'.$dir;
     
      $this->folder = $dir.$_SESSION['id'];
      if(file_exists($this->folder)){
        $this->folder = $dir.$_SESSION['id']."/";
      }
      else{
        $this->folder = mkdir($dir.$_SESSION['id'],0777);
        $this->folder = $dir.$_SESSION['id']."/";
      }
     	$this->type = $type;//aray('xx','vv');
      $this->size = $size;
      $fullpath = $this->folder.$uniqname.".".$extension;

      $pathabs   = $_SERVER['SERVER_NAME'].$dir2.$_SESSION['id']."/".$uniqname.".".$extension;
      if(!in_array($_FILES[$file]['type'],$this->type)){
        die('{"status":"0","text":"%action_filetype%"}');
      }
      if($_FILES[$file]['size'] > $this->size){
      die('{"status":"0","text":"%action_size%"}');
}
if(!empty($_FILES[$file]['tmp_name']) && is_uploaded_file($_FILES[$file]['tmp_name']))
           {
  $file   = $this->compress($_FILES[$file]['tmp_name'],$fullpath, 25);
  move_uploaded_file($file,$fullpath);
  return  $fullpath;

           }
 
     	else{echo "Error";}
      }
      else {return "null";}
   }

public function upload_account_image($table){
session_start();
$sesid     = $_SESSION['id'];
$date      = $_POST['date'];
$title     = $_POST['title'];
$upload    = " INSERT INTO $this->prefix$table SET
account    = '".$sesid."',
image      = '".$this->file('null','account_img',5048000,array('image/jpeg','image/png'),'/globalimg/account_images/')."',
title      = '".$title."',
type       = 'common',
date       = '".$date."',
timestamp  = '".time()."'
";
$res = $this->mycon->query($upload);
if($res){
echo '{"status":"1","text":"%action_success%"}';
}
else{
die( '{"status":"0","text":"%action_error%"}');

 }
}


public function upload_account_avatar($table){
session_start();
$sesid     = $_SESSION['id'];
 if(!in_array($_FILES['avatar_img']['type'],$this->validformats))
     {
        die('{"status":"0","text":"%action_filetype%"}');
      }
  if($_FILES['avatar_img']['size'] > $this->maxsize)
     {
      die('{"status":"0","text":"%action_size%"}');
    }
else
   {
array_map('unlink', glob('../globalimg/account_avatar/'.$sesid.'/*'));

$upload    = " UPDATE $this->prefix$table SET
avatar     = '".$this->path_abs($this->file('null','avatar_img',5048000,array('image/jpeg','image/png'),'/globalimg/account_avatar/'))."'
WHERE id = $sesid ";
$res = $this->mycon->query($upload);
if($res){
echo '{"status":"1","text":"%action_success%","protocol" : "'.$_SERVER['REQUEST_SCHEME'].'"}';
}
else{
die( '{"status":"0","text":"%action_error%"}');
//$error = error_get_last();
  // echo $error['message'];
 }

}
}
public function upload_account_canvas($table){
  
session_start();
$sesid     = $_SESSION['id'];
$img       = $_POST['image'];
$date      = $_POST['date'];
$img       = str_replace('data:image/png;base64,', '', $img);
$img       = str_replace(' ', '+', $img);
$fileData  = base64_decode($img);
$fileName  = uniqid().'.png';

$pathabs   = '../globalimg/account_images/'.$_SESSION['id']."/".$fileName;
$dir       = '../globalimg/account_images/';

      $folder = $dir.$_SESSION['id'];
      if(file_exists($folder)){
        $folder = $dir.$_SESSION['id']."/";
      }
      else{
        $folder = mkdir($dir.$_SESSION['id'],0777);
        $folder = $dir.$_SESSION['id']."/";
      }
 $path =  $folder.$fileName;
if(file_put_contents($path, $fileData)){
  $upload    = " INSERT INTO $this->prefix$table SET
  account    = '".$sesid."',
  image      = '".$pathabs."',
  title      = '',
  type       = 'common',
  date       = '".$date."',
  timestamp  = '".time()."'
  ";
$res = $this->mycon->query($upload);
if($res){
echo '{"status":"1","text":"%action_success%"}';
}
else{
die( '{"status":"0","text":"%action_error%"}');

 }
  
}
}

public function save_img($table){
  session_start();
  $sesid     = $_SESSION['id'];
  $date      = $_POST['date'];
  $getimg    = file_get_contents($_POST['img']);
  $fileName  = uniqid().'.png';


  $dir       = '../globalimg/account_saved_images/';

 $folder = $dir.$_SESSION['id'];
      if(file_exists($folder)){
        $folder = $dir.$_SESSION['id']."/";
      }
      else{
        $folder = mkdir($dir.$_SESSION['id'],0777);
        $folder = $dir.$_SESSION['id']."/";
      }
 $path =  $folder.$fileName;
 
if(file_put_contents($path, $getimg)){

$save = " INSERT INTO $this->prefix$table SET 
account    = '".$sesid."',
image      = '".$path."',
title      = '',
type       = 'saved',
date       = '".$date."',
timestamp  = '".time()."'
";
$this->mycon->query($save);
}

}


public function get_account_images($table){
  session_start();
  $sesid        = $_SESSION['id'];
  $limit        = !empty($_POST['limit']) ? $_POST['limit'] : $this->limit;
  $account      = !empty($_POST['sketch']) ? $_POST['sketch'] : $_SESSION['id'];
  $getimg       = " SELECT * FROM $this->prefix$table WHERE account = '".$account."'AND type = 'common' ORDER BY id DESC ";
  $getsavedimg  = " SELECT * FROM $this->prefix$table WHERE account = '".$account."'AND type =  'saved' ORDER BY id DESC ";
  $res          = $this->mycon->query($getimg);
  $res2         = $this->mycon->query($getsavedimg);
  if($res){
    while($param = $res->fetch_array()){
    $date      = json_decode($param['date'],true);
    $comments  = $this->statistic_counter_comments('comments','image',$param['id']);
    $likes     = $this->statistic_counter('likes','image',$param['id']);
    $object_owner = $param["account"] == $sesid ? '1' : '0';
    if($this->outp !== ""){$this->outp .= ",";};
    $this->outp .= '{"id"         :"'.$param['id'].      '",';
    $this->outp .= '"image"       :"'.$this->path_abs($param['image']).   '",';
    $this->outp .= '"imagerel"    :"'.$param['image'].   '",';
    $this->outp .= '"title"       :"'.$param['title'].   '",';
    $this->outp .= '"comments"    :"'.$comments.         '",';
    $this->outp .= '"object_type" :"image"                 ,';
    $this->outp .= '"object_owner":"'.$object_owner.     '",';
    $this->outp .= '"table"       :"account_images"        ,';
    $this->outp .= '"likes"       :"'.$likes.            '",';
    $this->outp .= '"owner_id"    :"'.$param['account']. '",';
    $this->outp .= '"date"        :"'.$date['date'].     '",';
    $this->outp .= '"month"       :"'.$date['month'].    '",';
    $this->outp .= '"year"        :"'.$date['year'].     '",';
    $this->outp .= '"hours"       :"'.$date['hours'].    '",';
    $this->outp .= '"minutes"     :"'.$date['minutes'].  '"}';
    };
if($res2){
    while($param2  = $res2->fetch_array()){
    $date          = json_decode($param2['date'],true);
    $comments      = $this->statistic_counter_comments('comments','image',$param2['id']);
    $likes         = $this->statistic_counter('likes','image',$param2['id']);
    $object_owner2 = $param2["account"] == $sesid ? '1' : '0';
    if($this->outp2 !== ""){$this->outp2 .= ",";};
    $this->outp2 .= '{"id"       :"'.$param2['id'].      '",';
    $this->outp2 .= '"object_type":"image"                 ,';
    $this->outp2 .= '"object_owner":"'.$object_owner2.   '",';
    $this->outp2 .= '"table"      :"account_images"        ,';
    $this->outp2 .= '"image"     :"'.$this->path_abs($param2['image']).   '",';
    $this->outp2 .= '"imagerel"  :"'.$param2['image'].   '",';
    $this->outp2 .= '"title"     :"'.$param2['title'].   '",';
    $this->outp2 .= '"comments"  :"'.$comments.          '",';
    $this->outp2 .= '"likes"     :"'.$likes.             '",';
    $this->outp2 .= '"owner_id"  :"'.$param2['account']. '",';
    $this->outp2 .= '"date"      :"'.$date['date'].      '",';
    $this->outp2 .= '"month"     :"'.$date['month'].     '",';
    $this->outp2 .= '"year"      :"'.$date['year'].      '",';
    $this->outp2 .= '"hours"     :"'.$date['hours'].     '",';
    $this->outp2 .= '"minutes"   :"'.$date['minutes'].   '"}';
    };
}


    $this->outp  = '{"record" : ['.$this->outp.'],"record2" : ['.$this->outp2.'],"object_type" : "image","table":"account_images"}';
    echo $this->outp; 
  }
  else{
    die('%action_error%');
  }
}
public function creategiftcardbs($table,$table2){
  @session_start();
  $keyval           = uniqid();
  $this->timestamp = time();
  $this->host      = $_SERVER['HTTP_HOST'];
  $this->link      = isset($_SESSION['id']) ? $this->host_prefix.$this->host."/swgift/".$keyval."/".$_SESSION['id'] : $this->host_prefix.$this->host."/swgift/".$keyval;
  $this->oglink    = $this->host_prefix.$this->host."/swcard/".$table."/".$keyval."/".$_SESSION['id'];
  //$keval2 = md5(uniqid(rand(),1));
if(!empty($_POST['event'])       AND
   !empty($_POST['sender'])      AND
   !empty($_POST['receiver'])    AND
   !empty($_POST['description']) AND
   !empty($_POST['style'])){
   $this->images = json_encode(array("img1"=>main::returnimagesets('imgsets',$_POST['imgset'],0),
                                     "img2"=>main::returnimagesets('imgsets',$_POST['imgset'],1),
                                     "img3"=>main::returnimagesets('imgsets',$_POST['imgset'],2),
                                     "img4"=>main::returnimagesets('imgsets',$_POST['imgset'],3),
                                     "img5"=>main::returnimagesets('imgsets',$_POST['imgset'],4),
                                     "img6"=>main::returnimagesets('imgsets',$_POST['imgset'],5)),JSON_UNESCAPED_UNICODE);
    
  $this->qmain = "INSERT INTO $this->prefix$table SET
  keyvalue    = '".$keyval.               "',
  account      = '".$_SESSION['id'].      "',
  kind        = '".$_POST['kind'].        "',
  event       = '".$this->mycon->real_escape_string($_POST['event']).       "',
  sender      = '".$this->mycon->real_escape_string($_POST['sender']).      "',
  receiver    = '".$this->mycon->real_escape_string($_POST['receiver']).    "',
  description = '".$this->mycon->real_escape_string($_POST['description']). "',
  style       = '".$this->getthemejson('theme',$_POST['style'])."',
  place       = '".$_POST['place'].       "',
  link        = '".$this->link.           "',
  proxy       = '".$this->oglink.         "',
  social      = '".$_POST['social'].      "', 
  audio       = '".$_POST['audio'].       "',
  img         = '".$this->images.         "',
  date        = '".$_POST['date'].        "',
  timestamp   = '".$this->timestamp.      "'";

  if($this->mycon->query($this->qmain)){
      
 //if(!empty($_POST['email'])){ main::sendtomail($_POST['email'],$this->host."/sc?gc=".$keval);};
  echo $this->oglink;
  
  }
  
	else{
	   return false;
     
   }

}

}

public function createlabel($table){
session_start();
$labelid = $_POST['label'];
$checkmylabel = "SELECT id,account FROM $this->prefix$table WHERE account = ".$_SESSION['id']." AND id = ".$labelid." ";
$checkmylabel = $this->mycon->query($checkmylabel);
$row = $checkmylabel->fetch_array();

if(isset($row['id'])){

$tags = $this->filter1($_POST['tags']);
$placejson = json_encode(array("placename"=>$_POST['placename'],"placeadress"=>$_POST['placeadress'],"placeid"=>$_POST['placeid']),JSON_UNESCAPED_UNICODE);
$query = "UPDATE  $this->prefix$table  SET 
label_name  = '".$this->mycon->real_escape_string($_POST['label_name']).    "',
label_logo  = '".$this->getthemejson('theme',$_POST['label_logo']).         "',
description = '".$this->mycon->real_escape_string(trim($_POST['description'])).   "',
tags        = '".$this->mycon->real_escape_string($tags).  "',
place       = '".$placejson.                                        "',
social      = '".$_POST['social'].                                  "',
date        = '".$_POST['date'].                                    "',
timestamp   = '".time().                                            "'
WHERE id = '".$labelid."' AND account = '".$_SESSION['id'].        "'";
if($this->mycon->query($query)){

echo('{"record":"%action_success%"}');
return true;
}
else{die('{"record":"%action_error%"}');}

   }
   
   else {
      die('{"record":"%action_error%"}');
   
   }
  
}

public function labelactivation($table){
session_start();
$labelid     = $_POST['label'];
$user        = $_SESSION['id'];
$status      = "";
$statusquery = "SELECT * FROM $this->prefix$table WHERE account = '".$user."' AND id  = '".$labelid."' ";
$statusquery = $this->mycon->query($statusquery);
if($statusquery){
$row = $statusquery->fetch_array();
switch($status){
 case $row['status'] == 1 : $status = 1;break;
 case $row['status'] == 0 : $status = 0;break;
}
}
else{
  die('{"record":"%action_error%"}');
}
$query = " UPDATE $this->prefix$table  SET status = '".$status."'  WHERE account = '".$user."' AND id  = '".$labelid."' ";
if($this->mycon->query($query)){
  echo '{"record":"%action_success%"}'; 
}
else{
  die('{"record":"%action_error%"}');
}

}
public function delete_label($table){
session_start();
$label_id = $_POST['labelid'];
$sesid    = $_SESSION['id'];
$delete   = " DELETE FROM $this->prefix$table WHERE id = $label_id AND account = $sesid ";

$delete   = $this->mycon->query($delete);
if($delete)
{
  echo '{"status":"1"}';
}
else
{
  die('{"status":"0"}');
}
}
public function uploadlabelimages($table){
  session_start();
  $label   = trim($_POST['label']);
  $path    = "";
  $times   = 0;

  if(count($_FILES) < 4){
  foreach($_FILES as $index=>$file){

    $folder = "../globalimg/label_images/".$label;
 
      if(file_exists($folder)){
        $path = $folder."/";
      }
      else{
        mkdir($folder,0777);
        $path = $folder."/";
      }

    $filename    = $file['name'];
    $filetype    = $file['type'];
    $filesize    = $file['size'];
    $filetmpname = $file['tmp_name'];

if(!empty($file['error'][$index])){
  return false;
}
if(!in_array($filetype,$this->validformats)){
die('{"record":"%action_filetype%"}');
}
if($filesize > $this->maxsize){
die('{"record":"%action_filesize%"}');
}

if(!empty($filetmpname) && is_uploaded_file($filetmpname)){
 $uniq_name = uniqid();
 $file      = $this->compress($filetmpname, $path.$uniq_name.".".pathinfo($filename, PATHINFO_EXTENSION), 25);
 $dest      = $path.$uniq_name.".".pathinfo($filename, PATHINFO_EXTENSION);
 move_uploaded_file($file,$dest);

$query    = "INSERT INTO $this->prefix$table SET 
label     = '".$_POST['label']."',
account   = '".$_SESSION['id']."',
image     = '".$dest."',
title     = '".$_POST['title']."',
date      = '".$_POST['date']."',
timestamp = '".time()."'
";
if($this->mycon->query($query)){

  if($times == 0){echo '{"record":"%action_success%"}';$times = 1;};
  }else{
    if($times == 0){die('{"record":"%action_error%"}');$times = 1;};
    };

           }
          
       }
  }
  else{die('{"record":"%3files%"}');}
       
}

public function uploadlabelimages_canvas($table){
  
session_start();
$sesid     = $_SESSION['id'];
$img       = $_POST['image'];
$date      = $_POST['date'];
$label     = trim($_POST['label']);
$title     = $_POST['title'];
$img       = str_replace('data:image/png;base64,', '', $img);
$img       = str_replace(' ', '+', $img);
$fileData  = base64_decode($img);
$fileName  = uniqid().'.png';
$folder    = "../globalimg/label_images/".$label;

      if(file_exists($folder)){
        $folder = $folder."/";
      }
      else{
        $folder = mkdir($folder,0777);
        $folder = $folder."/";
      }
 $path =  $folder.$fileName;
if(file_put_contents($path, $fileData)){
  $upload    = " INSERT INTO $this->prefix$table SET
  label      = '".$label."',
  account    = '".$sesid."',
  image      = '".$path."',
  title      = '".$title."',
  date       = '".$date."',
  timestamp  = '".time()."'
  ";
$res = $this->mycon->query($upload);
if($res){
echo '{"status":"1","text":"%action_success%"}';
}
else{
die( '{"status":"0","text":"%action_error%"}');

 }
  
}
}





public function convToUtf8($str) 
{ 
if( mb_detect_encoding($str,"UTF-8, ISO-8859-1, GBK")!="UTF-8" ) 
{ 

return  iconv("gbk","utf-8",$str); 

} 
else 
{ 
return $str; 
} 

}
public function createpost($table){
  session_start();
  $label = $_POST['objectid'];
  $path  = "";
  $times = 0;
  $description = $this->mycon->real_escape_string($_POST['text']);
  if(isset($_SESSION['id'])){
  
$query = "INSERT INTO $this->prefix$table SET 
objectid  = '".$label."',
account   = '".$_SESSION['id']."',
object    = '".$_POST['object']."',
text      = '".$description."',
date      = '".$_POST['date']."',
timestamp = '".time()."'
";
  if(count($_FILES) < 4){
if($this->mycon->query($query)){
   $last_id  = mysqli_insert_id($this->mycon);
  if($times == 0){echo '{"record":"%action_success%","status":"1"}';$times = 1;};
  }else{
    if($times == 0){die('{"record":"%action_error%","status":"0"}');$times = 1;};
    };
    /////////////////////////////////
    

  foreach($_FILES as $index=>$file){

    $folder = "../globalimg/post_images/".$last_id ;
 
      if(file_exists($folder)){
        $path = $folder."/";
      }
      else{
        mkdir($folder,0777);
        $path = $folder."/";
      }
  
    $filename    = iconv("utf-8","cp1251",$file['name']);
    $filetype    = $file['type'];
    $filesize    = $file['size'];
    $filetmpname = $file['tmp_name'];

 
if(!empty($file['error'][$index])){
  return false;
}
if(!in_array($filetype,$this->validformats)){
die('{"record":"%action_filetype%"}');
}
if($filesize > $this->maxsize){
die('{"record":"%action_filesize%"}');
}

if(!empty($filetmpname) && is_uploaded_file($filetmpname)){

 //move_uploaded_file($filetmpname,$path.uniqid().".".pathinfo($filename, PATHINFO_EXTENSION));
  $file            = $this->compress($filetmpname, $path.uniqid().".".pathinfo($filename, PATHINFO_EXTENSION), 25);
  $dest            = $path.uniqid().".".pathinfo($filename, PATHINFO_EXTENSION);
  move_uploaded_file($file,$dest);

           }
          
       }

  }
  else{
    die('{"record":"%3files%"}');
    }
  }
}
function compress($source, $destination, $quality) {

    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg') 
        $image = imagecreatefromjpeg($source);

    elseif ($info['mime'] == 'image/gif') 
        $image = imagecreatefromgif($source);

    elseif ($info['mime'] == 'image/png') 
        $image = imagecreatefrompng($source);

    imagejpeg($image, $destination, $quality);

    return $destination;
}

public function getpost($table,$table2){
$tab1 = $this->prefix.$table;
$tab2 = $this->prefix.$table2;
$private   = $_POST['status'];
$object_id = $_POST['objectid'];
$limit = !empty($_POST['limit']) ? $_POST['limit'] : $this->limit;
$query = "SELECT $tab1.id,$tab1.text,$tab1.date,$tab1.objectid,$tab2.label_name,$tab2.label_logo,$tab2.account,$tab2.id as label_id
FROM $tab1 INNER JOIN $tab2 ON $tab1.objectid = $tab2.id 
WHERE $tab1.objectid = '".$object_id."' ";

$query = $this->mycon->query($query);
while($param = $query->fetch_array()){

     $date               = json_decode($param['date'],true);
     $label_logo         = json_decode($param['label_logo'],true);
     $label_owner_name   = $this->userinfo($param['account'],'firstname')." ".$this->userinfo($param['account'],'lastname');
     $label_owner_avatar = $this->userinfo($param['account'],'avatar');
     $label_owner_id     = $this->userinfo($param['account'],'id');
     $img                = glob('../globalimg/post_images/'.$param['id']. "/*.{jpg,png,JPG,PNG}",GLOB_BRACE );
     $img                = json_encode($this->get_abs_path_array($img));
     $object_owner       =  $param['account'] == $_SESSION['id']  ? '1' : '0';

  if($this->outp2 !== ""){$this->outp2 .= ",";}
     $this->outp2 .= '{"id"               :"'  . $param['id'].                 '",';
     $this->outp2 .= '"owner_id"          :"'   .$label_owner_id.              '",';
     $this->outp2 .= '"object_type"       :"post"                                ,';
     $this->outp2 .= '"object_owner"      : "'  .$object_owner.'"                ,';
     $this->outp2 .= '"labelownername"    :"'   .$label_owner_name.            '",';
     $this->outp2 .= '"labelowneravatar"  :"'   .$label_owner_avatar.          '",';
     $this->outp2 .= '"labelid"           :"'   .$param['label_id'].           '",';
     $this->outp2 .= '"labelname"         :"'   .$param['label_name'].         '",';
     $this->outp2 .= '"images"            :     ['.$img.']                       ,';
     $this->outp2 .= '"labelback"         :"'   .$label_logo['background'].    '",';
     $this->outp2 .= '"labelcolor"        :"'   .$label_logo['color'].         '",';
     $this->outp2 .= '"labelaltcolor"     :"'   .$label_logo['altcolor'].      '",';
     $this->outp2 .= '"labelfont"         :"'   .$label_logo['font'].          '",';
     $this->outp2 .= '"comments_counter"  :"'  . $this->statistic_counter_comments('comments','post',$param['id']). '",';
     $this->outp2 .= '"text"    :"'  . $param['text'].   '",';
     $this->outp2 .= '"hours"   :"'  . $date['hours'].   '",';
     $this->outp2 .= '"minutes" :"'  . $date['minutes']. '",';
     $this->outp2 .= '"date"    :"'  . $date['date'].    '",';
     $this->outp2 .= '"month"   :"'  . $date['month'].   '",';
     $this->outp2 .= '"year"    :"'  . $date['year'].    '"}';
     

}
$this->outp ='{"record" : ['.$this->outp2.']}';
echo($this->outp);

}

public function getpost_feed($table,$table2){ //'friend','timeline'
session_start();
$sesid = $_SESSION['id'];
$tab1  = $this->prefix.$table;
$tab2  = $this->prefix.$table2;

$limit = !empty($_POST['limit']) ? $_POST['limit'] : $this->limit;
$feed = "SELECT 
  $tab2.id,
  $tab2.message,
  $tab2.og_message,
  $tab2.type,
  $tab2.sender,       
  $tab2.receiver, 
  $tab2.object_type,     
  $tab2.path,
  $tab2.date
FROM  $tab1 INNER JOIN $tab2 ON $tab1.friend  = $tab2.receiver WHERE $tab1.account = '".$sesid."' ORDER by $tab2.id DESC LIMIT 10  ";
$feed = $this->mycon->query($feed);
while($row     =  $feed->fetch_array()){
$sendername     =  $this->userinfo($row['sender'],'firstname')." ".$this->userinfo($row['sender'],'lastname');
$date           =  json_decode($row['date'],true);
$date_full      =  $date['date'].".".$date ['month'].".".$date ['year']." ".$date ['hours'].":".$date ['minutes'];
$senderavatar   =  $this->userinfo($row['sender'],'avatar');
$message        =  $this->filter_outp($row['message']);
$postowner      =  $row['sender']   == $_SESSION['id'] ? '1' : '0';
$object_owner   =  $row['sender'] == $_SESSION['id'] ||  $row['receiver'] == $_SESSION['id'] ? '1' : '0';

    $img        = glob($row['path']. "/*.{jpg,png,JPG,PNG}",GLOB_BRACE );
    $audio      = glob($row['path']. "/*.{wav,mp3,webm,ogg}",GLOB_BRACE );
    $imgg       = json_encode($this->get_abs_path_array($img));
    $audioo     = json_encode($this->get_abs_path_array($audio));
    $label_name = $row['object_type'] == 'label_timeline' ? $this->returnparam('label','label_name','id',$row['receiver']) : "";

  if($this->outp !== ""){$this->outp .= ","; }

  $this->outp .= '{ "id"                : "'.$row['id'].' "                         ,';
  $this->outp .= '  "message"           : "'.$message.'"                            ,';
  $this->outp .= '  "sender"            : "'.$this->filter_outp($sendername).'"     ,';
  $this->outp .= '  "object_type_glob"  : "'.$row['object_type'].'"                 ,';
  $this->outp .= '  "object_type"  : "timeline"                                ,';
  $this->outp .= '  "comments_counter"  :"'  . $this->statistic_counter_comments('comments','timeline',$row['id']). '",';
  $this->outp .= '  "label_name"        : "'.$label_name.'"         ,';
  $this->outp .= '  "owner_id"          : "'.$row['receiver'].'"    ,';
  $this->outp .= '  "object_owner"      : "'.$object_owner.'"       ,';
  $this->outp .= '  "senderavatar"      : "'.$senderavatar.'"       ,';
  $this->outp .= '  "postowner"         : "'.$postowner.'"          ,';
  $this->outp .= '  "date"              :"'.$date['date'].        '",';
  $this->outp .= '  "image"             :   ['.$imgg.']             ,';
  $this->outp .= '  "audio"             :   ['.$audioo.']           ,';
  $this->outp .= '  "path"              :"'.$row['path'].'"         ,';
  $this->outp .= '  "month"             :"'.$date['month'].       '",';
  $this->outp .= '  "year"              :"'.$date['year'].        '",';
  $this->outp .= '  "hours"             :"'.$date['hours'].       '",';
  $this->outp .= '  "minutes"           :"'.$date['minutes'].     '",';
  $this->outp .= '  "type"              :"'.$row['type'].         '",';
  $this->outp .= '  "og_params"         :['.$row['og_message'].']   }';
 
 }
   $this->outp = '{"record":['.$this->outp.']}';
     
   echo $this->outp;
   
}
public function getobjectimages(){
  
$post_id = $_POST['post'];
$path    = $_POST['path'];
$limit   = $_POST['limit'];
$dir = "../".$path.$post_id."/";
$images_unlimited = glob($dir . "*.{jpg,png}",GLOB_BRACE );
$images_limited   = array_slice(glob($dir . "*.{jpg,png}",GLOB_BRACE ),0,8);
$images = $limit == "nolimit" ? $images_unlimited : $images_limited;
for($i=0;$i < count($images);$i++)
{
  if($this->outp !== ""){$this->outp .= ",";}
     $this->outp  .= '{"id"    :"'.$i.         '",';
     $this->outp  .= '"image":"'.$images[$i]. '"}';
}
$this->outp ='{"record" : ['.$this->outp.']}';
echo($this->outp);
}

public function labelimages($table,$status){
session_start();
$sesid   = $_SESSION['id'];
$labelid = $_POST['label'];
 
$query = " SELECT * FROM $this->prefix$table WHERE label = '".$labelid."' ";
if($status !== 'null'){
  
  $query .= " AND account = '".$sesid."' ";
}
$query = $this->mycon->query($query);
if($query){

 while($row = $query->fetch_array()){
    $date         = json_decode($row['date'],true);
    $comments     = $this->statistic_counter_comments('comments','labelimage',$row['id']);
    $likes        = $this->statistic_counter('likes','labelimage',$row['id']);
    $object_owner = $row["account"] == $sesid ? '1' : '0';
   if($this->outp !== ""){$this->outp .= ",";}
    $this->outp .= '{"id"         :"'      . $row["id"].         '",';
    $this->outp .= '"owner_id"    :"'      . $row["account"].    '",';
    $this->outp .= '"object_type" :           "labelimage"         ,';
    $this->outp .= '"object_owner":           "'.$object_owner.'"  ,';
    $this->outp .= '"table"       :"labelimages"                   ,';
    $this->outp .= '"image"       :"'      . $this->path_abs($row["image"]).      '",';
    $this->outp .= '"imagerel"    :"'      . $row["image"].      '",';
    $this->outp .= '"title"       :"'      . $row["title"].      '",';
    $this->outp .= '"comments"    :"'      . $comments.          '",';
    $this->outp .= '"title"       :"'      . $row["title"].      '",';
    $this->outp .= '"year"        :"'      . $date["year"].      '",';
    $this->outp .= '"month"       :"'      . $date["month"].     '",';
    $this->outp .= '"date"        :"'      . $date["date"].      '"}';
 }

$this->outp ='{"record":['.$this->outp.'],"object_type" : "labelimage"}';
echo($this->outp);
}
else {die("Error Occured");}

}

function sendtomail($email,$message){
  if(!empty($email)){
  $strEmail = $email;
	$strSubject ='Your SweetCard';
	$strMessage = "Your SweetCard  ".$this->filter_inp($message);
  $headers = 'From:sweetvel@sweetvel.com' . "\r\n" .
	            'Reply-To: no-reply@sweetvel.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
	mail($strEmail,$strSubject,$strMessage,$headers);
}
else{return false;}
}
function comment($table){
  if($_POST['com_name'] !== '' AND $_POST['com_email'] !== '' AND $_POST['com_comment'] !== ''){
  $this->query = "INSERT INTO $this->prefix$table SET
   name       = '".$_POST['com_name']."',
   email      = '".$_POST['com_email']."',
   comment    = '".$_POST['com_comment']."',
   date       = '".$_POST['time']."',
   timestamp  = '".time()."'
   ";
   if(preg_match('/@/',$_POST['com_email'])){
   $this->mycon->query($this->query);
   echo('{"record": "%sended%"}');
  }
  else {die('{"record": "%action_email%"}');}
 }
  else {die('{"record": "%action_fields%"}');}

}

public function counter_tags($table,$val){
    $query      = " SELECT COUNT(*) AS tags_counter FROM $this->prefix$table WHERE LOWER(tags) REGEXP LOWER('".$val."') AND status = 1 ";
    $tag_counter = $this->mycon->query($query);
    $rower = $tag_counter->fetch_array();
    $numrows = $rower['tags_counter'];
    return $numrows;
}
	public function search($table,$table2){//label,account
    session_start();
    $sesid    = $_SESSION['id'];
    $tagser   = "" OR [];
    $tab1     = $this->prefix.$table;
    $tab2     = $this->prefix.$table2;
    $limit    = !empty($_POST['limit']) ? $_POST['limit'] : $this->limit;
    $searcher = !empty($_POST['search']) ? $this->mycon->real_escape_string(trim($_POST['search'])) : 'sweetvel';
    $input    = addslashes(trim($_POST['search']));
    $query      = " SELECT $tab1.id,
                           $tab1.label_name,
                           $tab1.label_logo,
                           $tab1.description,
                           $tab1.date,
                           $tab1.tags,
                           $tab2.id as ownerid,
                           $tab2.firstname,
                           $tab2.lastname,
                           $tab2.avatar
      FROM $tab1 INNER JOIN $tab2 ON $this->prefix$table.account = $this->prefix$table2.id WHERE
     (LOWER(tags) LIKE LOWER('%".$searcher."%') OR LOWER(REPLACE( $this->prefix$table.label_name , ' ' , '' )) 
     REGEXP LOWER(REPLACE( '".$searcher."' , ' ' , '' ))) AND $this->prefix$table.status = 1  LIMIT $limit ";
    $search = $this->mycon->query($query);
    
     $search_person = " SELECT * FROM $tab2 WHERE  
     (
     (LOWER(firstname) LIKE LOWER('%".trim($searcher)."%') OR
     LOWER(lastname)  LIKE LOWER('%".trim($searcher)."%') 
     )
     OR
     (
      CONCAT_WS('',LOWER(firstname),LOWER(lastname))   LIKE LOWER('%".trim($searcher)."%')
    OR 
      CONCAT_WS('',LOWER(lastname) ,LOWER(firstname))  LIKE LOWER('%".trim($searcher)."%') 
     )
     ) AND active = 1  LIMIT $limit ";

     $search_person = $this->mycon->query($search_person);

    while($row_person = $search_person->fetch_array()){
      if($this->outp3 !== ""){$this->outp3 .= ",";};
      $page = $row_person['id'] == $sesid ? 'account' : 'sketch/'.$row_person['id'];
      $this->outp3 .= '{"id"             :"'.$row_person["id"].                '",';
      $this->outp3 .= '"fname"           :"'.$row_person["firstname"].         '",';
      $this->outp3 .= '"lname"           :"'.$row_person["lastname"].          '",';
      $this->outp3 .= '"avatar"          :"'.$row_person["avatar"].            '",';
      
      $this->outp3 .= '"status_account"  :"'.$row_person["status_account"].    '",';
      $this->outp3 .= '"page"            :"'.$page.                            '"}';
    }
    
    while($row = $search->fetch_array()){
      $nums     = count($row['id']);
      
      $logo     = json_decode($row['label_logo'],true);
      $date     = json_decode($row['date'],true); 
      
      $likes     = $this->statistic_counter('likes','label',$row['id']);
      $views     = $this->statistic_counter('views','label',$row['id']);
      $followers = $this->statistic_counter_followers('follow',$row['id']);
      $rating    = $likes / $this->rateval1 + $views / $this->rateval2;
      $rating    = round($rating,2);
      
      if($this->outp !== ""){$this->outp .= ",";};
      $this->outp .= '{"id"          :"'.$row["id"].                '",';
      $this->outp .= '"likes"        :"'.$likes.                    '",';
      $this->outp .= '"views"        :"'.$views.                    '",';
      $this->outp .= '"followers"    :"'.$followers.                '",';
      $this->outp .= '"rating"       :"'.$rating.                   '",';
      $this->outp .= '"labelname"    :"'.$row["label_name"].        '",';
      $this->outp .= '"object_type"  :"label"                         ,';
      $this->outp .= '"ownerid"      :"'.$row["ownerid"].           '",';
      $this->outp .= '"owner_id"     :"'.$row["ownerid"].           '",';
      $this->outp .= '"ownerfname"   :"'.$row["firstname"].         '",';
      $this->outp .= '"ownerlname"   :"'.$row["lastname"].          '",';
      $this->outp .= '"owneravatar"  :"'.$row["avatar"].            '",';
      $this->outp .= '"logoback"     :"'.$logo['background'].       '",';
      $this->outp .= '"logocolor"    :"'.$logo['color'].            '",';
      $this->outp .= '"logoaltcolor" :"'.$logo['altcolor'].         '",';
      $this->outp .= '"logofont"     :"'.$logo['font'].             '",';
      $this->outp .= '"date"         :"'.$date['date'].             '",';
      $this->outp .= '"month"        :"'.$date['month'].            '",';
      $this->outp .= '"year"         :"'.$date['year'].             '",';
      $this->outp .= '"hours"        :"'.$date['hours'].            '",';
      $this->outp .= '"minutes"      :"'.$date['minutes'].          '",';
      $this->outp .= '"description"  :"'.$row["description"].       '"}';

      $tagser  .= htmlspecialchars($row['tags'],ENT_QUOTES);
      $tags     = array_unique(explode('#',$tagser));
    }
      $tags     = isset($tags) ? $tags : ['sweetvel'];
      $matches  = preg_grep('/^'.$_POST['search'].'\w+/i', $tags);
      foreach($matches as $mt){
      if($this->outp2 !== ""){$this->outp2 .= ",";};
      $this->outp2 .= '{"tags"    : "'.$mt.    '",';
      $this->outp2 .= '"nums"     : "'. $this->counter_tags($table,$mt). '"}';
 }

    $this->outp ='{"record":['.$this->outp.'],"record2":['.$this->outp2.'],"record3":['.$this->outp3.']}';
    echo $this->outp;
  }


public function search_contact($table){
  session_start();
 $sesid = $_SESSION['id'];
 $searcher = $this->mycon->real_escape_string($_POST['search']);
 $query =  "SELECT id,firstname,lastname,avatar,privacy FROM $this->prefix$table WHERE  
(
(LOWER(firstname) REGEXP LOWER('%".$searcher."%')                                    OR
 LOWER(lastname)  REGEXP LOWER(REPLACE( '".$searcher."' , ' ' , '' )))               OR
(LOWER(lastname)  REGEXP LOWER('%".$searcher."%')                                    OR
 LOWER(firstname) REGEXP LOWER(REPLACE( '".$searcher."' , ' ' , '' )))               OR
 CONCAT_WS('',LOWER(firstname),LOWER(lastname))  LIKE LOWER('%".trim($searcher)."%') OR
 CONCAT_WS('',LOWER(lastname ),LOWER(firstname)) LIKE LOWER('%".trim($searcher)."%') 
) 
  AND  privacy = 0  AND active = 1  AND id <> '".$sesid."'    ";
  $search = $this->mycon->query($query);
  while($row = $search->fetch_array()){
   if($this->outp !== ""){$this->outp .= "," ;}

   $this->outp .= '{ "id"        : "'.$row['id'].'"         ,';
   $this->outp .= '  "firstname" : "'.$row['firstname'].'"  ,';
   $this->outp .= '  "lastname"  : "'.$row['lastname'].'"   ,';
   $this->outp .= '  "avatar"    : "'.$row['avatar'].'"     }';

  }
  $this->outp = '{"record":['.$this->outp.']}';
  echo $this->outp;
}

public function expired($table){
	$this->query = "DELETE  FROM $this->prefix$table
	WHERE  '".time()."' > timestamp + 60 * 60 * 24 * 7 ";
	if($this->mycon->query($this->query)){echo "Deleted";}
	else{echo "Not Deleted";};
}
public function metrics($table){
  $this->time    = $_POST['time'];
  $this->device  = $_POST['device'];
  $this->coord   = $_POST['coord'];
  $this->referer = $_POST['referer'];
  $this->date    = date("Y-m-d");

  $this->exists = "SELECT ip,views,serverdate,coord FROM $this->prefix$table WHERE 
  ip = '".$_SERVER['REMOTE_ADDR']."' AND serverdate = '".$this->date."'";
	
  $this->exists = $this->mycon->query($this->exists);
  $this->rowexists = $this->exists->fetch_array();
  $this->serverdate = $this->rowexists['serverdate'];
  $this->nums = $this->exists->num_rows;
  if(empty($this->rowexists['ip'])){
  $this->query = "INSERT INTO $this->prefix$table SET
  ip         = '".$_SERVER['REMOTE_ADDR']."',
  device     = '".$this->device."',
  views      = '1',
  coord      = '".$this->coord."',
  referer    = '".$this->referer."',
  time       = '".$this->time."',
  serverdate = '".$this->date."',
  timestamp  = '".time()."'
  ";
  $this->query = $this->mycon->query($this->query);
}
else{
	if($this->serverdate == $this->date){
  $this->views = $this->rowexists['views'] + 1;
  $this->update = "UPDATE $this->prefix$table  SET
  views       = '".$this->views."',
  device      = '".$this->device."',
  referer    = '".$this->referer."',
  coord      = '".$this->coord."',
  time        = '".$this->time."'
  WHERE ip    = '".$_SERVER['REMOTE_ADDR']."' AND serverdate = '".$this->date."' ";
  $this->queryup = $this->mycon->query($this->update);
	}
	else{
		$this->query2 = "INSERT INTO $this->prefix$table SET
  ip         = '".$_SERVER['REMOTE_ADDR']."',
  device     = '".$this->device."',
  views      = '1',
  coord      = '".$this->coord."',
  referer    = '".$this->referer."',
  time       = '".$this->time."',
  serverdate = '".$this->date."',
  timestamp  = '".time()."'
  ";
  $this->query2 = $this->mycon->query($this->query2);
		
     }
   }
}
public function getmetrics($table){

    $outp = "";

  $this->commonquan         = "SELECT SUM(views) AS totalviews FROM $this->prefix$table";
  $this->commonquan         = $this->mycon->query($this->commonquan);
  $this->commonquanrow      = $this->commonquan->fetch_array();//Sum of views Total

  $this->querydayviews      = "SELECT SUM(views) AS totalviews FROM $this->prefix$table WHERE serverdate = '".date("Y-m-d")."'";//минуты секунды часы
  $this->querydayviews      = $this->mycon->query($this->querydayviews);
  $this->querydayviewsrow   = $this->querydayviews->fetch_array();//Sum of views Day

  $this->queryweekviews     = "SELECT SUM(views) AS totalviews FROM $this->prefix$table WHERE '".time()."' < timestamp + 60 * 60 * 24 * 7";//минуты секунды часы
  $this->queryweekviews     = $this->mycon->query($this->queryweekviews);
  $this->queryweekviewsrow  = $this->queryweekviews->fetch_array();//Sum of views Day

  $this->querymonthviews     = "SELECT SUM(views) AS totalviews FROM $this->prefix$table WHERE '".time()."' < timestamp + 60 * 60 * 24 * 30";//минуты секунды часы дни
  $this->querymonthviews     = $this->mycon->query($this->querymonthviews);
  $this->querymonthviewsrow  = $this->querymonthviews->fetch_array();//Sum of views Month(30days)

  $this->querycommon    = "SELECT * FROM $this->prefix$table";
  $this->queryday       = "SELECT * FROM $this->prefix$table  WHERE serverdate = '".date("Y-m-d")."'";//минуты секунды часы
  $this->queryweek      = "SELECT * FROM $this->prefix$table  WHERE '".time()."' < timestamp + 60 * 60 * 24 * 7";
  $this->querymonth     = "SELECT * FROM $this->prefix$table  WHERE '".time()."' < timestamp + 60 * 60 * 24 * 30";

  $this->querycommon    = $this->mycon->query($this->querycommon);
  $this->commonnums     = $this->querycommon->num_rows; //Common views All

  $this->queryday       = $this->mycon->query($this->queryday);
  $this->daynums        = $this->queryday ->num_rows;   //Common views Day

  $this->queryweek      = $this->mycon->query($this->queryweek);
  $this->weeknums       = $this->queryweek->num_rows;    //Common views Week

  $this->querymonth     = $this->mycon->query($this->querymonth);
  $this->monthnums      = $this->querymonth->num_rows;    //Common views Month(30days)


  if($outp != ''){$outp .=  ",";}
   $outp .= '{"totalviews"        :"'     .$this->commonquanrow['totalviews'].         '",';
   $outp .= '"totalviewsunique"         :"'     .$this->commonnums.                          '",';
   $outp .= '"totalday"      :"'     .$this->querydayviewsrow['totalviews'].      '",';
   $outp .= '"totaldayunique"            :"'     .$this->daynums.                             '",';
   $outp .= '"totalweek"  :"'     .$this->queryweekviewsrow['totalviews'].     '",';
   $outp .= '"totalweekunique"           :"'     .$this->weeknums.                            '",';
   $outp .= '"totalmonth" :"'     .$this->querymonthviewsrow['totalviews'].    '",';
   $outp .= '"totalmonthunique"          :"'     .$this->monthnums.       '"}';
  
  $outp ='{"record":['.$outp.']}';

  echo($outp);

}

public function feed($table){
//header("Access-Control-Allow-Origin: *");
//header("Content-type: application/json");
  $outp = "";
  $this->query = "SELECT * FROM $this->prefix$table WHERE lang = '".$_POST['lang']."'";
  if($_POST['pointdate'] !== "null"){$this->query .= " AND pointdate = '".$_POST['pointdate']."'"; }
  if($_POST['id']        !== "null"){$this->query .= " AND id  = '".$_POST['id']."'"; }
  $this->query .= " ORDER by id DESC ";

  $this->query = $this->mycon->query($this->query);
  $this->nums =  $this->query->num_rows;
  while($this->row = $this->query->fetch_array()){
    $this->author = explode('|',$this->row["author"]);
    $this->author[0] = !empty($this->author[0]) ? $this->author[0] : "";
    $this->author[1] = !empty($this->author[1]) ? $this->author[1] : "";

  if ($outp != "") {$outp .= ",";}
    $outp .= '{"title":"'      . $this->row["title"]    .   '",';
    $outp .= '"alttitle":"'    . $this->row["alttitle"].    '",';
    $outp .= '"desc":"'        . $this->row["description"]. '",';
    $outp .= '"pointdate":"   '. $this->row["pointdate"].   '",';
    $outp .= '"cols":"'        . $this->nums. '"              ,';
    $outp .= '"license":"'     . $this->row["license"].     '",';
    $outp .= '"licenselink":"' . $this->row["licenselink"]. '",';
    $outp .= '"author":"'      . $this->author[0].          '",';
    $outp .= '"authorlink":"'  . $this->author[1].          '",';
    $outp .= '"id":"'          . $this->row["id"].          '",';
    $outp .= '"img":"'         . $this->row["img"] .        '"}';
    
}
$outp ='{"record":['.$outp.']}';

echo($outp);

}

public function views($table){
  session_start();
  $sesid = "";
  $label = $_POST['label'];
  $kind  = $_POST['kind'];
 if(!empty($_SESSION['id'])){
  $sesid = $_SESSION['id'];
  $check = "SELECT * FROM $this->prefix$table WHERE object_id = '".$label."' AND account = '".$sesid."' AND object_type = '".$kind."' ";
  $check = $this->mycon->query($check)->fetch_array();
  if(!empty($check['account'])){
    $counter = $check['counter'] + 1;
    $query = " UPDATE $this->prefix$table SET
  account     = '".$sesid."',
  counter     = '".$counter."',
  object_id   = '".$label."',
  object_type = '".$kind."'  ,
  date        = '".$_POST['date']."' ,
  time        = '".time()."'
  WHERE object_id = '".$label."' AND account = '".$sesid."' AND object_type = '".$kind."' ";
  $this->mycon->query($query);
  }
  else{
  $query = " INSERT INTO $this->prefix$table SET
  account     = '".$sesid."',
  counter     = '1',
  object_id   = '".$label."',
  object_type = '".$kind."'  ,
  date        = '".$_POST['date']."',
  time        = '".time()."'
  ";
  $this->mycon->query($query);
  }
 }
  $countviews = "SELECT  SUM(counter) AS countviews FROM $this->prefix$table WHERE object_id = ".$label." AND object_type = '".$kind."' ";
  $countviews = $this->mycon->query($countviews)->fetch_array();
   echo '{"views":"'.$countviews['countviews'].'"}';

}

public function likes($table){
  session_start();
  
  $sesid            = "";
  $objectid         = $_POST['object_id'];
  $objecttype       = $this->mycon->real_escape_string($_POST['object_type']);
  $objectowner      = $_POST['account_receiver'];
  if(!empty($_SESSION['id'])){
  $sesid            = $_SESSION['id'];
  $check = "SELECT * FROM $this->prefix$table WHERE object_id = '".$objectid."' AND account = '".$sesid."' AND object_type = '".$objecttype."' ";
  $check = $this->mycon->query($check)->fetch_array();
  if(!empty($check['account'])){
  $this->activity_add($objectowner,$objectid,$objecttype,'like','undo');
  $query = " DELETE FROM $this->prefix$table WHERE  object_id = '".$objectid."' AND account = ".$sesid." AND object_type = '".$objecttype."' ";
  $this->mycon->query($query);
  echo '{"statuslike":"-1"}';
  
  }
  else{
        
  $query = " INSERT INTO $this->prefix$table SET
  account      = '".$sesid."',
  object_owner = '".$objectowner."',
  counter      = '1',
  object_type  = '".$objecttype."',
  object_id    = '".$objectid."',
  date         = '".$_POST['date']."',
  time         = '".time()."'
  ";
  $this->mycon->query($query);
  $this->activity_add($objectowner,$objectid,$objecttype,'like','do');

  echo '{"statuslike":"1"}';

  }
  }
}

public function likestatus($table){
session_start();
  $sesid      = $_SESSION['id'];
  $objectid   = $_POST['object_id'];
  $objecttype = $this->mycon->real_escape_string($_POST['object_type']);
  
  $check = "SELECT * FROM $this->prefix$table WHERE object_id = '".$objectid."' AND account = '".$sesid."' AND object_type = '".$objecttype."' ";
  $countlikes = "SELECT COUNT(*) as countlikes FROM $this->prefix$table WHERE object_id = '".$objectid."'  AND object_type = '".$objecttype."' ";
  $check      = $this->mycon->query($check)->fetch_array();
  $countlikes = $this->mycon->query($countlikes)->fetch_array();
  if(!empty($check['account'])){
  
  echo '{"likestatus":"1","likes_count":"'.$countlikes['countlikes'].'"}';
}
else{
  echo '{"likestatus":"0","likes_count":"'.$countlikes['countlikes'].'"}';

}

}

public function messenger($table){
session_start();
$sesid = $_SESSION['id'];
$query = "SELECT * FROM $this->prefix$table 
WHERE id IN  (SELECT MAX(id) FROM $this->prefix$table WHERE sender='". $sesid ."' OR receiver='". $sesid ."'
GROUP BY least(receiver ,  sender), greatest(receiver ,  sender) )
ORDER BY id DESC";
// AND id = (SELECT  MAX(id) FROM $this->prefix$table )
 $query = $this->mycon->query($query);
 while($row = $query->fetch_array()){
   $date           =  json_decode($row['date'],true);
   $date_full      =  $date['date'].".".$date ['month'].".".$date ['year'];
   $sendername     =  $this->userinfo($row['sender'],'firstname');
   $senderlastname =  $this->userinfo($row['sender'],'lastname');
   $receivername   =  $this->userinfo($row['receiver'],'firstname');
   $senderavatar   =  $this->active_avatar($this->userinfo($row['sender'],'active'),$this->userinfo($row['sender'],'avatar'));
   $receiveravatar =  $this->active_avatar($this->userinfo($row['receiver'],'active'),$this->userinfo($row['receiver'],'avatar'));
   $opponentid     =  $row['sender'] !== $sesid ? $row['sender'] : $row['receiver'];

   $message        =  $row['message'] !== 'undefined' ? $this->filter_inp($row['message']) : "";
  if($this->outp !== ""){$this->outp .= ","; }

  $this->outp .= '{ "id"               : "'.$row['id'].' "         ,';
  $this->outp .= '  "opponentid"       : "'.$opponentid.'"         ,';
  $this->outp .= '  "sendername"       : "'.$sendername.'"         ,';
  $this->outp .= '  "senderlastname"   : "'.$senderlastname.'"     ,';
  $this->outp .= '  "receivername"     : "'.$receivername.'"       ,';
  $this->outp .= '  "senderavatar"     : "'.$senderavatar.'"       ,';
  $this->outp .= '  "receiveravatar"   : "'.$receiveravatar.'"     ,';
  $this->outp .= '  "message"          : "'.$message.'"            ,';
  $this->outp .= '  "status"           : "'.$row['status'].' "     ,';
  $this->outp .= '  "receiverid"       : "'.$row['receiver'].' "   ,';
  $this->outp .= '  "senderid"         : "'.$row['sender'].' "     ,';
   $this->outp .= ' "date"             :"'.$date['date'].        '",';
  $this->outp .= '  "month"            :"'.$date['month'].       '",';
  $this->outp .= '  "year"             :"'.$date['year'].        '",';
  $this->outp .= '  "hours"            :"'.$date['hours'].       '",';
  $this->outp .= '  "minutes"          :"'.$date['minutes'].     '",';
  $this->outp .= '  "datefull"         : "'.$date_full.' "         }';
  
 }
   $this->outp = '{"record":['.$this->outp.']}';
   echo $this->outp;
}


public function chat($table){
session_start();

$sesid   = $_SESSION['id'];
$id      = $_POST['id'];
$limiter = isset($_POST['limit']) ? $_POST['limit'] : $this->limit;

$query     = "SELECT * FROM( SELECT * FROM  $this->prefix$table WHERE  
(receiver='". $sesid ."' AND sender='".$id ."') OR (sender='". $sesid ."' AND receiver='". $id ."') ORDER BY id DESC LIMIT ".$limiter."  ) as source ORDER by id ASC ";

$queryread = "UPDATE $this->prefix$table SET status = '1' WHERE  
receiver='". $sesid ."' AND sender='". $id ."'  AND status = '0' ";
 $queryread     =  $this->mycon->query($queryread);
 $query         =  $this->mycon->query($query);
 while($row     =  $query->fetch_array()){
$sendername     =  $this->userinfo($row['sender'],'firstname')." ".$this->userinfo($row['sender'],'lastname');
$date           =  json_decode($row['date'],true);
$date_full      =  $date['date'].".".$date ['month'].".".$date ['year']." ".$date ['hours'].":".$date ['minutes'];
$senderavatar   =  $this->active_avatar($this->userinfo($row['sender'],'active'),$this->userinfo($row['sender'],'avatar'));
$message        =  $this->filter_outp($row['message']);
$whosend        = $row['sender'] == $sesid ? 0 : 1;

//OG_PARAMS

    $img    = glob($row['path']. "/*.{jpg,png,JPG,PNG}",GLOB_BRACE );
    $audio  = glob($row['path']. "/*.{wav,mp3,webm,ogg}",GLOB_BRACE );

    $imgg   = json_encode($this->get_abs_path_array($img));
    $audioo = json_encode($this->get_abs_path_array($audio));

  if($this->outp !== ""){$this->outp .= ","; }

  $this->outp .= '{ "id"               : "'.$row['id'].' "                         ,';
  $this->outp .= '  "message"          : "'.$message.'"                            ,';
  $this->outp .= '  "status"           : "'.$row['status'] .'"                     ,';
  $this->outp .= '  "sender"           : "'.$this->filter_outp($sendername).'"     ,';
  $this->outp .= '  "senderid"         : "'.$row['sender'].'"      ,';
  $this->outp .= '  "senderavatar"     : "'.$senderavatar.'"       ,';
  $this->outp .= '  "whosend"          :"'.$whosend.             '",';
  $this->outp .= '  "date"             :"'.$date['date'].        '",';
  $this->outp .= '  "image"            :   ['.$imgg.']             ,';
  $this->outp .= '  "audio"            :   ['.$audioo.']           ,';
  $this->outp .= '  "path"             :"'.$row['path'].'"         ,';
  $this->outp .= '  "month"            :"'.$date['month'].       '",';
  $this->outp .= '  "year"             :"'.$date['year'].        '",';
  $this->outp .= '  "hours"            :"'.$date['hours'].       '",';
  $this->outp .= '  "minutes"          :"'.$date['minutes'].     '",';
  $this->outp .= '  "type"             :"'.$row['type'].         '",';
  $this->outp .= '  "og_params"        :['.$row['og_message'].']   }';



 
 }
   $this->outp = '{"record":['.$this->outp.']}';
     
   echo $this->outp;
   
}


public function my_unreaded_messages($table){
session_start();
$sesid    = $_SESSION['id'];
$unread   = " SELECT COUNT(*) as unread  FROM $this->prefix$table WHERE  receiver ='".$sesid."'  AND status = 0 ";
$row      = $this->mycon->query($unread)->fetch_array();
echo $row['unread'];
}

public function sendmessage_preload(){
  
  session_start();
      $extension = "";
      $path  = "";
      $type  = $this->validformats;
      $size  = $this->maxsize;
     
     if(count($_FILES) < 4){

       if(count($_FILES) > 0)
       {
       foreach($_FILES as $index=>$file)
       {
      $uniqimgname  = uniqid();
      $filename     = $file['name'];
      $filetype     = $file['type'];
      $filesize     = $file['size'];
      $filetmpname  = $file['tmp_name'];
      if(!empty($file['error'][$index]))
      {
      return false;
      }
      if(!in_array($filetype,$type)){
        die('{"status":"0","text":"%action_filetype%"}');
      }
      if($filesize > $size){
      die('{"status":"0","text":"%action_size%"}');
}
if(!empty($filetmpname) && is_uploaded_file($filetmpname))
{
$tmpfname = tempnam("../globalimg/tmp", "sw");
$handle   = fopen($tmpfname, "w");
$file_f   = $filetype == 'audio/wav' ? $filetmpname : $this->compress($filetmpname,$tmpfname,25);
$tmppath  = fwrite( $handle, file_get_contents($file_f) );
$tmppath  = fclose($handle);
$img[]    = "../globalimg/tmp/".pathinfo($tmpfname,PATHINFO_BASENAME);
      }
     }
   }
foreach($img as $imgres)
{
  if($this->outp !== ""){$this->outp .= ",";}
  $this->outp .= '{"file":"'.$this->path_abs($imgres).'","filerel" : "'.$imgres.'"}';
}
echo '{"files" : ['.$this->outp.'] }';
}
}

public function sendmessage_preload_finish($table){
       session_start();
       $dir            = "";
       $type           = $_POST['type'];
       $message_status = !empty($_POST['status_message']) ? $_POST['status_message'] : 0;
       if(isset($_POST['files']))
       {
       $files       = $_POST['files'];
       $uniqdir     = uniqid();
       $dir         = '../globalimg/msg/'.$_SESSION['id'].'/'.$uniqdir;
       foreach($files as $loaded)
       {
         if(file_exists($loaded))
         {
       $folder      = @mkdir($dir,0777,true);
       $folder      = $dir.'/';
       $extension = $type == 'audio'? 'wav' : 'png';
       copy($loaded,$folder.uniqid().".".$extension);
       //unlink($loaded);
         }
         else
         {
        die('{"status":"0","text":"%file_expired%"}');
         }
       }
       }
$sesid     = $_SESSION['id'];
$receiver  = $_POST['receiver'] / $this->hash;
$message   = $this->filter_inp($_POST['message']);
$date      = $_POST['date'];
$query     = " INSERT INTO $this->prefix$table SET
sender     = '".$sesid."',
receiver   = '".$receiver."',
message    = '".$message."',
og_message = 'null',
type       = '".$type."',
path       = '".$dir."',
status     = '".$message_status."',
date       = '".$date ."',
time       = '".time()."'
";
if( !empty($sesid) ){
 $query = $this->mycon->query($query);
 if($query){
   echo '{"status":"1","record": "%action_success%"}';
 }
 else{
   die ('{"status":"1","record": "%action_error%"}');
 }
}
  else
  {
   die('{"status":"0","text":"%3files%"}');
  }

}

public function delete_preloaded_image()
{
    $file  = $_POST['file'];
    if(file_exists($file))
    {
    unlink($file);
    echo '{"status":"1"}';
    }
    else
     {
     die('{"status":"0"}');
    }
}

public function delete_preloaded_image_alt()
{
  $file  = $_POST['file'];
  $dir = glob(dirname($file). "/*");
  if(count($dir) < 2)
  {
    $this->deleteDir(dirname($file));
    echo '{"status":"1","dir":"'.count($dir).'","lastfile" : "1"}';
  }
  else
  {
    if($file)
    {
    unlink($file);
    echo '{"status":"1","dir":"'.count($dir).'","lastfile" : "0"}';
    }
  }
}



public function sendmessage_og($table){
 session_start();

 $uniqdir     = uniqid();
 $uniqfile    = uniqid();
 $dir         = '../globalimg/msg/'.$_SESSION['id'].'/'.$uniqdir;
 $folder      = mkdir($dir,0777,true);
 $file        = file_get_contents($_POST['image']);
 $path        = $dir.'/'.$uniqfile.'.png';
 
file_put_contents($path, $file);

$sesid    = $_SESSION['id'];
$receiver = $_POST['receiver'] / $this->hash;
$date     = $_POST['date'];

////////OG_VARS///////
$title    = $_POST['title'];
$desc     = $_POST['desc'];
$og_desc  = $_POST['og_desc'];
$og_imgc  = $_POST['og_desc'];
$url      = $_POST['url'];
$video    = $_POST['video'];
$message  = json_encode(array('title' =>$this->filter_inp($title),
                              'desc'  =>$this->filter_inp($og_desc),
                              'url'   =>$this->filter_inp($url),
                              'video' =>$this->filter_inp($video),
                              'image' =>$this->filter_inp($this->path_abs($path))
                                      ),JSON_UNESCAPED_UNICODE);
$query     = " INSERT INTO $this->prefix$table SET
sender     = '".$sesid."',
receiver   = '".$receiver."',
og_message = '".$message."',
message    = '".$desc."',
type       = 'link',
path       = '".$dir."',
status     = '0',
date       = '".$date ."',
time       = '".time()."'
";
if( !empty($sesid) ){
 $query = $this->mycon->query($query);
 if($query){
   echo '{"status":"1","record": "%action_success%"}';
 }
 else{
  die ('{"status":"0","record": "%action_error%"}');
 }
} 
}

public function sendmessage_voice($table){
session_start();
$sesid     = $_SESSION['id'];
$receiver  = $_POST['receiver'] / $this->hash;
$message   = $_POST['message'];
$date      = $_POST['date'];

 $uniqdir  = uniqid();
 $uniqfile = uniqid();
 $dir      = '../globalimg/msg/'.$_SESSION['id'].'/'.$uniqdir;
 $folder   = mkdir($dir,0777,true);
 $path     = $dir.'/'.$uniqfile.'.wav';

$query     = " INSERT INTO $this->prefix$table SET
sender     = '".$sesid."',
receiver   = '".$receiver."',
og_message = '',
message    = '".$message."',
type       = 'audio',
path       = '".$dir."',
status     = '0',
date       = '".$date ."',
time       = '".time()."'
";
if( !empty($sesid) ){
 $query = $this->mycon->query($query);
 move_uploaded_file($_FILES['voice']['tmp_name'], $path);
 if($query){
   echo '{"status":"1","record": "%action_success%"}';
 }
 else{
  die ('{"status":"1","record": "%action_error%"}');
 }
} 

}



public function delete_message($table){
session_start();
$sesid     = $_SESSION['id'];
$messageid = $_POST['messageid'];

$delete_files = " SELECT * FROM $this->prefix$table WHERE id = $messageid ";
$delete_files = $this->mycon->query($delete_files)->fetch_array();
if(is_dir($delete_files['path']))
{
$this->deleteDir($delete_files['path']."/");
}

$query = "DELETE FROM $this->prefix$table WHERE id = '".$messageid."' AND (sender = '".$sesid."' OR receiver = '".$sesid."') ";
$query = $this->mycon->query($query);
if($query){

   echo '{"status":"1","record": "%action_success%"}';
 }
 else{
  die ('{"status":"0","record": "%action_success%"}');
 }

}

public function sendmessage($table){
session_start();
      
      $path  = "";
      $type  = $this->validformats;
      $size  = $this->maxsize;
     
     if(count($_FILES) < 4){

       if(count($_FILES) > 0)
       {
       $uniqdir     = uniqid();
       $dir         = '../globalimg/msg/'.$_SESSION['id'].'/'.$uniqdir;
      
       $folder      = mkdir($dir,0777,true);
       $folder      = $dir.'/';
      
       foreach($_FILES as $index=>$file)
       {
      $uniqimgname = uniqid();
      $filename    = $file['name'];
      $filetype    = $file['type'];
      $filesize    = $file['size'];
      $filetmpname = $file['tmp_name'];
      $extension   = pathinfo($filename, PATHINFO_EXTENSION);
     
      $fullpath    = $folder.$uniqimgname.".".$extension;
      if(!in_array($filetype,$type)){
        die('{"status":"0","text":"%action_filetype%"}');
      }
      if($filesize > $size){
      die('{"status":"0","text":"%action_size%"}');
}
if(!empty($filetmpname) && is_uploaded_file($filetmpname))
{
  $file   = $this->compress($filetmpname,$fullpath, 25);
  move_uploaded_file($file,$fullpath);
  $path = $dir;
      }

     }
   }
     	
$sesid     = $_SESSION['id'];
$receiver  = $_POST['receiver'] / $this->hash;
$message   = $this->filter_inp($_POST['message']);
$date      = $_POST['date'];
$query     = " INSERT INTO $this->prefix$table SET
sender     = '".$sesid."',
receiver   = '".$receiver."',
message    = '".$message."',
og_message = 'null',
type       = 'common',
path       = '".$dir."',
status     = '0',
date       = '".$date ."',
time       = '".time()."'
";
if( !empty($sesid) ){
 $query = $this->mycon->query($query);
 if($query){
   echo '{"status":"1","record": "%action_success%"}';
 }
 else{
   die ('{"status":"1","record": "%action_error%"}');
 }
}

  }
  else
  {
   die('{"status":"0","text":"%3files%"}');
  }
}
public function send_comment($table){
  session_start();
  $text         = $this->filter_inp($_POST['text']);
  $date         = $_POST['date'];
  $sesid        = $_SESSION['id'];
  $object_id    = $_POST['object_id'];
  $object_type  = $_POST['object_type'];
  $object_owner = $_POST['object_owner'] / $this->hash;
if( !empty($sesid) AND $text !== " " AND $text !== ""){
  $query = " INSERT INTO $this->prefix$table SET 
  account      = '".$sesid."',
  object_id    = '".$object_id."',
  object_type  = '".$object_type."',
  object_owner = '".$object_owner."',
  text         = '".$text."',
  date         = '".$date."',
  time         = '".time()."'
  ";
  $query = $this->mycon->query($query);
  if($query){
      $last_id  = mysqli_insert_id($this->mycon);
      $this->activity_add($object_owner,$last_id,$object_type,'comment','do');
  }
  else{
    return false;
  }
}
}

public function get_comments($table){
  session_start();
  $sesid        = $_SESSION['id'];
  $object_id    = $_POST['object_id'];
  $object_type  = $_POST['object_type'];
  $object_type2 =
  $limit     = isset($_POST['limit']) ? $_POST['limit'] : $this->limit20;
  $comments  = "SELECT * FROM $this->prefix$table WHERE object_id = '".$object_id."' AND object_type = '".$object_type."' ORDER by id DESC LIMIT $limit ";
  $comments  = $this->mycon->query($comments);
  $comments_count = $comments->num_rows;
 
  while($row = $comments->fetch_array()){
    if($this->outp !== ""){$this->outp .= ",";};
    $date          = json_decode($row['date'],true);
    $user_name     = $this->userinfo($row['account'],'firstname');
    $user_lname    = $this->userinfo($row['account'],'lastname');
    $user_avatar   = $this->userinfo($row['account'],'avatar');
    $comment_owner = $row['account']      == $sesid ? 1 : 0;
    $object_owner  = $row['object_owner'] == $sesid ? 1 : 0;;

    $this->outp .= '{"id"            :"'.$row['id'].'",'          ;
    $this->outp .= '"owner_id"       :"'.$row['account'].'"     ,';
    $this->outp .= '"text"           :"'.$this->filter_outp($row['text']).'",';
    $this->outp .= '"comment_owner"  :"'.$comment_owner.'"      ,';
    $this->outp .= '"object_owner"   :"'.$object_owner.'"       ,';
    $this->outp .= '"object_type"    :"'.$row['object_type'].'" ,';
    $this->outp .= '"user_name"      :"'.$user_name.'"          ,';
    $this->outp .= '"user_lname"     :"'.$user_lname.'"         ,';
    $this->outp .= '"user_avatar"    :"'.$user_avatar.'"        ,';
    $this->outp .= '"date"           :"'.$date['date'].'"       ,';
    $this->outp .= '"month"          :"'.$date['month'].'"      ,';
    $this->outp .= '"year"           :"'.$date['year'].'"       ,';
    $this->outp .= '"hours"          :"'.$date['hours'].'"      ,';
    $this->outp .= '"minutes"        :"'.$date['minutes'].'"    }';
  }
  $this->outp = '{"record" : ['.$this->outp.'],"comments" : "'.$comments_count.'","object_type":"'.$object_type.'"}';
  echo $this->outp;
}

public function delete_comment($table){
  session_start();
  
  $sesid         = $_SESSION['id'];
  $comment       = $_POST['comment'];
  $object_type   = $_POST['object_type'];
  $object_owner  = $_POST['object_owner'];

  if($object_owner === '1')
  {
    $delete_owner   = "DELETE FROM $this->prefix$table WHERE id = '".$comment."' AND object_type = '".$object_type."'";
    if($this->mycon->query($delete_owner) &&  $this->mycon->affected_rows > 0){

     $this->activity_remove($comment,$object_type,'comment','nosession');
     echo '{"status":"1","object_owner":"1","affected" : "'.$this->mycon->affected_rows.'","object_type":"'.$object_type.'","id":"'.$comment.'"}';
     }
     else                                                        
     {
     die('{"status":"0","object_owner":"1","text":"error"}');
     } 
  }
  else
  {
    $delete_guest   = " DELETE FROM $this->prefix$table WHERE id = '".$comment."' AND object_type = '".$object_type."' AND account = '".$sesid."' ";
    if($this->mycon->query($delete_guest) &&  $this->mycon->affected_rows > 0){
    
    $this->activity_remove($comment,$object_type,'comment','nosession');
    echo '{"status":"1","object_owner":"0","affected" : "'.$this->mycon->affected_rows.'","object_type":"'.$object_type.'"}';

     }
     else
     {
     die('{"status":"0","object_owner":"0","text":"error"}');
     } 
  }

}

public function get_first_image($path)
{
  $img = glob($path."/*.{jpg,png,JPG,PNG}",GLOB_BRACE );
  if(count($img) > 0)
  {
  return $img[0];
  }
  else
  {
    echo " ";
  }
}

public function save_video($table){
  session_start();
  if(!empty($_SESSION['id']))
  {
    $sesid     = $_SESSION['id'];
    $link      = $_POST['link'];
    $url       = $_POST['url'];
    $date      = $_POST['date'];
    $save      = " INSERT INTO $this->prefix$table SET 
    account    = '".$sesid."',
    link       = '".$link."',
    url        = '".$url."',
    date       = '".$date."',
    timestamp  = '".time()."'
   ";
  if($this->mycon->query($save))
  {
    echo '{"status":"1","text":"%saved%"}';
  }
  else
  {
    echo '{"status":"0","text":"%action_error%"}';
  }
  }
}
public function delete_video($table){
  session_start();
  if(!empty($_SESSION['id']))
  {
  $sesid  =  $_SESSION['id'];
  $video  =  $_POST['video_id'];
  $delete =  "DELETE FROM $this->prefix$table WHERE account = '".$sesid."' AND id = '".$video."' ";
  if($this->mycon->query($delete))
  {
    echo '{"status":"1","text":"%action_success%"}';
  }
  else
  {
    die('{"status":"0","text":"%action_error%"}');
  }
  }
}
public function get_video($table)
{
  session_start();
  if(!empty($_SESSION['id']))
  {
    $limit    =  !empty($_POST['limit']) ? $_POST['limit']  : $this->limit;
    $sesid    =  !empty($_POST['sketch']) ? $_POST['sketch'] : $_SESSION['id'];
    $getvideo = "SELECT link,account,id,url,date FROM $this->prefix$table WHERE account = '".$sesid."' ORDER BY id DESC LIMIT $limit " ;
    $getvideo = $this->mycon->query($getvideo);
    while($param = $getvideo->fetch_array())
    {
       $date         = json_decode($param['date'],true);
       $object_owner = $param['account'] == $_SESSION['id'] ? '1' : '0';
       if($this->outp !== ""){$this->outp .= "," ;}
       $this->outp  .= '{"id"           :"'.$param['id'].'"       ,'; 
       $this->outp  .= ' "link"         :"'.$param['link'].'"     ,';
       $this->outp  .= ' "url"          :"'.$param['url'].'"      ,';
       $this->outp  .= ' "account"      :"'.$param['account'].'"  ,';
       $this->outp  .= ' "object_owner" :"'.$object_owner.'"      ,';
       $this->outp  .= ' "hours"        :"'.$date['hours'].'"     ,';
       $this->outp  .= ' "minutes"      :"'.$date['minutes'].'"   ,';
       $this->outp  .= ' "year"         :"'.$date['minutes'].'"   }';
    }
      $this->outp = '{"record":['.$this->outp.']}';
      echo $this->outp;
  }
} 
public function activity($table){
  session_start();
 
  $sesid = $_SESSION['id'];
  $limit = !empty($_POST['limit']) ? $_POST['limit'] : 7;
  $query = "SELECT * FROM $this->prefix$table WHERE
   object_owner  =  '".$sesid."' AND
   activator    !=  '".$sesid."' AND
   object_type  != ''            AND 
   action_type  != ''            AND
   object_id    != ''            AND
   object_owner != ''            AND
   activator    != ''
   ORDER BY id DESC LIMIT $limit ";
  $query = $this->mycon->query($query);
  while($row = $query->fetch_array()){
    if($this->outp !== ""){$this->outp .= ",";}
    $activator_fname  = $this->userinfo($row['activator'],'firstname');
    $activator_lname  = $this->userinfo($row['activator'],'lastname');
    $activator_avatar = $this->userinfo($row['activator'],'avatar');
    $activator_id     = $row['activator'];
    $date             = json_decode($row['date'],true);

    
   
    $object_id  = $this->returnparam('comments'       ,'object_id'   ,'id'    ,$row['object_id']);///GET POST ID FROM COMMENTS TABLE
    $label_id   = $this->returnparam('labelimages'    ,'label'       ,'id'    ,$row['object_id']);///GET POST ID FROM COMMENTS TABLE
   
   if($row['action_type']   == 'like'){
    switch($row['object_type']){
       case   'post'    : 
       $title  =  "%likedpost%" ;
       $title2 =  $this->returnparam('posts'         ,'text'        ,'id'    ,$row['object_id'])  ;
       $object_target =  "";
       break;
       case   'timeline'    : 
       $title         =  "%likedpost%";
       $title2        =  $title2 =  $this->returnparam('timeline'     ,'message'        ,'id'    ,$row['object_id'])  ;////COMMENT
       $object_target =  $this->get_first_image($this->returnparam('timeline'     ,'path'        ,'id'    ,$row['object_id']));//CHOSSE POST TEXT AS TARGET
       break;
      case   'label'   :  
      $title  =  "%likedlabel%";
      $title2 =  $this->returnparam('label'    ,'label_name' ,'id'  ,$row['object_id'])  ;  ;
      $object_target =  "";
      break;
      case   'labelimage'   : 
      $title         =  "%feed_likedlabel_image%";
      $title2        =  $this->returnparam('label'         ,'label_name'  ,'id'    ,$label_id)  ;
      $object_target =  $this->returnparam('labelimages'   ,'image'       ,'id'    ,$row['object_id']);
      break;
      case   'image'   :  
      $title  =  "%likedimage%" ;
      $title2 =  " "  ; 
      $object_target =  $this->returnparam('account_images' ,'image'       ,'id'    ,$row['object_id']    );
      break;
      default :
      $title  =  " --" ;
      $title2 =  " "  ; 
      $object_target =  "";
    }
   }
    
   if($row['action_type']   == 'follow'){
    switch($row['object_type']){
      case   'post'    : 
       $title  =  " "  ;
       $title2 =  " "   ;
       $object_target =  "";
       break;
      case   'label'   :  
      $title =  "%nowfollows%";
      $title2 =  $this->returnparam('label'    ,'label_name' ,'id'  ,$row['object_id'])  ;  ;
      $object_target =  "";
      break;
      case   'image'   :  $title =  $this->returnparam('comments' ,'text'       ,'id'  ,$row['object_id']) ;
      $title =  " ";
      $title2 =  " "  ; 
      $object_target =  "";
      break;
      default :
      $title  =  " " ;
      $title2 =  " "  ; 
      $object_target =  "";
    }
   }
   else if($row['action_type'] == 'comment'){
     switch($row['object_type']){
      case   'post'    : 
       $title         =  $this->returnparam('posts'         ,'text'        ,'id'    ,$object_id);////TITLE
       $title2        =  $this->returnparam('comments'      ,'text'        ,'id'    ,$row['object_id']);////COMMENT
       $object_target =  "";//CHOSSE POST TEXT AS TARGET
       break;
       case   'timeline'    : 
       $title         =  "%commentedimage%";
       $title2        =  $this->returnparam('comments'      ,'text'        ,'id'    ,$row['object_id']);////COMMENT
       $object_target =  $this->get_first_image($this->returnparam('timeline'     ,'path'        ,'id'    ,$object_id));
       break;
      case   'label'   : 
       $title         =  $this->returnparam('label'         ,'label_name'  ,'id'    ,$row['object_id']);
       $title2        =  $this->returnparam('label'         ,'label_name'  ,'id'    ,$row['object_id']);
       $object_target =  " ";//CHOOSE LABEL NAME AS TARGET OR WE CAN CHOOSE LABAEL_LOGO
       break;
       case   'labelimage'   : 
       $title         =  "%feed_commentedlabel_image%";
       $title2        =  $this->returnparam('comments'      ,'text'        ,'id'    ,$row['object_id']);
       $object_target =  $this->returnparam('labelimages'   ,'image'       ,'id'    ,$object_id);//SCHOSE LABEL NAME AS TARGET OR WE CAN CHOOSE LABAEL_LOGO
       break;
      case   'image'   :  
      $title         =  "%commentedimage%";
      $title2        =  $this->returnparam('comments'       ,'text'        ,'id'    ,$row['object_id']);
      $object_target =  $this->returnparam('account_images' ,'image'       ,'id'    ,$object_id    );////CHOSE IMAGE AS TARGET
      break;
      default :
      $title  =  " " ;
      $title2 =  " "  ; 
      $object_target =  "";
    }

   }
      else if($row['action_type'] == 'friend_req' ){
  
       $title         =  "%nowfollowsyou%";
       $title2        =  " ";
       $object_target =  " ";
   }
         else if($row['action_type'] == 'friend_confirm' ){
  
       $title         =  "%confirmedfriend%";
       $title2        =  " ";
       $object_target =  " ";
   }

    $this->outp .= '{ "title"               : "'.$title.'"                 ,'; 
    $this->outp .= '  "title2"              : "'.$title2.'"                ,';
    $this->outp .= '  "object_target"       : "'.$object_target.'"         ,';
    $this->outp .= '  "objecttype"          : "'.$row['object_type'].'"    ,';
    $this->outp .= '  "actiontype"          : "'.$row['action_type'].'"    ,';
    $this->outp .= '  "activator_id"        : "'.$activator_id.'"          ,';  
    $this->outp .= '  "activator_avatar"    : "'.$activator_avatar.'"      ,';  
    $this->outp .= '  "activator_lname"     : "'.$activator_lname.'"       ,';  
    $this->outp .= '  "activator_fname"     : "'.$activator_fname.'"       ,'; 
    $this->outp .= '   "date"               :"'.$date['date'].'"           ,';
    $this->outp .= '   "month"              :"'.$date['month'].'"          ,';
    $this->outp .= '   "year"               :"'.$date['year'].'"           ,';
    $this->outp .= '   "hours"              :"'.$date['hours'].'"          ,';
    $this->outp .= '   "minutes"            :"'.$date['minutes'].'"        }';
  }
  $this->outp = '{"record":['.$this->outp.']}';
  echo $this->outp;
}


public function friends_activity($table1,$table2){  //friends / activity
  session_start();
  $tab1  = $this->prefix.$table1;
  $tab2  = $this->prefix.$table2;

  $sesid = $_SESSION['id'];
  $limit = !empty($_POST['limit']) ? $_POST['limit'] : 30;
  $query = "SELECT * FROM $tab2 WHERE (activator IN (SELECT friend FROM $tab1 WHERE account = $sesid)) OR activator IN ((SELECT account FROM $tab1 WHERE friend = $sesid AND status = 1))
  AND 
   object_type  != ''            AND 
   action_type  != ''            AND
   object_id    != ''            AND
   object_owner != ''            AND
   activator    != ''
   ORDER BY $tab2.id DESC";
  $query = $this->mycon->query($query);
  while($row = $query->fetch_array()){
    if($this->outp !== ""){$this->outp .= ",";}
    $activator_fname  = $this->userinfo($row['activator'],'firstname');
    $activator_lname  = $this->userinfo($row['activator'],'lastname');
    $activator_avatar = $this->userinfo($row['activator'],'avatar');

    $owner_name   = $this->userinfo($row['object_owner'],'firstname')." ".$this->userinfo($row['object_owner'],'lastname');
    $owner_avatar = $this->userinfo($row['object_owner'],'avatar');
  
    $date             = json_decode($row['date'],true);

    $object_id  = $this->returnparam('comments'       ,'object_id'   ,'id'    ,$row['object_id']);///GET POST ID FROM COMMENTS TABLE
    $object_image_comments = $this->returnparam('comments' ,'object_id'  ,'id'    ,$row['object_id']);///GET IMAGE ID FROM COMMENTS TABLE
    $object_post     = $this->returnparam('comments' ,'object_id'        ,'id'    ,$row['object_id']);///GET POST ID FROM COMMENTS TABLE

   if($row['action_type']   == 'like'){
    switch($row['object_type']){
      case   'post'    : 
       $title  =  "%feed_likedpost%" ;
       $title2 =  " "  ;
       $title3 =  $this->returnparam('posts'         ,'text'        ,'id'    ,$row['object_id'])  ;
       $object_target =  "";
       break;
       case   'timeline'    : 
       $title         =  "%feed_likedpost%";
       $title2        =  $title2 =  $this->returnparam('timeline'     ,'message'        ,'id'    ,$row['object_id'])  ;////COMMENT
       $title3        = " ";
       $object_target =  $this->get_first_image($this->returnparam('timeline'     ,'path'        ,'id'    ,$row['object_id']));//CHOSSE POST TEXT AS TARGET
       break;
      case   'label'   :  
      $title  =  "%feed_likedlabel%";
      $title2 =  $this->returnparam('label'         ,'label_name' ,'id'  ,$row['object_id'])   ;
      $title3 =  $this->returnparam('label'         ,'description' ,'id'  ,$row['object_id']);
      $object_target =  " ";
      break;
      case   'image'   :  
      $title  =  "%feed_likedpost%" ;
      $title2 =  " "  ; 
      $title3 =  " ";
      $object_target =  $this->returnparam('account_images' ,'image'       ,'id'    ,$row['object_id']    );
      break;
           default : 
        $title         =  "%feed_becamefriends%";
       $title2        =  " ";
       $title3 =  " ";
       $object_target =  " ";
    }
   }
    
   if($row['action_type']   == 'follow'){
    switch($row['object_type']){
      case   'post'    : 
       $title  =  " "  ;
       $title2 =  " "   ;
       $title3 =  " ";
       $object_target =  "";
       break;
      case   'label'   :  
      $title =  "%feed_startedfollowinglabel%";
      $title2 =  $this->returnparam('label'    ,'label_name' ,'id'  ,$row['object_id'])  ;  
      $title3 =  $this->returnparam('label'    ,'description' ,'id'  ,$row['object_id']) ;
      $object_target =  " ";
      break;
      case   'image'   :  $title =  $this->returnparam('comments' ,'text'       ,'id'  ,$row['object_id']) ;
      $title =  " ";
      $title2 =  " "  ; 
      $title3 =  " ";
      $object_target =  "";
      break;
         default : 
        $title         =  "%feed_becamefriends%";
       $title2        =  " ";
       $title3 =  " ";
       $object_target =  " ";
    }
   }
   else if($row['action_type'] == 'comment'){
     switch($row['object_type']){
            case   'post'    : 
       $title  =  "%feed_commentedlabel%";
       $title2 =  $this->returnparam('comments'       ,'text'        ,'id'    ,$row['object_id']);
       $title3 =  $this->returnparam('posts'          ,'text'        ,'id'    ,$object_post)   ;
       $object_target =  " ";
       break;
      case   'label'   : 
       $title         =  $this->returnparam('label'         ,'label_name'  ,'id'    ,$row['object_id']);
       $title2        =  $this->returnparam('label'         ,'label_name'  ,'id'    ,$row['object_id']);
       $title3        =  " ";
       $object_target =  " ";//SCHOSE LABEL NAME AS TARGET OR WE CAN CHOOSE LABAEL_LOGO
       
       break;
        case   'timeline'    : 
       $title         =  "%commentedimage%";
       $title2        =  $this->returnparam('comments'      ,'text'        ,'id'    ,$row['object_id']);////COMMENT
        $title3 =  " ";
       $object_target =  $this->get_first_image($this->returnparam('timeline'     ,'path'        ,'id'    ,$object_id));
       break;
      case   'image'   :  
      $title         =  "%feed_commentedpost%";
      $title2        =  $this->returnparam('comments'       ,'text'        ,'id'    ,$row['object_id']);
      $title3        =  " ";
      $object_target =  $this->returnparam('account_images' ,'image'       ,'id'    ,$object_image_comments    );////CHOSE IMAGE AS TARGET
      break;
      default : 
        $title         =  "%feed_becamefriends%";
       $title2        =  " ";
       $title3 =  " ";
       $object_target =  " ";
    }

   }
      else if($row['action_type'] == 'friend_req' ){
  
       $title         =  "%feed_startedfollowing%";
       $title2        =  " ";
       $title3 =  " ";
       $object_target =  " ";
   }
         else if($row['action_type'] == 'friend_confirm' ){
  
       $title         =  "%feed_becamefriends%";
       $title2        =  " ";
       $title3 =  " ";
       $object_target =  " ";
   }


    $this->outp .= '{ "title"               : "'.$title.'"                 ,'; 
    $this->outp .= '  "title2"              : "'.$title2.'"                ,';
    $this->outp .= '  "title3"              : "'.$title3.'"                ,';
    $this->outp .= '  "object_target"       : "'.$object_target.'"         ,';
    $this->outp .= '  "objecttype"          : "'.$row['object_type'].'"    ,';
    $this->outp .= '  "actiontype"          : "'.$row['action_type'].'"    ,';
    $this->outp .= '  "activator_avatar"    : "'.$activator_avatar.'"      ,';  
    $this->outp .= '  "activator_lname"     : "'.$activator_lname.'"       ,';  
    $this->outp .= '  "activator_fname"     : "'.$activator_fname.'"       ,'; 
    $this->outp .= '  "owner_name"          : "'.$owner_name.'"            ,'; 
    $this->outp .= '  "owner_avatar"        : "'.$owner_avatar.'"          ,'; 
    $this->outp .= '   "date"               :"'.$date['date'].'"           ,';
    $this->outp .= '   "month"              :"'.$date['month'].'"          ,';
    $this->outp .= '   "year"               :"'.$date['year'].'"           ,';
    $this->outp .= '   "hours"              :"'.$date['hours'].'"          ,';
    $this->outp .= '   "minutes"            :"'.$date['minutes'].'"        }';
  }
  $this->outp = '{"record":['.$this->outp.']}';
  echo $this->outp;
}

public function  friends_posts_activity($table1,$table2){ //friends //account_images
  session_start();
  $sesid = $_SESSION['id'];
  $tab1 = $this->prefix.$table1;
  $tab2 = $this->prefix.$table2;
  
  $feed = " SELECT $tab2.account,$tab2.image,$tab2.title,$tab2.id,$tab2.date FROM $tab1
   INNER JOIN $tab2 ON $tab1.friend = $tab2.account WHERE $tab1.account = $sesid ORDER BY $tab2.id DESC";
  $feed = $this->mycon->query($feed);
  while($feed_row = $feed->fetch_array())
  {
  $comments     = $this->statistic_counter_comments('comments','image',$feed_row['id']);
  $likes        = $this->statistic_counter('likes','image',$feed_row['id']);
  $date         = json_decode($feed_row['date'],true);
  $owner_fname  = $this->userinfo($feed_row['account'],'firstname');
  $owner_lname  = $this->userinfo($feed_row['account'],'lastname' );
  $owner_name   = $owner_fname." ".$owner_lname;
  $owner_avatar = $this->userinfo($feed_row['account'],'avatar'   );
  if($this->outp  !== ""){$this->outp .= ",";}
  $this->outp  .= '{ "id"               : "'.$feed_row['id'].'"       ,';
  $this->outp  .= '  "owner_id"         : "'.$feed_row['account'].'"  ,';
  $this->outp  .= '  "object_type"      : "image"                     ,';
  $this->outp  .= '  "owner_name"       : "'.$owner_name.'"           ,';
  $this->outp  .= '  "owner_avatar"     : "'.$owner_avatar.'"         ,';
  $this->outp  .= '  "image"            : "'.$this->path_abs($feed_row['image']).'"    ,';
  $this->outp  .= '  "title"            : "'.$feed_row['title'].'"    ,';
  $this->outp  .= '  "likes_counter"    : "'.$likes.'"                ,';
  $this->outp  .= '  "comments_counter" : "'.$comments.'"             ,';
  $this->outp  .= '  "date"              : "'.$date['date'].'"        ,';
  $this->outp  .= '  "month"            : "'.$date['month'].'"        ,';
  $this->outp  .= '  "year"             : "'.$date['year'].'"         ,';
  $this->outp  .= '  "minutes"          : "'.$date['minutes'].'"      ,';
  $this->outp  .= '  "hours"            : "'.$date['hours'].'"        }';
  }
  $this->outp = '{"record":['.$this->outp.']}';
  echo $this->outp;
}
public function follow($table){
session_start();

$followed  = $_POST['followed'];
$date      = $_POST['date'];
$label_owner     = $this->returnparam('label','account','id',$followed);
if(isset($_SESSION['id'])){
$follower  = $_SESSION['id'];

$query = " SELECT id,account,friend FROM $this->prefix$table WHERE account = '".$follower."' AND friend = '".$followed."' AND follow_type = 'label' ";
$check = $this->mycon->query($query)->fetch_array();
if(!empty($check['id'])){
  $unfollow = "DELETE  FROM $this->prefix$table WHERE account = '".$follower."' AND friend = '".$followed."'  AND follow_type = 'label'  ";
  $this->activity_add($label_owner,$followed,'label','follow','undo');
  $unfollow = $this->mycon->query($unfollow);
  echo '{"status0" : "%follow%","status1" : "0"}';
}
else{
$follow          = "INSERT INTO $this->prefix$table SET 
account          = '".$follower."',
friend           = '".$followed."',
status           = '0',
follow_type      = 'label',
date             = '".$date."'    ,
time             = '".time()."'   
";
$this->activity_add($label_owner,$followed,'label','follow','do');
$follow = $this->mycon->query($follow);
echo '{"status0" : "%following%","status1" : "1"}';
   }
  }
  else{
     die('{"status0" : "%action_authorize%","status1" : "0"}');
  }
 }

public function checkfollow($table){
  session_start();



if(isset($_SESSION['id'])){
$followed  = $_POST['followed'];
$follower  = $_SESSION['id'];
$query = "SELECT id,account,friend FROM $this->prefix$table WHERE account = '".$follower."' AND friend = '".$followed."' AND follow_type = 'label'  ";
$check = $this->mycon->query($query)->fetch_array();
if(!empty($check['id'])){
  echo '{"status0" : "%following%","status1" : "1"}';
}
else{
  echo '{"status0" : "%follow%","status1" : "0"}';
  }
 }
 else{
    die('{"status0" : "%action_authorize%","status1" : "0"}');
  }
}
public function activity_add($objectowner,$objectid,$objecttype,$actiontype,$changer){

  if($changer == 'do'){
 $activity    = " INSERT INTO gc_activity SET 
 activator    = '".$_SESSION['id']."',
 object_owner = '".$objectowner."'   ,
 object_id    = '".$objectid."'      ,
 object_type  = '".$objecttype."'    ,
 action_type  = '".$actiontype."'    ,
 status       =      '0'             ,
 date         = '".$_POST['date']."' ,
 time = '".time()."'
 ";
 $activity = $this->mycon->query($activity);
 ob_end_clean();
}
else if ($changer == 'undo'){
  $activity = " DELETE FROM gc_activity WHERE
  activator  = '".$_SESSION['id']."'
  AND action_type = '".$actiontype."' AND object_id = '".$objectid."' AND object_type = '".$objecttype."' ";
  $activity = $this->mycon->query($activity);
  ob_end_clean();
}
}

public function activity_remove($objectid,$objecttype,$actiontype,$status){
  session_start();
$activity = " DELETE FROM gc_activity WHERE 
 action_type = '".$actiontype."' AND object_id = '".$objectid."' AND object_type = '".$objecttype."' ";
  if($status == 'session'){
  $activity .= " AND activator  = '".$_SESSION['id']."' ";
  }
  $activity = $this->mycon->query($activity);
  ob_end_clean();
  }
  public function activity_remove_alt($objectidalt,$objecttype,$actiontype,$status){
  session_start();
$activity = " DELETE FROM gc_activity WHERE 
 action_type = '".$actiontype."' AND object_id_alt = '".$objectidalt."' AND object_type = '".$objecttype."' ";
  if($status == 'session'){
  $activity .= " AND activator  = '".$_SESSION['id']."' ";
  }
  $activity = $this->mycon->query($activity);
  ob_end_clean();
  }
public function card_existance(){
  $table     = $_POST['table'];
  $key       = $_POST['keyvalue'];
  $check     = " SELECT keyvalue FROM $this->prefix$table WHERE keyvalue = '".$key."' ";
  $check_row = $this->mycon->query($check)->fetch_array();
  
  if(!empty($check_row['keyvalue'])){
    echo '{"status":"1"}';
  }
  else{
    echo '{"status":"0"}';
  }

}
 
public function add_to_friends($table){
session_start();
$sesid            = $_SESSION['id'];
$requested_friend = $_POST['requested_friend'];
$date             = $_POST['date'];

$check = " SELECT * FROM $this->prefix$table  WHERE (account =  $sesid AND friend = $requested_friend) 
OR  (friend =  $sesid AND account = $requested_friend) AND follow_type = 'account' ";
$check = $this->mycon->query($check)->fetch_array();

if(!empty($check['id'])){
   if($check['status'] == 0 AND $check['account'] == $sesid)
   {
   $unfollow = "DELETE FROM $this->prefix$table  WHERE account =  $sesid AND friend = $requested_friend AND follow_type = 'account' ";
   $unfollow = $this->mycon->query($unfollow);
     if($unfollow )
      {
      $this->activity_add($requested_friend,$requested_friend,'friend','friend_req','undo');
      echo '{"status" : 1,"text" : "%request_declined%"}';
      }
     else
      {
       echo '{"status" : 0,"text" : "%action_error%"}';
      }
   }
     else if($check['status'] == 1 AND $check['account'] == $sesid){
        $remove_friend = "UPDATE $this->prefix$table SET status = 0 WHERE
        (friend  = '".$sesid."' AND account = '".$requested_friend."') OR
        (account = '".$sesid."' AND friend  = '".$requested_friend."') AND follow_type = 'account' ";
          $remove_friend = $this->mycon->query($remove_friend);

              if($remove_friend)
               {
                 $this->activity_remove($sesid,'friend','friend_confirm','nosession');
     
                echo '{"status" : 1,"text" : "%unfollow%"}';
                 }
                  else
              {
              echo '{"status" : 0,"text" : "%action_error%"}';
                 }

            }
          else if($check['status'] == 0 AND $check['friend'] == $sesid){
          
         $confirm = " UPDATE $this->prefix$table SET 
            status = 1, 
            date   = '".$date."',
            time   = '".time()."'
            WHERE friend = '".$sesid."' AND account = '".$requested_friend."' AND status = 0  AND follow_type = 'account' ";
            $confirm = $this->mycon->query($confirm);
            if($confirm)
              {
                $this->activity_add($requested_friend,$requested_friend,'friend','friend_confirm','do');
               echo '{"status": "1","text":"%friends%"}';
                
                    } 
                  else
                  {
                      die('{"status": "0","text":"%action_error%"}');
                  }
          }
          else if($check['status'] == 1 AND $check['friend'] == $sesid){

            $delete_friend = " UPDATE $this->prefix$table SET 
            status = 0, 
            date   = '".$date."',
            time   = '".time()."'
            WHERE friend = '".$sesid."' AND account = '".$requested_friend."' AND status = 1 AND follow_type = 'account'  ";
            $delete_friend = $this->mycon->query($delete_friend);
            if($delete_friend)
              {
                $this->activity_add($requested_friend,$requested_friend,'friend','friend_confirm','undo');
               echo '{"status": "1","text":"%addtofriends%"}';
                
                    } 
                  else
                  {
                      die('{"status": "0","text":"%action_error%"}');
                  }

          }
}

 else{
  if($requested_friend !== $sesid){
  $add_friend  = "INSERT INTO $this->prefix$table SET
  account = '".$sesid."',
  friend  = '".$requested_friend."',
  status  = 0 ,
  date    = '".$date."',
  time    = '".time()."',
  follow_type = 'account'
  ";
  $add_friend = $this->mycon->query($add_friend);
  if($add_friend)
   {
     $this->activity_add($requested_friend,$requested_friend,'friend','friend_req','do');
     echo '{"status" : 1,"text" : "%request_sended%"}';
   }
  else
   {
     echo '{"status" : 0,"text" : "%action_error%"}';
    }
  }
}

}

public function confirm_friend($table){

session_start();
$sesid   = $_SESSION['id'];
$account = $_POST['account'];
$date    = $_POST['date'];

$check = " SELECT friend,account FROM  $this->prefix$table WHERE friend = '".$sesid."' AND account = '".$account."'  AND status = 0 AND follow_type = 'account' ";
$check = $this->mycon->query($check)->fetch_array();
if(!empty($check['friend'])){
$confirm   = " UPDATE $this->prefix$table SET 
status     = 1, 
date       = '".$date."',
time       = '".time()."'
WHERE (friend = '".$sesid."' AND account = '".$account."') OR (account = '".$sesid."' AND friend = '".$account."')   AND status = 0 AND follow_type = 'account' ";
$confirm = $this->mycon->query($confirm);
if($confirm)
{
  echo '{"status": "1","text":"%friends%"}';
  $this->activity_add($account,$account,'friend','friend_confirm','do');
}
else
{
  die('{"status": "0","text":"%action_error%"}');
}
}
}
public function delete_friend($table){
session_start();
$sesid   = $_SESSION['id'];
$account = $_POST['account'];
$delete  = " UPDATE  $this->prefix$table SET status = 0 WHERE
(friend  = '".$sesid."' AND account = '".$account."') OR
(account = '".$sesid."' AND friend  = '".$account."')
AND status = 1 AND follow_type = 'account' ";
$delete  = $this->mycon->query($delete);
if($delete)
{
  echo '{"status": "1","text":"%removed%"}';
  $this->activity_add($account,$account,'friend','friend_confirm','undo');
}
else
{
  die('{"status": "0","text":"%action_error%"}');
}
}
public function check_friend_status($table){
session_start();
$sesid            = $_SESSION['id'];
$requested_friend = $_POST['requested_friend'];
$result           = [];

$check = " SELECT * FROM $this->prefix$table WHERE (account =  '".$sesid."' AND friend = '".$requested_friend."') OR
(friend =  '".$sesid."' AND account = '".$requested_friend."')  AND follow_type = 'account' ";
$check =   $this->mycon->query($check)->fetch_array();

if(!empty($check['id']))
{
  if($check['status'] == 0 AND $check['account'] == $sesid)
  {
   echo '{"status" : 1,"text" :         "%unfollow%" }';
  }
  else if($check['status'] == 1 AND $check['account'] == $sesid)
  {
    echo '{"status" : 1,"text" :         "%removefromfriends%" }';
  }
  else if($check['status'] == 0 AND $check['friend'] == $sesid)
  {
    echo '{"status" : 1,"text" :         "%addtofriends%" }';
  }
  else if($check['status'] == 1 AND $check['friend'] == $sesid)
  {
    echo '{"status" : 1,"text" :         "%removefromfriends%" }';
  }
}
else
{
  echo '{"status" : 1,"text" :         "%addtofriends%" }';
}



}


public function get_friends_followers($table1,$table2){//friends / ACCOUNT
  $tab1 = $this->prefix.$table1;
  $tab2 = $this->prefix.$table2;
  session_start();
  $sesid   = $_SESSION['id'];
  $account = !empty($_POST['sketch'])? $_POST['sketch'] : $_SESSION['id'];

  
  $friend_requests = " SELECT $tab1.friend,$tab1.date,$tab2.online,$tab2.lastvisit,$tab2.firstname,$tab2.lastname,$tab2.avatar,$tab2.id FROM $tab1 INNER JOIN $tab2
  ON $tab1.account = $tab2.id WHERE friend = $account  AND status = 0 ";
  if(!empty($_POST['limit'])){
  $friend_requests.= " LIMIT ".$_POST['limit']." ";
  }

  $my_requests     = " SELECT $tab1.friend,$tab1.date,$tab2.firstname,$tab2.online,$tab2.lastvisit,$tab2.lastname,$tab2.avatar,$tab2.id FROM $tab1 INNER JOIN $tab2
  ON $tab1.friend = $tab2.id WHERE account = $account  AND status = 0   ";
 if(!empty($_POST['limit'])){
  $my_requests    .= " LIMIT ".$_POST['limit']." ";
  }

  $friend_list     =  " SELECT $tab1.friend,$tab1.date,$tab2.firstname,$tab2.lastname,$tab2.online,$tab2.lastvisit,$tab2.avatar,$tab2.id FROM $tab1 INNER JOIN $tab2
  ON $tab1.account = $tab2.id OR $tab1.friend = $tab2.id  WHERE ($tab2.id != $account) AND ($tab1.friend = $account OR $tab1.account = $account )  AND status = 1 ";
  if(!empty($_POST['limit'])){
  $friend_list    .= " LIMIT ".$_POST['limit']." ";
  }
  $friend_requests = $this->mycon->query($friend_requests);
  while($friends_requests_row = $friend_requests->fetch_array()){
    if($this->outp !== ""){$this->outp .= ",";}
   
    $date        = json_decode($friends_requests_row['date'],true);
    $owner1      = $friends_requests_row['id'] == $sesid ? '1' : '0';

    $lastvisit     = $friends_requests_row['lastvisit'];
    $online = json_decode($friends_requests_row['online'],true);
    $online_status = $online['status'];
    $device        = $online['device'];
    
    
    $this->outp .= '{ "id"           :"'.$friends_requests_row['id'].'"        ,';
    $this->outp .= '  "owner"        :"'.$owner1.                            '",';
    $this->outp .= '  "date"         :"'.$date['date'].                      '",';
    $this->outp .= '  "month"        :"'.$date['month'].                     '",';
    $this->outp .= '  "year"         :"'.$date['year'].                      '",';
    $this->outp .= '  "hours"        :"'.$date['hours'].                     '",';
    $this->outp .= '  "minutes"      :"'.$date['minutes'].                   '",';
    $this->outp .= '  "online_status":"'.$online_status.                     '",';
    $this->outp .= '  "device"       :"'.$device.                            '",';
    $this->outp .= '  "lastvisit"    :['.$lastvisit.']                         ,';
    $this->outp .= '  "firstname"    :"'.$friends_requests_row['firstname'].'" ,';
    $this->outp .= '  "lastname"     :"'.$friends_requests_row['lastname'].'"  ,';
    $this->outp .= '  "avatar"       :"'.$friends_requests_row['avatar'].'"    }';
  } 

  $friend_list = $this->mycon->query($friend_list);
  while($friend_list_row = $friend_list->fetch_array()){
    if($this->outp2 !== ""){$this->outp2 .= ",";}  
    $date2          = json_decode($friend_list_row['date'],true);
    $owner2         = $friend_list_row['id'] == $sesid ? '1' : '0';

    $online2        = json_decode($friend_list_row['online'],true);
    $online_status2 = $online2['status'];
    $device2        = $online2['device'];
    $lastvisit2     = $friend_list_row['lastvisit'];

    $this->outp2 .= '{ "id"         :"'.$friend_list_row['id'].'"              ,';
    $this->outp2 .= '  "owner"      :"'.$owner2.                             '",';
    $this->outp2 .= '  "date"       :"'.$date2['date'].                      '",';
    $this->outp2 .= '  "month"      :"'.$date2['month'].                     '",';
    $this->outp2 .= '  "year"       :"'.$date2['year'].                      '",';
    $this->outp2 .= '  "hours"      :"'.$date2['hours'].                     '",';
    $this->outp2 .= '  "minutes"    :"'.$date2['minutes'].                   '",';
    $this->outp2 .= '  "online_status":"'.$online_status2.                   '",';
    $this->outp2 .= '  "device"       :"'.$device2.                          '",';
    $this->outp2 .= '  "lastvisit"    :['.$lastvisit2.']                       ,';
    $this->outp2 .= '  "firstname"  :"'.$friend_list_row['firstname'].'"       ,';
    $this->outp2 .= '  "lastname"   :"'.$friend_list_row['lastname'].'"        ,';
    $this->outp2 .= '  "avatar"     :"'.$friend_list_row['avatar'].'"          }';
  } 

  $my_requests = $this->mycon->query($my_requests);
  while($my_requests_row = $my_requests->fetch_array()){
    if($this->outp3 !== ""){$this->outp3 .= ",";}  
    $date3        = json_decode($my_requests_row['date'],true);
    $owner3       = $my_requests_row['id'] == $sesid ? '1' : '0';

    $online3        = json_decode($my_requests_row['online'],true);
    $online_status3 = $online3['status'];
    $device3        = $online3['device'];
    $lastvisit3     = $my_requests_row['lastvisit'];

    $this->outp3 .= '{ "id"         :"'.$my_requests_row['id'].'"              ,';
    $this->outp3 .= '  "owner"      :"'.$owner3.                             '",';
    $this->outp3 .= '  "date"       :"'.$date3['date'].                      '",';
    $this->outp3 .= '  "month"      :"'.$date3['month'].                     '",';
    $this->outp3 .= '  "year"       :"'.$date3['year'].                      '",';
    $this->outp3 .= '  "hours"      :"'.$date3['hours'].                     '",';
    $this->outp3 .= '  "minutes"    :"'.$date3['minutes'].                   '",';
    $this->outp3 .= '  "online_status":"'.$online_status3.                   '",';
    $this->outp3 .= '  "device"       :"'.$device3.                          '",';
    $this->outp3 .= '  "lastvisit"    :['.$lastvisit3.']                       ,';
    $this->outp3 .= '  "firstname"  :"'.$my_requests_row['firstname'].'"       ,';
    $this->outp3 .= '  "lastname"   :"'.$my_requests_row['lastname'].'"        ,';
    $this->outp3 .= '  "avatar"     :"'.$my_requests_row['avatar'].'"          }';
  } 
  $this->outp = '{"record":['.$this->outp.'],"record2":['.$this->outp2.'],"record3":['.$this->outp3.']}';
  echo $this->outp;
  //print_r(getallheaders()['Cookie']);
  //print_r(getallheaders());
 //echo session_id();
}


public function delete_profile(){
  session_start();
  $sesid    = $_SESSION['id'];
  $delete1  = " DELETE FROM `gc_account`     WHERE id               =  '".$sesid."'      ";
  $delete2  = " DELETE FROM `gc_label`       WHERE account          =  '".$sesid."'      ";
  $delete3  = " DELETE FROM `gc_posts`       WHERE account          =  '".$sesid."'      ";
  $delete4  = " DELETE FROM `gc_cardbs`      WHERE account          =  '".$sesid."'      ";
  $delete5  = " DELETE FROM `gc_item`        WHERE account          =  '".$sesid."'      ";
  $delete6  = " DELETE FROM `gc_activity`    WHERE object_owner     =  '".$sesid."' OR activator =  '".$sesid."'  ";
  $delete7  = " DELETE FROM `gc_follow`      WHERE account_follower =  '".$sesid."'      ";
  $delete8  = " DELETE FROM `gc_comments`    WHERE account          =  '".$sesid."'      ";
  $delete9  = " DELETE FROM `gc_likes`       WHERE account          =  '".$sesid."'      ";
  $delete10 = " DELETE FROM `gc_labelimages` WHERE account          =  '".$sesid."'      ";


  ///////////////////////DELETE FILES////////////////////////
  $file_labelimg = " SELECT id,label FROM `gc_labelimages` WHERE account   =  '".$sesid."'  ";
  $file_labelimg = $this->mycon->query($file_labelimg);
  while($complete = $file_labelimg->fetch_array()){
    if(!empty($complete['label'])){
    $this->deleteDir("../globalimg/label_images/".$complete['label']."/");
    }
  };
  $file_postimg   = " SELECT id FROM `gc_posts` WHERE account   =  '".$sesid."'  ";
  $file_postimg   = $this->mycon->query($file_postimg);
  while($complete_post = $file_postimg->fetch_array()){
    if(!empty($complete_post['id'])){
    $this->deleteDir("../globalimg/post_images/".$complete_post['id']."/");
    }
  };
  $file_avatar  = $this->deleteDir("../globalimg/account_avatar/".$sesid."/");

  $delete1  = $this->mycon->query($delete1 );
  $delete2  = $this->mycon->query($delete2 );
  $delete3  = $this->mycon->query($delete3 );
  $delete4  = $this->mycon->query($delete4 );
  $delete5  = $this->mycon->query($delete5 );
  $delete6  = $this->mycon->query($delete6 );
  $delete7  = $this->mycon->query($delete7 );
  $delete8  = $this->mycon->query($delete8 );
  $delete9  = $this->mycon->query($delete9 );
  $delete10 = $this->mycon->query($delete10);


 
if($delete1 AND $delete2 AND $delete3 AND $delete4 AND $delete5 AND $delete6){
  echo '{"status" : "1"}';
   session_destroy();
  }
  else{
  echo '{"status" : "0"}';
  }

}
public function toggle_profile($table)
{
  session_start();
  if(!empty($_SESSION['id']))
  {
  $status = $_POST['status'];
  $deact  = " UPDATE $this->prefix$table 
  SET active = '".$status."' , active_time = '".time()."' WHERE id = '".$_SESSION['id']."' ";
  if($this->mycon->query($deact))
  {
    echo '{"status":"1","text":"%action_updated%"}';
  }
  else
  {
    die('{"status":"0","text":"%action_error%"}');
  }
  }
}
public function online($table){
  session_start();
  $sesid      = isset($_SESSION['id']);
  $device     = $_POST['device'];
  $lastvisit  = $_POST['date'];
  $status     = $_POST['status'];
  $time       = time();
  //$json->device    = $device;
  //$json->time      = $time;

  $online_val = json_encode(array('device'=>$device,'time'=>$time,'status'=>$status));
  $online   = " UPDATE $this->prefix$table  SET 
  online    = '".$online_val."',
  lastvisit = '".$lastvisit."'

  WHERE id = $sesid";
  $online = $this->mycon->query($online);

  if($online)
  {
    echo '{"status":"1"}';
  }
  else
  {
    die('{"status":"0"}');
  }
}

public function state_exchange($val)
{
  switch($val)
  {
    case 1  :  $val = "%write_me%"         ;break;
    case 2  :  $val = "%boring%"           ;break;
    case 3  :  $val = "%dont_distrub%"     ;break;
    case 4  :  $val = "%busy%"             ;break;
    case 5  :  $val = "%back_in_five_min%" ;break;
    case 6  :  $val = "%on_the_place%"     ;break;
    default :  $val = "%on_the_place%"     ;break;
  }
  return $val;
}
public function status_exchange($val)
{
  switch($val)
  {
    case 0  :  $val = "%empty%"            ;break;
    case 1  :  $val = "%love_sweetvel%"    ;break;
    case 2  :  $val = "%in_love%"          ;break;
    case 3  :  $val = "%happy%"            ;break;
    case 4  :  $val = "%sad%"              ;break;
    case 5  :  $val = "%got_difficulties%" ;break;
    default :  $val = " "                  ;break;
  }
  return $val;
}
public function set_state($table)
{
session_start();
$sesid        = $_SESSION['id'];

$state_param  = $_POST['state_param'];
$state        = " UPDATE $this->prefix$table SET 
state         = '".$state_param."'
WHERE id      = $sesid ";
$state  = $this->mycon->query($state);
if($state)
{
  echo '{"status":"1","param":"'.$state_param.'","text":"'.$this->state_exchange($state_param).'"}';
}
else
{
die('{"status":"0"}');
}
}
public function set_status($table)
{
session_start();
$sesid            = $_SESSION['id'];
$status_param     = $_POST['status_param'];
$state            = " UPDATE $this->prefix$table SET 
status_relations  = '".$status_param."'
WHERE id          = $sesid ";
$state  = $this->mycon->query($state);
if($state)
{
  echo '{"status":"1","param":"'.$status_param.'","text":"'.$this->status_exchange($status_param).'"}';
}
else
{
die('{"status":"0"}');
}
}
public function clear_tmp()
{
   $files_arr = glob('C:/xampp1/htdocs/giftcard3/globalimg/tmp/*.tmp');
   if(count($files_arr) > 0)
   {
   foreach($files_arr as $files)
   {
   @$file_time = filectime($files);
   
  if(file_exists($files))
  {
  if(  ($file_time + 60 * 3) < time() ){
    unlink($files);
     }
    }
   }
  }
}
public function __destruct()
{ 
   $this->clear_tmp();
   $this->mycon->close();
}

}
class output extends main{


}


  ?>
