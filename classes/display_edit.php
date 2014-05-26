<?php

include_once('item.class.php');
include_once('recette.class.php');

$craft = $_GET['craft'];
$return = "";

$item = new Item( $_GET['itemID'] );
$myRecette = Recette::get_recettes_by_item_id($item);
$ingredients = $myRecette[0]->load_direct_ingredients();

$return .= '<legend><img src="./images'.$item->image.'"  width="21" height="21"/> '.$item->name.'</legend>';

$return .= '<div class="row">';
$return .= '<div class="small-8 columns">';
$return .= '<label for="right-label" class="right inline">Quantité</label>';
$return .= '</div>';
$return .= '<div class="small-4 columns">';
$return .= '<input type="number" id="qte-'.$item->id.'" min="0" value="'.$craft['Quantite'].'">';
$return .= '</div>';
$return .= '</div>';

if( count( $ingredients ) )
{
    foreach ( $ingredients as $ingredient )
    {
        $return .= '<div class="row collapse">';
        $return .= '<div class="small-3 columns">';
        $return .= '<input type="number" class="stock" placeholder="Stock" data-item="'.$ingredient->item->id.'" id="stock-'.$ingredient->item->id.'-'.$item->id.'" value="'.$craft['Stocks'][$ingredient->item->id].'" min="0">';
        $return .= '</div>';
        $return .= '<div class="small-9 columns">';
        $return .= '<span class="postfix">/ '.$ingredient->nombre * $craft['Quantite'].' <img src="./images'.$ingredient->item->image.'" width="21" height="21" /> '.$ingredient->item->name.'</span>';
        $return .= '</div>';
        $return .= '</div>';
    }
}

$return .= '<a data-item="'.$item->id.'" class="green right saveEdit" title="Valider"><i class="fa fa-check fa-2x"></i></a>';
$return .= '<a data-item="'.$item->id.'" class="red left cancelEdit" title="Annuler"><i class="fa fa-times fa-2x"></i></a>';

echo $return;
