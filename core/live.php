<?php
 header('Access-Control-Allow-Origin: *');  
include_once "main.php";

class live extends main{


/////////////////////////////LIVE LIKES////////////////////////////////
public function live_like($table,$table2){
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
session_start();
$query = "SELECT COUNT(*) as counter FROM $this->prefix$table INNER JOIN  $this->prefix$table2
ON $this->prefix$table2.id = $this->prefix$table.object_id WHERE $this->prefix$table2.account = '".$_SESSION['id']."' ";
$query   = $this->mycon->query($query);
$row     = $query->fetch_array();
$counter = $row['counter'];
echo "data:{$counter}\n\n";
flush();
}
//////////////////////////////LIVE_POST_LIKES/////////////////
public function live_post_like($table){
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
session_start();
$sesid    = $_SESSION['id'];
$id       = $_POST['id'];
$query    = " SELECT COUNT(*) as counter FROM $this->prefix$table WHERE   object_owner ='".$sesid."' AND object_type = 'post' ";
$row      = $this->mycon->query($query)->fetch_array();
$counter  = $row['counter'];
echo "data:{$counter}\n\n";
flush();
}

///////////////////////////////////////LIVE_FOLLOWER//////////////////
public function live_followers($table,$table2){
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
$tab1 =  $this->prefix.$table;
$tab2 =  $this->prefix.$table2;

session_start();
$query = " SELECT COUNT(*) as counter FROM $tab1 INNER JOIN  $tab2
ON $tab1.object_followed = $tab2.id WHERE $tab1.account_follower != '".$_SESSION['id']."' AND  $tab2.account = '".$_SESSION['id']."'  ";
$query   = $this->mycon->query($query);
$row     = $query->fetch_array();
$counter = $row['counter'];
echo "data:{$counter}\n\n";
flush();
}
public function who_follows($table,$table2){
$tab1 =  $this->prefix.$table;
$tab2 =  $this->prefix.$table2;
session_start();
$query = " SELECT  $tab1.account_follower,$tab2.label_name,$tab2.id FROM $tab1 INNER JOIN  $tab2
ON $tab1.object_followed = $tab2.id WHERE   $tab2.account = '".$_SESSION['id']."' ORDER BY $tab1.id DESC ";
$row       = $this->mycon->query($query)->fetch_array();
$user      = $this->userinfo($row['account_follower'],'firstname')." ".$this->userinfo($row['account_follower'],'lastname');
$avatar    = $this->userinfo($row['account_follower'],'avatar');
$data = '{"record":{"user":"'.$user.'","avatar":"'.$avatar.'","title":"'.$row['label_name'].'"}}';
echo $data;
}
public function wholiked($table,$table2){
session_start();
$query = "SELECT $this->prefix$table2.label_name,$this->prefix$table.account FROM $this->prefix$table INNER JOIN  $this->prefix$table2
ON $this->prefix$table2.id = $this->prefix$table.object_id WHERE $this->prefix$table2.account = '".$_SESSION['id']."' 
ORDER by $this->prefix$table.id DESC";
$query     = $this->mycon->query($query);
$row       = $query->fetch_array();
$counter   = $query->num_rows;
$title     = $row['label_name'];
$user      = $this->userinfo($row['account'],'firstname')." ".$this->userinfo($row['account'],'lastname');
$avatar    = $this->userinfo($row['account'],'avatar');
$data = '{"record":{"user":"'.$user.'","avatar":"'.$avatar.'","title":"'.$title.'","type":"label","counter":"'.$counter.'"}}';
echo $data;
}
public function wholiked_post($table,$table2){
session_start();
$query = "SELECT $this->prefix$table2.text,$this->prefix$table.account FROM $this->prefix$table INNER JOIN  $this->prefix$table2
ON $this->prefix$table2.id = $this->prefix$table.object_id WHERE $this->prefix$table2.account = '".$_SESSION['id']."' 
ORDER by $this->prefix$table.id DESC";
$query     = $this->mycon->query($query);
$row       = $query->fetch_array();
$counter   = $query->num_rows;
$title     = $row['text'];
$user      = $this->userinfo($row['account'],'firstname')." ".$this->userinfo($row['account'],'lastname');
$avatar    = $this->userinfo($row['account'],'avatar');
$data = '{"record":{"user":"'.$user.'","avatar":"'.$avatar.'","title":"'.substr($title,0,25).'...","type":"post",",counter":"'.$counter.'"}}';
echo $data;
}
//////////////////////LIVE_CHAT/////////////////////////////////////////

public function live_chat($table){
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
session_start();
$sesid    = $_SESSION['id'];
$id       = $_POST['id'];
$query    = " SELECT COUNT(*) as counter FROM $this->prefix$table WHERE  receiver ='". $sesid ."' ";
$queryst  = " SELECT COUNT(*) as status  FROM $this->prefix$table WHERE  receiver ='". $sesid ."' OR sender = '". $sesid ."' AND status = '1'";
$unread   = " SELECT COUNT(*) as unread  FROM $this->prefix$table WHERE  receiver ='". $sesid ."'  AND status = '0'";
$row      = $this->mycon->query($query)->fetch_array();
$rowst    = $this->mycon->query($queryst)->fetch_array();
$rowunr   = $this->mycon->query($unread)->fetch_array();

$counter = $row['counter'];
$status  = $rowst['status'];
echo 'data:{' .PHP_EOL;
echo 'data:"messages":"'.$row['counter']. '", '.PHP_EOL;
echo 'data:"status"  :"'.$rowst['status'].'" ,'.PHP_EOL;
echo 'data:"unread"  :"'.$rowunr['unread'].'" '.PHP_EOL;
echo 'data:}'.PHP_EOL;
echo PHP_EOL;
flush();
}

public function live_common($table){
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
session_start();
$sesid    = $_SESSION['id'];

$query    = " SELECT COUNT(*) as counter  FROM $this->prefix$table WHERE  object_owner ='". $sesid ."' AND activator != '".$sesid."'";
$quantity = " SELECT COUNT(*) as quantity FROM $this->prefix$table WHERE  object_owner ='". $sesid ."' AND status = '0' AND activator != '".$sesid."'";
$row      = $this->mycon->query($query)->fetch_array();
$rowquan  = $this->mycon->query($quantity)->fetch_array();
$counter  = $row['counter'];
echo 'data:{' .PHP_EOL;
echo 'data:"update"    :"'.$row['counter'].      '",'.PHP_EOL;
echo 'data:"quantity"  :"'.$rowquan['quantity'].'"  '.PHP_EOL;
echo 'data:}'.PHP_EOL;
echo PHP_EOL;
flush();
}
public function live_common_refresh($table){
session_start();
$sesid    = $_SESSION['id'];
$update   = "  UPDATE $this->prefix$table SET status  = '1' WHERE  object_owner = '".$sesid."' ";
$delete   = "  DELETE FROM $this->prefix$table WHERE  object_owner = '".$sesid."' AND '".time()."' > `time` + 60 * 60  * 24 * 14   ";
$this->mycon->query($update);
$this->mycon->query($delete);
 }
public function live_common_checkupdates($table){
session_start();
$sesid    = $_SESSION['id'];
$check    = "SELECT COUNT(*) as counter FROM $this->prefix$table  WHERE  object_owner = '".$sesid."' AND status = '0' AND activator != '".$sesid."'  ";
$row      = $this->mycon->query($check)->fetch_array();
if(!empty($row['counter'])){
    $counter = $row['counter'];
    echo '{"status":"1","quantity" : "'.$counter.'"}';
}
else{
   echo '{"status":"0","quantity" : "'.$counter.'"}';
}

 }
 ///////////////////////////FRIEND_REQUEST_LIVE///////
 public function live_friends($table){
     header('Content-Type: text/event-stream');
     header('Cache-Control: no-cache');
     session_start();
     $sesid         = $_SESSION['id'];
     $request       = " SELECT COUNT(*) as counter  FROM $this->prefix$table WHERE friend  = '".$sesid."' AND status = 0 ";
     $confirmation  = " SELECT COUNT(*) as counter2 FROM $this->prefix$table WHERE account = '".$sesid."' AND status = 1 ";

     $request       = $this->mycon->query($request)->fetch_array();
     $confirmation  = $this->mycon->query($confirmation)->fetch_array();

     $requests      = $request['counter'];
     $confirmations = $confirmation['counter2'];

     echo 'data:{' .PHP_EOL;
     echo 'data:"request"       :  "'.$requests.      '",'.PHP_EOL;
     echo 'data:"confirmation"  :  "'.$confirmations.'"  '.PHP_EOL;
     echo 'data:}'.PHP_EOL;
     echo PHP_EOL;
     flush();
 }
 public function live_friends_new_request($table1,$table2){
     $tab1 = $this->prefix.$table1;
     $tab2 = $this->prefix.$table2;
     session_start();
     $sesid = $_SESSION['id'];
     $data  = " SELECT $tab2.firstname,$tab2.lastname,$tab2.avatar,$tab1.friend,$tab1.account,$tab1.id  
     FROM $tab1 INNER JOIN $tab2 ON $tab1.account = $tab2.id WHERE $tab1.friend = $sesid ";
     $data = $this->mycon->query($data)->fetch_array();
     echo '{"activator_fname" :"'.$data['firstname'].'",
            "activator_lname" :"'.$data['lastname']. '",
            "activator_avatar":"'.$data['avatar'].  '",
            "actiontype"      :  "friendrequest"      ,
            "title2"          :  " "                  ,
            "title"           :"%friendrequest%"    }';

 }
 public function live_friends_request_confirmed($table1,$table2){
     $tab1 = $this->prefix.$table1;
     $tab2 = $this->prefix.$table2;
     session_start();
     $sesid = $_SESSION['id'];
     $data  = " SELECT $tab2.firstname,$tab2.lastname,$tab2.avatar,$tab1.friend,$tab1.account,$tab1.id  
     FROM $tab1 INNER JOIN $tab2 ON $tab1.friend = $tab2.id WHERE $tab1.account = $sesid AND status = 1 ";
     $data = $this->mycon->query($data)->fetch_array();
     echo '{"activator_fname" :"'.$data['firstname'].'",
            "activator_lname" :"'.$data['lastname']. '",
            "activator_avatar":"'.$data['avatar'].  '",
            "actiontype"      :  "friendrequest"      ,
            "title2"          :  " "                  ,
            "title"           :"%confirmedfriend%"    }';

 }
}



?>