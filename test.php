<?php 
  ini_set('display_errors','On');
  ini_set('display_startup_errors','On'); 
  print "<pre>";

require_once('classes/item.class.php'); 
require_once('classes/ingredient.class.php');
require_once('classes/recette.class.php');
require_once('classes/panoplie.class.php');
include_once("page/simphp-2.0.php");
   
  echo ini_get('display_errors');
    
  print "\n----------- Test1 ------------\n";  
  $item = new Item(12);
  print_r($item);   
  
  print "\n----------- Test2 ------------\n";  
  $item = new Item('Plastron Lunaire');
  print_r($item);
  
  print "\n----------- Test3 ------------\n";  
  $list = Item::get_all_items();
  print count($list)." Items in the list";
  print_r($list[0]);
  
  print "\n----------- Test4 ------------\n";  
  $item = new Item('Bec de Corbac');
  $list = Recette::get_recettes_including_item($item);
  print " -- présent dans les recettes : \n";
  foreach($list as $recette) {
      print("* ".$recette->item->name."\n");
  }
  print "\n----------- Test5 ------------\n";  
  $list[0]->load_direct_ingredients();
  foreach($list[0]->ingredients as $ingr) {
      print $ingr->nombre." x ".$ingr->item->name." (".$ingr->item->id.")\n";
  }       
  $list[0]->load_ingredient_prices();
  $list[0]->get_craft_price();
  print_r($list[0]);  
  
  print "\n----------- Test6 ------------\n";  
  $item = new Item('Sceau du Maitre Maroquinier');
  $list = Recette::get_recettes_including_item($item);
  $list[0]->load_recursive_ingredients();
  foreach($list[0]->ingredients as $ingr) {
      print $ingr->nombre." x ".$ingr->item->name." (".$ingr->item->id.")\n";
  }
  print "\n----------- Test6b ------------\n";  
  $item = new Item('Bottes Lardantes');
  $list = Recette::get_recettes_by_item_id($item);
  $list[0]->load_recursive_ingredients();
  foreach($list[0]->ingredients as $ingr) {
      print $ingr->nombre." x ".$ingr->item->name." (".$ingr->item->id.")\n";
  }
        
  print "\n----------- Test7 ------------\n";  
  $list = Panoplie::get_all_panoplies();
  $list[0]->get_composants();
  print_r($list[0]); 

  $item = new Item(688);
  print " /* ".$item->name."  */ \n[";
  $item->export_json("min");
  print_r($item);
  print $item->get_category_img_url("panoplies")."\n";

  print "</pre>";
  //include('hdvhistory.php');
  include('builds.php');
  
  print "fin";
  
  echo $visitor_count;  
?>