<?php
require_once('item.class.php');
require_once('ingredient.class.php');

/***************************************************************
 *                    O B J E T   R E C E T T E
 ***************************************************************/
  
class Recette {

    var $id;          
    var $item;          // Item object resulting
    var $level;
    var $metier;        // string
    var $ingredients;   // array
    
    private static $METIERS;
    private $craft_price;
    
    /** class constructor **/   
    public function Recette($id = null) {
                        
        // lazy loading of Metiers
        if (!isset(self::$METIERS)) {
            self::$METIERS = array();

            $req = BDD::get()->prepare('SELECT * FROM METIERS'); 
		        $req->execute();
            $resp = $req->fetchAll();
            foreach($resp as $r) {
                self::$METIERS[$r['id']] = $r['name'];
            }
        }                
        if (is_numeric($id)) {
            $this->init_from_id($id);
        }   
    }
    
    /* --------- get data from recette id ------------- */     
    private function init_from_id($id) {
        $req = BDD::get()->prepare('SELECT * FROM RECETTES WHERE id='.$id);
		    $req->execute();
        $resp = $req->fetch(); 
        $this->init($resp);
    } 

    /* --------- initialise contents ------------- */     
    private function init($resp) {
        $this->id = $resp['id'];
        $this->level = $resp['level'];
        $this->item = new Item($resp['item']);
        $this->metier = self::$METIERS[$resp['metier']];
        $ingredients = array(); 
    }
    
    /** --------- load ingredients needed  ------------- **/    
    public function load_direct_ingredients() {
        $req = BDD::get()->prepare('SELECT ITEMS.*,nombre FROM INGREDIENTS LEFT JOIN ITEMS on ITEMS.id=INGREDIENTS.item WHERE recette='.$this->id);
		    $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            $item = new Item($r);
            $this->ingredients[] = new Ingredient($r['nombre'], $item);
        }
        return  $this->ingredients;
    }
    
    /** --------- load ALL ingredients needed recursively ------------- **/   
    public function load_recursive_ingredients() {
        $this->load_recursive_ingredients2($this->id);
        return $this->ingredients;
    }
    
    private function load_recursive_ingredients2($rec_id, $nombre = 1) {
        $req = BDD::get()->prepare('SELECT ITEMS.*,nombre FROM INGREDIENTS LEFT JOIN ITEMS on ITEMS.id=INGREDIENTS.item WHERE recette='.$rec_id);
		    $req->execute();
        $resp = $req->fetchAll();
        $tmp = array();
        foreach($resp as $r) {
            if ($r['craftable'] == 0) {  
                $item = new Item($r);
                $this->merge_ingredients(new Ingredient($r['nombre'] * $nombre, $item));
            } else {
                $tmp[] = $r;
            }
        }
        foreach ($tmp as $ingr) {
            $req = BDD::get()->prepare('SELECT id FROM RECETTES WHERE RECETTES.item='.$ingr['id']);
            $req->execute();
            $resp = $req->fetch();
            $this->load_recursive_ingredients2($resp['id'], $ingr['nombre'] * $nombre);
        }
    }
    
    /* --------- merge ingredients to the existing ones ------------- */   
    private function merge_ingredients($ingredient) {
        $found = 0;
        if (!isset($this->ingredients)) {
            $this->ingredients = array();
        }
        foreach($this->ingredients as $ingr) {
            if ($ingr->item->id == $ingredient->item->id) {
                $found = 1;
                $ingr->nombre += $ingredient->nombre;
            }
        }
        if ($found == 0) {
            $this->ingredients[] = $ingredient;
        }
    }
    
    /** --------- load prices of all ingredients and of recette ------------- **/
    public function load_ingredient_prices() {
        $req = array($this->item->id);
        if (!isset($this->ingredients)) {
            load_direct_ingredients();
        }
        foreach($this->ingredients as $ingr) {
            $req[] = $ingr->item->id;
        }
        //print_r($req);
        $resp = Item::get_price_for_array($req);
        //print_r($resp);
        $this->item->price =  $resp[$this->item->id];
        foreach($this->ingredients as $ingr) {
            $ingr->item->price = $resp[$ingr->item->id];
        }        
    }
    
    /** --------- get RECETTEs for a given metier ------------- **/   
    public static function get_recettes_by_metier_id($metier_id) {
        $ret = array();
        $req = BDD::get()->prepare('SELECT * FROM RECETTES WHERE metier='.$metier_id);
		    $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            $rec = new Recette();
            $rec->init($r);
            $ret[] = $rec;
        }
        return $ret;
    }
    /** --------- get RECETTEs for a given item ------------- **/   
    public static function get_recettes_by_item_id($item) {
        $item_id = $item;
        if (is_object($item)) {
            $item_id = $item->id;
        }
        $ret = array();
        $req = BDD::get()->prepare('SELECT * FROM RECETTES WHERE item='.$item_id);
		    $req->execute();
        $resp = $req->fetchAll();
        foreach($resp as $r) {
            $rec = new Recette();
            $rec->init($r);
            $ret[] = $rec;
        }
        return $ret;
    }
    
    /** --------- get RECETTEs including one item ------------- **/   
    public static function get_recettes_including_item($item) {
        $ret = array();
        $item_id = $item;
        if (is_object($item)) {
            $item_id = $item->id;
        }
        $str = 'SELECT RECETTES.* FROM RECETTES LEFT JOIN INGREDIENTS on RECETTES.id=INGREDIENTS.recette WHERE INGREDIENTS.item='.$item_id ;         
        $req = BDD::get()->prepare($str);
		    $req->execute();
        $resp = $req->fetchAll();
        //print count($resp)."\n".$str."\n";
        foreach($resp as $r) {
            $rec = new Recette();
            $rec->init($r);
            $ret[] = $rec;
        }
        return $ret;
    }     

    /** --------- get craft price ------------- **/   
    public function get_craft_price() {
        if (isset($this->craft_price)) {
            return $this->craft_price;
        }
        if (!isset($this->ingredients)) {
            load_direct_ingredients();
            load_ingredient_prices();
        }
        $this->craft_price = new Price();
        foreach($this->ingredients as $ingr) {
            if (isset($ingr->item->price)) {
                $this->craft_price->min += ($ingr->item->price->min * $ingr->nombre);
                $this->craft_price->max += ($ingr->item->price->max * $ingr->nombre);
                $this->craft_price->avg += ($ingr->item->price->avg * $ingr->nombre);
            }
        }
        return $this->craft_price;
    }
}
?>