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
  
	$lvlmin = (isset($_GET{'lvlmin'} )) ? $_GET{'lvlmin'} : null;
	$lvlmax = (isset($_GET{'lvlmax'} )) ? $_GET{'lvlmax'} : null;
	$key = (isset($_GET{'key'} )) ? $_GET{'key'} : null;
	$rarity = (isset($_GET{'rarity'} )) ? $_GET{'rarity'} : null;
	$craft = (isset($_GET{'craft'} )) ? $_GET{'craft'} : null;
	$drop = (isset($_GET{'drop'} )) ? $_GET{'drop'} : null;
	$recolte = (isset($_GET{'recolte'} )) ? $_GET{'recolte'} : null;
	$carac0 = (isset($_GET{'carac0'} )) ? $_GET{'carac0'} : null;  
  
  
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
      <div class="large-4 medium-6 columns">
        <form class="custom">
            <fieldset>
                <legend>Recherche Avancée</legend>
                <div class="row">
                    <div style="float:left;width:100%;">
                        <table style="border:0;width:100%;" cellspacing=0 cellpadding=0 ><tr>
                            <td align="right" width="21%" style="padding:0;">Type :</td>
                            <?php print_categoryFilter($type1, $type2, $type3, $dummy, "nano") ?>
                        </tr></table>
                        
                    </div>
                    <div class="left" style="line-height: 27px;width:21%;text-align:right;">
                      <label style="margin:0;">Level : </label>
                      <label style="margin:0;">Mot clef : </label>
                      <label style="margin:0;">Rareté : </label>
                    </div>
                    <div class="columns" style="line-height: 27px;padding:0;width:45%;">
                      <div>
                        <table style="border:0;width:100%;" cellspacing=0 cellpadding=0 ><tr>
                          <td style="padding-left:2px;">
                            <input type=text style="width:100%" class="small" name=lvlmin placeholder="min" /></td> 
                          <td style="padding-right:0;">
                            <input type=text style="width:100%" class="small" name=lvlmax placeholder="max" /></td>
                        </tr></table>
                      </div>
                      <div  style="padding-left:4px;">
                        <input type=text style="width:100%;margin:4px 0;" class="small" name=key />
                        <select name="rarity" style="width:100%;margin:4px 0;" class="small"/>
                              <option ></option>
                              <option value=0>Commun</option>
                              <option value=1>Inhabituel</option>
                              <option value=2>Rare</option>
                              <option value=3>Mythique</option>
                              <option value=4>Légendaire</option>
                              <option value=5>Relique</option>
                        </select> 
                      </div>
                    </div>
                    <div class="right" style="line-height: 27px;text-align:right;padding:0;width:30%;">
                        <label style="margin:0;">Artisanat : <input type=checkbox name=craft value=X /></label>
                        <label style="margin:0;">Drop : <input type=checkbox name=drop value=X /></label>
                        <label style="margin:0;">Récolte : <input type=checkbox name=recolte value=X /></label>
                    </div>                     


                    <div class="left">
                        <label class="left">Caractéristiques :</label>
                        <div class="right">
                        <?php
                          print_all_caracteristics($caracs);
                        ?>
                        </div>
                    </div>
                </div>  <!-- /row --> 
              </div>               
            </fieldset>
        </form>
        </div>
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