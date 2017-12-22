<?php
header('Content-Type: text/html; charset=utf-8'); 
include 'opengraph.php';

@$graph = OpenGraph::fetch($_POST['oglinkprev']);

if(is_object($graph))
{
$outp = json_encode(array('status'=>'1','url'=>$graph->url,'title'=>$graph->title,'desc'=>$graph->description,'image'=>$graph->image),JSON_UNESCAPED_UNICODE);
}
else
{
$outp = json_encode(array('status'=>'0'));
}
echo $outp;

?>