<?php

include ("../../../inc/includes.php");
global $CFG_GLPI;
use Html;
use PluginIistoolsTicketprint;

Session::checkLoginUser();
echo Html::includeHeader();
$ticket_id = $_GET['ticket_id'];

$ticket = new Ticket();
$ticket->getFromDBwithData($ticket_id);

echo "<html>";
echo "<head>";

echo "<title>Ticket nyomtatás</title>";


echo "<style>

body{
    font-family: Arial;
    margin:40px;
}

.print-button{
    position:fixed;
    top:20px;
    right:20px;
}

/* Nyomtatási optimalizálás */
@media print {
    @page {
        size: A4;
        margin: 1.5cm; 
    }
    body { background: white; margin: 0; padding: 0; }
    .itil-timeline-content-card { box-shadow: none; border: 1px solid #ccc; }
    .itil-timeline-icon { -webkit-print-color-adjust: exact; }
    .print-button{display:none;}
    .itil-timeline-item {
        page-break-inside: avoid;
        break-inside: avoid;
        margin-bottom: 15px; /* Kicsit kisebb hely a lapon */
    }

    /* A fejléc maradjon mindig felül */
    .print-header {
        page-break-after: avoid;
    }

    /* Elrejtünk minden felesleges GLPI elemet (gombok, menük) */
    .no-print, #header, #footer, .primary-footer, .breadcrumb {
        display: none !important;
    }
}

/* Fejléc stílusa */
.print-header {
    margin-bottom: 30px;
    padding-top: 10px;
}

.header-top-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding-bottom: 5px;
}

.header-title {
    text-align: right;
}

.header-title h1 {
    margin: 0;
    font-size: 24px;
    color: #333;
    letter-spacing: 1px;
}

.ticket-id {
    font-size: 14px;
    color: #666;
    font-weight: bold;
}

.header-separator {
    height: 2px;
    background-color: #000;
    width: 100%;
}

/* Lábléc stílusa */
.print-footer {
    margin-top: 50px;
    font-family: sans-serif;
}

.footer-separator {
    height: 1px;
    background-color: #ccc;
    width: 100%;
    margin-bottom: 10px;
}

.footer-content {
    display: flex;
    justify-content: space-between;
    font-size: 10px;
    color: #777;
}

.footer-official {
    font-style: italic;
    text-transform: uppercase;
}

/* Timeline finomítás a nyomtatáshoz */
.itil-timeline-item {
    page-break-inside: avoid; /* Ne törje el a kártyát oldal közepén */
}


/* Fő jegy speciális stílusa */
.itil-timeline-item.main-ticket .itil-timeline-icon {
    background: #007bff;
    color: #fff;
    border-color: #0056b3;
}

.main-card {
    border-left: 4px solid #007bff !important; /* Kék hangsúly az oldalán */
    background: #fff;
}

.itil-timeline-title-area {
    padding: 10px 15px 5px 15px;
    background: #fdfdfd;
}

.itil-timeline-title-area h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #212529;
    font-weight: 700;
}

.badge-status {
    background: #e7f1ff;
    color: #007bff;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
    margin-right: 10px;
    vertical-align: middle;
}

.main-body {
    font-size: 1.05rem; /* A nyitó szöveg legyen kicsit olvashatóbb */
    border-top: 1px solid #f8f9fa;
}


.itil-timeline-item {
    display: flex;
    margin-bottom: 20px;
    position: relative;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Az idővonal függőleges vonala */
.itil-timeline-item::before {
    content: '';
    position: absolute;
    left: 17px;
    top: 30px;
    bottom: -25px;
    width: 2px;
    background: #e9ecef;
}

.itil-timeline-item:last-child::before {
    display: none;
}

.itil-timeline-icon {
    width: 35px;
    height: 35px;
    background: #fff;
    border: 2px solid #007bff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
    color: #007bff;
    font-size: 14px;
}

/* Különböző típusok színei */
.itil-timeline-item.itilsolution .itil-timeline-icon { border-color: #28a745; color: #28a745; }
.itil-timeline-item.tickettask .itil-timeline-icon { border-color: #ffc107; color: #ffc107; }

.itil-timeline-content-card {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    margin-left: 20px;
    flex-grow: 1;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.itil-timeline-header {
    background: #ffffff;
    padding: 8px 15px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.user-name {
    font-weight: 600;
    color: #495057;
}

.item-date {
    font-size: 0.85em;
    color: #6c757d;
}

.itil-timeline-body {
    padding: 15px;
    background: #fff;
    line-height: 1.5;
    color: #333;
}

.itil-timeline-footer {
    padding: 5px 15px;
    font-size: 11px;
    color: #999;
    border-top: 1px solid #f1f1f1;
    background: #fafafa;
}

</style>";
echo "</head>";
echo "<body>";

echo "<button class='print-button' onclick='window.print()'>Nyomtatás / PDF</button>";

// header

$plugin_dir = Plugin::getWebDir('iistools');
$logo_url = $plugin_dir . "/pics/iis_logo.png";

echo "<div class='print-header'>";
    echo "<div class='header-top-row'>";
        // GLPI Logó behúzása
        echo "<div class='header-logo'>";
            echo "<img src='".$logo_url."' style='max-height: 50px;'>";
        echo "</div>";
        
        // Cím és vonalkód helye (ha szeretnéd)
        echo "<div class='header-title'>";
            echo "<h1>HIBAJEGY ÖSSZESÍTŐ</h1>";
            echo "<span class='ticket-id'>#" . $ticket->fields['id'] . "</span>";
        echo "</div>";
    echo "</div>";
    echo "<div class='header-separator'></div>";
echo "</div>";
//ticket
$user = new User();

$creator_id = $ticket->fields['users_id_recipient'];
$creator_name = "Ismeretlen";
if ($creator_id > 0 && $user->getFromDB($creator_id)) {
    $creator_name = $user->getFriendlyName();
}

$open_date = Html::convDateTime($ticket->fields['date']);


echo "<div class='itil-timeline-item main-ticket'>";
    // Bal oldali ikon - a nyitásnak általában egy "csillag" vagy "jegy" ikon jár
    echo "<div class='itil-timeline-icon'><i class='fas fa-ticket-alt'></i></div>";

    echo "<div class='itil-timeline-content-card main-card'>";
        
        // Fejléc
        echo "<div class='itil-timeline-header'>";
            echo "<div>";
                echo "<span class='badge-status'>NYITÁS</span> ";
                echo "<span class='user-name'>$creator_name</span>";
            echo "</div>";
            echo "<span class='item-date'>$open_date</span>";
        echo "</div>";

        // Jegy címe (Title) - GLPI 10-ben ez kiemelt
        echo "<div class='itil-timeline-title-area'>";
            echo "<h3>" . Html::entity_decode_deep($ticket->fields['name']) . "</h3>";
        echo "</div>";

        // Jegy leírása (Content)
        echo "<div class='itil-timeline-body main-body'>";
            echo Html::entity_decode_deep($ticket->fields['content']);
        echo "</div>";

        // Módosítási infó, ha releváns
        if (!empty($ticket->fields['date_mod']) && $ticket->fields['date_mod'] != $ticket->fields['date']) {
            echo "<div class='itil-timeline-footer'>";
                echo "Utolsó rendszerszintű frissítés: " . Html::convDateTime($ticket->fields['date_mod']);
            echo "</div>";
        }

    echo "</div>";
echo "</div>";


$timeline = $ticket->getTimelineItems();

foreach($timeline as $item) {
    $ticketitem_id = $item['item']['id'];
    $ticketitem_class = $item['type'];

    if($item['type'] == 'Log') continue;
    if(!PluginIistoolsTicketprint::isMarked($ticketitem_id, $ticketitem_class)) continue;

    // Típus alapú ikon és osztály meghatározása (GLPI 10 stílus)
    $type_class = strtolower($item['type']);
    $icon = "fas fa-comment"; // Alapértelmezett
    
    if ($item['type'] == 'ITILSolution') $icon = "fas fa-check-circle";
    if ($item['type'] == 'TicketTask') $icon = "fas fa-tasks";

    // Felhasználó adatainak lekérése (biztonságosabb kezeléssel)
    $user_id = $item['item']['users_id'] ?? $item['item']['users_id_recipient'] ?? 0;
    $username = "Rendszer / Ismeretlen";
    if ($user_id > 0 && $user->getFromDB($user_id)) {
        $username = $user->getFriendlyName();
    }

    $date = Html::convDateTime($item['item']['date']);

    echo "<div class='itil-timeline-item $type_class'>";
        // Bal oldali ikon és vonal
        echo "<div class='itil-timeline-icon'><i class='$icon'></i></div>";
        
        echo "<div class='itil-timeline-content-card'>";
            // Fejléc: Név és Dátum
            echo "<div class='itil-timeline-header'>";
                echo "<span class='user-name'>$username</span>";
                echo "<span class='item-date'>$date</span>";
            echo "</div>";

            // Tartalom
            echo "<div class='itil-timeline-body'>";
                echo Html::entity_decode_deep($item['item']['content']);
            echo "</div>";

            // Ha van módosítási dátum
            if (!empty($item['item']['date_mod']) && $item['item']['date_mod'] != $item['item']['date']) {
                echo "<div class='itil-timeline-footer'>";
                    echo "Utoljára módosítva: " . Html::convDateTime($item['item']['date_mod']);
                echo "</div>";
            }
        echo "</div>";
    echo "</div>";
}

//footer
$print_date = date("Y.m.d. H:i:s");
$printing_user = $_SESSION["glpiname"] ?? "Rendszerfelhasználó";

echo "<div class='print-footer'>";
    echo "<div class='footer-separator'></div>";
    echo "<div class='footer-content'>";
        echo "<div class='footer-info'>";
            echo "Nyomtatva: " . $print_date . " | Generálta: " . $printing_user;
        echo "</div>";
        echo "<div class='footer-official'>";
            echo "Ez egy automatikusan generált GLPI bizonylat.";
        echo "</div>";
    echo "</div>";
echo "</div>";

echo "</body>";
echo "</html>";