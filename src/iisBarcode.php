<?php
namespace GlpiPlugin\Iistools;

use CommonDBTM;
use MassiveAction;
use Html;
use Plugin;
use Computer;
use Session;
use TCPDF;
use Toolbox;

require GLPI_ROOT.'/plugins/iistools/vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;


class iisBarcode extends CommonDBTM {
    public static $rightname = 'plugin_iistoolsCars';
    public static $docsPath = GLPI_PLUGIN_DOC_DIR.'/iistools/';

    static function showMassiveActionsSubForm(MassiveAction $ma) {
      
      switch ($ma->getAction()) {
        case 'Generate':
          echo Html::submit(__('Create PDF', 'iistools'), ['value' => 'create']);
          return true;
          case 'GenerateCSV':
            echo Html::submit(__('Create CSV', 'iistools'), ['value' => 'create']);
            return true;
            case 'GenerateXLS':
              echo Html::submit(__('Create XLS', 'iistools'), ['value' => 'create']);
              return true;
      }
      return parent::showMassiveActionsSubForm($ma);
    }

    static function processMassiveActionsForOneItemtype(MassiveAction $ma, CommonDBTM $item, array $ids) {
      global $CFG_GLPI;
      $computers = [];

      foreach ($ids as $key) {
          $computer = [];
          $item->getFromDB($key);
          $computer['id'] = $item->getField('id');
          $computer['name'] = $item->getField('name');
          $computers[] = $computer;
      }
      
      
      switch ($ma->getAction()) {
            case 'GenerateCSV' :
              return;
            case 'GenerateXLS' :
              return;    
            case 'Generate' :
                
                  $pdf = new TCPDF('portrait', 'mm', 'A4', true, 'UTF-8', false);
                /*---------------pdf-------- */
                  $pdf->SetCreator(PDF_CREATOR);
                  $pdf->SetAuthor('IIS');
                  $pdf->SetTitle(__('Computers QR code', 'iistools'));
                  $pdf->SetSubject(__('IIS Computers catalog', 'iistools'));
                  $pdf->SetKeywords('IIS, Computers, QR');

                  // set default header data
                  //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
                  $pdf->setFooterData(array(0,64,0), array(0,64,128));

                  // set header and footer fonts
                  $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                  $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

                  // set default monospaced font
                  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

                  // set margins
                  //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                  $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

                  // set auto page breaks
                  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                  // set image scale factor
                  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

                  $pdf->setFontSubsetting(true);
                  $pdf->SetFont('dejavusans', '', 10, '', true);
                  $pdf->AddPage();
                  $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

                  $x=0;
                  $y=0;
                  $ComputerIndex=0;
                  foreach($computers as $computer){
                    $x=$ComputerIndex*60;
                    
                    $link = Toolbox::formatOutputWebLink($CFG_GLPI["url_base"].Toolbox::getItemTypeFormURL(Computer::class, false)."?id=".$computer['id']);
                    $QRCodeFile=self::create($link);
                    
                    $linkHTML = "<a href='".$link."'>";
                    $linkHTML .= $computer['name'];
                    if ($_SESSION["glpiis_ids_visible"] || empty($computer['name'])) {
                      $linkHTML .= " (".$computer["id"].")";
                    }
                    $linkHTML .= "</a>";

                    
                    $html ="<div style=\"text-align:center;\">".$computer['name']."";
                    //$html.="<br>";
                    //$html.="<img src=\"".$QRCodeFile."\" alt=\"".$computer['name']."\" width=\"150\" />";
                    $html.="</div>";
                    // Print text using writeHTMLCell()
                    $pdf->writeHTMLCell(60, 60, $x+PDF_MARGIN_LEFT, $y+PDF_MARGIN_TOP, $html, 1, 1, 0, true, 'center', false);
                    $pdf->Image($QRCodeFile, $x+PDF_MARGIN_LEFT+10, $y+PDF_MARGIN_TOP+15, 40, 40, 'PNG', $link, '', true, 150, '', false, false, 1, false, false, false);

                    if($ComputerIndex==2){
                      $ComputerIndex=0;
                      $y+=60;
                    }else{
                      $ComputerIndex++;
                    }
                  }

                /*---------------pdf end........... */

                $type=Computer::class;
                $pdfFile = $_SESSION['glpiID'].'_'.$type.'.pdf';
                $pdf->Output(self::$docsPath.$pdfFile, 'FI');
                
                //file_put_contents(self::$docsPath.$pdfFile, $file);
                $testFile=self::$docsPath.$pdfFile;

                if (file_exists($testFile)) {
                  $msg = "<a href='".Plugin::getWebDir('iistools').'/front/send.php?file='.urlencode($pdfFile)
                  ."'>".__('PDF Download', 'iistools')."</a>";
                  Session::addMessageAfterRedirect($msg);
                }else{
                  die("A fájl nem létezik -> ".$testFile);
                }

              $ma->itemDone($item->getType(), 0, MassiveAction::ACTION_OK);
              return;
        }
        return;
    }

    static function create($p_code, $p_type= 'QRcode', $p_ext = 'png') {
        $file = self::$docsPath.$_SESSION['glpiID']."_qrcode.png";  
        $qrCode = new QrCode($p_code); 
        $writer = new PngWriter();
        $writer->write($qrCode)->saveToFile($file);
      return $file;
    }

    public static function getTypeName($nb = 0)
    {
        return __('IIS Tools QR', 'iistools', $nb);
    }
}