<?php
require_once('bdd.class.php');
require_once('price.class.php');
require_once('caracteristic.class.php');
require_once('drop.class.php');

/***************************************************************
 *                    O B J E T   I T E M 
 ***************************************************************/

class Item {
            
    var $id;
    var $name;
    var $type1;
    var $type2;
    var $type3;
    var $level;
    var $off_id; // id de l'item sur le forum officiel
    var $image;      // string relative path of image file
    var $craftable;  // boolean
    var $dropable;   // boolean
    var $rarety;     // string
    
    var $price;     
    var $caracs;     // caracteristics
    var $drops;     // droppable on 
    
    private static $ITEMTYPES;
    private static $ITEMTYPENUMS;
    private static $RARETIES;
    
    /* class constructor */
    public function Item($id_or_name = null) { 
        
        // lazy loading of item types
        if (!isset(self::$ITEMTYPES)) {
            self::$ITEMTYPES = array();
            self::$ITEMTYPENUMS = array();
            
            $req = BDD::get()->prepare('SELECT * FROM ITEMTYPES ORDER BY id ASC'); 
		        $req->execute();
            $resp = $req->fetchAll();
            foreach($resp as $r) {
                self::$ITEMTYPES[$r['id']] = $r['name'];
                $n =  substr($r['off_id'], 0, 3);
                if (is_numeric($n)) {
                    self::$ITEMTYPENUMS[$r['id']] = $n;
                }
            }

            // also init rareties
            self::$RARETIES = array("Commun","Inhabituel","Rare","Mythique","Légendaire","Relique");
        }   
        if (is_numeric($id_or_name)) {
            $this->init_from_id($id_or_name);
        } else if (is_string($id_or_name) && (strlen($id_or_name) > 0)) {
            $this->init_from_name($id_or_name);
        } else if (is_array($id_or_name)) {
            $this->init($id_or_name);
        }       
    }
    
    /* --------- get data from item id ------------- */
    private function init_from_id($id) {
        $req = BDD::get()->prepare('SELECT * FROM ITEMS WHERE id='.$id);
		    $req->execute();
        $resp = $req->fetch(); 
        $this->init($resp);
    } 
    /* --------- get data from item name ------------- */
    private function init_from_name($name) {

        $req = BDD::get()->prepare("SELECT * FROM ITEMS WHERE name='".$name."'"); 
		    $req->execute();
        $resp = $req->fetch();
        $this->init($resp);
    } 
      
    /* --------- initialise contents ------------- */ 
    
    private function init($resp) {
        $this->id = $resp['id'];
        $this->name = $resp['name'];
        $this->type1 = self::$ITEMTYPES[$resp['type1']]; 
        $this->type2 = self::$ITEMTYPES[$resp['type2']]; 
        $this->type3 = self::$ITEMTYPES[$resp['type3']]; 
        $this->level = $resp['level'];
        $this->off_id = $resp['off_id'];
        $this->image = $resp['image'];
        $this->craftable = $resp['craftable'];
        $this->dropable = $resp['dropable'];
        $this->rarety = self::$RARETIES[$resp['rarety']]; 
    }
          
    /** --------- get alphabetic list of all items ------------- **/ 
                                              
    public static function get_all_items() {
        $ret = array();
        $req = BDD::get()->prepare('SELECT * FROM ITEMS ORDER BY name ASC;'); 
		    $req->execute();
        $respList = $req->fetchAll();
        foreach($respList as $resp) {
            $item = new Item();
            $item->init($resp);
            $ret[] = $item;
        }
        return $ret;
    }
     
    /** --------- get list of all items by type ------------- **/                                               
    public static function get_items_by_type($type1, $type2, $type3) {
        $ret = array();
        $item = new Item();
        $str = "SELECT * FROM ITEMS ";
        if (!is_null($type1)) {
            $str = $str." WHERE type1=".$type1;
        } 
        if (!is_null($type2)) {
            $str = $str." AND type2=".$type2;
        } 
        if (!is_null($type3)) {
            $str = $str." AND type3=".$type3;
        } 
        $req = BDD::get()->prepare($str.' ORDER BY name ASC;'); 
		    $req->execute();
        $respList = $req->fetchAll();
        foreach($respList as $resp) {
            $item = new Item();
            $item->init($resp);
            $ret[] = $item;
        }
        return $ret;
    } 
    /** --------- get list of all items by type ------------- **/                                               
    public static function find_items($name) {
        $ret = array();
        $req = BDD::get()->prepare("SELECT * FROM ITEMS WHERE name like '%".$name."%' ORDER BY name ASC;"); 
		    $req->execute();
        $respList = $req->fetchAll();
        foreach($respList as $resp) {
            $item = new Item();
            $item->init($resp);
            $ret[] = $item;
        }
        return $ret;
    }
    
    /** --------- get the last price of an item ------------- **/                              
    public function get_price() {
        if (!isset($this->price)) {
            $response = self::get_price_for_array(array($this->id));
            foreach ($response as $r) {
                $this->price = $r;
            }    
        } 
        return $this->price;
    }
    
    /** --------- get the caracteristics of an item ------------- **/                        
    public function get_caracteristics() {
        if (!isset($this->caracs)) {
            $req = BDD::get()->prepare("SELECT effect,name,image FROM ITEMCARACS LEFT JOIN CARACS on ITEMCARACS.carac=CARACS.id WHERE item=".$this->id);
            $req->execute();
            $response = $req->fetchAll();
            foreach ($response as $r) {
                $this->caracs[] = new Caracteristic($r);
            }    
        } 
        return $this->caracs;
    }
    /** --------- get the last price of an item ------------- **/                        
    public function get_drops() {
        if (!isset($this->drops)) {
            $req = BDD::get()->prepare("SELECT * FROM DROPS LEFT JOIN MONSTERS on DROPS.monster=MONSTERS.id WHERE item=".$this->id);
            $req->execute();
            $response = $req->fetchAll();
            foreach ($response as $r) {
                $this->drops[] = new Drop($r);
            }    
        } 
        return $this->drops;
    }
         
    /** --------- get the last price of an array of items ------------- **/ 
    public static function get_price_for_array($item_id_array) {   
        $req = BDD::get()->prepare("set @num := 0, @type := ''; ");
        $req->execute();
        $str = "SELECT  item, min,max,date, mid,  ROUND(AVG(mid)) as avg ";
        $str .= "FROM (";
        $str .= "       SELECT item, min,max,date, ROUND((min+max)/2) as mid,";
        $str .= "         @num := if(@type = item, @num + 1, 1) as row_number, ";
        $str .= "         @type := item as dummy ";
        $str .= "      FROM TARIFS ";
        $str .= "      WHERE item in (".implode(",",$item_id_array).") ";
        $str .= "      ORDER BY item, date DESC ";
        $str .= ") as x where  x.row_number <= 5 ";
        $str .= "GROUP BY item ";        

        $req = BDD::get()->prepare($str);
		    $req->execute();
        
		    $resp = $req->fetchAll();
        //print $str."\n";
        //print_r($resp);
        $ret = array();
        foreach($resp as $r) {
            $price = new Price($r);
            $ret[$r['item']] = $price;
        }
        return $ret;
    }

    /** -------------- export json HDV content -------------- **/
    public function export_json($value) {
        $comma = "";
        $req = BDD::get()->prepare("SELECT * FROM TARIFS WHERE item=".$this->id." ORDER BY date ASC"); 
        $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            
            print "[Date.UTC(".str_replace("-",", ",$r['date'])."),".$r[$value]."],\n";
            $comma = ',';    
        }
    }


    /** -------------- get URL of category image -------------- **/
    public function get_category_img_url($category) {
        $ret = "";
        $item = new Item();
        $i = $item->getCategoryId($category);
        $n = self::$ITEMTYPENUMS[$i];
        if (is_numeric($n)) {
            $ret = "/images/category/".$n.".png";
        } else {
            $ret = "/images/category/cat_".$category.".png";
        }
        return $ret; 
    }
    
    /** -------------  get Type id ------------ **/
    public function getCategoryId($category) {
        foreach (array_keys(self::$ITEMTYPES) as $i) {
            if (self::$ITEMTYPES[$i] == $category) {
                return $i;
            }
        }
        return 0;
    }
    
    /** -------------  get Type Off id ------------ **/
    public function getCategoryOffId($category) {
        $req = BDD::get()->prepare('SELECT ITEMTYPES.off_id FROM ITEMTYPES WHERE name="'.$category.'"');
	    $req->execute();
        $resp = $req->fetch();
        return $resp['off_id'];
    }

    /** -------------- get all Type1  -------------- **/
    public static function get_all_type1() {
        $ret = array();
        $req = BDD::get()->prepare("select distinct type1,ITEMTYPES.name from ITEMS LEFT JOIN ITEMTYPES on ITEMS.type1=ITEMTYPES.id");
        $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            $ret[] = $r['name'];
        }
        return $ret;
    }
    
    public static function get_all_type2_for_type1($type1) {
        $ret = array();
        $req = BDD::get()->prepare("select distinct type2,ITEMTYPES.name from ITEMS LEFT JOIN ITEMTYPES on ITEMS.type2=ITEMTYPES.id WHERE ITEMS.type1=".$type1);
        $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            if ($r['name'] != "") {
                $ret[] = $r['name'];
            }
        }
        return $ret;
    }
    public static function get_all_type3_for_type2($type2) {
        $ret = array();
        $req = BDD::get()->prepare("select distinct type3,ITEMTYPES.name from ITEMS LEFT JOIN ITEMTYPES on ITEMS.type3=ITEMTYPES.id WHERE ITEMS.type2=".$type2);
        $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            if ($r['name'] != "") {
                $ret[] = $r['name'];
            }
        }
        return $ret;
    }
    
    /** --------- get main caracteristics (with image files) ------------- **/                        
    public function get_main_caracteristics() {
        $caracs = array();
        $req = BDD::get()->prepare("SELECT * FROM CARACS WHERE image IS NOT NULL");
        $req->execute();
        $response = $req->fetchAll();
        foreach ($response as $r) {
            $caracs[] = new Caracteristic($r);
        }    

        return $caracs;
    }

}                     

?>