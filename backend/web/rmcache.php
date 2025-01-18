<?php
function execPrint($command) {
    $result = array();
    exec($command, $result);
    print($command . "<br/>\n");
    foreach ($result as $line) {
        print($line . "<br/>\n");
    }
}

print("<pre>" . execPrint("rm -R -f assets/*") . "</pre>");