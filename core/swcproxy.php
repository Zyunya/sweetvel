<?php require "main.php" ; $main = new output; ?>

<!DOCTYPE html>

<html>
<head>
<title><?php  echo $main->returnparam($_GET['tab'],"event","keyvalue",$_GET['gc']) ?></title>
<meta http-equiv="Content-Type"  content="text/html; charset=UTF-8" />
<meta property="og:url"          content="<?php  echo $main->returnparam($_GET['tab'],"proxy","keyvalue",$_GET['gc']) ?>"/>
<meta property="og:title"        content="<?php  echo $main->returnparam($_GET['tab'],"event","keyvalue",$_GET['gc']) ?>"/>
<meta property="og:description"  content="<?php  echo $main->returnparam($_GET['tab'],"description","keyvalue",$_GET['gc']) ?>"/>
<meta property="og:image"        content="<?php  echo "https://sweetvel.com/".json_decode($main->returnparam($_GET['tab'],"img","keyvalue",$_GET['gc']))->img1 ?>"/>
<meta property="og:type"         content="website" />


</head>
    <body>
    <?php header("Refresh: 1; url=".$main->returnparam($_GET['tab'],"link","keyvalue",$_GET['gc']));   ?>
     
    

    </body>

</html>

 