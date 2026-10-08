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

$id = (int)($_REQUEST['ID'] ?? 0);
$isNew = $id <= 0;

if (!$isNew) {
    $product = ProductTable::getByPrimary($id)->fetch();

    if (!$product) {
        require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';
        ShowError(Loc::getMessage('PRODUCT_NOT_FOUND'));
        require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
        return;
    }
} else {
    $product = [
        'ID' => 0,
        'NAME' => '',
        'CODE' => '',
        'PRICE' => 0,
        'ACTIVE' => 'Y',
    ];
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && check_bitrix_sessid()
    && isset($_POST['save'])
) {
    $fields = [
        'NAME' => trim((string)($_POST['NAME'] ?? '')),
        'CODE' => trim((string)($_POST['CODE'] ?? '')),
        'PRICE' => (float)str_replace(',', '.', (string)($_POST['PRICE'] ?? 0)),
        'ACTIVE' => isset($_POST['ACTIVE']) ? 'Y' : 'N',
        'DATE_UPDATE' => new \Bitrix\Main\Type\DateTime(),
    ];

    if ($fields['NAME'] === '') {
        $error = Loc::getMessage('ERROR_NAME_REQUIRED');
    } elseif ($fields['PRICE'] < 0) {
        $error = Loc::getMessage('ERROR_PRICE_INVALID');
    } else {
        if ($isNew) {
            $fields['DATE_CREATE'] = new \Bitrix\Main\Type\DateTime();
            $result = ProductTable::add($fields);
        } else {
            $result = ProductTable::update($id, $fields);
        }

        if ($result->isSuccess()) {
            LocalRedirect(
                'websbro_democatalog_products.php?lang=' . LANGUAGE_ID
            );
        }

        $error = implode('<br>', $result->getErrorMessages());
    }

    $product = array_merge($product, $fields);
}

$APPLICATION->SetTitle(
    $isNew
        ? Loc::getMessage('PAGE_TITLE_ADD')
        : Loc::getMessage('PAGE_TITLE_EDIT')
);

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php';

if (!empty($error)) {
    CAdminMessage::ShowMessage([
        'TYPE' => 'ERROR',
        'MESSAGE' => $error,
    ]);
}

$aTabs = [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('TAB_PRODUCT'),
        'TITLE' => Loc::getMessage('TAB_PRODUCT_TITLE'),
    ],
];

$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>
<form method="post" action="<?= htmlspecialcharsbx($APPLICATION->GetCurPage()) ?>?ID=<?= $id ?>&lang=<?= LANGUAGE_ID ?>">
    <tr>
        <td width="40%"><span class="required">*</span><?= Loc::getMessage('FIELD_NAME') ?>:</td>
        <td width="60%">
            <input
                type="text"
                name="NAME"
                value="<?= htmlspecialcharsbx($product['NAME']) ?>"
                size="60"
                maxlength="255"
            >
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('FIELD_CODE') ?>:</td>
        <td>
            <input
                type="text"
                name="CODE"
                value="<?= htmlspecialcharsbx($product['CODE']) ?>"
                size="60"
                maxlength="255"
            >
        </td>
    </tr>
    <tr>
        <td><span class="required">*</span><?= Loc::getMessage('FIELD_PRICE') ?>:</td>
        <td>
            <input
                type="text"
                name="PRICE"
                value="<?= htmlspecialcharsbx($product['PRICE']) ?>"
                size="20"
            >
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('FIELD_ACTIVE') ?>:</td>
        <td>
            <input
                type="checkbox"
                name="ACTIVE"
                value="Y"
                <?= $product['ACTIVE'] === 'Y' ? 'checked' : '' ?>
            >
        </td>
    </tr>
    <?php $tabControl->Buttons(); ?>
    <input type="submit" name="save" value="<?= Loc::getMessage('SAVE') ?>" class="adm-btn-save">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <?= bitrix_sessid_post() ?>
</form>
<?php
$tabControl->End();

require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';
