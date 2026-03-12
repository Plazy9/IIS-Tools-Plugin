<?php

use Ticket;
use ITILFollowup;
use Glpi\Application\View\TemplateRenderer;


class PluginIistoolsTicketprint extends CommonGLPI {
    
static function getTable() {
        return "glpi_plugin_iistools_ticketitems_flags";
    }

   public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
    //print_r($item->get_item_to_display_tab);
      if ($item->getType() == 'Ticket') {
         return "Iis Nyomtatás";
      }

      return '';
   }

    static function postTicketItemsContent($item) {
        global $CFG_GLPI;

        $currentItem = $item['item'];
        //echo $currentItem::getType();
        if (in_array($currentItem::getType(), ['TicketTask','ITILFollowup', 'ITILSolution'])) {
            $checked="";
            // Plugin tábla lekérdezése a checkbox állapotához
            $checked = self::isMarked($currentItem->fields['id'], $currentItem::getType()) ? "checked" : "cccc";

            // Checkbox beszúrása a timeline blokk aljára

            $ajaxURL=$CFG_GLPI["root_doc"] . "/plugins/iistools/ajax/saveTaskItemPrintableFlag.php";
            echo "<span class='badge bg-white-overlay' style='color: rgba(43, 43, 43, 0.8)'>";
            echo "<input type='checkbox' 
                    id='iistools_ticketitems_".$currentItem->fields['id']."' 
                    class='iistools-print-flag' 
                    data-followup='".$currentItem->fields['id']."' 
                    onchange='saveTaskItemPrintableFlag(".$currentItem->fields['id'].", \"".$currentItem::getType()."\", this.checked ? 1 : 0, \"".$ajaxURL."\");'
                    $checked> ".
            
                 __('Printable Ticket item', 'iistools').
                 "</span>";
        }
    }

    public function addItemToPrint($ticketitem_id, $ticketitem_class) {
        global $DB;

        
        if(!self::isMarked($ticketitem_id, $ticketitem_class)){
            $DB->insert(
                        self::getTable(), 
                        [
                            'ticketItem_id'      => $ticketitem_id,
                            'ticketItem_class'  => $ticketitem_class
                        ]
                        );
        }

        return true;
    }

    public function removeItemToPrint($ticketitem_id, $ticketitem_class) {
        global $DB;
        if(self::isMarked($ticketitem_id, $ticketitem_class)){
            $DB->delete(
                        self::getTable(), 
                        [
                            'ticketItem_id'     => $ticketitem_id,
                            'ticketItem_class'  => $ticketitem_class
                        ]
                        );
        }

        return true;

    }

   static function isMarked($ticketitem_id, $ticketitem_class) {
        global $DB;
        
        $criteria = [
                        //'SELECT'    => ['COUNT' => 'id'],
                        'FROM'      => self::getTable(),
                        'WHERE'     => [
                            'ticketItem_id'       => $ticketitem_id,
                            'ticketItem_class'       => $ticketitem_class,
                        ]
                    ];
        $flag = $DB->request($criteria);
  
        return count($flag)!=0;
    }

   public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
       //$item->showListForItem($item);
      if ($item->getType() == 'Ticket') {
        
        self::showForTicket($item);
      }

      return true;
   }

    static function showForTicket(CommonGLPI $item) {
        global $CFG_GLPI, $DB;

        $ticket_id = $item->fields['id'];
        $url = $CFG_GLPI["root_doc"] . "/plugins/iistools/front/ticket_print_tab.php?ticket_id=" . $ticket_id;

        echo "<a href='$url' target='_blank' class='btn btn-primary'>
            Nyomtatható lista
        </a>";

        echo "<hr>";
        
        

    }
}