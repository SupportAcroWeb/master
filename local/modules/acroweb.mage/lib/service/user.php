<?php

namespace Acroweb\Mage\Service;

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\UserTable;
use CUser;

class User
{
    /**
     * @return int|null
     */
    public static function currentUserId(): int|null
    {
        return CurrentUser::get()->getId();
    }

    public static function updateProfile($userId, $fields)
    {
        $user = new CUser;
        $user->Update($userId, $fields);

        return ($user->LAST_ERROR == "") ? true : ['TYPE' => 'ERROR', 'MESSAGE' => $user->LAST_ERROR];
    }

    public static function getPropUserForId($userId, $nameProp)
    {
        return UserTable::getList(array(
            'select' => array($nameProp),
            'filter' => array(
                'ACTIVE' => 'Y',
                '=ID' => $userId,
            )
        ))->fetch()[$nameProp] ?? false;
    }

    public static function getInfoUserForId($userId, $select = ['*', 'UF_*'], $checkActive = true)
    {
        $result = [];
        $filter = ['=ID' => $userId];
        
        if ($checkActive) {
            $filter['ACTIVE'] = 'Y';
        }
        
        $res = UserTable::getList([
            'select' => $select,
            'filter' => $filter
        ]);

        while ($prop = $res->fetch()) {
            $result[$prop["ID"]] = $prop;
        }

        return $result;
    }

    public static function getFieldsUser($code)
    {
        global $USER_FIELD_MANAGER;
        $result = [];
        $fields = $USER_FIELD_MANAGER->GetUserFields("USER");
        $obEnum = new \CUserFieldEnum;

        $rsEnum = $obEnum->GetList(array(), array("USER_FIELD_ID" => $fields[$code]["ID"]));
        while ($arEnum = $rsEnum->GetNext()) {
            $result[$arEnum["ID"]] = $arEnum;
        }

        return $result;
    }
}