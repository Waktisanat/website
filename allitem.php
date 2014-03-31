<?php   
  ini_set('display_errors','On');
  ini_set('display_startup_errors','On'); 

  include_once('classes/item.class.php' );
  include_once('parts/pagination.php' );
  include_once('parts/display.php' );

    $currentMenu = "obj";

	$limit =(isset($_GET["limit"])) ? $_GET["limit"] : 90;
	$page = (isset($_GET{'page'} )) ? $_GET{'page'} : 0;
	$offset = $limit * $page ;

	$type1 = (isset($_GET{'type1'} )) ? $_GET{'type1'} : null;
	$type2 = (isset($_GET{'type2'} )) ? $_GET{'type2'} : null;
	$type3 = (isset($_GET{'type3'} )) ? $_GET{'type3'} : null;
  
	$list = Item::get_items_by_type($type1,$type2,$type3);
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
        print "<span class='level'> (lvl&nbsp;".$item->level.")</span>\n";
        print "<br/>";

        if ((($i+1) % $break) == 0) {
            print "</div>\n<div class=\"large-4 medium-6 columns\">";
        } 
    }
  
    echo '</div>';    
  }
  function print_type_buttons($item, $types, $t1 = null, $t2 = null) {
    $prefix = "type1=";
    if (!is_null($t1)) {
        $prefix = "type1=".$t1."&type2=";
    }
    if (!is_null($t2)) {
        $prefix = "type1=".$t1."&type2=".$t2."&type3=";
    }
    foreach($types as $typ) {
        $id = $item->getCategoryId($typ);
        print "<a class=\"button micro secondary radius\" href=\"?".$prefix.$id."\">";
        print_itemtype($item, $typ);
        print "</a> ";
    }
  }
?>

<!doctype html>
<html class="no-js" lang="en">
<?php include( 'page/head.php' ); ?>
<body>

<?php include( 'page/page_header.php' ); ?>
    <div class="row">
        <div class="large-12 columns">
            <h1>Liste des objets :</h1>
        </div>
    </div>

    <div class="row">
        <fieldset >
            <legend>Filtre</legend>
            <div>
<?php
$item = new Item();
if (is_null($type1)) {
    $types = Item::get_all_type1();
    print_type_buttons($item, $types);
} else if (is_null($type2)) {
    print_itemtype($item, $list[0]->type1);
    $types = Item::get_all_type2_for_type1($type1);
    if (count($types)>0) {
        print " &#10151; ";
        print_type_buttons($item, $types, $type1);
    }
} else if (is_null($type3)) {
    print_itemtype($item, $list[0]->type1);
    print " &#10151; ";
    print_itemtype($item, $list[0]->type2);
    $types = Item::get_all_type3_for_type2($type2);
    if (count($types)>0) {
        print " &#10151; ";
        print_type_buttons($item, $types, $type1, $type2);
    }
} else {
    print_itemtype($item, $list[0]->type1);
    print " &#10151; ";
    print_itemtype($item, $list[0]->type2);
    print " &#10151; ";
    print_itemtype($item, $list[0]->type3);
}
?>            
                <a title="Supprimer les filtres" class="right" href="/allitem.php">&times;</a>
            </div>
        </fieldset>
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
            $prefix="";
            if (!is_null($type1)) {
              $prefix = "&type1=".$type1;
              if (!is_null($type2)) {
                $prefix .= "&type2=".$type2;
                if (!is_null($type3)) {
                  $prefix .= "&type3=".$type3;
                }
              }
            }

            pagination( $page, $pageCount, $prefix ); 
        ?>
        </div>
    </div>
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>