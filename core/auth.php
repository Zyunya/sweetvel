<?php
require_once 'main.php';
header('Access-Control-Allow-Origin: http://185.70.109.18:4000'); 

 class Auth extends main  {


public function Oath($table){
 session_start();
 if(isset($_SESSION['id']))
 {
  $outp = "";
  if($outp !== ""){$outp .= ",";}
  $query = "SELECT * FROM $this->prefix$table WHERE id = '".$_SESSION['id']."'";
  $query = $this->mycon->query($query);
  $param = $query->fetch_array();

  $user = $this->filter_outp($param['firstname'])." ".$this->filter_outp($param['lastname']);

 $outp =  json_encode(array('sender'=>$user,'id'=>$param['id'],'senderavatar'=>$param['avatar']));

   echo $outp;
 }
 else{
   die('{"status" : "0","text":"no_response_from_server"}');
 }
}


}

$auth = new Auth();
$auth->Oath('account');