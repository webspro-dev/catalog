<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$aMenu = [
    [
        'parent_menu' => 'global_menu_content',
        'section' => 'websbro_democatalog',
        'sort' => 500,
        'text' => Loc::getMessage('MENU_CATALOG'),
        'title' => Loc::getMessage('MENU_CATALOG'),
        'url' => 'websbro_democatalog_products.php?lang=' . LANGUAGE_ID,
        'icon' => 'iblock_menu_icon_types',
        'page_icon' => 'iblock_page_icon_types',
        'items_id' => 'websbro_democatalog',
    ],
];

return $aMenu;
