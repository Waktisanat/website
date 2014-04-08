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
        <div class="large-8 columns">
            <div class="callout panel radius">
                <p>Waktisanat un Fan site pour vous assister dans l'<strong>Artisanat du jeu Wakfu</strong>.</p>
                <p>Le site permet de suivre l'<strong>historique de l'hôtel de vente d'Aerafal</strong>.</p>
                <p>Une <strong>encyclopédie présentant tous les objets de Wakfu et leur recette</strong> est également à votre disposition.</p>
                <p>Un <strong>moteur de recherche avancé</strong> vous permettra de trouver rapidement les objets souhaités.</p>
                <p>Vous pouvez ajouter les <strong>objets en Favori</strong> afin de pouvoir les retrouver plus efficacement par la suite.</p>
            </div>
            
            <div class="callout panel radius">
                <p>N'hésitez pas à nous faire savoir si vous rencontrez des difficultés ou des erreurs...</p>
                <p>Nous sommes également ouvert à vos propositions en terme de design du site <em>(oui ce n'est pas notre fort)</em>, mais aussi sur des fonctionnalités ou des outils à mettre en place toujours dans le but de vous faciliter la vie.</p>
            </div>
		</div>
        
        <div class="large-4 columns">
            <fieldset>
                <legend>Waktisanat, c'est :</legend>
                <ul>
                    <li><a href="./allitem.php"><strong><?php echo $countItem; ?></strong> Items</a></li>
                    <li><a href="./allpano.php"><strong><?php echo $countPano; ?></strong> Panoplies</a></li>
                    <li><a href="./favoris.php">Objets Favoris</a></li>
                    <li><a href="./searchadv.php">Recherche avancée</a></li>
                    <li><a href="./analyse.php">Suivi HDV d'Aerafal</a></li>
                    <li>Derniers imports :
                        <ul>
                            <li>Items : <?php print BDD::get_last_item_import(); ?></li>
                            <li>Prix : <?php print BDD::get_last_price_import(); ?></li>
                        </ul>
                    </li>
                </ul>
            </fieldset>

            <fieldset>
                <legend>Prochainement, ce sera :</legend>
                <ul>
                    <li>Export (GDoc/xls) d'une recette pour le suivi de ressources</li>
                    <li>Comparaison de différents équipements et panoplies</li>
                    <li>A suivre...</li>
                </ul>
            </fieldset>
            
            <fieldset>
                <legend>Le coin des Devs</legend>
                <ul>
                    <li><a href="http://docs.google.com/spreadsheet/ccc?key=0Aj3D1TOIZvCAdFZBbjdKQlI1enNjVHB2VzZGTjMyS0E#gid=1">Suivi de Projet</a></li>
                    <li><a href="http://docs.google.com/document/d/1sBwUq6eqS8hd1yzhnrKYGlHCK_ERIEW1D38d7e6CAIQ">Cahier des charges</a></li>
                </ul>
            </fieldset>
        </div>
    </div>
    
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>