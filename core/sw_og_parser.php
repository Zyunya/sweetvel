<?php

class og_parser 
{
public function og()

{   
    if(isset($_POST['oglinkprev']))
    {
   
    $content  = new DOMDocument();
    $url      = $_POST['oglinkprev'];
    @$html    = file_get_contents($url);

    if($html)
    {
    @$content->loadHTML($html);
    $data = (object)[];
    $data->status = '1';
    foreach($content->getElementsByTagName('meta') as $meta)
    {    
     if(preg_match('/og:title/'            , $meta->getAttribute('property')))  {$data->title  =   utf8_decode($meta->getAttribute('content')); }
     if(preg_match('/og:description/'      , $meta->getAttribute('property')))  {$data->desc   =   utf8_decode($meta->getAttribute('content')); }
     if(preg_match('/og:image/'            , $meta->getAttribute('property')))  {$data->image  =   utf8_decode($meta->getAttribute('content')); }
     if(preg_match('/og:url/'              , $meta->getAttribute('property')))  {$data->url    =   utf8_decode($meta->getAttribute('content')); }
     if(preg_match('/og:video:secure_url/' , $meta->getAttribute('property')))
     if(preg_match('/embed/' , $meta->getAttribute('content')))
     {
      $data->video  =  utf8_decode($meta->getAttribute('content')); 
    }
    }
     echo json_encode($data,JSON_UNESCAPED_UNICODE);
     
    } 
     else
     {
      echo '{"status":"0"}';
     }
    
    }
  }
}

$og_parser = new og_parser();
$og_parser->og();


?>