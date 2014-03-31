<?php

require_once('item.class.php');

class Panoplie extends Item {

    var $composants;
    
    /* class constructor */
    public function Panoplie($id) {
        parent::__construct($id);
        
    }
    /** ----------------- get all panoplies --------------- **/
    public static function get_all_panoplies() {
        $ret = array();
        new Item(); // init
        $req = BDD::get()->prepare("SELECT ITEMS.* FROM ITEMS LEFT JOIN ITEMTYPES on ITEMS.type1=ITEMTYPES.id WHERE ITEMTYPES.name='panoplies'");
		    $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            $ret[] = new Panoplie($r);
        }
        return $ret;
    }
    
    /** ----------------- get all panoplies sorted by level (asc) --------------- **/
    public static function get_all_panoplies_by_level() {
        $ret = array();
        new Item(); // init
        $req = BDD::get()->prepare("SELECT ITEMS.* FROM ITEMS LEFT JOIN ITEMTYPES on ITEMS.type1=ITEMTYPES.id WHERE ITEMTYPES.name='panoplies' ORDER BY ITEMS.level ASC");
		    $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            $ret[] = new Panoplie($r);
        }
        return $ret;
    }
    
    /** ----------------- get all panoplies containing a given item --------------- **/
    public static function get_panoplies_with_item($item) {
        $ret = array();
        new Item(); // init
        $item_id = $item;
        if (is_object($item)) {
            $item_id = $item->id;
        }
        $req = BDD::get()->prepare("
          SELECT IT2.* FROM ITEMS as IT1
          LEFT JOIN PANOITEMS on IT1.id=PANOITEMS.item 
          LEFT JOIN ITEMS as IT2 on IT2.id= PANOITEMS.panoplie
          WHERE PANOITEMS.item=".$item_id);
		    $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            $ret[] = new Panoplie($r);
        }
        return $ret;
    }
    
    /** ----------------- get components of this panoplie --------------- **/
    public function get_composants() {
        if (!isset($this->composants)) {
            $req = BDD::get()->prepare('SELECT ITEMS.* FROM ITEMS LEFT JOIN PANOITEMS on ITEMS.id=PANOITEMS.item WHERE PANOITEMS.panoplie='.$this->id);
    		    $req->execute();
            $resp = $req->fetchAll();
            foreach($resp as $r) {
                $this->composants[] = new Item($r);
            }
        }
        return $this->composants;
    }
}
?>