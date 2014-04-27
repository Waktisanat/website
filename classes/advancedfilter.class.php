<?php

require_once('item.class.php' );

class AdvancedFilter {
    
    var $page;
    var $type1;
    var $type2;
    var $type3;
    var $min;
    var $max;
    var $key;
    var $rarity;
    var $craft;
    var $drop;
    var $recolte;
    var $carnum;
    var $caracs;
    var $carand;
    
    public function AdvancedFilter($GET) {
        $this->page = (isset($GET{'page'} )) ? $GET{'page'} : 0;
  
      	$this->type1 = (isset($GET{'type1'} )) ? $GET{'type1'} : null;
      	$this->type2 = (isset($GET{'type2'} )) ? $GET{'type2'} : null;
      	$this->type3 = (isset($GET{'type3'} )) ? $GET{'type3'} : null;
        
      	$this->min = (isset($GET{'min'} )) ? $GET{'min'} : null;
      	$this->max = (isset($GET{'max'} )) ? $GET{'max'} : null;
      	$this->key = (isset($GET{'key'} )) ? $GET{'key'} : null;
      	$this->rarity = (isset($GET{'rarity'} )) ? $GET{'rarity'} : null;
      	$this->craft = (isset($GET{'craft'} )) ? $GET{'craft'} : null;
      	$this->drop = (isset($GET{'drop'} )) ? $GET{'drop'} : null;
      	$this->recolte = (isset($GET{'recolte'} )) ? $GET{'recolte'} : null;
        $this->carand = (isset($GET{'carand'} )) ? $GET{'carand'} : 1;
        $this->carnum = (isset($GET{'carnum'} )) ? $GET{'carnum'} : 0;
        $this->caracs = array();
        for ($i = 0; $i <= $this->carnum; $i++) {
      	   if (isset($GET{'carac'.$i})) {
              $c = $GET{'carac'.$i};
              if ((strlen($c)>0) && (!in_array($c, $this->caracs))) {
                  $this->caracs[] = $GET{'carac'.$i};
              }
           }  
        }
        $this->carnum = count($this->caracs);
    }


    public function get_suffix($arg) {        
        $suffix = "&carnum=".$this->carnum."&carand=".$this->carand;
        if ($arg != "page") { $suffix .= "&page=".$this->page; }
        if ($arg != "type") {
            if (!is_null($this->type1)) { $suffix .= "&type1=".$this->type1; }
            if (!is_null($this->type2)) { $suffix .= "&type2=".$this->type2; }
            if (!is_null($this->type3)) { $suffix .= "&type3=".$this->type3; }
        }
        if (($arg != "min")&&(!is_null($this->min))) { $suffix .= "&min=".$this->min; }
        if (($arg != "max")&&(!is_null($this->max))) { $suffix .= "&max=".$this->max; }
        if (($arg != "key")&&(!is_null($this->key))) { $suffix .= "&key=".$this->key; }
        if (($arg != "rarity")&&(!is_null($this->rarity))) { $suffix .= "&rarity=".$this->rarity; }
        if (($arg != "craft")&&(!is_null($this->craft))) { $suffix .= "&craft=".$this->craft; }
        if (($arg != "drop")&&(!is_null($this->drop))) { $suffix .= "&drop=".$this->drop; }
        if (($arg != "recolte")&&(!is_null($this->recolte))) { $suffix .= "&recolte=".$this->recolte; }
        
        if (($arg != "car") && is_array($this->caracs)) {
            $i = 0;
            foreach ($this->caracs as $car) {
                $suffix .= "&carac".$i."=".$car;
                $i++;
            }
        }
        return $suffix;
    }
    
    public function get_items_request() {
        $c = count($this->caracs);
        $cs = (($c > 0 ) &&($this->carand == 1)) ? ",COUNT(*) as cnt" : "";
        $req1 = "SELECT ITEMS.*".$cs." FROM ITEMS ";
        $req2 = "";        
        $f1 = array();
        if (!is_null($this->type1)) { $f1[] = "type1='".$this->type1."'"; }
        if (!is_null($this->type2)) { $f1[] = "type2='".$this->type2."'"; }
        if (!is_null($this->type3)) { $f1[] = "type3 in (".$this->type3.") "; }
        
        if ($this->min != "") { $f1[] = "level>='".$this->min."'"; }
        if ($this->max != "") { $f1[] = "level<='".$this->max."'"; }
        
        if ($this->key != "") { $f1[] = "name like '%".$this->key."%'"; }
        if ($this->rarity != "") { $f1[] = "rarety='".$this->rarity."'"; }
        if ($this->craft != "") { $f1[] = "craftable='1'"; }
        if ($this->drop != "") { $f1[] = "dropable='1'"; }
        if ($this->recolte != "") { $f1[] = "(type1=37 and type2<>34 and dropable=0)"; }
        
        if ($c > 0) {
            $req2 = "LEFT JOIN ITEMCARACS on ITEMCARACS.item = ITEMS.id ";
            $fc = array();
            foreach ($this->caracs as $car) {
                $fc[] = "ITEMCARACS.carac='".$car."'";    
            }
            $and = ($this->carand == 1) ? "HAVING cnt=".$c." " : "";
            $f1[] = "(".implode(" OR ", $fc).") GROUP BY ITEMS.id ".$and;
        }
        if (count($f1) > 0) {
            $req1 = $req1.$req2."WHERE ".implode(" AND ", $f1);
        }
        $req1 = $req1." ORDER BY level,name ";
        return $req1;
    }
}

?>