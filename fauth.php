<?php
header("Access-Control-Allow-Origin: *");
include_once 'core/main.php';


class autht extends main{


public function fb_in($table){
$response = file_get_contents('https://graph.facebook.com/v2.9/oauth/access_token?client_id=643393609200822&redirect_uri=https://sweetvel.com/fauth.php&client_secret=962d229e07c05a91d3f856699a4a623a&code='.$_GET['code'].' ');
$repsonse_fin = json_decode($response,true);
$params = file_get_contents('https://graph.facebook.com/me?fields=id,email,gender,name,first_name,last_name,verified&access_token='.$repsonse_fin['access_token']);
$params_p = json_decode($params,true);




	if($params_p['verified'] == '1'){

	
   $query = "SELECT * FROM $this->prefix$table WHERE login =  '".$params_p['email']."'";
   $result = $this->mycon->query($query);
   $row = $result->fetch_array();
	if(!empty($row['login'])){
		$queryupdate = "UPDATE $this->prefix$table SET
login       = '".$params_p['email']."',
ip          = '".$_SERVER['REMOTE_ADDR']."',
firstname   = '".$params_p['first_name']."',
lastname    = '".$params_p['last_name']."',
gender      = '".$params_p['gender']."',
avatar      = 'https://graph.facebook.com/".$params_p['id']."/picture?type=large'
WHERE login = '".$params_p['email']."' AND regtype = 'fb'";
 $this->mycon->query($queryupdate);


$lifetime=60*60*24*7;

session_start();
setcookie(session_name(),session_id(),time()+$lifetime);
		$object =  '{"id":"'.$params_p['id'].'","email":"'.$params_p['email'].'","verified":"'.$params_p['verified'].'","sesid":"'.session_id().'","sesname":"'.session_name().'"}';
		echo "<h3 id ='jsp'>".$object."<h3/>";

	

    $_SESSION['id'] = $row['id'];
    $_SESSION['firstname'] = $row['firstname'];
    echo $row['id'];
		
    
	}
	   else{
		   $queryinsert = "INSERT INTO $this->prefix$table
SET 
login       = '".$params_p['email']."',
ip          = '".$_SERVER['REMOTE_ADDR']."',
firstname   = '".$params_p['first_name']."',
lastname    = '".$params_p['last_name']."',
gender      = '".$params_p['gender']."',
avatar      = 'https://graph.facebook.com/".$params_p['id']."/picture?type=large',
date       = 'date',
regtype    = 'fb',
privacy    = '0'
";
		 $this->mycon->query($queryinsert);
		 $selectx = "SELECT * FROM $this->prefix$table WHERE login =  '".$params_p['email']."'";
         $result = $this->mycon->query($selectx);
         $rowx = $result->fetch_array(); 
         session_start(); 
	     setcookie(session_name(),session_id(),time()+365*24*3600);
         $_SESSION['id'] = $rowx['id'];
         $_SESSION['table'] = $table;
         echo $rowx['id'];
         $this->start_label($_SESSION['id']);
	   }
     }
		if (!empty($_SERVER['HTTP_REFERER'] ))
		{
 // header("Refresh:3 URL = ".$_SERVER['HTTP_REFERER']);
		}
else
   {
	echo "No referrer";
   }
	
 }

}
		$obj = new autht;
	    $obj->fb_in('account');
        
?>
<script>
	

</script>