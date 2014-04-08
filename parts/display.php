<?php

	/* Affiche un item (li - lien - image - nom) */
	function print_item($item) {
		echo '<li>';
		echo '<a href="./item.php?id='.$item->id.'">'; 
		echo '<img src="./images'.$item->image.'"  width=21 height=21/> ';
		echo $item->name;    
		echo '</a>';
		echo '</li>';
	}
  
	/* Affiche un item "ingrdient" (li - lien - nombre de ressource - nom - image) */
	function print_ingredient($item, $nombre) {
		echo '<li>';
		echo $nombre .' x <a href="./item.php?id='.$item->id.'">'. $item->name; 
		echo ' <img src="./images'.$item->image.'" width=21 height=21/> ';
		echo '</a>';
		echo '</li>';
	}
  
	/* Affiche une panoplie et les items le constituant */
	function print_pano($panoplie) {
		echo '<div class="callout panel radius">';
    echo '<a href="./item.php?id='.$panoplie->id.'">';
    echo ' <img src="./images'.$panoplie->image.'" />';		
    echo $panoplie->name."</a>"; 
		//echo ' <img src="./images'.$panoplie['image'].'" />';
		echo '<ul>';
    $composants = $panoplie->get_composants();
    if (is_array($composants)) {
  		foreach($composants  as $composant )
  		{
    		echo '<li>';
    		echo '<a href="./item.php?id='.$composant->id.'">'; 
    		echo '<img src="./images'.$composant->image.'" width=21 height=21/> ';
    		echo $composant->name;    
    		echo '</a>';
        if ($composant->craftable) {
            print " <img class='craftable' >";
        }
    		echo '</li>';
  		}
    }
		echo '</ul>';
		echo '</div>';
	}

  function print_itemtype($item, $type) {
     $url =  $item->get_category_img_url($type);
     print "<img src=\"./".$url."\" class='category radius' title=\"".$type."\" alt=\"".$type."\" >";
  }
  
  function print_full_itemtype($item) {
      if( !empty( $item->type1 ) )
      {
      	print_itemtype($item, $item->type1);
        if( !empty( $item->type2 ) ) {
          print_itemtype($item, $item->type2);
        }
        if( !empty( $item->type3 ) ) {
          print_itemtype($item, $item->type3);
        }
      }
  } 
  
  function display_tendency($item) {   
      if (is_object($item->price)) {
          print "<a href=\"./hdvhistory.php?id=".$item->id."\">";
          $mid = ($item->price->min+$item->price->max)/2;
          if ($mid < $item->price->avg) {
              print "<img class=\"arr_down\" >";
          } else if ($mid > $item->price->avg) {
              print "<img class=\"arr_up\" >";
          } else {
              print "<img class=\"arr_egal\" >";
          }
          print "</a>";
      }      
  } 

?>