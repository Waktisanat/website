<?php
define('__ROOT__', dirname(dirname(__FILE__))); 

class BDD {
    
    private $bdd;
    private $count;
    private static $singleton;
  
    private function BDD() {
        include(__ROOT__.'/connect_base.php.ini'); 
        $this->bdd = $bdd;
        $this->count = 0;
    }
    
    public static function get() {
        $inst = self::getInstance();
        $inst->count++;
        return $inst->bdd;
    }
    
    public static function getInstance() {
        if (is_null(self::$singleton)) {
            self::$singleton = new BDD();
        }
        return self::$singleton;    
    }
    
    public static function getCount() {
        $inst = self::getInstance();
        return $inst->count;
    }
    
    public static function get_last_item_import() {
        $req = self::get()->prepare("SELECT * FROM INFOS WHERE INFOS.Key='Last Item Import'");
        $req->execute();
        $resp = $req->fetch();
        return $resp['Value'];
    }
    public static function get_last_price_import() {
        $req = self::get()->prepare("SELECT * FROM INFOS WHERE INFOS.Key='Last Price Import'");
        $req->execute();
        $resp = $req->fetch();
        return $resp['Value'];
    }
}
?>