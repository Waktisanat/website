<?php

  ini_set('display_errors','On');
  ini_set('display_startup_errors','On'); 

  include_once('classes/item.class.php' );
  include_once('classes/advancedfilter.class.php' );

  $currentMenu = "search";
  $init = array(2, 1, null);
  $WAKFUBLD = "WakfuBuild";
  if (isset($_COOKIE[$WAKFUBLD."INIT"])) {
      $init = explode(",", $_COOKIE[$WAKFUBLD."INIT"]);
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
      $build[$equ]=$_GET{'set'};
      if ($build[$equ] < 0) {
          $build[$equ] = null;
      }
      setcookie( $WAKFUBLD.$bld, implode ( "," , $build ), strtotime( '+100 days' ) );
      if ($ch[0] == 1) {
          $build1 = $build;    
      } else {
          $build2 = $build;
      }
  }
  $caracs1 = merge_caracs($build1);
  $caracs2 = merge_caracs($build2);
  if ((count($caracs1)>0) && (count($caracs2)>0)) {
    Caracteristic::compare_arrays($caracs1,$caracs2);
  }
  
  include_once('parts/pagination.php' );
  include_once('parts/display.php' );
  include_once('parts/type_filter.php' );
  include_once('parts/advsearchdisplay.php' );
  
  $args = new AdvancedFilter($_GET);
  $sql = $args->get_items_request();    
	$list = Item::get_sql_item_list($sql);
  $limit = 50;
  
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
  
  function merge_caracs($build) {
      $ret = array();
      foreach($build as $equ) {
          if ($equ != "") {
              $item = new Item($equ);
              $cars = $item->get_caracteristics();
              if (is_array($cars)) {
                  $ret = Caracteristic::merge_arrays($ret,$cars);
              }
          }
      }
      return $ret;
  }
  
  
  function display_items($list, $max, $sel1, $sel2, $choice, $args) {  
      $count = count($list);
      print "<div class='right'>";
      print "<a href=\"?s1=".$sel1."&s2=".$sel2."&ch=".$choice."&set=-1&".$args->get_suffix("")."\">";
      print "<strong><span class='red'>x&nbsp;";
      print "</span></strong></a></div>";
      print "<small>".$count." objets trouvés.</small><br/>";
      for ($i = 0; ($i < $max) && ($i < $count) ; $i++) {
        $item = $list[$i];	
        print "<a href=\"?s1=".$sel1."&s2=".$sel2."&ch=".$choice."&set=".$item->id."&".$args->get_suffix("")."\">";  
    		print '<img src="./images'.$item->image.'"  width=21 height=21 class="showInfo" data-dropdown="L'.$i.'" data-options="is_hover:true" /> ';
        print $item->name;    
    		print "</a>";
    		display_item_tooltip("L".$i, $item);
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
              print "<div style='width:20px;height:19px;' class='left'></div> ";
          }
          print $carac->effect ." ". $carac->name." ";
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
  
  function display_equip_btn($side, $buildNb, $buildNb2, $ch, $equ, $args, $config, $build) {
      print "<td class=\"buildbox ";
      if ($ch == $side."_".$equ) {
          print "blinkbox";
      }
      print "\">";
      $s2 = ($side == 1) ? 2 : 1;
      $target = "?s".$side."=".$buildNb."&s".$s2."=".$buildNb2."&ch=".$side."_".$equ."&"
          .$args->get_suffix("type")."&type1=".$config[$equ][0]."&type2=".$config[$equ][1]."&type3=".$config[$equ][2] ;
      print "<a>";
      if ($build[$equ] != null) {
          $item = new Item($build[$equ]);
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
			if ($build[$equ] != null) {
          display_item_tooltip($id, $item);
      }
      print "</td>";
  }
  
  function display_build_picker($side, $buildNb, $build2, $choice, $args, $config, $build) {
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
            display_equip_btn($side, $buildNb, $build2, $choice, 0, $args, $config, $build); // casque
            display_equip_btn($side, $buildNb, $build2, $choice, 1, $args, $config, $build); // epaulette
            print "</tr><tr>";
            display_equip_btn($side, $buildNb, $build2, $choice, 2, $args, $config, $build); // amulette
            display_equip_btn($side, $buildNb, $build2, $choice, 3, $args, $config, $build); // anneau_g
            display_equip_btn($side, $buildNb, $build2, $choice, 4, $args, $config, $build); // anneau_d
            print "</tr><tr>";
            display_equip_btn($side, $buildNb, $build2, $choice, 5, $args, $config, $build); // cape
            display_equip_btn($side, $buildNb, $build2, $choice, 6, $args, $config, $build); // main_g
            display_equip_btn($side, $buildNb, $build2, $choice, 7, $args, $config, $build); // main_d
            print "</tr><tr>";
            display_equip_btn($side, $buildNb, $build2, $choice, 8, $args, $config, $build);  // plastron
            display_equip_btn($side, $buildNb, $build2, $choice, 9, $args, $config, $build);  // ceinture
            display_equip_btn($side, $buildNb, $build2, $choice, 10, $args, $config, $build); // bottes
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
                  display_items($list, $limit, $sel1, $sel2, $choice, $args);
                  print "</div>";
                  print "<div style='width:100%;height:1px;clear:both;' class='blinkbox' ></div>";

              } 
              //print $sql;
         ?>    
        
      </div>
      <div class="large-4 medium-6 columns" style="float:right;" >
            <?php
                display_build_picker(1, $sel1, $sel2, $choice, $args, $config, $build1);
                print("<span class='infobulle'>");
                display_caracs($caracs1);
                print "</span>";
            ?>
      <!-- write build caracteristics -->
      </div>
      <div class="large-4 medium-6 columns" style="float:right;" >
            <?php
                display_build_picker(2, $sel2, $sel1, $choice, $args, $config, $build2);
                print("<span class='infobulle'>"); 
                display_caracs($caracs2);
                print "</span>";
            ?>
      <!-- write build caracteristics -->
      </div>
      
      
    </div>
    
    
<?php include( 'page/footer_script.php' ); ?>
    <script>
    setInterval(function(){
        $(".blinkbox").toggleClass("blinklight");
     },1000)
    </script>
</body>
</html>