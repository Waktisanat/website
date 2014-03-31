<?php
    function pagination( $page, $pageCount, $urlsuffix = '', $step = 1 ) {
        $prev = $page - $step;
        $next = $page + $step;
        $start = 0;
        $limit = $pageCount;
        
        if ($step == 1) {
            $start = $page - 4;
            $limit = $page + 4;
            
            if( $start < 0 )
            {
                $start = 0;
            }
            
            if( $limit > $pageCount )
            {
                $limit = $pageCount;
            }
        }
        
        echo '<div class="pagination-centered">';
            echo '<ul class="pagination">';
        
            	if( $page > 0 )
            	{
            	   echo "<li class='arrow'><a href=\"?page=0".$urlsuffix."\">&laquo;</a></li>";
            	   echo "<li class='arrow'><a href=\"?page=".$prev.$urlsuffix."\">&lsaquo;</a></li>";
            	}
        	
            	for ($i = $start; $i <= $limit ; $i += $step) {
                    echo "<li"; 
            		if( $page == $i )
            		{
            			echo " class='current'";
            		}
            		echo "><a href=\"?page=".$i.$urlsuffix."\">";
                    		
                    if ($step > 1) {
                        echo $i."-".($i+$step-1);
                    } else {
                        echo $i+1;
                    }
            		echo "</a></li>";
            	}
        	
            	if( $page < $pageCount )
            	{
            	   echo "<li class='arrow'><a href=\"?page=".$next.$urlsuffix."\">&rsaquo;</a></li>";
            	   echo "<li class='arrow'><a href=\"?page=".$pageCount.$urlsuffix."\">&raquo;</a></li>";
            	}
            echo '</ul>';
        echo '</div>';
    }
?>

