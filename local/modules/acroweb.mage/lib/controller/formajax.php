<?php

namespace Acroweb\Mage\Controller;

use Acroweb\Mage\Helper\FormHelper;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class FormAjax extends \Bitrix\Main\Engine\Controller
{
    public function configureActions()
    {
        return [
            'send' => [
                '-prefilters' => [
                    '\Bitrix\Main\Engine\ActionFilter\Authentication',
                ],
            ],
        ];
    }

    public function sendAction()
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $params = $request->getPostList()->toArray();

        return FormHelper::send($params);
    }
}