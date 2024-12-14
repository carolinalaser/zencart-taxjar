<?php
$autoLoadConfig[1000][] = [
    'autoType' => 'class',
    'loadFile' => 'observers/TaxJarAdminObserver.php',
    'classPath' => DIR_WS_CLASSES
];

$autoLoadConfig[1000][] = [
    'autoType'   => 'classInstantiate',
    'className'  => 'TaxJarAdminObserver',
    'objectName' => 'TaxJarAdminObserver'
];

?>