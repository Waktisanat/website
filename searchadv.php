<?php   
  ini_set('display_errors','On');
  ini_set('display_startup_errors','On'); 

  include_once('classes/item.class.php' );
  include_once('classes/advancedfilter.class.php' );
  include_once('parts/pagination.php' );
  include_once('parts/display.php' );
  include_once('parts/type_filter.php' );

  $currentMenu = "search";


	$limit =(isset($_GET["limit"])) ? $_GET["limit"] : 40;
	$page = (isset($_GET{'page'} )) ? $_GET{'page'} : 0;
	$offset = $limit * $page ;

  $args = new AdvancedFilter($_GET);
  $sql = $args->get_items_request();    
	$result = Item::get_sql_item_list($sql);
	$list = $result[0];
  $countItem = $result[1];
  
  $dummy = ($countItem > 0) ? $list[0] : new Item();
  if ($countItem == 0) {
      $dummy->type1 = $dummy->getCategoryName_from_ID($args->type1);
      $dummy->type2 = $dummy->getCategoryName_from_ID($args->type2);
      $dummy->type3 = $dummy->getCategoryName_from_ID($args->type3);
  }
  $caracs = $dummy->get_main_caracteristics();
	
	$pageCount = ceil( $countItem / $limit ) - 1;

  /******************************************************************/
  function list_all( $limit, $offset, $list , $filter, $caracs) {
    print "<TABLE width='99%' ><thead><tr>";
    print " <th>Item</th>";
    print " <th style='text-align:center'>Lvl</th>";
    print " <th style='text-align:center'>Type</th>";
    foreach($filter->caracs as $select) {
        foreach($caracs as $car) {
            if ($select == $car->id) {
                print " <th style='text-align:center'><img src=\"./images/carac/".$car->image."\" class='wshadowed'";
                print " title=\"".$car->name."\" alt=\"".$car->name."\" ></th>";
            }
        }
    }
    print " <th style='text-align:center'>Prix&nbsp;HDV</th>";
    print "</tr></thead>";
    print "<tbody>";
         
    for ($i = $offset; ($i < $offset + $limit) && ($i < count($list)); $i++)
    {
        $item = $list[$i];
        $item->get_price();
        $item->get_caracteristics();
        
        print "<tr><td>";	
        print '<a href="./item.php?id='.$item->id.'">'; 
    		print '<img src="./images'.$item->image.'"  width=21 height=21 class="shadowed"/> ';
    		print $item->name;    
    		print "</a>";
        print "</td>";
        
        print "<td align='middle'>".$item->level."</td>\n";
        
        print "<td align='middle'>";
        print_full_itemtype($item);
        $r = array_search($item->rarety, Item::get_rareties(), true);
        print "<img src=\"./images/rar_".$r.".png\" title=\"".$item->rarety."\" alt=\"".$item->rarety."\" class='wshadowed'/>";
        if ($item->craftable) {
            print "<img src=\"./images/craft.png\" />";
        }
        print "</td>\n";
        
        foreach($filter->caracs as $select) {
            print "<td align='middle'>";
            foreach($item->caracs as $car) {
                if ($select == $car->id) {
                    print $car->effect;
                }
            }
            print "</td>\n";
        }
                
        print("<td align='right' nowrap>".$item->price->avg."<img class='kama' />");
        
        display_tendency($item);
        print "</td>\n";
        
        print "</tr>";	

    }
  
    print "</tbody>";
    print "</table>";    
  }
  
  function print_all_caracteristics($caracs, $args) {
    $cnt = 0;
    foreach($args->caracs as $select) {
        $options="";
        $img = "";
        foreach($caracs as $car) {
            $selected = ($select == $car->id) ? " SELECTED " : "";
            if ($select == $car->id) {
                $img = "<img src=\"./images/carac/".$car->image."\" class='wshadowed' style=\"margin-top: 1px;vertical-align: top;\" >";
            }
            $options .= "<option value=\"".$car->id."\" ".$selected." >".$car->name."</option>\n";
        }
        print "<div >".$img;
        print "<select name=\"carac".$cnt."\" class=\"small\" onChange=\"document.getElementById('advseach').submit()\" />";
        print "<option ></option>";
        print $options;    
        print "</select>";    
        print "</div>";    
        $cnt++;
    }
    print "<input type=hidden name=\"carnum\" value=\"".$cnt."\" >";
    print "<select name=\"carac".$cnt."\" class=\"small\" onChange=\"document.getElementById('advseach').submit()\" style=\"margin-left:21px\" />";
    print "<option ></option>";
    foreach($caracs as $car) {
        print "<option value=\"".$car->id."\" >".$car->name."</option>";
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
      <div class="large-4 medium-6 columns right">
        <form id="advseach" name="advseach" class="custom">
            <fieldset>
                <legend>Recherche Avancée</legend>
                <div class="row">
                    <div style="float:left;width:100%;">
                        <table style="border:0;width:100%;" cellspacing=0 cellpadding=0 ><tr>
                            <td align="right" width="21%" style="padding:0;">Type :</td>
                            <?php print_categoryFilter($args->type1, $args->type2, $args->type3, $dummy, "nano", $args->get_suffix("type")); ?>
                        </tr></table>
                        <?php 
                        if (!is_null($args->type1)) { print "<input type=hidden name=type1 value=\"".$args->type1."\" >"; }
                        if (!is_null($args->type2)) { print "<input type=hidden name=type2 value=\"".$args->type2."\" >"; } 
                        if (!is_null($args->type3)) { print "<input type=hidden name=type3 value=\"".$args->type3."\" >"; } 
                        ?>
                    </div>
                    <div class="left" style="line-height: 27px;width:21%;text-align:right;">
                      <label style="margin:0;" nowrap>Level : </label>
                      <label style="margin:0;" nowrap>Mot&nbsp;clef&nbsp;:&nbsp;</label>
                      <label style="margin:0;" nowrap>Rareté&nbsp;:&nbsp;</label>
                    </div>
                    <div class="columns" style="line-height: 27px;padding:0;width:45%;">
                      <div>
                        <table style="border:0;width:100%;" cellspacing=0 cellpadding=0 ><tr>
                          <td style="padding-left:2px;">
                            <input type=text style="width:100%" class="small" name=min placeholder="min" 
                            <?php if (!is_null($args->min)) print " value='".$args->min."' "; ?> 
                            onChange="document.getElementById('advseach').submit()" /></td> 
                          <td style="padding-right:0;">
                            <input type=text style="width:100%" class="small" name=max placeholder="max" 
                            <?php if (!is_null($args->max)) print " value='".$args->max."' "; ?> 
                            onChange="document.getElementById('advseach').submit()" /></td>
                        </tr></table>
                      </div>
                      <div  style="padding-left:4px;">
                        <input type=text style="width:100%;margin:4px 0;" class="small" name=key 
                            <?php if (!is_null($args->key)) print " value='".$args->key."' "; ?> 
                            onChange="document.getElementById('advseach').submit()" />
                        <select name="rarity" style="width:100%;margin:4px 0;" class="small" onChange="document.getElementById('advseach').submit()" />
                              <option ></option>
                              <?php 
                                $rars = Item::get_rareties();
                                for ($i = 0; $i < count($rars); $i++) {
                                    print "<option value='".$i."' ";
                                    if (($args->rarity != "") && ($args->rarity == $i)) {
                                        print "SELECTED";
                                    }
                                    print " >".$rars[$i]."</option>\n";
                                } 
                              ?>
                        </select> 
                      </div>
                    </div>
                    <div class="right" style="line-height: 27px;text-align:right;padding:0;width:30%;">
                        <label style="margin:0;">Artisanat&nbsp;:&nbsp;<input type=checkbox name=craft value=X 
                            <?php if (!is_null($args->craft)) print " CHECKED "; ?> 
                            onChange="document.getElementById('advseach').submit()" /></label>
                        <label style="margin:0;">Drop&nbsp;:&nbsp;<input type=checkbox name=drop value=X  
                            <?php if (!is_null($args->drop)) print " CHECKED "; ?> 
                            onChange="document.getElementById('advseach').submit()" /></label>
                        <label style="margin:0;">Récolte&nbsp;:&nbsp;<input type=checkbox name=recolte value=X  
                            <?php if (!is_null($args->recolte)) print " CHECKED "; ?> 
                            onChange="document.getElementById('advseach').submit()" /></label>
                    </div>                     


                    <div class="left">
                        <label class="left">Caractéristiques :
                        <?php
                            if (count($args->caracs) > 0) {
                                $c1 = ($args->carand == 1) ? " CHECKED " : "";
                                $c2 = ($args->carand == 1) ? "" : " CHECKED ";
                                print " <i>(et:<input type=radio name=carand value=1 ".$c1;
                                print " style=\"vertical-align: middle;\" onChange=\"document.getElementById('advseach').submit()\" >\n";
                                print ", ou:<input type=radio name=carand value=0 ".$c2;
                                print " style=\"vertical-align: middle;\" onChange=\"document.getElementById('advseach').submit()\" >)</i>\n";
                            } 
                        ?></label>
                        <div class="right">
                        <?php
                          print_all_caracteristics($caracs,$args);
                        ?>
                        </div>
                    </div>
                </div>  <!-- /row --> 
            </fieldset>
        </form>
          <?php   /*
            print "<pre>";
            print_r($args);
            //print ($args->get_suffix("type"));
            print "</pre>";    */
            ?>
      </div>               
        
      <div class="large-8 medium-6 columns">  
          <?php
              print "<h3> Nombre d'éléments trouvés : ".count($list)."</h3>";
              list_all( $limit, $offset, $list, $args, $caracs );
          ?>
      </div>
    </div>

    <div class="row"> 
        <div class="large-12 medium-12 columns">  
        <?php

            pagination( $page, $pageCount, $args->get_suffix("page") ); 
        ?>
        </div>
    </div>
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>