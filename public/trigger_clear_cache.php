<?php
    $artisanPath = '/home/simak-unsri.haecloud.my.id/public_html/artisan';
    $phpPath = '/usr/local/lsws/lsphp84/bin/lsphp';

    $output = shell_exec("$phpPath $artisanPath optimize:clear 2>&1");

    echo "<pre>Output Perintah: 
    " . htmlspecialchars($output) . "</pre>";
    echo "<br>Cache telah dibersihkan pada: " . date("Y-m-d H:i:s");
?>