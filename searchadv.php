<?php   
  ini_set('display_errors','On');
  ini_set('display_startup_errors','On'); 

  include_once('classes/item.class.php' );
  include_once('parts/pagination.php' );
  include_once('parts/display.php' );
  include_once('parts/type_filter.php' );



	$limit =(isset($_GET["limit"])) ? $_GET["limit"] : 90;
	$page = (isset($_GET{'page'} )) ? $_GET{'page'} : 0;
	$offset = $limit * $page ;

	$type1 = (isset($_GET{'type1'} )) ? $_GET{'type1'} : null;
	$type2 = (isset($_GET{'type2'} )) ? $_GET{'type2'} : null;
	$type3 = (isset($_GET{'type3'} )) ? $_GET{'type3'} : null;
  
	$list = Item::get_items_by_type($type1,$type2,$type3);
  $countItem = count($list);
  $dummy = ($countItem > 0) ? $list[0] : new Item();
  $caracs = $dummy->get_main_caracteristics();
	
	$pageCount = ceil( $countItem / $limit ) - 1;
  /******************************************************************/
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
  
  function print_all_caracteristics($caracs) {
    print "<select name=\"carac0\" class=\"small\"/>";
    print "<option ></option>";
    foreach($caracs as $car) {
        //print "<a class=\"button nano secondary radius\" href=\"?\">";
        print "<option value=\"".$car->id."\" style=\"background-image:url('/images/carac/".$car->image."');\" >";
        //print "<img src=\"/images/carac/".$car->image."\" class=\"category radius\" title=\"\" alt=\"\" >";
        print $car->name;
        print "</a> ";
    }
    print "</select>";
  }
  
?>

<!doctype html>
<html class="no-js" lang="en">
<?php include( 'page/head.php' ); ?>
<body>

<?php include( 'page/page_header.php' ); ?>

    <div class="row">
        <form class="custom">
            <fieldset>
                <legend>Recherche Avancée</legend>
                <div class="row">
                    <div style="float:left;margin-right:10px;">
                        <table style="border:0;" cellspacing=0 cellpadding=0 ><tr>
                        <td nowrap style="line-height: 27px;">Type :</td>
                        <?php print_categoryFilter($type1, $type2, $type3, $dummy, "nano") ?>
                        </tr></table>
                    </div>
                    <div style="float:left;margin-right:10px;">
                        <table style="border:0;margin:0 auto;" cellspacing=0 cellpadding=0 ><tr>
                        <td nowrap style="line-height: 27px;">Level :</td> 
                        <td><input type=text style="width:40px" class="small" name=lvlmin placeholder="min" /></td>
                        <td><input type=text style="width:40px" class="small" name=lvlmax placeholder="max" /></td>
                        </tr></table>
                    </div>
                    <div style="float:left;height:33px;margin-right:10px;">
                        <table style="border:0;margin:0 auto;" cellspacing=0 cellpadding=0 ><tr>
                        <td nowrap style="line-height: 27px;">Mot clef :</td> 
                        <td><input type=text style="width:130px" class="small" name=key /></td>
                        </tr></table>
                    </div>
                    <div style="float:left;margin-right:10px;">
                        <table style="border:0;margin:0 auto;" cellspacing=0 cellpadding=0 ><tr>
                        <td nowrap style="line-height: 27px;">Rareté :</td> 
                        <td>
                        <select name="rarity" class="small"/>
                            <option ></option>
                            <option value=0>Commun</option>
                            <option value=1>Inhabituel</option>
                            <option value=2>Rare</option>
                            <option value=3>Mythique</option>
                            <option value=4>Légendaire</option>
                            <option value=5>Relique</option>
                        </select> 
                        </td></tr></table>
                    </div>
                    <div style="float:left;height:33px;margin-right:10px;">
                        <table style="border:0;margin:0 auto;" cellspacing=0 cellpadding=0 ><tr>
                        <td style="line-height: 27px;">Artisanat</td><td><input type=checkbox name=craft /></td>
                        <td style="line-height: 27px;">Drop</td><td><input type=checkbox name=drop /></td>                             
                        <td style="line-height: 27px;">Récolte</td><td><input type=checkbox name=recolte /></td>
                        </tr></table>
                    </div>
                </div>
                <div class="row">
                    <label>Caractéristiques :</label>
                        <?php
                          print_all_caracteristics($caracs);
                        ?>
                </div>
               </div>               
            </fieldset>
        </form>
    </div>
    <div class="row">
        <div class="large-12 medium-12 columns">  
        <?php
             // list_all( $limit, $offset, $list );
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