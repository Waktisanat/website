<div class="row">
    <div class="large-9 columns">
        <ul class="inline-list menu_short">
            <!--<li><a href="./" class="button radius tiny diamond <?php if( $currentMenu == "home" ){ echo "current"; } ?>">Accueil</a></li>-->
            <li><a href="./allitem.php" class="button radius tiny diamond <?php if( $currentMenu == "obj" ){ echo "current"; } ?>">Objets</a></li>
            <li><a href="./allpano.php" class="button radius tiny diamond <?php if( $currentMenu == "pano" ){ echo "current"; } ?>">Panoplies</a></li>
            <li><a href="./analyse.php" class="button radius tiny diamond <?php if( $currentMenu == "tarif" ){ echo "current"; } ?>">Tarifs HDV</a></li>
            <li><a href="./favoris.php" class="button radius tiny diamond <?php if( $currentMenu == "fav" ){ echo "current"; } ?>">Favoris</a></li>
            <li><a href="./searchadv.php" class="button radius tiny diamond <?php if( $currentMenu == "search" ){ echo "current"; } ?>">Recherche avancée</a></li>
        </ul>
    </div>
    <div class="large-3 columns">
        <form class="search_short" action="search.php" method=GET>
            <div class="small-11 columns" style="padding:0;">
                <input type="text" id=item name=item placeholder="Chercher...">
            </div>
            <div class="small-1 columns" style="padding:0;">
                <span class="postfix"><img src="./images/search_icon.gif" style="cursor:pointer" onclick="submit();"></span>
            </div>
        </form>
    </div>
</div>
    