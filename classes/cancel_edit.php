<?php

include_once('item.class.php');
include_once('recette.class.php');

$craft = $_GET['craft'];
$return = "";

$item = new Item( $_GET['itemID'] );
$myRecette = Recette::get_recettes_by_item_id($item);
$ingredients = $myRecette[0]->load_direct_ingredients();

$return .= '<a data-delete="'.$craft["Item"].'" class="red delete" title="Supprimer de ma liste de craft" style="position: absolute; right: -0.80rem; top: -0.35rem;"><i class="fa fa-times fa-2x"></i></a>';
$return .= '<legend>'.$craft["Quantite"].' x <img src="./images'.$item->image.'"  width="21" height="21"/> <a href="./item.php?id='.$item->id.'">'.$item->name.'</a></legend>';

if( count( $ingredients ) )
{
    $return .= '<ul class="no-bullet">';
    foreach ( $ingredients as $ingredient )
    {
        $return .= '<li><img src="./images'.$ingredient->item->image.'"  width="21" height="21"/> ';

        if( $craft["Stocks"][$ingredient->item->id] >= $ingredient->nombre * $craft["Quantite"] ) {
            $return .= '<strike>';
        }
        $return .= $craft["Stocks"][$ingredient->item->id].' / '.$ingredient->nombre * $craft["Quantite"].' <a href="./item.php?id='.$ingredient->item->id.'">'.$ingredient->item->name.'</a>';
        if( $craft["Stocks"][$ingredient->item->id] == $ingredient->nombre * $craft["Quantite"] ) {
            $return .= '</strike>';
        }
        $return .= '</li>';


    }
    $return .= '</ul>';
}

$return .= '<a href="#" class="green right displayEdit" data-item="'.$craft["Item"].'" title="Mettre à jour mes stocks"><i class="fa fa-pencil-square-o fa-2x"></i></a>';

echo $return;
