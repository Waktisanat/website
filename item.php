<?php 
  ini_set('display_errors','On');
  ini_set('display_startup_errors','On'); 

    include_once('classes/item.class.php'); 
    include_once('classes/recette.class.php'); 
    include_once('classes/panoplie.class.php'); 
    include_once('parts/display.php');

    $currentMenu = "obj";

	if (isset($_GET["id"])) {						
		$currentItem = new Item( $_GET['id'] );
		$myRecette = Recette::get_recettes_by_item_id($currentItem);
    if (count( $myRecette ) && is_object($myRecette[0])) {
        $ingredients = $myRecette[0]->load_direct_ingredients();
    }
		$recettes = Recette::get_recettes_including_item($currentItem);
		$caracs = $currentItem->get_caracteristics();
		$drops = $currentItem->get_drops();
		$tarif = $currentItem->get_price();
		$panoplie = Panoplie::get_panoplies_with_item( $_GET['id'] );

    /* favoris */
    $WAKFUFAV = "WakfuFav";
    $favs = (isset($_COOKIE[$WAKFUFAV])) ? explode(",", $_COOKIE[$WAKFUFAV]) : array();
    $def = (in_array($currentItem->id, $favs)) ? "on" : "off"; 
    $fav =  (isset($_GET["fav"])) ?  $_GET["fav"] : $def ;
    $favtg = ($fav == "on") ? "off" : "on";
    $set = False;
    if (($def == "off") && ($fav == "on") && (count($favs) < 20)) { // was not Fav, but it is now.
        $favs[] = $currentItem->id;
        $set = True;   
    } else if (($def == "on") && ($fav == "off")) { // need to remove from Fav
        if(($key = array_search($currentItem->id, $favs)) !== false) {
            unset($favs[$key]);
            $set = True;
        }
    }
    setcookie( $WAKFUFAV, implode ( "," , $favs ), strtotime( '+30 days' ) );
	}
  

?>
<!doctype html>
<html class="no-js" lang="fr">
<?php include( 'page/head.php' ); ?>
<body>
<?php include( 'page/page_header.php' ); ?>
    <div class="row">
        <div class="large-12 columns">
				<?php 
					echo "<h1>".$currentItem->name; 
					if ( $currentItem->image != null ) {
            $src = $currentItem->image;
            $big = str_replace("/21/", "/42/",$src);
            if (file_exists("./images".$big)) {
                $src = $big;
            }
						echo ' <img src="./images'.$src.'" width="42" height="42"  /> ';
					}
					//echo '<small>(Id&nbsp;:&nbsp;'.$currentItem->id.')</small>';
          echo "</h1>";
				?>
        </div>
    </div>

    <div class="row">
		<div class="large-4 medium-6 columns">
			<div class="callout panel radius">				
				<?php
          print "<a href=\"?id=".$currentItem->id."&fav=".$favtg."\" class=\"right\"><img src=\"./images/fav".$fav.".png\"></a>" ;
          
					/* Type - Catégorie de l'item */
					if( !empty( $currentItem->type1 ) )
					{
						echo "<b>Type : </b>";
						print_itemtype($currentItem, $currentItem->type1);
					  if( !empty( $currentItem->type2 ) ) {
            	echo ' &#10151; ';
              print_itemtype($currentItem, $currentItem->type2);
            }
            if( !empty( $currentItem->type3 ) ) {
            	echo ' &#10151; ';
              print_itemtype($currentItem, $currentItem->type3);
            }
            echo "<br/><br/>";
					}
          
					if( !empty( $currentItem->rarety ) )
					{
              echo "<b>Rareté : </b>".$currentItem->rarety;
              echo "<br/><br/>";
					}
					/* Caractéristiques pour les équipements */
					if ( !empty($caracs) )
					{
						echo "<b>Caractéristiques :</b>";
						echo "<ul>";
						foreach($caracs as $carac)
						{
							echo "<li>";
              if (!is_null($carac->image)) {
                  print "<img src=\"./images/carac/".$carac->image."\" >";
              }
              print $carac->effect ." ". $carac->name."</li>";
						}
						echo "</ul>";
					}
					/* possibilité de drop */
					if ( !empty($drops) )
					{
						echo "<b>Droppable sur :</b>";
						echo "<ul>";
						foreach($drops as $drop)
						{
							echo "<li>". $drop->percent ." sur ".$drop->monster."</li>";
						}
						echo "</ul>";
					}
					
					/* Tarifs */
					if ( !empty($tarif) )
          {
              echo "<b>Prix HDV :</B> ";
              echo " &#126; ".$tarif->avg."<img class='kama' /> ";
              $mid = ($tarif->min+$tarif->max)/2;
              if ($mid < $tarif->avg) {
                  print "<img class=\"arr_down\" >";
              } else if ($mid > $tarif->avg) {
                  print "<img class=\"arr_up\" >";
              } else {
                  print "<img class=\"arr_egal\" >";
              }
              print "<a class=\"button micro secondary radius right\" href=\"./hdvhistory.php?id=".$currentItem->id."\" style=\"background:none;\" >";
              print "<img src=\"./images/chart.png\" ></a>";
          }
          
          
                    /* Lien vers l'encyclo */
                    $url = "http://www.wakfu.com/fr/mmorpg/encyclopedie/objets/";
			
        			if( !empty( $currentItem->type1 ) )
        			{
        			    $url .= trim( $currentItem->getCategoryOffId( $currentItem->type1 ) );
        			}
        			
        			if( !empty( $currentItem->type2 ) )
        			{
        			    $url .= "/".trim( $currentItem->getCategoryOffId( $currentItem->type2 ) );
        			}
        			
        			if( !empty( $currentItem->type3 ) )
        			{
        			    $url .= "/".trim( $currentItem->getCategoryOffId( $currentItem->type3 ) );
        			}
        			
        			$url .= "/".$currentItem->off_id;
        			
        			print "<p><br/><a href=".$url." target='_blank'>Voir sur l'encyclopédie Officielle</a></p>";
				?>
			</div>
		</div>
	
	  <?php
			/* Si l'objet est craftable, on affiche les ingrédients nécessaires */
			if ( $currentItem->type1 == "panoplies" )
			{
          $pano = new Panoplie($currentItem->id);
		?>
			<div class="large-4 medium-6 columns">
				Composants de la panoplie :<br />
				<?php
					print_pano($pano);
				?>
			</div>
		<?php
			}
		?>

	
		<?php
			/* Si l'objet est craftable, on affiche les ingrédients nécessaires */
			if ( !empty($ingredients) )
			{
		?>
			<div class="large-4 medium-6 columns">
				Ingrédients nécessaires au craft :<br />
				<div class="callout panel radius">
          <b>Métier : </b><?php print $myRecette[0]->metier." <span style='font-size:80%;color:gray;'>(lvl ".$myRecette[0]->level.")</span>"; ?>
					<ul>
						<?php
							foreach($ingredients as $ingredient)
							{
								print_ingredient($ingredient->item, $ingredient->nombre);
							}
						?>
					</ul>
				</div>
			</div>
		<?php
			}
		?>
		
		<?php
			/* Si l'objet est présent dans une recette, on l'affiche */
			if ( !empty($recettes) )
			{
		?>
			<div class="large-4 medium-6 columns">
				Présent dans les recettes :<br />
				<div class="callout panel radius">
					<ul>
						<?php
						foreach($recettes as $recette)
						{
							$item = $recette->item ;
							print_item($item);
						}
						?>
					</ul>
				</div>
			</div>
		<?php
			}
		?>
		
		<?php
			/* Si l'objet fait partie d'une panoplie, on l'affiche (avec les autres items de la panoplie) */
			if ( !empty($panoplie) )
			{
		?>
			<div class="large-4 medium-6 columns">
				Fait partie de la panoplie :<br />
				<?php
					print_pano( $panoplie[0] );
				?>
			</div>
		<?php
			} 
		?>
    </div>
    
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>