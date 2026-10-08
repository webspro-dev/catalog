<?php

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses(
    'websbro.democatalog',
    [
        'Websbro\DemoCatalog\Model\ProductTable' => 'lib/Model/ProductTable.php',
        'Websbro\DemoCatalog\EventHandler' => 'lib/EventHandler.php',
    ]
);
