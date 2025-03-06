<?php
require_once 'config.php';
require_once 'dbConnect.php';

if (!empty($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    $sqlQ = "SELECT t.transaction_id, t.payer_name, t.payer_email, t.payer_country, 
                    t.paid_amount, t.paid_amount_currency, t.payment_status, t.created, l.license_key 
             FROM transactions t 
             LEFT JOIN Licensz l ON t.order_id = l.order_id 
             WHERE t.order_id = ?";
    
    $stmt = $db->prepare($sqlQ);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($transaction_id, $payer_name, $payer_email, $payer_country, 
                           $paid_amount, $paid_amount_currency, $payment_status, $created, $license_key);
        $stmt->fetch();

        // DOCX XML tartalom generálása
        $xmlContent = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
        <w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
            <w:body>
                <w:p><w:r><w:t>Fizetési Nyugta</w:t></w:r></w:p>
                <w:p><w:r><w:t>Tranzakciós azonosító: ' . $transaction_id . '</w:t></w:r></w:p>
                <w:p><w:r><w:t>Vásárló neve: ' . $payer_name . '</w:t></w:r></w:p>
                <w:p><w:r><w:t>Email: ' . $payer_email . '</w:t></w:r></w:p>
                <w:p><w:r><w:t>Ország: ' . $payer_country . '</w:t></w:r></w:p>
                <w:p><w:r><w:t>Fizetett összeg: ' . $paid_amount . ' ' . $paid_amount_currency . '</w:t></w:r></w:p>
                <w:p><w:r><w:t>Fizetési státusz: ' . $payment_status . '</w:t></w:r></w:p>
                <w:p><w:r><w:t>Dátum: ' . $created . '</w:t></w:r></w:p>';

        if (!empty($license_key)) {
            $xmlContent .= '
                <w:p><w:r><w:t>Licensz Kulcs</w:t></w:r></w:p>
                <w:p><w:r><w:t>' . $license_key . '</w:t></w:r></w:p>
                <w:p><w:r><w:t>Mindenképp mentsd el a licensz kulcsot!</w:t></w:r></w:p>';
        }

        $xmlContent .= '</w:body></w:document>';

        // ZIP struktúra létrehozása
        $zip = new ZipArchive();
        $fileName = "Fizetesi_Nyugta_$order_id.docx";
        $filePath = sys_get_temp_dir() . "/$fileName";

        if ($zip->open($filePath, ZipArchive::CREATE) === true) {
            // Word document szerkezeti fájlok
            $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                <Default Extension="xml" ContentType="application/xml"/>
                <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
            </Types>');

            $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
            </Relationships>');

            $zip->addFromString('word/_rels/document.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"></Relationships>');

            $zip->addFromString('word/document.xml', $xmlContent);

            $zip->addFromString('docProps/core.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
                xmlns:dc="http://purl.org/dc/elements/1.1/"
                xmlns:dcterms="http://purl.org/dc/terms/"
                xmlns:dcmitype="http://purl.org/dc/dcmitype/"
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <dc:title>Fizetési Nyugta</dc:title>
                <dc:creator>Automata Rendszer</dc:creator>
                <cp:revision>1</cp:revision>
            </cp:coreProperties>');

            $zip->addFromString('docProps/app.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
            <Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"
                xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
                <Application>PHP DOCX Generator</Application>
            </Properties>');

            $zip->close();

            // Fájl letöltése
            header("Content-Description: File Transfer");
            header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
            header("Content-Disposition: attachment; filename=\"$fileName\"");
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . filesize($filePath));
            readfile($filePath);

            // Ideiglenes fájl törlése
            unlink($filePath);
            exit;
        } else {
            echo "Nem sikerült létrehozni a DOCX fájlt!";
        }
    } else {
        echo "Érvénytelen rendelési azonosító!";
    }
} else {
    echo "Nincs rendelési azonosító megadva!";
}
?>
