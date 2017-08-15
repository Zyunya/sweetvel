<?php


@include_once '../core/main.php';


class admin extends main  {

  

  public function autoriz($table){
  if(!empty($_POST['log']) AND !empty($_POST['pass'])){
  $this->login    = trim(htmlspecialchars(stripslashes($_POST['log'])));
  $this->password = trim(htmlspecialchars(stripslashes($_POST['pass'])));

  $this->query = "SELECT * FROM $this->prefix$table WHERE login = '".$this->login."'";
  $this->query = $this->mycon->query($this->query);
  $this->row = $this->query->fetch_array();
  if(!empty($this->row['login'])){
    if($this->row['password'] == $this->password){
      session_start();
     $_SESSION['id']   = $this->row['id'];
     $_SESSION['name'] = $this->row['name'];

     echo '{"status":"1","text":"Success"}';
     
    }
    else {die('{"status":"0","text":"Wrong Password"}');}
  }
    else{
         die('{"status":"0","text":"Wrong Login"}');
  }
  }
   else { 
        die('{"status":"0","text":"Fill All Fields"}');
        }

  }

public function get_users($table){
session_start();
$query     = "SELECT * FROM $this->prefix$table";
 if(!empty($_SESSION['id'])){
$query     = $this->mycon->query($query);
$quantity  = $query->num_rows;
while($row = $query->fetch_array()){
if($this->outp !== ""){$this->outp .= "," ;};
$date = json_decode($row['date'],true);
$this->outp .='{"userfname" :"'.$row['firstname'].'"  ,';
$this->outp .=' "userlname" :"'.$row['lastname'].'"   ,';
$this->outp .=' "userid"    :"'.$row['id'].'"         ,';
$this->outp .=' "useravatar":"'.$row['avatar'].'"     ,';
$this->outp .=' "gender"    :"'.$row['gender'].  '"   ,';
$this->outp .=' "regtype"   :"'.$row['regtype']. '"   ,';
$this->outp .=' "date"      :"'.$date['date'].   '"   ,';
$this->outp .=' "month"     :"'.$date['month'].  '"   ,';
$this->outp .=' "year"      :"'.$date['year'].   '"   }';
}
$this->outp = '{"record":['.$this->outp.'],"quantity":"'.$quantity.'"}';
echo $this->outp;
 }
}

public function delete_profile($table){
   session_start();
   if(!empty($_SESSION['id'])){

  $delete = " DELETE FROM $this->prefix$table WHERE id = '".$_POST['id']."'";
  $delete = $this->mycon->query($delete);
  if($delete){
    echo '{"status" : "1","text" : "deleted"}';
  }
  else{
    echo '{"status" : "0","text" : "Error"}';
  }
   }
   else{
     return false;
   }
}
public function get_visitors($table){
  session_start();
$this->query = "SELECT * FROM $this->prefix$table ORDER By id DESC LIMIT 50 ";
 if(!empty($_SESSION['id'])){
$this->query = $this->mycon->query($this->query);
while($row = $this->query->fetch_array()){
if($this->outp !== ""){$this->outp .= "," ;};
$this->outp .='{"time"    :"'.$row['time'].'"      ,';
$this->outp .=' "ip"      :"'.$row['ip'].'"        ,';
$this->outp .=' "referer" :"'.$row['referer'].'"   ,';
$this->outp .=' "device"  :"'.$row['device'].  '"  ,';
$this->outp .=' "coord"   :"'.$row['coord']. '"    }';

}
$this->outp = '{"record":['.$this->outp.']}';
echo $this->outp;
 }
}
public function get_callback($table){
  session_start();
$this->query = "SELECT * FROM $this->prefix$table ORDER BY id DESC";
 if(!empty($_SESSION['id'])){
$this->query = $this->mycon->query($this->query);
while($row = $this->query->fetch_array()){
if($this->outp !== ""){$this->outp .= "," ;};
$date = json_decode($row['date'],true);
$this->outp .='{"name"      :"'.$row['name'].'"         ,';
$this->outp .=' "email"     :"'.$row['email'].'"        ,';
$this->outp .=' "comment"   :"'.$row['comment'].'"      ,';
$this->outp .=' "date"      :"'.$date['date'].  '"      ,';
$this->outp .=' "month"     :"'.$date['month'].  '"     ,';
$this->outp .=' "year"      :"'.$date['year'].  '"      ,';
$this->outp .=' "hours"     :"'.$date['hours'].  '"     ,';
$this->outp .=' "minutes"   :"'.$date['minutes']. '"    }';

}
$this->outp = '{"record":['.$this->outp.']}';
echo $this->outp;
 }
}
public function log_out(){
  session_start();
 
  if(isset($_SESSION['id'])){
  session_destroy();
  session_unset();
  
}
echo '1';
}

public function check_session(){
     session_start();
  if(isset($_SESSION['id'])){

     echo '1';
  }
  else{
    
     echo '0';
     
  }
}



}



?>