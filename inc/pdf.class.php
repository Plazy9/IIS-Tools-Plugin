<?php 

//use TCPDF;
//use Html;
use Plugin;
//se Search;
use Glpi\Toolbox\DataExport;

class PluginIistoolsPDF extends TCPDF {

    // Konstruktor az alapbeállításokhoz
    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
        
        // Alapértelmezett margók (bal, felső, jobb)
        $this->SetMargins(10, 30, 10);
        $this->SetAutoPageBreak(TRUE, 15);
    }

    public function outputData(array $data)
    {
        /** @var array $CFG_GLPI */
        global $CFG_GLPI, $PDF_TABLE;

        if (
            !isset($data['data'])
            || !isset($data['data']['totalcount'])
            || $data['data']['count'] <= 0
            || $data['search']['as_map'] != 0
        ) {
            return false;
        }

       // Define begin and end var for loop
       // Search case
        $begin_display = $data['data']['begin'];
        $end_display   = $data['data']['end'];

       // Compute number of columns to display
       // Add toview elements
        $nbcols          = count($data['data']['cols']);

       // Display List Header
        //echo Search::showHeader($data['display_type'], $end_display - $begin_display + 1, $nbcols);
        //$plugin_dir = Plugin::getWebDir('iistools');
        $plugin_dir = Plugin::getPhpDir('iistools');
        $logo_url =  $plugin_dir . "/pics/iis_logo.png";

        $main_color = "#2c3e50"; // Sötét antracit/kék
        $accent_color = "#3498db"; // GLPI kék
        
        
        $itemtype = $data['itemtype'];

        $item_instance = new $itemtype();
        $display_name = $item_instance->getTypeName(Session::getPluralNumber()); 

        $report_title = mb_strtoupper($display_name, 'UTF-8');

        $real_name = trim(($_SESSION['glpifirstname'] ?? '') . ' ' . ($_SESSION['glpirealname'] ?? ''));

        if (empty($real_name)) {
            $real_name = $_SESSION['glpiname'];
        }

        $PDF_TABLE = "
        <style>
            .header-table { width: 100%; border-bottom: 2px solid $accent_color; margin-bottom: 20px; }
            .title { font-size: 18pt; font-weight: bold; color: $main_color; text-align: right; }
            .subtitle { font-size: 10pt; color: #7f8c8d; text-align: right; }
            .logo-cell { vertical-align: middle; }
        </style>

        <table class=\"header-table\" cellpadding=\"5\">
            <tr>
                <td width=\"30%\" class=\"logo-cell\">
                    <img src=\"".$logo_url."\" style=\"height: 45px;\">
                </td>
                <td width=\"70%\" style=\"text-align: right;\">
                    <div class=\"title\">$report_title</div>
                    <div class=\"subtitle\">Generálva: ".date('Y-m-d H:i')." | Felhasználó: ".$real_name."</div>
                </td>
            </tr>
        </table>
        <br><br>";

        $PDF_TABLE .= "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" >";

       // New Line for Header Items Line
        $headers_line        = '';
        $headers_line_top    = '';

        $PDF_TABLE .= "<thead>";
        
        /*
        $PDF_TABLE .= "<tr>";
        $PDF_TABLE .= "<td colspan =\"".$nbcols."\">asdfsdaf</td>";
        $PDF_TABLE .= "</tr>";
        */
        $style = "";
        $odd = false;
        if ($odd) {
            $style = " style=\"background-color:#DDDDDD;\" ";
        }
        $PDF_TABLE .= "<tr $style nobr=\"true\">";
        //$headers_line_top .= Search::showBeginHeader($data['display_type']);
        //$headers_line_top .= Search::showNewLine($data['display_type']);

        $header_num = 1;

       // Display column Headers for toview items
        $metanames = [];
        foreach ($data['data']['cols'] as $val) {
            $name = $val["name"];

           // prefix by group name (corresponding to optgroup in dropdown) if exists
            if (isset($val['groupname'])) {
                $groupname = $val['groupname'];
                if (is_array($groupname)) {
                    //since 9.2, getSearchOptions has been changed
                    $groupname = $groupname['name'];
                }
                $name  = "$groupname - $name";
            }

           // Not main itemtype add itemtype to display
            if ($data['itemtype'] != $val['itemtype']) {
                if (!isset($metanames[$val['itemtype']])) {
                    if ($metaitem = getItemForItemtype($val['itemtype'])) {
                        $metanames[$val['itemtype']] = $metaitem->getTypeName();
                    }
                }
                $name = sprintf(
                    __('%1$s - %2$s'),
                    $metanames[$val['itemtype']],
                    $val["name"]
                );
            }
            $options='';
            $PDF_TABLE .= "<th $options>";
            $PDF_TABLE .= htmlspecialchars($name,);
            $PDF_TABLE .= "</th>";

            // $headers_line .= Search::showHeaderItem(
            //     $data['display_type'],
            //     $name,
            //     $header_num,
            //     '',
            //     (!$val['meta']
            //                                     && ($data['search']['sort'] == $val['id'])),
            //     $data['search']['order']
            // );
        }

       // Add specific column Header

        if (isset($CFG_GLPI["union_search_type"][$data['itemtype']])) {
            $PDF_TABLE .= "<th $options>";
            $PDF_TABLE .= htmlspecialchars($name,);
            $PDF_TABLE .= "</th>";    
            // $headers_line .= Search::showHeaderItem(
            //     $data['display_type'],
            //     __('Item type'),
            //     $header_num
            // );
        }
       // End Line for column headers
        $PDF_TABLE .= '</tr>';
        //$headers_line .= Search::showEndLine($data['display_type'], true);

        //$headers_line_top    .= $headers_line;
        $PDF_TABLE .= "</thead>";
        //$headers_line_top    .= Search::showEndHeader($data['display_type']);

        //echo $headers_line_top;

       // Num of the row (1=header_line)
        $row_num = 1;

        $typenames = [];
       // Display Loop
        foreach ($data['data']['rows'] as $row) {
           // Column num
            $item_num = 1;
            $row_num++;
           // New line
            $style = "";
            $odd = $row_num % 2;
            if ($odd) {
                $style = " style=\"background-color:#DDDDDD;\" ";
            }
            $PDF_TABLE .= "<tr $style nobr=\"true\">";
            // echo Search::showNewLine(
            //     $data['display_type'],
            //     ($row_num % 2),
            //     $data['search']['is_deleted']
            // );

           // Print other toview items
            foreach ($data['data']['cols'] as $col) {
                $colkey = "{$col['itemtype']}_{$col['id']}";
                if (!$col['meta']) {
                    //$value = "dasdsda";
                    $value = $row[$colkey]['displayname'];
                    $extraparam = Search::displayConfigItem(
                                        $data['itemtype'],
                                        $col['id'],
                                        $row
                                    );
                    $value = DataExport::normalizeValueForTextExport($value);
                    $value = htmlspecialchars($value);
                    $value = preg_replace('/#LBBR#/', '<br>', $value);
                    $value = preg_replace('/#LBHR#/', '<hr>', $value);
                    $PDF_TABLE .= "<td $extraparam valign='top'>";
                    $PDF_TABLE .= $value;
                    $PDF_TABLE .= "</td>";
                    // echo Search::showItem(
                    //     $data['display_type'],
                    //     $row[$colkey]['displayname'],
                    //     $item_num,
                    //     $row_num,
                    //     Search::displayConfigItem(
                    //         $data['itemtype'],
                    //         $col['id'],
                    //         $row
                    //     )
                    // );
                } else { // META case
                    $value = $row[$colkey]['displayname'];

                    $value = DataExport::normalizeValueForTextExport($value);
                    $value = htmlspecialchars($value);
                    $value = preg_replace('/#LBBR#/', '<br>', $value);
                    $value = preg_replace('/#LBHR#/', '<hr>', $value);
                    $PDF_TABLE .= "<td $extraparam valign='top'>";
                    $PDF_TABLE .= $value;
                    $PDF_TABLE .= "</td>";
                    // echo Search::showItem(
                    //     $data['display_type'],
                    //     $row[$colkey]['displayname'],
                    //     $item_num,
                    //     $row_num
                    // );
                }
            }

            if (isset($CFG_GLPI["union_search_type"][$data['itemtype']])) {
                if (!isset($typenames[$row["TYPE"]])) {
                    if ($itemtmp = getItemForItemtype($row["TYPE"])) {
                        $typenames[$row["TYPE"]] = $itemtmp->getTypeName();
                    }
                }
                $value = $typenames[$row["TYPE"]];
                $value = DataExport::normalizeValueForTextExport($value);
                $value = htmlspecialchars($value);
                $value = preg_replace('/#LBBR#/', '<br>', $value);
                $value = preg_replace('/#LBHR#/', '<hr>', $value);
                $PDF_TABLE .= "<td $extraparam valign='top'>";
                $PDF_TABLE .= $value;
                $PDF_TABLE .= "</td>";
                // echo Search::showItem(
                //     $data['display_type'],
                //     $typenames[$row["TYPE"]],
                //     $item_num,
                //     $row_num
                // );
            }
           // End Line
            $PDF_TABLE .= '</tr>';
            //echo Search::showEndLine($data['display_type']);
        }

       // Create title
        $title = '';
        if (
            ($data['display_type'] == Search::PDF_OUTPUT_LANDSCAPE)
            || ($data['display_type'] == Search::PDF_OUTPUT_PORTRAIT)
        ) {
            $title = Search::computeTitle($data);
        }

       // Display footer (close table)
        //echo Search::showFooter($data['display_type'], $title, $data['data']['count']);
        $font       = 'helvetica';
        $fontsize   = 8;
        if (isset($_SESSION['glpipdffont']) && $_SESSION['glpipdffont']) {
            $font       = $_SESSION['glpipdffont'];
        }
        $type = $data['display_type'];
        $count = $data['data']['count'];
        $pdf = new GLPIPDF(
            [
                'font_size'     => $fontsize,
                'font'          => $font,
                'orientation'   => $type == Search::PDF_OUTPUT_LANDSCAPE ? 'L' : 'P',
                'margin_top'    => 5,
            ],
            $count,
            $title,
        );

        $PDF_TABLE .= '</table>';
        $pdf->writeHTML($PDF_TABLE, true, false, true);
        $pdf->Output('iis_export_glpi.pdf', 'I');
    }



}