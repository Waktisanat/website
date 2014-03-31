<?php

/***************************************************************
 *                    O B J E T   P R I C E
 ***************************************************************/

class Price {
    
    var $min;
    var $max;
    var $avg;
    var $date;
    
    /* class constructor */
    public function Price($record = null) {

        $this->min = 0;
        $this->max = 0;
        $this->avg = 0;
        if (is_array($record)) {
            $this->min = $record['min'];
            $this->max = $record['max'];
            $this->avg = $record['avg'];
            $this->date = $record['date'];
        }
    }
    
}

?>