$(document).ready(function() {
    $(document).on('shown.bs.dropdown', function (e) {
        let exportUl = $('ul[aria-labelledby="dropdown-export"]');
        
        // Ha létezik a menü és még nem adtuk hozzá a gombunkat
        if (exportUl.length && !$('#iistools-custom-pdf').length) {
            
            // Megkeressük az ELSŐ létező gyári export linket (pl. a fekvő PDF-et)
            // Ez tartalmazza az összes ?item_type=Ticket&sort... paramétert!
            let originalLink = exportUl.find('a[href*="report.dynamic.php"]').first().attr('href');
            
            if (originalLink) {
                // Kicseréljük a gyári fájlt a miénkre, de megtartjuk az összes paramétert
                let customUrl = originalLink.replace('report.dynamic.php', '../plugins/iistools/front/export_pdf.form.php');

                let customExport = `
                    <li id="iistools-custom-pdf" class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="${customUrl}" target="_blank" style="background-color: #f4f7fb;">
                            <i class="fas fa-lg fa-file-pdf" style="color: #005a9c;"></i>
                            <strong>IISTOOLS Egyedi Export</strong>
                        </a>
                    </li>`;
                
                exportUl.append(customExport);
            }
        }
    });
});