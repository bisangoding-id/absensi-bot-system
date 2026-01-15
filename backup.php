<?php
if (isset($_GET['file'])) {
    $target = ($_GET['file'] == 'wa') ? 'webhook_whatsapp.php' : 'webhook_telegram.php';
    
    if (file_exists($target)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($target).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($target));
        
        readfile($target);
        exit;
    } else {
        echo "File tidak ditemukan.";
    }
}
?>