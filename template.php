<?php
include 'core/main.php';
include 'admin/core/admincore.php';

class shablonizer extends main  {


  private $lang;
  private $array;
  private $file;
  private $arr;


  public function lang($page){
    $this->langarray = array('ru','en');
    $this->serverlang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
    $this->autolang =(!in_array($this->serverlang,$this->langarray)) ? 'en' : $this->serverlang;
   if(!empty($_GET['lang'])){
     $this->getlang = !in_array($_GET['lang'],$this->langarray) ? $this->autolang : $_GET['lang'];
   }
   else{$this->getlang = $this->autolang;}

    $this->array = parse_ini_file("lang/".$this->getlang.".ini"); //Открываем соответствующий языковой файл
      //Выводим значение языковой константы

     $this->file = file_get_contents($page);

     $this->arr = str_ireplace(array(
 "%home%",          "%from%",          "%choose%",            //0
 "%theme%",         "%theme1%",        "%whatevent%",         //1
 "%receiver%",      "%sender%",        "%wishes%",            //2
 "%imageset%",      "%creategc%",      "%gcresult%",          //3
 "%gclink%",        "%copy%",          "%sendtoemail%",       //4
 "%email%",         "%audio%",         "%preview%",           //5
 "%send%",          "%maintitle%",     "%subtitle%",          //6
 "%lang%",          "%features%",      "%about%",             //7
 "%home%",          "%contacts%",      "%dftitle%",           //8
 "%dfsubtitle%",    "%df1%",           "%df2%",               //9
 "%df3%",           "%df4%",           "%df5%",               //10
 "%df6%",           "%dfd1%",          "%dfd2%",              //11
 "%dfd3%",          "%dfd4%",          "%dfd5%",              //12
 "%dfd6%",          "%name%",          "%required%",          //13
 "%comment%",       "%your%",          "%sergeychakir%",      //14
 "%gfleadership%",  "%creatorceo%",    "%info1%",             //15
 "%socialapps%",    "%yours%",         "%askme%",             //16
 "%ceoinfo%",       "%ceoshortinfo%",  "%playpauseclick%",    //17
 "%close%",         "%titleparent%",   "%feed%",              //18
 "%today%",         "%tomorrow%",      "%fullfeed%",          //19
 "%openfulldesc%",  "%congratulate%",  "%someone%",           //20
 "%options%",       "%share%",         "%license%",           //21
 "%attribution%",   "%authorship%",    "%findevent%",         //22
    ),
  array(
 $this->array['home'],           $this->array['from'],           $this->array['choose'],           //0
 $this->array['theme'],          $this->array['theme1'],         $this->array['whatevent'],        //1
 $this->array['receiver'],       $this->array['sender'],         $this->array['wishes'],           //2
 $this->array['imageset'],       $this->array['creategc'],       $this->array['gcresult'],         //3
 $this->array['gclink'],         $this->array['copy'],           $this->array['sendtoemail'],      //4
 $this->array['email'],          $this->array['audio'],          $this->array['preview'],          //5
 $this->array['send'],           $this->array['maintitle'],      $this->array['subtitle'],         //6
 $this->array['lang'],           $this->array['features'],       $this->array['about'],            //7
 $this->array['home'],           $this->array['contacts'],       $this->array['dftitle'],          //8
 $this->array['dfsubtitle'],     $this->array['df1'],            $this->array['df2'],              //9
 $this->array['df3'],            $this->array['df4'],            $this->array['df5'],              //10
 $this->array['df6'],            $this->array['dfd1'],           $this->array['dfd2'],             //11
 $this->array['dfd3'],           $this->array['dfd4'],           $this->array['dfd5'],             //12
 $this->array['dfd6'],           $this->array['name'],           $this->array['required'],         //13
 $this->array['comment'],        $this->array['your'],           $this->array['sergeychakir'],     //14
 $this->array['leadership'],     $this->array['creatorceo'],     $this->array['info1'],            //15
 $this->array['socialapps'],     $this->array['yours'],          $this->array['askme'],            //16
 $this->array['ceoinfo'],        $this->array['ceoshortinfo'],   $this->array['playpauseclick'],   //17
 $this->array['close'],          $this->array['titleparent'],    $this->array['feed'],             //18
 $this->array['today'],          $this->array['tomorrow'],       $this->array['fullfeed'],         //19
 $this->array['openfulldesc'],   $this->array['congratulate'],   $this->array['someone'],          //20
 $this->array['options'],        $this->array['share'],          $this->array['licence'],          //21
 $this->array['attribution'],    $this->array['authorship'],     $this->array['findevent'],        //22
                ), $this->file);



eval (' ?' . '>' . $this->arr. '<' . '?php ');
  }


  public function tex(){
    if(isset($_POST['sub2'])){
  echo $_POST['tex'];

    }
  }

}



?>
