<?php
  include_once('classes/item.class.php');  
  include_once('parts/display.php' ); 
  $WAKFUFAV = "WakfuFav";
  $currentMenu = "fav";
  $favs = (isset($_COOKIE[$WAKFUFAV])) ? explode(",", $_COOKIE[$WAKFUFAV]) : array();
  
  $fav_list = array();
  foreach($favs as $id) {
      $item = new Item($id);
      $fav_list[] = $item; 
  } 

  
?>
<!doctype html>
<html class="no-js" lang="fr">
<?php include( 'page/head.php' ); ?>
<body>
<?php include( 'page/page_header.php' ); ?>
    <div class="row">
        <div class="large-12 columns">
            <h3>Favoris :</h3>
        </div>
    </div>

    <div class="row">
    <table>
<?php 
    foreach ($fav_list as $item) {
        print "<tr><td>";
        print "<img src=\"./images".$item->image."\" />";
        print "<a href=\"./item.php?id=".$item->id."\"> ";
        print $item->name."</a>";
        print "</td><td>";
        print "<span class='level'>lvl ".$item->level."</span>\n";
        print "</td><td>";
        $tarif = $item->get_price();
        print $tarif->avg." <img class='kama' /> ";
        display_tendency($item);
        print "</td></tr>";
    }
 ?>    
    </table>
	  </div>
    
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>