<?php
require_once('item.class.php');

/***************************************************************
 *                    O B J E T   I N G R E D I E N T 
 ***************************************************************/

class Ingredient {
    
    var $nombre;
    var $item;
    
    /* class constructor */
    public function Ingredient($number_of_items, $item_id) {
        $this->nombre = $number_of_items;
        if (is_numeric($item_id)) {
            $this->item = new Item($item_id);
        } else if (is_object($item_id)) {
            $this->item = $item_id;
        }       
    }     
}

?>