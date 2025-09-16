<?php

namespace Homeart\Integration\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Localization\Loc;


class IntegrationModuleMainTable extends DataManager
{
    public static function getTableName()
    {
        return 'homeart_integration_main';
    }

    public static function getMap()
    {
        return [
            (new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('HOMEART_INTEGRATION_TABLE_ID'),
            ])),

            (new TextField('ADDITIONAL_PARAMS', [
                'required' => true,
                'title' => Loc::getMessage('HOMEART_INTEGRATION_ADDITIONAL_PARAMS'),
                'serialized' => true
            ])),

            (new DatetimeField('TIMESTAMP', [
                'default_value' => new DateTime(),
                'title' => Loc::getMessage('HOMEART_INTEGRATION_TABLE_TIMESTAMP_FIELD'),
            ])),
        ];
    }
}
