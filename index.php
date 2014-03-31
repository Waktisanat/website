<?php 
  require_once('classes/item.class.php'); 
  include_once('classes/panoplie.class.php');
  require_once('classes/recette.class.php'); 
  include_once('parts/display.php' ); 
?>

<?php $currentMenu = "home"; ?>

<!doctype html>
<html class="no-js" lang="en">
<?php include( 'page/head.php' ); ?>
<body>

    <?php include( 'page/page_header.php' ); ?>
    
    <?php
        $items = Item::get_all_items();
        $countItem = count($items);
        
        $panos = Panoplie::get_all_panoplies_by_level();
        $countPano = count($panos);
    ?>

    <div class="row">
        <div class="large-6 medium-6 columns">
            <div class="callout panel radius">
                <ul>
                    <li>Item d'Exemple : <a href="./item.php?id=3870">Plastron Lunaire (id: 3870)</a></li>
                    <li><a href="./allitem.php">Voir tous les objets</a></li>
                    <li><a href="./allpano.php">Voir toutes les panoplies</a></li>
                    <li><a href="./analyse.php">Voir les Tarifs HDV</a></li>
                    <li><a href="./favoris.php">Voir les objets favoris</a></li>
                    <li><a href="http://docs.google.com/spreadsheet/ccc?key=0Aj3D1TOIZvCAdFZBbjdKQlI1enNjVHB2VzZGTjMyS0E#gid=1">Suivi de Projet</a></li>
                    <li><a href="http://docs.google.com/document/d/1sBwUq6eqS8hd1yzhnrKYGlHCK_ERIEW1D38d7e6CAIQ">Cahier des charges</a></li>
                </ul>
                <span style="font-size:60%">Dernier import des Items : <?php print BDD::get_last_item_import(); ?><br> 
                Dernier import des Prix : <?php print BDD::get_last_price_import(); ?></span> 
            </div>
		</div>
        
        <div class="large-6 medium-6 columns">
            <ul class="button-group">
                <li><a class="button" href="./allitem.php"><strong><?php echo $countItem; ?></strong><br /> Items</a></li>
                <li><a class="button" href="./allpano.php"><strong><?php echo $countPano; ?></strong><br /> Panoplies</a></li>
		        </ul>
            <div class="row collapse">
                <form action="search.php" method=GET>
                    <div class="small-11 columns" style="padding:0;">
                        <input type="text" id=item name=item placeholder="Chercher...">
                    </div>
                    <div class="small-1 columns" style="padding:0;">
                        <span class="postfix"><img src="./images/search_icon.gif" style="cursor:pointer" onclick="submit();"></span>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>