<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php';

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('websbro.democatalog')) {
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
    ShowError(Loc::getMessage('MODULE_NOT_INSTALLED'));
    require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
    return;
}

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

use Websbro\DemoCatalog\Model\ProductTable;

$sTableID = 'websbro_democatalog_products';
$oSort = new CAdminSorting(
    $sTableID,
    'ID',
    'desc'
);

$lAdmin = new CAdminList(
    $sTableID,
    $oSort
);

if ($lAdmin->EditAction()) {
    foreach ((array)$lAdmin->GetEditFields() as $id => $fields) {
        $id = (int)$id;

        if ($id <= 0) {
            continue;
        }

        $fields['PRICE'] = (float)str_replace(',', '.', (string)($fields['PRICE'] ?? 0));
        $fields['ACTIVE'] = isset($fields['ACTIVE']) ? 'Y' : 'N';
        $fields['DATE_UPDATE'] = new \Bitrix\Main\Type\DateTime();

        $result = ProductTable::update($id, $fields);

        if (!$result->isSuccess()) {
            $lAdmin->AddUpdateError(
                implode('<br>', $result->getErrorMessages()),
                $id
            );
        }
    }
}

if (($ids = $lAdmin->GroupAction())) {
    if ($_REQUEST['action_target'] === 'selected') {
        $ids = [];
        $result = ProductTable::getList([
            'select' => ['ID'],
        ]);

        while ($row = $result->fetch()) {
            $ids[] = (int)$row['ID'];
        }
    }

    foreach ($ids as $id) {
        $id = (int)$id;

        switch ($_REQUEST['action']) {
            case 'delete':
                $result = ProductTable::delete($id);

                if (!$result->isSuccess()) {
                    $lAdmin->AddGroupError(
                        implode('<br>', $result->getErrorMessages()),
                        $id
                    );
                }
                break;

            case 'activate':
                ProductTable::update($id, [
                    'ACTIVE' => 'Y',
                    'DATE_UPDATE' => new \Bitrix\Main\Type\DateTime(),
                ]);
                break;

            case 'deactivate':
                ProductTable::update($id, [
                    'ACTIVE' => 'N',
                    'DATE_UPDATE' => new \Bitrix\Main\Type\DateTime(),
                ]);
                break;
        }
    }
}

$headers = [
    ['id' => 'ID', 'content' => 'ID', 'sort' => 'ID', 'default' => true],
    ['id' => 'NAME', 'content' => Loc::getMessage('COLUMN_NAME'), 'sort' => 'NAME', 'default' => true],
    ['id' => 'CODE', 'content' => Loc::getMessage('COLUMN_CODE'), 'sort' => 'CODE', 'default' => true],
    ['id' => 'PRICE', 'content' => Loc::getMessage('COLUMN_PRICE'), 'sort' => 'PRICE', 'default' => true],
    ['id' => 'ACTIVE', 'content' => Loc::getMessage('COLUMN_ACTIVE'), 'sort' => 'ACTIVE', 'default' => true],
    ['id' => 'DATE_CREATE', 'content' => Loc::getMessage('COLUMN_DATE_CREATE'), 'sort' => 'DATE_CREATE', 'default' => true],
];

$lAdmin->AddHeaders($headers);

$result = ProductTable::getList([
    'select' => [
        'ID',
        'NAME',
        'CODE',
        'PRICE',
        'ACTIVE',
        'DATE_CREATE',
    ],
    'order' => [
        $by => $order,
    ],
]);

while ($row = $result->fetch()) {
    $row['PRICE'] = number_format((float)$row['PRICE'], 2, '.', ' ');

    $editUrl = 'websbro_democatalog_product_edit.php?ID=' . (int)$row['ID'] . '&lang=' . LANGUAGE_ID;

    $listRow = $lAdmin->AddRow(
        $row['ID'],
        $row,
        $editUrl,
        Loc::getMessage('EDIT_TITLE')
    );

    $listRow->AddViewField(
        'NAME',
        '<a href="' . htmlspecialcharsbx($editUrl) . '">'
        . htmlspecialcharsbx($row['NAME'])
        . '</a>'
    );

    $listRow->AddViewField(
        'ACTIVE',
        $row['ACTIVE'] === 'Y'
            ? Loc::getMessage('YES')
            : Loc::getMessage('NO')
    );
}

$lAdmin->AddGroupActionTable([
    'delete' => Loc::getMessage('ACTION_DELETE'),
    'activate' => Loc::getMessage('ACTION_ACTIVATE'),
    'deactivate' => Loc::getMessage('ACTION_DEACTIVATE'),
]);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage('PAGE_TITLE'));

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

$aContext = [
    [
        'TEXT' => Loc::getMessage('ADD_PRODUCT'),
        'LINK' => 'websbro_democatalog_product_edit.php?lang=' . LANGUAGE_ID,
        'TITLE' => Loc::getMessage('ADD_PRODUCT'),
        'ICON' => 'btn_new',
    ],
];

$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->DisplayList();

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
