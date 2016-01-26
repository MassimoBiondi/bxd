<?php
//    error_reporting(E_ERROR |  E_PARSE);
    error_reporting(E_ERROR | E_WARNING | E_PARSE);

/**
 * Class to handle articles
 */

class bxd
{
     static private $baseDir, $page, $ext, $fltr, $files, $recr;
    /**
    * Sets the object's properties using the values in the supplied array
    *
    * @param assoc The property values
    */

    public function __construct() {
        $setings = parse_ini_file("config.ini");
//        
//        
//        $uri = str_replace($setings['General']['html_root'],"",$_SERVER['REQUEST_URI']);        
//        
//        self::$page = preg_replace('/\\.[^.\\s]{3,4}$/', '', $uri);
//        if (self::$page == '' ) {
//            self::$page = 'index';
//        }
//        
//        self::$baseDir = $_SERVER['DOCUMENT_ROOT']
//                   .$setings['General']['content_root']
//                   .DIRECTORY_SEPARATOR;
//        
//        self::$ext = $setings['General']['default_ext'];
        
        $theDir = preg_replace('/\\.[^.\\s]{3,4}$/', '', str_replace($settings['html_root'],"",$_SERVER['REQUEST_URI']));
        //echo "theDir: ".$theDir."<br>";

        self::$baseDir = $_SERVER['DOCUMENT_ROOT'].$settings['html_root'].$settings['content_root'].$theDir;
        echo self::$baseDir;
        
        $theFile = end((explode(DIRECTORY_SEPARATOR,$theDir)));
        
            
    }
    
    public function bxd($args='') {
        
        if(gettype($args)=='array') {
            $cont = (isset($args['cont']) ? $args['cont'] : self::$page);
            $entr = (isset($args['entr']) ? $args['entr'] : self::$page.'.'.self::$ext );
            self::$recr = (isset($args['recr']) && $args['recr'] ? true : false);
            if(isset($args['fltr'])) {
                if (gettype($args['fltr']) == "array") {
                    self::$fltr = array_map('strtolower', $args['fltr']);
                } elseif (gettype($args['fltr']) == "string") {
                    self::$fltr[] = strtolower($args['fltr']);
                }
            } else {
                self::$fltr = array();
            }
            $rndr = (isset($args['rndr']) ? $args['rndr'] : '');
        } else {
            $cont = ($args != '') ? $args : self::$page;
            $entr = $cont.'.'.self::$ext;                   // this is the same as the container name if not provided
            self::$recr = false;
            $fltr = '';
            $rndr = '';
        }
        
        
        if ($entr == '*') {
            $file = self::bxdFiles(self::$baseDir.$cont.DIRECTORY_SEPARATOR,  self::$fltr, $recr);
        } else {
            $file = self::$baseDir.$cont.DIRECTORY_SEPARATOR.$entr;
            echo "assembled File: ".$file."<br>";
            //$content = self::bxdContent($file);
        }

        print_r( $cont.' / '.$entr.' / '.$recr.' / '.$fltr.' / '.$rndr);
        echo '<br>';
        //echo self::$baseDir;
        echo '<br>';
//        echo $file;
//        echo '<br>';
        
       
        
        if (gettype($file) == 'array') {
            foreach ($file as $entry) {
                echo $entry." -> ".self::bxdFileType($entry)."<br>";
            }
        } else {
            echo $file." -> ".self::bxdFileType($file)."<br>";
            $content = self::bxdContent($file);
            $parts = preg_split('/[\n]*[-]{3}[\n]/', $content, 3);
            print_r($parts);
        }
        
        
        
    }
    
    public function bxdFileType($file) {
        echo "filetype file: ".$file;
        $file_info = new finfo(FILEINFO_MIME);	// object oriented approach!
        $mime_type = $file_info->buffer(file_get_contents($file));  // e.g. gives "image/jpeg"

        return $mime_type;
    }
    
    public function bxdContent($file) {
//        echo 'File: '.$file.'<br>';
        $content = file_get_contents($file);
        if ($content == '') {
            $content = "<span style='color: red'>".error_get_last()['message']."</span>";
        }
        return $content;
    }
    
    /**
    * Sets the object's properties using the edit form post values in the supplied array
    *
    * @param assoc The form post values
    */

    public function bxdFiles($base_dir, $fltr=array(), $recr=false) {
        $files = array();
        foreach(scandir($base_dir) as $file) {
            if(substr($file, 0, 1) === '.') {continue;}
            $dir = $base_dir.DIRECTORY_SEPARATOR.$file;
            if(is_dir($dir) && self::$recr ) {
                $files = array_merge($files, self::bxdFiles($dir));
            } else {
                if (!empty(self::$fltr)) {
                    if (in_array(pathinfo($dir, PATHINFO_EXTENSION),self::$fltr)) {
                        $files []= $dir;
                    }
                } elseif(!is_dir($dir)) {
                    $files []= $dir;
                }
            }
        }
        return $files;
    }

    
 
}