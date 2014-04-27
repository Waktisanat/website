<?php

  ini_set('display_errors','On');
  ini_set('display_startup_errors','On'); 

  include_once('classes/item.class.php' );
  include_once('classes/advancedfilter.class.php' );

  $currentMenu = "search";
  
  $sel1 = (isset($_GET{'s1'} )) ? $_GET{'s1'} : 1;
  $sel2 = (isset($_GET{'s2'} )) ? $_GET{'s2'} : 2;
  $choice = (isset($_GET{'ch'} )) ? $_GET{'ch'} : null;
  
  $WAKFUBLD = "WakfuBuild";
  $build1 = (isset($_COOKIE[$WAKFUBLD.$sel1])) ? explode(",", $_COOKIE[$WAKFUBLD.$sel1]) : array(null,null,null,null,null,null,null,null,null,null,null);
  $build2 = (isset($_COOKIE[$WAKFUBLD.$sel2])) ? explode(",", $_COOKIE[$WAKFUBLD.$sel2]) : array(null,null,null,null,null,null,null,null,null,null,null);
  
  if (isset($_GET{'set'} )) {
      $ch = explode("_", $choice);
      $bld = ($ch[0] == 1) ? $sel1 : $sel2;
      $build = ($ch[0] == 1) ? $build1 : $build2;
      $equ = $ch[1];
      $build[$equ]=$_GET{'set'};
      setcookie( $WAKFUBLD.$bld, implode ( "," , $build ), strtotime( '+100 days' ) );
      if ($ch[0] == 1) {
          $build1 = $build;    
      } else {
          $build2 = $build;
      }
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
  
  function display_items($list, $max, $sel1, $sel2, $choice, $args) {  
      $count = count($list);
      print "<small>".$count." objets trouvés.</small><br/>";
      for ($i = 0; ($i < $max) && ($i < $count) ; $i++) {
        $item = $list[$i];	
        print "<a href=\"?s1=".$sel1."&s2=".$sel2."&ch=".$choice."&set=".$item->id."&".$args->get_suffix("")."\">";  
    		print '<img src="./images'.$item->image.'"  width=21 height=21/> ';
    		print $item->name;    
    		print "</a>";
        print "<span class='level'> (lvl&nbsp;".$item->level.")</span>\n";
        print "<br/>";
      }
      if ($count > $max) {
        $more = $count - $max;
        print "<small>".$more." objets supplémentaires...</small>";
      }    
  }
  
  function display_equip_btn($side, $buildNb, $ch, $equ, $args, $config, $build) {
      print "<td class=\"buildbox ";
      if ($ch == $side."_".$equ) {
          print "blinkbox";
      }
      print "\">";
      print "<a href=\"?s1=".$buildNb."&ch=".$side."_".$equ."&";
      print $args->get_suffix("type")."&type1=".$config[$equ][0]."&type2=".$config[$equ][1]."&type3=".$config[$equ][2]."\" >";
      if ($build[$equ] != null) {
          $item = new Item($build[$equ]);
          $src = $item->image;
          $big = str_replace("/21/", "/42/",$src);
          if (file_exists("./images".$big)) {
              $src = $big;
          }
					print "<img src=\"./images".$src."\" width=42 height=42 class=\"wshadowed\" >";
      } else { 
          print "<img src=\"./images/build/".$config[$equ][3]."\" width=42 height=42 class=\"wshadowed\" >";
      }
      print "</a></td>";
  }
  
  function display_build_picker($side, $buildNb, $build2, $choice, $args, $config, $build) {
  ?>
      <table border=1 cellspacing=0 cellpadding=0>
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
            display_equip_btn($side, $buildNb, $choice, 0, $args, $config, $build); // casque
            display_equip_btn($side, $buildNb, $choice, 1, $args, $config, $build); // epaulette
            print "</tr><tr>";
            display_equip_btn($side, $buildNb, $choice, 2, $args, $config, $build); // amulette
            display_equip_btn($side, $buildNb, $choice, 3, $args, $config, $build); // anneau_g
            display_equip_btn($side, $buildNb, $choice, 4, $args, $config, $build); // anneau_d
            print "</tr><tr>";
            display_equip_btn($side, $buildNb, $choice, 5, $args, $config, $build); // cape
            display_equip_btn($side, $buildNb, $choice, 6, $args, $config, $build); // main_g
            display_equip_btn($side, $buildNb, $choice, 7, $args, $config, $build); // main_d
            print "</tr><tr>";
            display_equip_btn($side, $buildNb, $choice, 8, $args, $config, $build);  // plastron
            display_equip_btn($side, $buildNb, $choice, 9, $args, $config, $build);  // ceinture
            display_equip_btn($side, $buildNb, $choice, 10, $args, $config, $build); // bottes
            print "</tr>";

          ?>
        </tbody>
      </table>  
       <?php
       print_r($build);
  }
  
  
  
  
?><!doctype html>
<html class="no-js" lang="en">
<?php include( 'page/head.php' ); ?>
<body >

<?php //include( 'page/page_header.php' ); ?>

    <div class="row">
      <div class="large-4 medium-6 columns right" >
        <fieldset>
          <legend>Filtre</legend>
          <?php display_adv_search_form($args, false); ?>
        </fieldset>
        
         <?php
              if ($args->type3 != null) {
                  print "<div style=\"height:500px;overflow-y: scroll;\">";
                  display_items($list, $limit, $sel1, $sel2, $choice, $args);
                  print "</div>";
              } 
              print $sql;
         ?>    
        
      </div>
      <div class="large-4 medium-6 columns" style="float:right;" >
            <?php
                display_build_picker(1, $sel1, $sel2, $choice, $args, $config, $build1); 

            ?>
      <!-- write build caracteristics -->
      </div>
      
      
    </div>
    <!-- ?php include( 'page/footer_script.php' ); ?-->
    <script src="js/vendor/jquery.js"></script>
    <script>
    setInterval(function(){
        $(".blinkbox").toggleClass("blinklight");
     },1000)
    </script>
</body>
</html>