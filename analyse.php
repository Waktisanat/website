<?php  
ini_set('display_errors','On');
ini_set('display_startup_errors','On'); 

  require_once('classes/item.class.php'); 
  require_once('classes/recette.class.php'); 
  include_once('parts/pagination.php' );
  include_once('parts/display.php' );

    $currentMenu = "tarif";
        
	$limit = isset($_GET["limit"]) ? $_GET["limit"] : 20;
	$page = isset($_GET["page"]) ? $_GET["page"] : 0;
	$metier = isset($_GET["metier"]) ? $_GET["metier"] : 1;
	$calc = isset($_GET["calc"]) ? $_GET["calc"] : 1;
	$sort = isset($_GET["sort"]) ? $_GET["sort"] : 0;
	$freec = isset($_GET["freec"]) ? $_GET["freec"] : 0;
	
	$offset = $limit * $page ;

  /*-----------------------------------------------*/
  /*                GET DATA CORE                  */
  /*-----------------------------------------------*/
  
  $recettes = Recette::get_recettes_by_metier_id($metier);
  $tempList = array(); 
  foreach($recettes as $rec) {
      $tmp = array();
      $rec->load_recursive_ingredients();
      $rec->load_ingredient_prices();
                            
      $tmp['prix'] = isset($rec->item->price) ?  get_calc_price( $rec->item->price, $calc) : 0;
      $tmp['ingredients'] = $rec->ingredients;
      $tmp['iteminfo'] = $rec->item;
      $craft = $rec->get_craft_price();
      if ($freec) {
        $craft  = new Price();
        foreach($rec->ingredients as $ingr) {
            if (isset($ingr->item->price) && ($ingr->item->dropable)) {
                $craft->min += $ingr->item->price->min * $ingr->nombre;
                $craft->max += $ingr->item->price->max * $ingr->nombre;
                $craft->avg += $ingr->item->price->avg * $ingr->nombre;
            }
        }
      }
      $tmp['craft'] = get_calc_price( $craft, $calc);
      $tmp['benef'] = $tmp['prix'] - $tmp['craft'];
      $tempList[] = $tmp;
  }                         
  
  tri_recettes($tempList, $sort);
  //$tempList;  
  /*-----------------------------------------------*/
  
	$countItem = count($recettes);
	$pageCount = ceil( $countItem / $limit ) - 1;
                        
  /*-----------------------------------------------*/
  /*              DISPLAY FUNCTIONS                */
  /*-----------------------------------------------*/
  function display_select_metier($metier) {
    print("<select name='metier' onChange=\"document.getElementById('form1').submit();\">");
		$req = BDD::get()->prepare('SELECT * FROM METIERS ');
		$req->execute();
		$resp = $req->fetchAll();
    foreach ($resp as $m) {
       print("<option value=".$m['id']." ");
       if ($m['id'] == $metier) print(" selected='selected' ");
       print(">".$m['name']);
       print("</option>");
    }
		print("</select>");
  }     
              
  function display_select($val,$name,$strset) {
    print("<select name='".$name."' onChange=\"document.getElementById('form1').submit();\">");
    for ($i = 0; $i < count($strset); $i++) {
       print("<option value=".$i." ");
       if ($i == $val) print(" selected='selected' ");
       print(">".$strset[$i]);
       print("</option>");
    }
		print("</select>");
  }  
  
  /*-----------------------------------------------*/
  /*              UTILITY FUNCTIONS                */
  /*-----------------------------------------------*/
  function get_calc_price($price, $calc) {
    if ($calc == 0) {
        return $price->min;
    } else if ($calc == 1) {
        return $price->avg;
    } 
    return $price->max;
  }
  
  function tri_recettes(&$tempList, $clef) {
      $fin = 0;
      while (!$fin) {
          $fin = 1;
          for($i = 0; $i < count($tempList) -1; $i++) {
              $swap = 0;
              switch ($clef) {
                  default:
                  case 0 : // alphabétique
                      $swap = ($tempList[$i]['iteminfo']->name > $tempList[$i+1]['iteminfo']->name);
                      break;
                  case 1 : // bénéfice
                      $swap = ($tempList[$i]['benef'] < $tempList[$i+1]['benef']);
                      break;
                  case 2 : // level
                      $swap = ($tempList[$i]['iteminfo']->level > $tempList[$i+1]['iteminfo']->level);
                      break;
                  case 3 : // prix/u
                      $swap = ($tempList[$i]['prix'] < $tempList[$i+1]['prix']);
                      break;
              }
              if ($swap) {
                  $tmp = $tempList[$i];
                  $tempList[$i] = $tempList[$i+1];
                  $tempList[$i+1] = $tmp;
                  $fin = 0;
              }
          }
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
            <h1>Analyse de rentabilité.</h1>
        </div>
    </div>

    <div class="row">
        <form action="" name="form1" id="form1" method="GET">
            <input type="hidden" name="limit" id="limit" value="<?PHP print($limit); ?>">
            <input type="hidden" name="page" id="page" value="0"><!-- on purpose. if filter change, go back to first page. -->
            
            <fieldset>
                <legend>Filtre</legend>
                
                <div class="row">
                    <div class="small-4 columns"> 
                        <div class="row">
                            <div class="small-3 columns">
                                <label class="right inline" style="margin:0;">Métier :</label>
                            </div>
                            <div class="small-9 columns">
                                <?PHP display_select_metier($metier); ?>
                            </div>
                        </div>
                    </div>
                    <div class="small-3 columns">
                        <div class="row">
                            <div class="small-3 columns">
                                <label class="right inline" style="margin:0;">Prix :</label>
                            </div>
                            <div class="small-9 columns">
                                <?PHP display_select($calc,"calc",array("minimum", "moyenne", "maximum")); ?>
                            </div>
                        </div>
                    </div>
                    <div class="small-3 columns">
                        <div class="row">
                            <div class="small-3 columns">
                                <label class="right inline" style="margin:0;">Tri :</label>
                            </div>
                            <div class="small-9 columns">
                                <?PHP display_select($sort,"sort",array("Alphabétique", "Bénéfice", "Level", "Prix HDV")); ?>
                            </div>
                        </div>
                    </div>
                    <div class="small-2 columns">  
                        <label class="right inline" style="margin:0;">Récolte gratuit : <input type="checkbox" name="freec" value="1" <?PHP
                         if ($freec) {
                            print("checked='checked'");
                         }
                        ?> onChange="document.getElementById('form1').submit();" ></label> 
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <div class="row">
        <?php  pagination( $page, $pageCount, "&metier=".$metier."&calc=".$calc."&sort=".$sort ); ?>
    </div>

    <div class="row">
        <table  width=99%>
            <thead>
                <tr>
                    <th width="20%">Recette</th>
                    <th>Lvl</th>
                    <th>Ingrédients</th>
                    <th>Prix&nbsp;HDV</th>
                    <th>Prix&nbsp;Craft</th>
                    <th>Bénéfice</th>
                </tr>
            </thead>
            
            <tbody>
                <?php
                for($i=$offset; ($i < $offset + $limit) && ($i < $countItem); $i++) {
                    $rec = $tempList[$i]; 
                    $cnt = 1000*$i;                         
                    $item = $rec['iteminfo'];
         
                    print("<tr>");
                    print("<td><a href=\"./item.php?id=".$item->id."\"><IMG src=\"./images".$item->image."\" >".$item->name."</a></td>");
                    print("<td align=\"center\">".$item->level."</td>");    
                    print("<td>");
                        print("<ul class='inline-list ingredients'>");
                        foreach($rec['ingredients'] as $ingr) {
                           $col = ($ingr->item->dropable) ? "droppable" : "collectable";
                           if (!isset($ingr->item->price))  $col="red";
                           print("<li>");
                           print("<span class=\"left ".$col."\">".$ingr->nombre."x </span>");
                           print("<img src=\"./images".$ingr->item->image."\" alt=".$ingr->item->name." class='left showInfo' data-dropdown=\"hover".$cnt."\" data-options='is_hover:true' />");
        
                           // tooltip
                           print("<div id=\"hover".$cnt."\" data-dropdown-content class='f-dropdown content infobulle'>");
                           print("<a href=\"./item.php?id=".$ingr->item->id."\"><strong>".$ingr->item->name."</strong></a><br/>");
                           print("<span class='infobulle'>");
                           if (!isset($ingr->item->price)) {
                              print("&#8226; <span style='color:red'>n'est pas en HDV...</span><br/>\n");
                           } else {
                              print("&#8226; HDV : ".$ingr->item->price->min."<img class='kama' />-".$ingr->item->price->max."<img class='kama'/>\n");
                              display_tendency($ingr->item);
                              print "<br/>";
                           }
                           $source = ($ingr->item->dropable) ?  "Drop" : "Récolte" ;
                           print("&#8226; <span class=\"".$col."\">".$source."</span><br/>\n");
                           print("&#8226; Level : ".$ingr->item->level." ");
                           print("</span></div>");
                           print("</li>");
                           $cnt++;
                        }
                        print("</ul>");
                    print("</td>");
                    print("<td align='right' nowrap>".$rec['prix']."<img class='kama' />");
                    display_tendency($item);
                    print "</td>\n";
                    print("<td style='text-align:right;' nowrap>".$rec['craft']."<img class='kama' /></td>");
                    $col = ($rec['benef'] < 0) ? "red" : "green";
                    print("<td style='text-align:right;' nowrap><span class=\"".$col."\">".$rec['benef']."</span><img class='kama' /></td>");  
                    print("</tr>");
                }             
                ?>
            </tbody>
        </table>
	</div>

	<div class="row">
        <?php  pagination( $page, $pageCount, "&metier=".$metier."&calc=".$calc."&sort=".$sort ); ?>
    </div>
    
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>