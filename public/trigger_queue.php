<?php
    $logFile = '/home/simak-unsri.haecloud.my.id/public_html/cron_queue.log';
    $output = shell_exec('/usr/local/lsws/lsphp84/bin/lsphp /home/simak-unsri.haecloud.my.id/public_html/artisan queue:work --once 2>&1');

    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$timestamp] Result: " . ($output ? $output : "Queue processed (no pending jobs)") . PHP_EOL, FILE_APPEND);
    echo "Queue triggered at $timestamp";
?>