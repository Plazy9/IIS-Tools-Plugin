<?php 

global $CFG_GLPI;
echo "
function csiga(){
    alert('csiga');
}";

echo "function saveTaskItemPrintableFlag(ticketItem_id, ticketItem_class, value, url){
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            ticketItem_id: ticketItem_id,
            ticketItem_class: ticketItem_class,
            value: value
        },
        success: function(resp) {
            console.log('Mentés sikeres:', resp);
        },
        error: function(err) {
            console.error('Hiba a mentésnél', err);
        }
    });
}";
