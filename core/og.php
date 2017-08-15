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
     if(preg_match('/og:title/'            , $meta->getAttribute('property')))  {$data->title  =   $meta->getAttribute('content'); }
     if(preg_match('/og:description/'      , $meta->getAttribute('property')))  {$data->desc   =   $meta->getAttribute('content'); }
     if(preg_match('/og:image/'            , $meta->getAttribute('property')))  {$data->image  =   $meta->getAttribute('content'); }
     if(preg_match('/og:url/'              , $meta->getAttribute('property')))  {$data->url    =   $meta->getAttribute('content'); }
     if(preg_match('/og:video:secure_url/' , $meta->getAttribute('property')))
     if(preg_match('/embed/' , $meta->getAttribute('content')))
     {
      $data->video  =  utf8_decode($meta->getAttribute('content')); 
    }
    }
     $steam      = json_encode($data,JSON_UNESCAPED_UNICODE);
     $stream_res = json_decode($steam,true);
     echo "<a class = 'nodec'  href = '".$stream_res['url']."' target = '".$stream_res['url']."'>
      <table class = 'boxsh4' cellspacing = '10px' border = '0' id = 'table_ogpreview' width = '100%'>
       <tr >
         <td align = 'center' rowspan = '3'><img style = 'max-height:150px' src = '".$stream_res['image']."' ></td>
         <td><span class = 'font2 color2 fsize15px'>".$stream_res['title']."</span></td>
       </tr>
       <tr>
       <td><span class = 'font2 color2 fsize12px'>".$stream_res['desc']."</span></td>
       </tr>
       <tr><td  ><span id = 'gcresultlink' class = 'font2 color2 fsize10px'>".$stream_res['url']."</span></td></tr>
     </table>
	 </a>";
     
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
