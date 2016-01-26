<?php
    $box = new cms();

class cms {

/**
    __contruct
**/

    public function __construct() {
        $settings = parse_ini_file("config.ini");

        $this->html_root    = (isset($settings['html_root'])    ? $settings['html_root']    : '/');
        $this->content_root = (isset($settings['content_root']) ? $settings['content_root'] : '/content');
        $this->extension    = (isset($settings['extension'])    ? $settings['extension']    : 'txt');
        $this->filter[]     = (isset($settings['filter'])       ? $settings['filter']       : '');
        $order              = strtolower(isset($settings['order']) && (strtolower($settings['order']) == 'asc' || strtolower($settings['order']) == 'desc') ? $settings['order'] : 'asc');
        if ($order == 'desc') {
            $this->order = SCANDIR_SORT_DESCENDING;
        } else {
            $this->order = SCANDIR_SORT_ASCENDING;
        }
        $this->file_error   = (isset($settings['fileerror'])    ? $settings['fileerror']    : false);
        $this->recursive    = (isset($settings['recursive'])    ? $settings['recursive']    : false);
        $this->renderer     = (isset($settings['renderer'])     ? $settings['renderer']     : '');

//        echo "order: ".$this->order."<br>";


        $this->theDir = pathinfo($_SERVER['REQUEST_URI']);
//        echo "theDir: ".$this->theDir['dirname']."<br>";
//        echo "theFile: ".$this->theDir['filename']."<br>";

        $this->thePath = $_SERVER['DOCUMENT_ROOT'].$this->html_root.$this->content_root.str_replace($this->html_root,"",$this->theDir['dirname']).DIRECTORY_SEPARATOR.$this->theDir['filename'];
        echo "thePath: ".$this->thePath."<br>";

        $this->theFile = end((explode(DIRECTORY_SEPARATOR,$this->theDir['filename']))).".".$this->extension;
//        echo "theFile: ".$this->theFile."<br>";

//        echo file_get_contents($this->thePath.DIRECTORY_SEPARATOR.$this->theFile)."<br>";

    }

/**
    getContent
**/
    public function getContent($args=false) {

        if(gettype($args)=='array') {
            echo "<h2>array</h2>";
            echo "Arguments:";
            echo "<pre class='args'>";
            print_r($args);
            echo "</pre>";

            $thisFilename = (isset($args['filename'])  ? $args['filename']  : '*');
            $path         = (isset($args['directory']) ? $args['directory'] : $this->theDir['filename']);
            $thisPath     = $_SERVER['DOCUMENT_ROOT'].$this->html_root.$this->content_root.str_replace($this->html_root,"",$this->theDir['dirname']).DIRECTORY_SEPARATOR.$path;

            if(isset($args['filter'])) {
                if (gettype($args['filter']) == "array") {
                    $thisExtFltr = array_map('strtolower', $args['filter']);
                } elseif (gettype($args['filter']) == "string") {
                    $thisExtFltr[] = strtolower($args['filter']);
                }
            } else {
                $thisExtFltr = array();
            }

            $thisRecursive    = (isset($args['recursive'])    ? $args['recursive']    : false);

            $order = strtolower(isset($args['order']) && (strtolower($args['order']) == 'asc' || strtolower($args['order']) == 'desc') ? $args['order'] : 'asc');
            if ($order == 'desc') {
                $thisOrder = SCANDIR_SORT_DESCENDING;
            } else {
                $thisOrder = SCANDIR_SORT_ASCENDING;
            }

            $thisFile_error = (isset($args['fileerror'])    ? $args['fileerror']    : false);

            echo "filter: ";
            print_r($thisExtFltr);
            echo "<br>";
            echo "path :".$path."<br>";
            echo "thispath :".$thisPath."<br>";

            echo "<pre class='files'>";
            if ($thisFilename == '*') {
                $result = $this->checkDirectory($thisPath, $thisExtFltr, $thisRecursive, $thisOrder,$thisFile_error);
                print_r($result);
                $content = $this->renderFile($result);
            } else {
                print_r($this->checkFile($thisPath, $thisFilename, $this->order,$this->file_error));
            }
            echo "</pre>";

        } elseif(gettype($args)=='string') {
            if ($args == '*') {
                echo "<h2>string - > $args</h2>";
                print_r($this->checkDirectory($this->thePath, $this->filter, $this->recursive, $this->order,$this->file_error));
            } else {
                echo "<h2>string - > $args</h2>";
                print_r($this->checkFile($this->thePath, $args, $this->order,$this->file_error));
            }
        } else {
            echo "<h2>-empty-</h2>";
            print_r($this->checkFile($this->thePath, $args, $this->order,$this->file_error));
        }
    }

/**
    checkFile
**/
    public function checkFile($pathname='', $filename='', $sortorder='', $error=false) {
        $filename = $pathname.DIRECTORY_SEPARATOR.$filename;
        if (file_exists($filename) && is_file($filename)) {
            $result[] = $filename;
        } else {
            if ($error) {
                $result[] = "File not Found";
            } else {
                foreach(scandir($pathname, $sortorder) as $file) {
                    if(substr($file, 0, 1) === '.') {continue;}
                    $result[] = $pathname.DIRECTORY_SEPARATOR.$file;
                    break;
                }
            }
        }
        return $result;
    }

/**
    checkDirectory
**/

    public function checkDirectory($pathname='', $filter=array(), $recursive=false, $sortorder=0, $error=false) {
        $result = array();
        foreach(scandir($pathname, $sortorder) as $file) {
            if(substr($file, 0, 1) === '.') {continue;}
            $dir = $pathname.DIRECTORY_SEPARATOR.$file;
            if(is_dir($dir) && $recursive ) {
                $result = array_merge($result, $this->checkDirectory($dir,$filter,$recursive,$sortorder, $error));
            } else {
                if (!empty(array_filter($filter))) {
                    if (in_array(pathinfo($dir, PATHINFO_EXTENSION),$filter)) {
                        $result[]= $dir;
                    }
                } elseif(!is_dir($dir)) {
                    $result[]= $dir;
                }
            }
        }
        return $result;
    }

/**
    renderFile
**/

    public function renderFile($files=array(), $renderer='') {
    			 $result = '';
        foreach($files as $file) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            echo "EXT:$ext<br>";
            
            switch($renderer) {
            			case "":
            						break;
            			default:
			                switch($ext) {
			                    case "md":
			                        if(file_exists(getcwd().'/bxd/renderer/markdown/Parsedown.php')) {
			                            require_once(getcwd().'/bxd/renderer/markdown/Parsedown.php');
			                            $content = file_get_contents($file);
			                            $parsedown = new Parsedown();
			                            $result .= "<span>".$parsedown->text($content)."</span>";
			                        } else {
			                            $result .= "<span>".file_get_contents($file)."</span>";
			                        }
			                        break;
			                    case "txt":
			                    case "html":
			                    case "htm":
			                    case "php":
			                        $result .= "<span>".file_get_contents($file)."</span>";
			                        break;
			                    case "bmp":
			                    case "png":
			                    case "apng":
			                    case "jpg":
			                    case "jpeg":
			                    case "gif":
			                    case "svg":
			                    			$result .= '<img src="'.$file.'" alt="'.basename($file).'" >';
			                        break;
			                    default:
			                    			$result .= '<span><a href="'.$file.'">'.basename($file).'</a></span>';
			                        
			                }
	            			
            						break;
            }
                       
            
        }  // end foreach
    }




}