<?php
  $time_start = microtime_float();
  ini_set('display_errors','On');
  ini_set('display_startup_errors','On'); 

  include_once('classes/item.class.php' );
  include_once('classes/advancedfilter.class.php' );

  $currentMenu = "search";
  $init = array(2, 1, null);
  $WAKFUBLD = "WakfuBuild";
  if (isset($_COOKIE[$WAKFUBLD."INIT"])) {
      $init = explode(",", $_COOKIE[$WAKFUBLD."INIT"]);
      if (!isset($_GET{'type3'})) {
          $init[2] = null;
      }
  }
  
  $sel1 = (isset($_GET{'s1'} )) ? $_GET{'s1'} : $init[0];
  $sel2 = (isset($_GET{'s2'} )) ? $_GET{'s2'} : $init[1];
  $choice = (isset($_GET{'ch'} )) ? $_GET{'ch'} : $init[2];
  $init = array($sel1, $sel2, $choice);
  setcookie( $WAKFUBLD."INIT", implode ( "," , $init ) );
  
  $build1 = (isset($_COOKIE[$WAKFUBLD.$sel1])) ? explode(",", $_COOKIE[$WAKFUBLD.$sel1]) : array(null,null,null,null,null,null,null,null,null,null,null);
  $build2 = (isset($_COOKIE[$WAKFUBLD.$sel2])) ? explode(",", $_COOKIE[$WAKFUBLD.$sel2]) : array(null,null,null,null,null,null,null,null,null,null,null);
  
  if (isset($_GET{'set'} )) {
      $ch = explode("_", $choice);
      $bld = ($ch[0] == 1) ? $sel1 : $sel2;
      $build = ($ch[0] == 1) ? $build1 : $build2;
      $equ = $ch[1];
      set_equipment($build, $equ, $_GET{'set'});
      
      setcookie( $WAKFUBLD.$bld, implode ( "," , $build ), strtotime( '+100 days' ) );
      if ($ch[0] == 1) {
          $build1 = $build;    
      } else {
          $build2 = $build;
      }
  }
  $buildItems1 = load_items($build1);
  $buildItems2 = load_items($build2);
  $caracs1 = merge_caracs($buildItems1);
  $caracs2 = merge_caracs($buildItems2);
  
  if ((count($caracs1)>0) && (count($caracs2)>0)) {
    Caracteristic::compare_arrays($caracs1,$caracs2);
  }
  
  include_once('parts/pagination.php' );
  include_once('parts/display.php' );
  include_once('parts/type_filter.php' );
  include_once('parts/advsearchdisplay.php' );
  
  $limit = 50;  
  $args = new AdvancedFilter($_GET);
  $sql = $args->get_items_request();    
	$result = Item::get_sql_item_list($sql, $limit);
  $list = $result[0];
  foreach ($list as $item) {
      $item->get_caracteristics();
  }
  $count = $result[1];
  $bcount1 = BDD::getCount();
  
  $dummy = new Item();
  $config = array( 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armure"), $dummy->getCategoryId("casque"), "casque.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armure"), $dummy->getCategoryId("epaulettes"), "epaulette.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armure"), $dummy->getCategoryId("amulette"), "amulette.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armure"), $dummy->getCategoryId("anneau"), "anneau_g.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armure"), $dummy->getCategoryId("anneau"), "anneau_d.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armure"), $dummy->getCategoryId("cape"), "cape.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armes"), $dummy->getCategoryId("armes-2-mains").",".$dummy->getCategoryId("seconde-main"), "main_g.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armes"), $dummy->getCategoryId("armes-2-mains").",".$dummy->getCategoryId("armes-1-main"), "main_d.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armure"), $dummy->getCategoryId("plastron"), "plastron.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armure"), $dummy->getCategoryId("ceinture"), "ceinture.png"), 
      array($dummy->getCategoryId("equipements"), $dummy->getCategoryId("armure"), $dummy->getCategoryId("bottes"), "bottes.png"), 
  );
  $time_start2 = microtime_float();
  
  function microtime_float()
  {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
  
  function set_equipment(&$build, $equ, $set) {
      if ($set < 0) {
          if (($equ == 6) || ($equ == 7)) { // armes
              if ($build[$equ] != "") {
                  $item = new Item($build[$equ]);
                  if ($item->type3 == "armes-2-mains") {
                      $build[6] = null;
                      $build[7] = null;
                      //print "clean both hands <br>";
                  }   
              }
          }
          $build[$equ] = null;
      } else if (($equ == 6) || ($equ == 7)) { // armes
          $item = new Item($set);
          //print "IT1:".$item->type3."<br>";
          if ($item->type3 == "armes-2-mains") {
              //print "set both hands <br>";
              $build[6] = $set;
              $build[7] = $set;
          } else if (($build[6] == $build[7]) && ($build[6] >= 0)) {
              $item = new Item($build[6]);
              //print "hands full with ".$item->type3." <br>";
              if ($item->type3 == "armes-2-mains") {
                  $build[6] = null;
                  $build[7] = null;
                  //print "clean both hands <br>";
              }
              $build[$equ]=$set; 
          } else {
              //print "SET2 <br>";
              $build[$equ]=$set;
          }
      } else {
          //print "SET3 <br>";
          $build[$equ]=$set;
      }
  }
  
  function load_items($build) {
      $ret = array();
      foreach($build as $equ) {
          if ($equ != "") {
              $ret[] = new Item($equ);
          } else {
              $ret[] = null;
          }
      }
      return $ret;
  }
  
  function merge_caracs($buildItems) {

      $ret = array();
      $p = 0;
      foreach($buildItems as $item) {
          if (is_object($item)) {
              if (($p != 7) || ($item->type3 != "armes-2-mains")) {
                  $cars = $item->get_caracteristics();
                  if (is_array($cars)) {
                      $ret = Caracteristic::merge_arrays($ret,$cars);
                  }
              }
          }
          $p++;
      }
      // if partial pano, add bonus of part
      $cars = Item::get_pano_bonus_caracteristics($buildItems);
      if (is_array($cars)) {
          $ret = Caracteristic::merge_arrays($ret,$cars);
          //print_r($ret);
      }
      return $ret;
  }
  
  
  function display_items($list, $max, $sel1, $sel2, $choice, $args, $count) {  
      print "<small>".$count." objets trouvés.</small><br/>";
      for ($i = 0; ($i < $max) && ($i < $count) ; $i++) {
        $item = $list[$i];
        print "<a href=\"?s1=".$sel1."&s2=".$sel2."&ch=".$choice."&set=".$item->id."&".$args->get_suffix("")."\">";  
    		print '<img src="./images'.$item->image.'"  width=21 height=21 class="showInfo" data-dropdown="L'.$i.'" data-options="is_hover:true" /> ';
        print $item->name;    
    		print "</a>";
    		display_item_tooltip("L".$i, $item);
        if ($item->type2 == "armes") {
            if ($item->type3 == "armes-2-mains") { print "&#8660;"; } // <=>
            if ($item->type3 == "armes-1-main") { print "&#8658;"; } // =>
            if ($item->type3 == "seconde-main") { print "&#8656;"; } // <=
        }	
        print "<span class='level'> (lvl&nbsp;".$item->level.")</span>\n";
        print "<br/>";
      }
      if ($count > $max) {
        $more = $count - $max;
        print "<small>".$more." objets supplémentaires...</small>";
      }    
  }
  function display_caracs($caracs) {
      if (is_array($caracs)) {
        foreach($caracs as $carac) {
          if (!is_null($carac->image)) {
              print "<img src=\"./images/carac/".$carac->image."\" class=\"wshadowed\" > ";
          } else {
              print "<div style='width:24px;height:19px;' class='left'></div> ";
          }
          if (strcmp($carac->effect,"0")==0) { print "<span style=\"color:gray\">"; }
          print $carac->effect ." ". $carac->name." ";
          if (strcmp($carac->effect,"0")==0) { print "</span>"; }
          $diff = str_replace("%","",str_replace("+","",$carac->diff));
          if ($diff != 0) {
              $pos = strpos($carac->diff, "+");
              if ($pos === false) {
                  print "<span class=red style=\"font-size:80%\" >(".$carac->diff.")</span>";
              } else {
                  print "<span class=green style=\"font-size:80%\" >(".$carac->diff.")</span>";
              }
          }
          print "<br/>";
  			}
      }
  }
  
  function display_item_tooltip($id, $item) {
      //print("<img src=\"./images".$ingr->item->image."\" alt=".$ingr->item->name." class='left showInfo' data-dropdown=\"hover".$cnt."\" data-options='is_hover:true' />");
      $caracs = $item->get_caracteristics();  
      // tooltip
      print("<div id=\"".$id."\" data-dropdown-content class='f-dropdown content infobulle'>");
      print("<h5>".$item->name."</h5>");
      print("<span class='infobulle'>");
      display_caracs($caracs);
      
      $source = ($item->dropable) ?  "Drop" : "Récolte" ;
      print("&#8226; ".$source."<br/>\n");
      print("&#8226; Level : ".$item->level." ");
      print("</span></div>");
  }
  
  function display_equip_btn($side, $buildNb, $buildNb2, $ch, $equ, $args, $config, $itemList) {
      print "<td class=\"buildbox ";
      if ($ch == $side."_".$equ) {
          print "blinkbox";
      }
      print "\">";
      $s2 = ($side == 1) ? 2 : 1;
      $target = "?s".$side."=".$buildNb."&s".$s2."=".$buildNb2."&ch=".$side."_".$equ."&"
          .$args->get_suffix("type")."&type1=".$config[$equ][0]."&type2=".$config[$equ][1]."&type3=".$config[$equ][2] ;
      print "<a>";
      if (is_object($itemList[$equ])) {
          $item = $itemList[$equ];
          $src = $item->image;
          $big = str_replace("/21/", "/42/",$src);
          if (file_exists("./images".$big)) {
              $src = $big;
          }
          $id = $side."_".$equ;
					print "<img src=\"./images".$src."\" width=42 height=42 class=\"wshadowed showInfo\" data-dropdown=\"".$id."\" data-options=\"is_hover:true\" ";
          print " onclick=\"document.location.href='".$target."' \" >";
      } else { 
          print "<img src=\"./images/build/".$config[$equ][3]."\" width=42 height=42 class=\"wshadowed\" ";
          print " onclick=\"document.location.href='".$target."' \" >";
      }
      print "</a>";
			if (is_object($itemList[$equ])) {
          display_item_tooltip($id, $item);
          if ($ch == $side."_".$equ) { // if blinking
              print "<div style='position:relative'>";
              print "<a href=\"?s".$side."=".$buildNb."&s".$s2."=".$buildNb2."&ch=".$ch."&set=-1&".$args->get_suffix("")."\">";
              print "<img src='./images/delete.png' style='position:absolute;top:-45px;right:-4px;' alt='supprimer' title='supprimer'>";
              print "</a></div>";
          }
      }
      print "</td>";
  }
  
  function display_build_picker($side, $buildNb, $build2, $choice, $args, $config, $itemList) {
  ?>
      <table border=1 cellspacing=0 cellpadding=0 style="margin:5px;">
        <tbody>
          <tr>
            <td rowspan=4 >
                <img src="./images/build/mannequin3.png">
            </td>
            <td>
          <?php
            $s2 = ($side == 1) ? 2 : 1;
            print "<Select name=sel".$side." class=buildnumber onchange='document.location.href=\"?s".$side."=\"+this.value+\"&s".$s2."=".$build2."\"; '>";
            for ($i = 1; $i < 10; $i++) {
              if ($buildNb == $i) { print "<option SELECTED>".$i."</option>\n"; }
              else { print "<option>".$i."</option>\n"; }
            }
            print "</select>";
            print "</td>";
            display_equip_btn($side, $buildNb, $build2, $choice, 0, $args, $config, $itemList); // casque
            display_equip_btn($side, $buildNb, $build2, $choice, 1, $args, $config, $itemList); // epaulette
            print "</tr><tr>";
            display_equip_btn($side, $buildNb, $build2, $choice, 2, $args, $config, $itemList); // amulette
            display_equip_btn($side, $buildNb, $build2, $choice, 3, $args, $config, $itemList); // anneau_g
            display_equip_btn($side, $buildNb, $build2, $choice, 4, $args, $config, $itemList); // anneau_d
            print "</tr><tr>";
            display_equip_btn($side, $buildNb, $build2, $choice, 5, $args, $config, $itemList); // cape
            display_equip_btn($side, $buildNb, $build2, $choice, 6, $args, $config, $itemList); // main_g
            display_equip_btn($side, $buildNb, $build2, $choice, 7, $args, $config, $itemList); // main_d
            print "</tr><tr>";
            display_equip_btn($side, $buildNb, $build2, $choice, 8, $args, $config, $itemList);  // plastron
            display_equip_btn($side, $buildNb, $build2, $choice, 9, $args, $config, $itemList);  // ceinture
            display_equip_btn($side, $buildNb, $build2, $choice, 10, $args, $config, $itemList); // bottes
            print "</tr>";

          ?>
        </tbody>
      </table>  
       <?php
       //print_r($build);
  }
  
  
  
  
?><!doctype html>
<html class="no-js" lang="en">
<?php include( 'page/head.php' ); ?>
<body >

<?php 
     include( 'page/page_header.php' ); 
     /*
     print "Debug :<br>";
     print_r($init);
     print "<br>";
     print_r($build1);
     print "<br>";
     print_r($build2);
     */
     
     ?> 
    <div class="row">
      <div class="large-4 medium-6 columns right" >
        <fieldset>
          <legend>Filtre</legend>
          <?php 
              display_adv_search_form($args, false); 
          ?>
        </fieldset>
        
         <?php
              if ($args->type3 != null) {
                  print "<div style='width:100%;height:1px;' class='blinkbox' ></div>";
                  print "<div style='height:500px;width:1px;float:left;' class='blinkbox' ></div>";                 
                  print "<div style='height:500px;width:1px;float:right;' class='blinkbox' ></div>";                 
                  print "<div style=\"height:500px;overflow-y: scroll;width:98%;float:right;\">";
                  display_items($list, $limit, $sel1, $sel2, $choice, $args, $count);
                  print "</div>";
                  print "<div style='width:100%;height:1px;clear:both;' class='blinkbox' ></div>";

              } 
              //print $sql;
         ?>    
        
      </div>
      <div class="large-4 medium-6 columns" style="float:right;" >
            <?php
                display_build_picker(1, $sel1, $sel2, $choice, $args, $config, $buildItems1);
                print("<span class='infobulle'>");
                display_caracs($caracs1);
                print "</span>";
            ?>
      <!-- write build caracteristics -->
      </div>
      <div class="large-4 medium-6 columns" style="float:right;" >
            <?php
                display_build_picker(2, $sel2, $sel1, $choice, $args, $config, $buildItems2);
                print("<span class='infobulle'>"); 
                display_caracs($caracs2);
                print "</span>";
            ?>
      <!-- write build caracteristics -->
      </div>
      
      
    </div>
<p>&nbsp;</p>    
    
<?php include( 'page/footer_script.php' ); 
/*<script src="js/vendor/jquery.js"></script>
<script src="js/foundation.min.js"></script>
<script src="js/app.js"></script>
<script>
    $(document).foundation();
</script>
  */
?>
    <script>
    setInterval(function(){
        $(".blinkbox").toggleClass("blinklight");
     },1000)
    </script>
<?php
$bcount2 = BDD::getCount(); 
$time_end = microtime_float();
$gentime = $time_end - $time_start;
$gentime2 = $time_start2 - $time_start;
print "<small>Page generated in ".$gentime." seconds.</small><br>";
print "<small>Data collected in ".$gentime2." seconds.</small><br>";
print "<small>Database queries : ".$bcount1." / ".$bcount2."</small><br>";
?>
</body>
</html>