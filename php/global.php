<?php

class db_mysql{

    protected $server = 'localhost';
    protected $port = '3306';
    protected $dbname = 'herbNoutfitter';
    protected $username = 'darthTreb';
    protected $password = 'hn3#jfdb^3gy2!O4';
    public $connection;

    public function __construct(){

        $this->connection = mysqli_connect($this->server, $this->username, $this->password, $this->dbname);

        if (!$this->connection) {
            die("Connection failed: " . mysqli_connect_error());
        }
    }
    
    public function disconnect(){

        $this->connection->close();
    }
}

class template{

    public $html;

    function __construct($daTemplate){

        $path_parts = pathinfo($_SERVER['DOCUMENT_ROOT']);
        $x = $path_parts['dirname'];

        if(substr_startswith($daTemplate, 'html/')){

            //$filename = realpath($x . "/template/" . $daTemplate);
            //$filename = $x . "/template/" . $daTemplate;
            $filename = "template/".$daTemplate;

            $handle = fopen($filename, "r");

            $this->html = fread($handle, filesize($filename));

            fclose($handle);
        }
        elseif(substr_startswith($daTemplate, 'php/')){

            //include realpath($x . "/template/" . $daTemplate);
            //include $x . "/template/" . $daTemplate;
            include "template/".$daTemplate;
        }
    }
}

function substr_startswith($haystack, $needle){

    return substr($haystack, 0, strlen($needle)) === $needle;
}

function miniMIME($fname) {
    
    $fh=fopen($fname,'rb');
    if ($fh) { 
        $bytes6=fread($fh,6);
        fclose($fh); 
        if ($bytes6===false) return false;
        if (substr($bytes6,0,3)=="\xff\xd8\xff") return 'image/jpeg';
        if ($bytes6=="\x89PNG\x0d\x0a") return 'image/png';
        if ($bytes6=="GIF87a" || $bytes6=="GIF89a") return 'image/gif';
        return 'application/octet-stream';
    }
    return false;
}

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

function getUserData($meta){
    
    if(isset($_SESSION['auth'])){
        
        return $_SESSION['auth'][$meta];
    }
    else{
        return "";
    }
}
?>
