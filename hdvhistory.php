<?php
ini_set('display_errors','On');
ini_set('display_startup_errors','On'); 

  require_once('classes/item.class.php'); 
  require_once('classes/recette.class.php'); 

  $id = isset($_GET["id"]) ? $_GET["id"] : 688;
  $item = new Item($id);
  //print_r($item);
?>
<doctype html>
<html class="no-js" lang="fr">
<?php include( 'page/head.php' ); ?>
<body>
<script type='text/javascript' src='js/jquery-2.1.0.min.js'></script>
<script type="text/javascript" src="charts/js/highstock.js" ></script>
<script type="text/javascript" src="charts/js/themes/skies.js" ></script>
<script type="text/javascript" src="charts/js/modules/exporting.js" ></script>
<script type="text/javascript">

$(function() {

		$('#container').highcharts('StockChart', {

			//RangeSelector correspond aux boutons de zoom qui se trouvent en haut à gauche du graphique
			rangeSelector : {
			   buttons: [
			   //Ici, nous définissons les boutons, et la taille de leur zoom
			   //type: On définie s’il faut compter en jour, mois ou année
			   //count: Le nombre de jours, mois, ou années à afficher
			   //text: Le texte à afficher dans le bouton
								{type: 'day',count: 7,text: '7j'},
								{type: 'month',count: 1,text: '1m'},
								{type: 'month',count: 6,text: '6m'},
								{type: 'year',count: 1,text: '1a'},
								{type: 'all',text: 'Tout'}],
			   //selected : 5 = Par défaut, nous sélectionnons le cinquième bouton "Tout".
			   //Pour compter les boutons, il faut partir de 0, et non de 1
			   selected : 4
			},
			legend: {   //La légende affiche, le nom de la courbe, ainsi sa couleur
				//enabled: false pour désactiver et true pour activer
				enabled: true
			},
			yAxis: {
						title: {
				//Ce texte s'affiche à la verticale sur le coté gauche du graphique.
				//Nous affichons ici le titre et l'unité de mesure récupérés dans l'URL
                    text: 'Prix en Kamas'
						}
			},
			tooltip: {
						shared: true,
				//Ajout d'une unité de mesure lors du survole d'un point du graphique
				//L'unité de mesure provient de l'URL
						valueSuffix: ' K'
			},	
			title : {
			//Titre du graphique
			//Le titre provient de l'URL
				text : '<?php print str_replace("'","\'",$item->name); ?>'
			},

			series: [{
                name: 'max',
                color: '#FF0000',
        				//Formatage de la date sous la forme: Année, Mois, Jour, 

                data: [
                    <?php $item->export_json('max'); ?>

                ],
            }, {
                name: 'min',
                color: '#00FF00',
        				//Formatage de la date sous la forme: Année, Mois, Jour, 

                data: [
                    <?php $item->export_json('min'); ?>   

                ],
            }
			]
		});

});



</script>
       <!-- ?php $item->export_json('min'); ? -->
    <div class="row">
        <div id="container" style="width:100%; height:400px;"></div>
    </div>
</body>
</html>

