<?php

  ini_set('display_errors','On');
  ini_set('display_startup_errors','On'); 

  include_once('classes/item.class.php' );
  include_once('parts/pagination.php' );
  include_once('parts/display.php' );



	$limit =(isset($_GET['limit'])) ? $_GET['limit'] : 90;
	$page = (isset($_GET{'page'} )) ? $_GET{'page'} : 0;
	$offset = $limit * $page ;
	$item =(isset($_GET['item'])) ? $_GET['item'] : 90;

 	$list = Item::find_items($item);
  $countItem = count($list);	
	$pageCount = ceil( $countItem / $limit ) - 1;


  function list_all( $limit, $offset, $list ) {
    $break = ceil($limit / 3);
    print "<div class=\"large-4 medium-6 columns\">\n";
         
    for ($i = $offset; ($i < $offset + $limit) && ($i < count($list)); $i++)
    {
        $item = $list[$i];	
        echo '<a href="./item.php?id='.$item->id.'">'; 
    		echo '<img src="./images'.$item->image.'"  width=21 height=21/> ';
    		echo $item->name;    
    		echo "</a>";
        print "<span style='font-size:60%;color:gray;'> (lvl&nbsp;".$item->level.")</span>\n";
        print "<br/>";

        if ((($i+1) % $break) == 0) {
            print "</div>\n<div class=\"large-4 medium-6 columns\">";
        } 
    }
  
    echo '</div>';    
  }


  /*************************************************************************/
  if ($countItem == 0) {
  /*************************************************************************/
?>
<!doctype html>
<html class="no-js" lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Erreur: aucun objet trouvé</title>
    </head>
    <body>
        Aucun objet correspondant trouvé.
    </body>
</html>  
<?php

  /*************************************************************************/
  } else if ($countItem == 1) {
    $url = "item.php?id=".$list[0]->id;
  /*************************************************************************/
?>
<!doctype html>
<html class="no-js" lang="fr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="refresh" content="1;url=<?php print $url; ?>">
        <script type="text/javascript">
            window.location.href = "<?php print $url; ?>";
        </script>
        <title>Page Redirection</title>
    </head>
    <body>
    </body>
</html>  
<?php

  /*************************************************************************/
  } else if ($countItem > 1) {
  /*************************************************************************/

?>

<!doctype html>
<html class="no-js" lang="fr">
<?php include( 'page/head.php' ); ?>
<body>
<?php include( 'page/page_header.php' ); ?>
    <div class="row">
        <div class="large-12 columns">
            <h1>Liste des objets :</h1>
			      <a class="button tiny radius right" href="/">Retour à l'accueil</a>
        </div>
    </div>
    <div class="row">
        <div class="large-12 medium-12 columns">  
        <?php
            list_all( $limit, $offset, $list );
        ?>
        </div>

    </div>
    <div class="row"> 
        <div class="large-12 medium-12 columns">
        <?php
            $prefix="&item=".$item;
            pagination( $page, $pageCount, $prefix ); 
        ?>
  
        </div>
    </div>
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>
<?php  
  }
?>