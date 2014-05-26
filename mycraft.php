<?php
    include_once('classes/item.class.php');
    include_once('classes/recette.class.php');
    include_once('parts/display.php' );
?>

<!doctype html>
<html class="no-js" lang="fr">
<?php include( 'page/head.php' ); ?>
<body>
<?php include( 'page/page_header.php' ); ?>
    <div class="row">
        <div class="large-12 columns">
            <h1>Ma liste de crafts <small class="countCraft"></small></h1>
        </div>
    </div>

    <div class="row">
        <div class="informations"></div>

        <ul id="MyCraft" class="small-block-grid-1 medium-block-grid-3"></div>

        <script type="text/javascript">
            MyCraft();

            $(".countCraft").html( " ("+ CountCrafts() +")" );

            $(document.body).on("click", ".displayEdit", function( event ){
                event.preventDefault();
                emptyInformations();
                displayEdit( $(this).data("item") );
            });

            $(document.body).on("click", ".cancelEdit", function( event ){
                event.preventDefault();
                emptyInformations();
                cancelEdit( $(this).data("item") );
            });

            $(document.body).on("click", ".saveEdit", function( event ){
                event.preventDefault();
                emptyInformations();
                saveEdit( $(this).data("item") );
            });

            $(document.body).on("click", ".delete", function( event ){
                event.preventDefault();
                emptyInformations();
                deleteItem( $(this).data("item") );
                $(".countCraft").html( " ("+ CountCrafts() +")" );
            });
        </script>
	</div>
    
    <?php include( 'page/footer_script.php' ); ?>
</body>
</html>