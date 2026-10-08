<?php

namespace Websbro\DemoCatalog\Model;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\Type\DateTime;

class ProductTable extends DataManager
{
    public static function getTableName()
    {
        return 'websbro_democatalog_product';
    }

    public static function getMap()
    {
        return [
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),

            new StringField('NAME', [
                'required' => true,
                'validation' => static function () {
                    return [
                        new LengthValidator(null, 255),
                    ];
                },
            ]),

            new StringField('CODE', [
                'validation' => static function () {
                    return [
                        new LengthValidator(null, 255),
                    ];
                },
            ]),

            new \Bitrix\Main\ORM\Fields\FloatField('PRICE', [
                'required' => true,
                'default_value' => 0,
            ]),

            new BooleanField('ACTIVE', [
                'values' => ['N', 'Y'],
                'default_value' => 'Y',
            ]),

            new DatetimeField('DATE_CREATE', [
                'required' => true,
                'default_value' => static function () {
                    return new DateTime();
                },
            ]),

            new DatetimeField('DATE_UPDATE', [
                'required' => true,
                'default_value' => static function () {
                    return new DateTime();
                },
        ]),

        ];
    }
}
