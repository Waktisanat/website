<?php
  include_once('classes/item.class.php' );

  function print_all_caracteristics($caracs, $filter) {
    $cnt = 0;
    foreach($filter->caracs as $select) {
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
        print "<select name=\"carac".$cnt."\" class=\"small\" onChange=\"document.getElementById('advsearch').submit()\" />";
        print "<option ></option>";
        print $options;    
        print "</select>";    
        print "</div>";    
        $cnt++;
    }
    print "<input type=hidden name=\"carnum\" value=\"".$cnt."\" >";
    print "<select name=\"carac".$cnt."\" class=\"small\" onChange=\"document.getElementById('advsearch').submit()\" style=\"margin-left:21px\" />";
    print "<option ></option>";
    foreach($caracs as $car) {
        print "<option value=\"".$car->id."\" >".$car->name."</option>";
    }
    print "</select>";
  }

  function display_adv_search_form($filter, $disp_type_filter_btn = true) {
    $dummy = new Item();
    $caracs = $dummy->get_main_caracteristics();
?>
    <div class="row">
        <form id="advsearch" name="advsearch" class="custom">
        <div style="float:left;width:100%;">
            <?php if ($disp_type_filter_btn == true) { ?>
            <table style="border:0;width:100%;" cellspacing=0 cellpadding=0 ><tr>
                <td align="right" width="21%" style="padding:0;">Type :</td>
                <?php print_categoryFilter($filter->type1, $filter->type2, $filter->type3, $dummy, "nano", $filter->get_suffix("type")); ?>
            </tr></table>
            <?php
            }
             
            $v1 = (!is_null($filter->type1)) ? " value=\"".$filter->type1."\" " : "" ; 
            $v2 = (!is_null($filter->type2)) ? " value=\"".$filter->type2."\" " : "" ; 
            $v3 = (!is_null($filter->type3)) ? " value=\"".$filter->type3."\" " : "" ; 
            print "<input type=hidden name=type1 ".$v1." >"; 
            print "<input type=hidden name=type2 ".$v2." >"; 
            print "<input type=hidden name=type3 ".$v3." >"; 
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
                <?php if (!is_null($filter->min)) print " value='".$filter->min."' "; ?> 
                onChange="document.getElementById('advsearch').submit()" /></td> 
              <td style="padding-right:0;">
                <input type=text style="width:100%" class="small" name=max placeholder="max" 
                <?php if (!is_null($filter->max)) print " value='".$filter->max."' "; ?> 
                onChange="document.getElementById('advsearch').submit()" /></td>
            </tr></table>
          </div>
          <div  style="padding-left:4px;">
            <input type=text style="width:100%;margin:4px 0;" class="small" name=key 
                <?php if (!is_null($filter->key)) print " value='".$filter->key."' "; ?> 
                onChange="document.getElementById('advsearch').submit()" />
            <select name="rarity" style="width:100%;margin:4px 0;" class="small" onChange="document.getElementById('advsearch').submit()" />
                  <option ></option>
                  <?php 
                    $rars = Item::get_rareties();
                    for ($i = 0; $i < count($rars); $i++) {
                        print "<option value='".$i."' ";
                        if (($filter->rarity != "") && ($filter->rarity == $i)) {
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
                <?php if (!is_null($filter->craft)) print " CHECKED "; ?> 
                onChange="document.getElementById('advsearch').submit()" /></label>
            <label style="margin:0;">Drop&nbsp;:&nbsp;<input type=checkbox name=drop value=X  
                <?php if (!is_null($filter->drop)) print " CHECKED "; ?> 
                onChange="document.getElementById('advsearch').submit()" /></label>
            <label style="margin:0;">Récolte&nbsp;:&nbsp;<input type=checkbox name=recolte value=X  
                <?php if (!is_null($filter->recolte)) print " CHECKED "; ?> 
                onChange="document.getElementById('advsearch').submit()" /></label>
        </div>                     


        <div class="left">
            <label class="left">Caractéristiques :
            <?php
                if (count($filter->caracs) > 0) {
                    $c1 = ($filter->carand == 1) ? " CHECKED " : "";
                    $c2 = ($filter->carand == 1) ? "" : " CHECKED ";
                    print " <i>(et:<input type=radio name=carand value=1 ".$c1;
                    print " style=\"vertical-align: middle;\" onChange=\"document.getElementById('advsearch').submit()\" >\n";
                    print ", ou:<input type=radio name=carand value=0 ".$c2;
                    print " style=\"vertical-align: middle;\" onChange=\"document.getElementById('advsearch').submit()\" >)</i>\n";
                } 
            ?></label>
            <div class="right">
            <?php
              print_all_caracteristics($caracs,$filter);
            ?>
            </div>
        </div>
        </form>
    </div>  <!-- /row --> 

<?php
  }
?>