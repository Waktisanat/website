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
     print "<img src=\"".$url."\" class='category radius' title=\"".$type."\" alt=\"".$type."\" >";
  } 
  
?>