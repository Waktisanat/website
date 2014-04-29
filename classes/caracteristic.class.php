<?php

class Caracteristic {
    
    var $id;
    var $effect;
    var $name;
    var $image;
    var $diff;
    
    public function Caracteristic($record) {
        $this->diff = 0;
        if (is_array($record)) {
            $this->effect = $record['effect'];
            $this->name = $record['name'];
            if (!is_null($record['image'])) {
                $this->image = $record['image'];
            }
            if (!is_null($record['id'])) {
                $this->id = $record['id'];
            }
        }
    }

    public static function merge_arrays($first, $second) {
        $res = array();
        foreach ($first as $car) {
            $c = new Caracteristic(null);
            $c->id = $car->id;
            $c->effect = $car->effect;
            $c->name = $car->name;
            $c->image = $car->image;
            $res[] = $c;
        }
        /* print "<br>Second:".$second. ":";
        print_r($second);
        print "<br>"; */
        foreach ($second as $c2) {
            $match = -1;
            for ($i=0; $i < count($res); $i++) {
                if ($res[$i]->name == $c2->name) {
                    $match = $i;
                    break;
                }
            }
            if ($match >= 0) {
                $res[$match]->effect = self::merge_effect($res[$match]->effect, $c2->effect);
            } else {
                $c = new Caracteristic(null);
                $c->id = $c2->id;
                $c->effect = $c2->effect;
                $c->name = $c2->name;
                $c->image = $c2->image;
                $res[] = $c;
            }
        }
        return $res;
    }
    
    private static function merge_effect($eff1, $eff2, $add = true) {
        $e1 = str_replace("%","",str_replace("+","",$eff1));
        $e2 = str_replace("%","",str_replace("+","",$eff2));
        if ($add == true) {
            $sum = intval($e1) + intval($e2);
        } else {
            $sum = intval($e1) - intval($e2);
        }
        //print "<br>(".$eff1." +".$add." ".$eff2.") = ".$sum;
        $ret = "".$sum;
        if (strpos($eff1.$eff2,"%") > 0) {
            $ret = $ret."%";
        }
        if ($sum >=0) {
            $ret = "+".$ret;
        }
        return $ret;
    }
    
    public static function compare_arrays(&$first, &$second) {
        self::compute_diff($first, $second);
        self::compute_diff($second, $first);
        self::compute_diff($first, $second);
        //print "<br>f1:".count($first)."<br>f2:".count($second);
    }
    
    private static function compute_diff(&$first, &$second) {
        foreach($first as $c1) {
            $match = -1;
            for ($i=0; $i < count($second); $i++) {
                if ($second[$i]->name == $c1->name) {
                    $match = $i;
                    break;     
                }
            }
            if ($match >= 0) {
                $second[$match]->diff = self::merge_effect($second[$match]->effect, $c1->effect, false);
            } else {
                $c = new Caracteristic(null);
                $c->id = $c1->id;
                $c->effect = 0;
                $c->diff = self::merge_effect(0, $c1->effect, false);
                $c->name = $c1->name;
                $c->image = $c1->image;
                $second[] = $c;
            }
        }
        //print "<br>F1:".count($first)."<br>F2:".count($second);
    }
}
?>