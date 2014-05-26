var selected_index = -1; //Index of the selected list item
var Crafts = localStorage.getItem("Crafts");//Retrieve the stored data
Crafts = JSON.parse(Crafts); //Converts string to object
if(Crafts == null) //If there is no data, initialize an empty array
    Crafts = [];

function CountCrafts() {
    return Crafts.length;
}

function AjoutCraft( item, qte, stocks ){
    if( CheckItemAlreadyStorage( item ) == false )
    {
        var craft = JSON.stringify({
            Item : item,
            Quantite : qte,
            Stocks : stocks
        });
        Crafts.push(craft);
        localStorage.setItem("Crafts", JSON.stringify(Crafts));
        return true;
    }
}

function CheckItemAlreadyStorage( item ){
    for(var i in Crafts){
        var obj = JSON.parse(Crafts[i]);

        if( obj.Item == item )
        {
            return true;
        }
    }
    return false;
}

function MyCraft(){
    $.ajax({
        type: "GET",
        url: "classes/display.php",
        data: {
            'crafts':Crafts
        },
        dataType: "html",
        success: function(response){
            $("#MyCraft").html(response);
        }
    });
}

function displayEdit( itemID ){
    for(var i in Crafts){
        var cli = JSON.parse(Crafts[i]);

        if( cli.Item == itemID )
        {
            selected_index = i;
        }
    }

    $.ajax({
        type: "GET",
        url: "classes/display_edit.php",
        data: {
            'itemID':itemID,
            'craft': JSON.parse(Crafts[selected_index])
        },
        dataType: "html",
        success: function(response){
            $("fieldset."+itemID).html(response);
        }
    });
}

function cancelEdit( itemID ){
    for(var i in Crafts){
        var cli = JSON.parse(Crafts[i]);

        if( cli.Item == itemID )
        {
            selected_index = i;
        }
    }

    $.ajax({
        type: "GET",
        url: "classes/cancel_edit.php",
        data: {
            'itemID':itemID,
            'craft': JSON.parse(Crafts[selected_index])
        },
        dataType: "html",
        success: function(response){
            $("fieldset."+itemID).html(response);
        }
    });
}

function deleteItem( itemID ){
    for(var i in Crafts){
        var cli = JSON.parse(Crafts[i]);

        if( cli.Item == itemID )
        {
            selected_index = i;
        }
    }

    Crafts.splice(selected_index, 1);
    localStorage.setItem("Crafts", JSON.stringify(Crafts));
    MyCraft();

    $(".informations").html( '<div data-alert class="alert-box">L\'objet a été supprimé de votre liste de craft.</div>' );
}

function saveEdit( itemID ){
    for(var i in Crafts){
        var cli = JSON.parse(Crafts[i]);

        if( cli.Item == itemID )
        {
            selected_index = i;
        }
    }

    var stocks = {};
    $("fieldset."+ itemID +" .stock").each( function() {
        stocks[$(this).data('item')] = $(this).val();
    });

    Crafts[selected_index] = JSON.stringify({
        Item : itemID,
        Quantite : $("#qte-"+itemID).val(),
        Stocks : stocks
    });//Alter the selected item on the table
    localStorage.setItem("Crafts", JSON.stringify(Crafts));
    cancelEdit( itemID );

    $(".informations").html('<div data-alert class="alert-box">Les données ont été sauvegardées.</div>');
}

function emptyInformations(){
    $(".informations").html("");
}