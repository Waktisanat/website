<?php

class Caracteristic {
    
    var $id;
    var $effect;
    var $name;
    var $image;
    
    public function Caracteristic($record) {
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

}

?>