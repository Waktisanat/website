<?php 
  include_once('classes/panoplie.class.php'); 
  include_once('parts/pagination.php' ); 
  include_once('parts/display.php' ); 

    $currentMenu = "pano";

	$page = isset($_GET{'page'} ) ? $_GET{'page'} : 0 ;
	
  $list = Panoplie::get_all_panoplies_by_level();
  $last = count($list);
	$pageCount = $list[$last-1]->level;
  $limit = 30;
  
  function getSubList($list, $level, $limit) {
      $ret = array();
      foreach($list as $pano) {
          if (($pano->level >= $level) and ($pano->level < $level + $limit)) {
              $pano->get_composants();
              $ret[] = $pano;
          }
      }
      return $ret;
  }
  
  function print_pano_pack( $page, $list, $limit) {
    echo '<h3>Liste des panoplies :</h3>';

    
	  $sublist = getSubList($list, $page, $limit);
    $break = ceil(count($sublist) / 3);
    $cnt = 0;
    
		print "<div class=\"large-4 medium-6 columns\">\n";    
    foreach ($sublist as $pano)
    {
        print "<div>";
        print "<img src=\"./images".$pano->image."\" class='showInfo' data-dropdown=\"hover".$cnt."\" data-options='is_hover:true'/>";
        print "<a href=\"./item.php?id=".$pano->id."\"> ";
        print $pano->name."</a> <span class='level'>(lvl ".$pano->level.")</span>\n";
        print "<div id=\"hover".$cnt."\" data-dropdown-content class='f-dropdown content infobulleLarge' >";
        print("<span class='infobulle'>");
        foreach($pano->composants as $ingr) {
            print "<a href=\"./item.php?id=".$ingr->id."\">";
            print "<img src=\"./images".$ingr->image."\" >";
            print $ingr->name."</a>\n";
            print "<span class='level'>(lvl ".$ingr->level.")</span>\n";
            if ($ingr->craftable) {
                print "<img class='craftable' >";
            }
            print "<br/>";
        }
        print "</span></div>";
        print "</div>\n";
        $cnt++ ;
        if (($cnt % $break) == 0) {
            print "</div>\n<div class=\"large-4 medium-6 columns\">";
        } 
    }
		echo '</div>';
  }

?>

<!doctype html>
<html class="no-js" lang="fr">
<?php include( 'page/head.php' ); ?>
<body>
<?php include( 'page/page_header.php' ); ?>
    <div class="row">
        <div class="large-12 columns">
            <h1>Toutes les panoplies (<?php echo count($list); ?>)</h1>
        </div>
    </div>

    <div class="row">

        <?php 
        
         print_pano_pack( $page, $list, $limit); ?>
         
        <div class="large-12 medium-12 columns">
        <?php 
         pagination( $page, $pageCount, "", $limit ); ?>
        </div>
	</div>
    
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>