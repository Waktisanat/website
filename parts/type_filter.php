<?php

  /*****************************************************************
   *   Displays the "Item Type Filter"
   *****************************************************************/
    
  
  function print_type_buttons($item, $types, $size, $suffix = "", $t1 = null, $t2 = null) {
    $prefix = "type1=";
    if (!is_null($t1)) {
        $prefix = "type1=".$t1."&type2=";
    }
    if (!is_null($t2)) {
        $prefix = "type1=".$t1."&type2=".$t2."&type3=";
    }
    foreach($types as $typ) {
        $id = $item->getCategoryId($typ);
        print "<a class=\"button ".$size." secondary radius\" href=\"?".$prefix.$id.$suffix."\">";
        print_itemtype($item, $typ);
        print "</a> ";
    }
  }
  
  function print_selected_itemtype($item, $type, $size, $suffix = "", $t1 = null, $t2 = null) {
    $prefix = "";
    if (!is_null($t1)) {
        $prefix = "type1=".$t1;
    }
    if (!is_null($t2)) {
        $prefix = "type1=".$t1."&type2=".$t2;
    }
    print "<a class=\"button ".$size." secondary active radius\" href=\"?".$prefix.$suffix."\" >";
    print_itemtype($item, $type);
    print "</a>";
  }
  
  /******************************************************************
   *                              M A I N  
  ******************************************************************/
  function print_categoryFilter($type1, $type2, $type3, $item, $size, $suffix = "") {
    if (is_null($type1)) {
        print "<td nowrap >";
        $types = Item::get_all_type1();
        print_type_buttons($item, $types, $size, $suffix);
    } else if (is_null($type2)) {
        print "<td nowrap >";
        print_selected_itemtype($item, $item->type1, $size, $suffix);
        $types = Item::get_all_type2_for_type1($type1);
        if (count($types)>0) {
            print " &#10151; ";
            print_type_buttons($item, $types, $size, $suffix, $type1);
        }
    } else if (is_null($type3)) {
        print "<td nowrap width=\"90px\">";
        print_selected_itemtype($item, $item->type1, $size, $suffix);
        print " &#10151; ";
        print_selected_itemtype($item, $item->type2, $size, $suffix, $type1);
        $types = Item::get_all_type3_for_type2($type2);
        if (count($types)>0) {
            print " &#10151; ";
            print "</td><td>";
            print_type_buttons($item, $types, $size, $suffix, $type1, $type2);
        }
    } else {
        print "<td nowrap >";
        print_selected_itemtype($item, $item->type1, $size, $suffix);
        print " &#10151; ";
        print_selected_itemtype($item, $item->type2, $size, $suffix, $type1);
        print " &#10151; ";
        print_selected_itemtype($item, $item->type3, $size, $suffix, $type1, $type2);
    }
    print "</td>";
  }
 
  
?>