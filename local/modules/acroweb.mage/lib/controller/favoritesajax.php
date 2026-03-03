<?php

namespace Acroweb\Mage\Controller;

use Acroweb\Mage\Config;
use Acroweb\Mage\Service\Favorites;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class FavoritesAjax extends \Bitrix\Main\Engine\Controller
{
    public function configureActions()
    {
        return [
            'add' => [
                '-prefilters' => [
                    '\Bitrix\Main\Engine\ActionFilter\Authentication',
                ],
            ],
            'del' => [
                '-prefilters' => [
                    '\Bitrix\Main\Engine\ActionFilter\Authentication',
                ],
            ],
            'get' => [
                '-prefilters' => [
                    '\Bitrix\Main\Engine\ActionFilter\Authentication',
                ],
            ],
            'deleteAll' => [
                '-prefilters' => [
                    '\Bitrix\Main\Engine\ActionFilter\Authentication',
                ],
            ],
        ];
    }

    public function addAction($id)
    {
        return Favorites::add($id);
    }

    public function delAction($id)
    {
        return Favorites::del($id);
    }

    public function getAction()
    {
        return Favorites::get();
    }

    public function deleteAllAction()
    {
        return Favorites::deleteAll();
    }
}