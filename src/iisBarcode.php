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

//require GLPI_ROOT.'/plugins/iistools/vendor/autoload.php';

// use Endroid\QrCode\QrCode;
// use Endroid\QrCode\Writer\PngWriter;

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


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
      //$plugin_dir = Plugin::getWebDir('iistools');
      $plugin_php_dir = Plugin::getPhpDir('iistools');

      foreach ($ids as $key) {
          $computer = [];
          $item->getFromDB($key);
          $computer['id'] = $item->getField('id');
          $computer['name'] = $item->getField('name');
          $link = Toolbox::formatOutputWebLink($CFG_GLPI["url_base"].Toolbox::getItemTypeFormURL(Computer::class, false)."?id=".$item->getField('id'));
          $computer['link']=$link;
          $computers[] = $computer;
      }
      
      
      switch ($ma->getAction()) {
            /* not necessary
            case 'GenerateCSV' :
              $type=Computer::class;
              $csvFile = $_SESSION['glpiID'].'_'.$type.'.csv';
              $testFile=self::$docsPath.$csvFile;

              $file = fopen($testFile, 'w');
              fwrite($file, "\xEF\xBB\xBF");
              fputcsv($file, 
                      array(__('ID', 'iistools'),
                            __('Name', 'iistools'),
                            __('Link', 'iistools')),
                      ";");
              foreach ($computers as $computer) {
                fputcsv($file, array( $computer['id'],  $computer['name'], $computer['link']), ";");
              }
              fclose($file);

              if (file_exists($testFile)) {
                $msg = "<a href='".Plugin::getWebDir('iistools').'/front/send.php?file='.urlencode($csvFile)
                ."'>".__('CSV Download', 'iistools')."</a>";
                Session::addMessageAfterRedirect($msg);
              }else{
                die("A fájl nem létezik -> ".$testFile);
              }

              $ma->itemDone($item->getType(), 0, MassiveAction::ACTION_OK);
              return;
            case 'GenerateXLS' :
              $type=Computer::class;
              $xlsxFile = $_SESSION['glpiID'].'_'.$type.'.xlsx';
              $testFile=self::$docsPath.$xlsxFile;

              $spreadsheet = new Spreadsheet();
              $sheet = $spreadsheet->getActiveSheet();  

              $sheet->setCellValue('A1', __('ID', 'iistools'));
              $sheet->setCellValue('B1', __('Name', 'iistools'));
              $sheet->setCellValue('C1', __('QR Code', 'iistools'));

              $row = 2;
              foreach($computers as $computer){
                $sheet->setCellValue('A' . $row, $computer['id']); // ID
                $sheet->setCellValue('B' . $row, $computer['name']); // Name

                $QRCodeFile=self::create($computer['id'], $computer['link']);
                
                $qrCode = new Drawing();
                $qrCode->setName('QR Code');
                $qrCode->setDescription('QR Code for ' . $computer['link']);
                $qrCode->setPath($QRCodeFile); 
                $qrCode->setHeight(100); 
                $qrCode->setCoordinates('C' . $row); 

                $qrCode->setWorksheet($sheet);
                $row++;
              }

              $writer = new Xlsx($spreadsheet);
              $writer->save($testFile);
              self::deleteTemporaryPNGs();
              
              if (file_exists($testFile)) {
                $msg = "<a href='".Plugin::getWebDir('iistools').'/front/send.php?file='.urlencode($xlsxFile)
                ."'>".__('XLSX Download', 'iistools')."</a>";
                Session::addMessageAfterRedirect($msg);
              }else{
                die("A fájl nem létezik -> ".$testFile);
              }

              $ma->itemDone($item->getType(), 0, MassiveAction::ACTION_OK);
              return;    
            */
            case 'Generate' :
              $width = 60;
              $height = 40;

              $pdf = new TCPDF('L', 'mm', array($width, $height), true, 'UTF-8', false);
              $pdf->setPrintHeader(false);
              $pdf->setPrintFooter(false);
              $pdf->SetMargins(2, 2, 2); // 2mm-es biztonsági margó
              $pdf->SetAutoPageBreak(false);

              // $pdf = new TCPDF('portrait', 'mm', 'A4', true, 'UTF-8', false);
              // $pdf->SetCreator(PDF_CREATOR);
              // $pdf->SetAuthor('IIS');
              // $pdf->SetTitle(__('Computers QR code', 'iistools'));
              // $pdf->SetSubject(__('IIS Computers catalog', 'iistools'));
              // $pdf->SetKeywords('IIS, Computers, QR');

              // // set default header data
              // //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
              // $pdf->setFooterData(array(0,64,0), array(0,64,128));

              // // set header and footer fonts
              // $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
              // $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

              // // set default monospaced font
              // $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

              // // set margins
              // //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
              // $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
              // $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
              // $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

              // // set auto page breaks
              // $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

              // // set image scale factor
              // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

              // $pdf->setFontSubsetting(true);
              // $pdf->SetFont('dejavusans', '', 10, '', true);
              // $pdf->AddPage();
              // $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

              // $x=0;
              // $y=0;
              // $ComputerIndex=0;
              foreach($computers as $computer){
                $pdf->AddPage();
                $link = $computer['link'];

                $style = array(
                    'border' => false,
                    'vpadding' => 0,
                    'hpadding' => 0,
                    'fgcolor' => array(0,0,0),
                    'bgcolor' => false
                );
                //tartalom, típus, x, y, szélesség, magasság, stílus, igazítás
                $pdf->write2DBarcode($link, 'QRCODE,H', 2, 2, 25, 25, $style, 'N');

                $pdf->SetFont('dejavusans', 'B', 8);
                $pdf->SetXY(28, 5);
                $pdf->Cell(0, 0, "ID: " . $computer['id'], 0, 1);
                
                $pdf->SetFont('dejavusans', '', 7);
                $pdf->SetX(28);
                // MultiCell, hogy ha hosszú a név, törje le több sorba
                $pdf->MultiCell($width - 30, 0, $computer['name'], 0, 'L');
                
                
                if (!empty($computer['serial'])) {
                    $pdf->SetX(28);
                    $pdf->Write(0, "S/N: " . $computer['serial']);
                }

                $logo_url = $plugin_php_dir . "/pics/iis_logo.png";
                if (file_exists($logo_url)) {
                    $logoWidth = 20; 
                    //$logoX = $width - $logoWidth - 2; // Szélesség - logó szélesség - margó
                    $logoY = $height - 12; // A címke aljától kicsit feljebb (igazítsd a logó magasságához)

                    // Paraméterek: fájl, x, y, szélesség, magasság, típus...
                    $pdf->Image($logo_url, 2, $logoY, $logoWidth, 0, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
                }else{
                  die("Itt kerestem a logót: " . $logo_url);
                }

              }

              /*---------------pdf end........... */

              $type=Computer::class;
              $pdfFile = $_SESSION['glpiID'].'_'.$type.'.pdf';
              $pdf->Output(self::$docsPath.$pdfFile, 'FI');
              //self::deleteTemporaryPNGs();
              //file_put_contents(self::$docsPath.$pdfFile, $file);
              $testFile=self::$docsPath.$pdfFile;

              if (file_exists($testFile)) {
                $msg = "<a href='".Plugin::getWebDir('iistools').'/front/send.php?file='.urlencode($pdfFile)
                ."' target='_blank'>".__('PDF Download', 'iistools')."</a>";
                Session::addMessageAfterRedirect($msg);
              }else{
                die("A fájl nem létezik -> ".$testFile);
              }

              $ma->itemDone($item->getType(), 0, MassiveAction::ACTION_OK);
              return;
        }
        return;
    }
/* deprecated
    static function deleteTemporaryPNGs() {
      $tempDir = self::$docsPath;
      $tempFiles = glob($tempDir . $_SESSION['glpiID'].'_*.png'); 
      foreach ($tempFiles as $file) {
          if (file_exists($file)) {
              unlink($file); // Fájl törlése
          }
      }
      return $file;
    }

  
    static function create($item_id, $p_code, $p_type= 'QRcode', $p_ext = 'png') {
        $file = self::$docsPath.$_SESSION['glpiID']."_".$item_id."_qrcode.png";  
        $qrCode = new QrCode($p_code); 
        $writer = new PngWriter();
        $writer->write($qrCode)->saveToFile($file);
      return $file;
    }
*/

    public static function getTypeName($nb = 0)
    {
        return __('IIS Tools QR', 'iistools', $nb);
    }
}