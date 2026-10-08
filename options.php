<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$moduleId = 'websbro.democatalog';

if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));
}

$aTabs = [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('TAB_GENERAL'),
        'TITLE' => Loc::getMessage('TAB_GENERAL_TITLE'),
        'OPTIONS' => [
            [
                'catalog_name',
                Loc::getMessage('OPTION_CATALOG_NAME'),
                '',
                ['text', 50],
            ],
        ],
    ],
];

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && check_bitrix_sessid()
    && isset($_POST['Update'])
) {
    $catalogName = trim((string)($_POST['catalog_name'] ?? ''));

    COption::SetOptionString(
        $moduleId,
        'catalog_name',
        $catalogName
    );
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);

$tabControl->Begin();
?>
<form method="post" action="<?= htmlspecialcharsbx($APPLICATION->GetCurPage()) ?>?mid=<?= urlencode($moduleId) ?>&lang=<?= LANGUAGE_ID ?>">
    <?php
    $tabControl->BeginNextTab();

    $catalogName = COption::GetOptionString(
        $moduleId,
        'catalog_name',
        Loc::getMessage('DEFAULT_CATALOG_NAME')
    );
    ?>
    <tr>
        <td width="40%"><?= Loc::getMessage('OPTION_CATALOG_NAME') ?>:</td>
        <td width="60%">
            <input
                type="text"
                name="catalog_name"
                value="<?= htmlspecialcharsbx($catalogName) ?>"
                size="50"
            >
        </td>
    </tr>

    <?php $tabControl->Buttons(); ?>

    <input type="submit" name="Update" value="<?= Loc::getMessage('SAVE') ?>" class="adm-btn-save">
    <?= bitrix_sessid_post() ?>
</form>
<?php
$tabControl->End();
