<?php

include ("../../../inc/includes.php");
use Glpi\Toolbox\DataExport;

if (!isset($_GET['item_type']) || !is_string($_GET['item_type']) || !is_a($_GET['item_type'], CommonGLPI::class, true)) {
    return;
}

$itemtype = $_GET['item_type'];
if ($itemtype === 'AllAssets') {
    Session::checkCentralAccess();
} else {
    Session::checkValidSessionId();
    $item = new $itemtype();
    if (!$item->canView()) {
        Html::displayRightError();
    }
}

if (isset($_GET["display_type"])) {
    if ($_GET["display_type"] < 0) {
        $_GET["display_type"] = -$_GET["display_type"];
        $_GET["export_all"]   = 1;
    }
    switch ($itemtype) {
        case 'KnowbaseItem':
            echo "Ez innen nem nyomtatható! Keresd->IIS Magyarország-> Pali";
            break;

        case 'Stat':
            echo "Ez innen nem nyomtatható! Keresd->IIS Magyarország-> Pali";
            break;

        default:
           // 1. ELŐKÉSZÍTÉS: Paraméterek feldolgozása (szűrések, sorrend)
            $pdf = new PluginIistoolsPDF('L', 'mm', 'A4'); 

            $params = Search::manageParams($itemtype, $_GET);
            
            // 2. ADATOK: Csak a nyers adatokat kérjük le tömbben
            $data = Search::getDatas($itemtype, $params);
            
            $pdf->outputData($data);

            exit();
            
            // print_r($data['data']['cols']);
            // echo "<hr>";
            // print_r($data['data']['rows']);
            // 3. DIZÁJN: Saját osztály példányosítása
            if (!class_exists('PluginIistoolsPDF')) {
                include_once("../inc/pdf.class.php");
            }

            // 'L' = Landscape (Fekvő), mert a képeiden is úgy volt
            $pdf = new PluginIistoolsPDF('L', 'mm', 'A4'); 
            // $pdf->SetTitle("IISTOOLS Export - $itemtype");
            // $pdf->AddPage();

            //  FEJLÉC: Oszlopnevek és ID-k kinyerése
            $columnIds = [];
            $headerNames = [];
            if (isset($data['data']['cols']) && is_array($data['data']['cols'])) {
                foreach ($data['data']['cols'] as $col) {
                    $columnIds[]   = $col['id'];
                    $headerNames[] = $col['name'];
                }
            }

            // 2. Szélességek meghatározása (ha még nem tetted meg)
            $totalCols = count($headerNames);
            $colWidth  = ($totalCols > 0) ? floor(277 / $totalCols) : 40;
            $widths    = array_fill(0, $totalCols, $colWidth);

            // Fejléc megrajzolása (ebben van a szín/betűtípus a class-ban)
            $pdf->drawTableHeader($headerNames, $widths);

            // 3. ADATOK KIÍRÁSA a 'rows' használatával
            if (isset($data['data']['rows']) && is_array($data['data']['rows'])) {
                $fill = false;
                foreach ($data['data']['rows'] as $row) {
                    
                    // A GLPI 'rows' tömbjében az adatok általában a 'raw' vagy közvetlenül a 'cols' kulcs alatt vannak
                    // De a legbiztosabb, ha a row-ban lévő oszlopokon megyünk végig
                    
                    reset($columnIds); // Alaphelyzetbe állítjuk az oszlop-mutatót
                    foreach ($data['data']['cols'] as $col) {
                        $colkey = "{$col['itemtype']}_{$col['id']}";
                        // Érték kinyerése: a GLPI 10-ben ez gyakran így néz ki: $row[$colId]
                        $value = $row[$colkey]['displayname'];
                        $value = DataExport::normalizeValueForTextExport($value);
                        $value = htmlspecialchars($value);
                        $value = preg_replace('/#LBBR#/', '<br>', $value);
                        $value = preg_replace('/#LBHR#/', '<hr>', $value);

                        $isLast = ($index == count($columnIds) - 1);
                        
                        // Cell(szélesség, magasság, szöveg, keret, újsor, igazítás, kitöltés)
                        $pdf->Cell($widths[$index], 7, $pdf->str_cut($value, 45), 1, $isLast ? 1 : 0, 'L', $fill);
                    }
                    $fill = !$fill; // Zebra csík váltása
                }
            } else {
                $pdf->Cell(0, 10, "Nincs megjeleníthető adat a listában.", 0, 1, 'C');
            }

            // 6. KIMENET: PDF lezárása és küldése a böngészőnek
            $pdf->Output("IISTOOLS_{$itemtype}_Export.pdf", 'I');
            exit();
    }
}
