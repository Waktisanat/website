<?php
/***************************************************************
 *                    O B J E T   D R O P
 ***************************************************************/

class Drop {
    
    var $percent;
    var $monster;
    var $off_id;
    
    public function Drop($record) {
        if (is_array($record)) {
            $this->percent = $record['percent'];
            $this->monster = $record['name'];
            $this->off_id = $record['off_id'];
            if ($this->monster == "") {
                $p = strrpos($this->off_id,"/");
                $p2 = strpos($this->off_id,"-", $p);
                $name = substr($this->off_id, $p2 + 1);
                $this->monster = ucwords(str_replace("-", " ", $name));
            }
        }
    }
}
?>