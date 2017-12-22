<?php
include_once 'main.php';

class timeline_class extends main 
{

public function timeline($table){
session_start();

$sesid          = !empty($_POST['receiver']) ? $_POST['receiver'] : $_SESSION['id'];
$limiter        = isset($_POST['limit']) ? $_POST['limit']        : $this->limit;
$object_type    =  $_POST['object_type'];
$query          = "SELECT * FROM $this->prefix$table WHERE receiver ='". $sesid ."' AND object_type = '".$object_type."'  ORDER BY id DESC LIMIT ".$limiter." ";
 $query         =  $this->mycon->query($query);
 while($row     =  $query->fetch_array()){
$sendername     =  $this->userinfo($row['sender'],'firstname')." ".$this->userinfo($row['sender'],'lastname');
$date           =  json_decode($row['date'],true);
$date_full      =  $date['date'].".".$date ['month'].".".$date ['year']." ".$date ['hours'].":".$date ['minutes'];
$senderavatar   =  $this->userinfo($row['sender'],'avatar');
$message        =  $this->filter_outp($row['message']);
$postowner      =  $row['sender']   == $_SESSION['id'] ? '1' : '0';
$object_owner   =  $row['sender'] == $_SESSION['id'] ||  $row['receiver'] == $_SESSION['id'] ? '1' : '0';

    $img    = glob($row['path']. "/*.{jpg,png,JPG,PNG}",GLOB_BRACE );
    $audio  = glob($row['path']. "/*.{wav,mp3,webm,ogg}",GLOB_BRACE );
    $imgg   = json_encode($this->get_abs_path_array($img));
    $audioo = json_encode($this->get_abs_path_array($audio));

  if($this->outp !== ""){$this->outp .= ","; }

  $this->outp .= '{ "id"                : "'.$row['id'].' "                         ,';
  $this->outp .= '  "message"           : "'.$message.'"                            ,';
  $this->outp .= '  "sender"            : "'.$this->filter_outp($sendername).'"     ,';
  $this->outp .= '  "object_type_glob"  : "'.$row['object_type'].' "                ,';
  $this->outp .= '  "object_type"       :     "timeline"                            ,';
  $this->outp .= '  "comments_counter"  :"'  . $this->statistic_counter_comments('comments','timeline',$row['id']). '",';
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

public function sendmessage_timeline($table){
session_start();
      
      $path  = "";
      $type  = $this->validformats;
      $size  = $this->maxsize;
     
     if(count($_FILES) < 4){

       if(count($_FILES) > 0)
       {
       $uniqdir     = uniqid();
       $dir         = '../globalimg/tmln/'.$_SESSION['id'].'/'.$uniqdir;
      
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
$sesid      = $_SESSION['id'];
$receiver   = !empty($_POST['receiver']) ? $_POST['receiver'] : $_SESSION['id'];
$message    = $this->filter_inp($_POST['message']);
$date       = $_POST['date'];
$object_type= $_POST['object_type'];
$query      = " INSERT INTO $this->prefix$table SET
sender      = '".$sesid."',
receiver    = '".$receiver."',
message     = '".$message."',
object_type = '".$object_type."',
og_message  = 'null',
type        = 'common',
path        = '".$path."',
date        = '".$date ."',
time        = '".time()."'
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
public function sendmessage_timeline_voice($table){
session_start();
$sesid     = $_SESSION['id'];
$receiver  = !empty($_POST['receiver']) ? $_POST['receiver'] : $_SESSION['id'];
$message   = $_POST['message'];
$date      = $_POST['date'];

 $uniqdir  = uniqid();
 $uniqfile = uniqid();
 $dir      = '../globalimg/tmln/'.$_SESSION['id'].'/'.$uniqdir;
 $folder   = mkdir($dir,0777,true);
 $path     = $dir.'/'.$uniqfile.'.wav';

$query     = " INSERT INTO $this->prefix$table SET
sender     = '".$sesid."',
receiver   = '".$receiver."',
og_message = '',
message    = '".$message."',
type       = 'audio',
path       = '".$dir."',
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

public function sendmessage_timeline_og($table){
 session_start();

 $uniqdir     = uniqid();
 $uniqfile    = uniqid();
 $dir         = '../globalimg/tmln/'.$_SESSION['id'].'/'.$uniqdir;
 $folder      = mkdir($dir,0777,true);
 $file        = file_get_contents($_POST['image']);
 $path        = $dir.'/'.$uniqfile.'.png';
 
file_put_contents($path, $file);

$sesid       = $_SESSION['id'];
$receiver    = !empty($_POST['receiver']) ? $_POST['receiver'] : $_SESSION['id'];
$date        = $_POST['date'];
$object_type = $_POST['object_type'];

////////OG_VARS///////
$title    = $_POST['title'];
$desc     = $_POST['desc'];
$og_desc  = $_POST['og_desc'];
$video    = $_POST['video'];
$url      = $_POST['url'];

$message  = json_encode(array('title' =>$this->filter_inp($title),
                              'desc'  =>$this->filter_inp($og_desc),
                              'url'   =>$this->filter_inp($url),
                              'video' =>$this->filter_inp($video),
                              'image' =>$this->filter_inp($this->path_abs($path))
                                      ),JSON_UNESCAPED_UNICODE);
$query      = " INSERT INTO $this->prefix$table SET
sender      = '".$sesid."',
receiver    = '".$receiver."',
og_message  = '".$message."',
message     = '".$desc."',
object_type = '".$object_type."',
type        = 'link',
path        = '".$dir."',
date        = '".$date ."',
time        = '".time()."'
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

public function delete_message_timeline($table){
session_start();
$sesid       = $_SESSION['id'];
$messageid   = $_POST['messageid'];
$path        = $_POST['path'];
$object_type = trim($_POST['object_type']);

$delete_post_account = "DELETE FROM $this->prefix$table WHERE id = '".$messageid."' AND (sender = '".$sesid."' OR receiver = '".$sesid."') ";
$delete_post_label   = "DELETE FROM $this->prefix$table WHERE id = '".$messageid."' AND sender = '".$sesid."' ";

if($object_type == 'label_timeline')
{
     if($this->mycon->query($delete_post_label)){
        echo '{"status":"1","record": "%action_success%"}';
          if(file_exists($path))
          {
          $this->deleteDir($path);
          }
        }
     else{
       die ('{"status":"0","record": "%action_error_query%"}');
          }
}
else if($object_type == 'account_timeline')
{
       if($this->mycon->query($delete_post_account)){
        echo '{"status":"1","record": "%action_success%"}';
          if(file_exists($path))
          {
          $this->deleteDir($path);
          }
        }
     else{
       die ('{"status":"0","record": "%action_error_query%"}');
          }
}
else
{
  die ('{"status":"0","record": "%action_error_global%"}');
}

}


}

?>