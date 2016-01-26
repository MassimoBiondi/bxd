<?php
    require_once('./bxd/bxd.php');

    $box = new bxd();

    $box->bxd(array('cont'=>'aaa','entr'=>'404.md'));
    echo '<hr>';

    $box->bxd(array('cont'=>'aaa','entr'=>'aaa.txt','recr'=>true, 'fltr'=>'md', 'rndr' => 'render'));
    echo '<hr>';

    $box->bxd(array('cont'=>'aaa','entr'=>'*'));
    echo '<hr>';

    echo "'cont'=>'aaa','entr'=>'*', 'recr'=>true <br>";
    $box->bxd(array('cont'=>'aaa','entr'=>'*', 'recr'=>true));
    echo '<hr>';
    
    echo "'cont'=>'aaa','entr'=>'*','fltr'=>array('md', 'jpg'), 'recr'=>true <br>";
    $box->bxd(array('cont'=>'aaa','entr'=>'*','fltr'=>array('md', 'jpg'), 'recr'=>true));
    echo '<hr>';

    $box->bxd('aaa');
    echo '<hr>';

    $box->bxd();

//   

//
//function expandDirectories($base_dir) {
//      $files = array();
//      foreach(scandir($base_dir) as $file) {
//            if($file == '.' || $file == '..') continue;
//            $dir = $base_dir.DIRECTORY_SEPARATOR.$file;
//            if(is_dir($dir)) {
////                $files []= $dir;
//                $files = array_merge($files, expandDirectories($dir));
//            } else {
//                $files []= $dir;
//            }
//      }
//      return $files;
//}
//
//$directories = expandDirectories('/Users/massimo/Sites/bxd/content/aaa');
//echo "<pre>";
//print_r($directories);
//echo "</pre>";