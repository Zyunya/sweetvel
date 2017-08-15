<?php 
include_once 'main.php';
class weather_server extends main
{

  public function weather()
{
session_start();

if(isset($_SESSION['id']))
{
$sesid        = $_SESSION['id'];
$location     = "SELECT * FROM `gc_account` WHERE id = $sesid ";
$location = $this->mycon->query($location)->fetch_array();
$ip       = $location['ip'] OR  $_SERVER['REMOTE_ADDR'];

$key ='17cc0bde28328ebc865f2a0d53ccc69e';
$request = file_get_contents('http://freegeoip.net/json/'.$ip);
$latlong = json_decode($request,true);
$weather = file_get_contents('http://api.openweathermap.org/data/2.5/weather?units=metric&lat=' . $latlong['latitude'] . '&lon=' .$latlong['longitude'] .'&appid='.$key);
//echo $weather;
$json        = json_decode($weather,true);
$weather     = isset($json['weather'][0])    ? $json['weather'][0]    : '';
$temperature = isset($json['main']['temp'])  ? $json['main']['temp']  : '';
$wind        = isset($json['wind']['speed']) ? $json['wind']['speed'] : '';
$humidity    = isset($json['main']['humidity']) ? $json['main']['humidity'] : '';
$sunrise     =  date('H : i', $json['sys']['sunrise']);
$sunset      =  date('H : i', $json['sys']['sunset']);
$country     = isset($json['sys']['country']) ? $json['sys']['country'] : '';
$city        = isset($json['name'])           ? $json['name']           : '';
$cityid      = isset($json['id'])             ? $json['id']             : '';
$params = '{"desc1"      :"'.$weather['main'].'",
            "desc2"      :"'.$weather['description'].'",
            "temperature":"'.$temperature.'",
            "wind"       :"'.$wind.'",
            "country"    :"'.$country.'",
            "city"       :"'.$city.'",
            "humidity"   :"'.$humidity.'",
            "sunrise"    :"'.$sunrise.'",
            "sunset"     :"'.$sunset.'",
            "cityid"     :"'.$cityid.'"
            }';
echo $params;

}
else
{
    die('{"status" : "1","text":"unable_retreive_data"}');
}
  }

}
$obj = new weather_server();
$obj->weather();
?>