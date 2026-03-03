<?php

namespace Acroweb\Components;

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Subscribe\SubscriptionTable;
use CBitrixComponent;
use CSubscription;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

Loc::loadMessages(__FILE__);

class SenderSubscribeComponent extends CBitrixComponent implements Controllerable, Errorable
{
    protected $errorCollection;

    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->errorCollection = new ErrorCollection();
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }

    public function configureActions()
    {
        return [
            'subscribe' => [
                'prefilters' => [],
                'postfilters' => []
            ]
        ];
    }

    public function subscribeAction($email)
    {
        if (!Loader::includeModule('subscribe')) {
            $this->errorCollection[] = new Error(Loc::getMessage('ACROWEB_SENDER_SUBSCRIBE_MODULE_NOT_INSTALLED'));
            return null;
        }

        if (!check_email($email)) {
            $this->errorCollection[] = new Error(Loc::getMessage('ACROWEB_SENDER_SUBSCRIBE_INVALID_EMAIL'));
            return null;
        }

        $existingSubscription = SubscriptionTable::getList([
            'filter' => ['=EMAIL' => $email, '=ACTIVE' => 'Y'],
            'limit' => 1
        ])->fetch();

        if ($existingSubscription) {
            $this->errorCollection[] = new Error(Loc::getMessage('ACROWEB_SENDER_SUBSCRIBE_EMAIL_ALREADY_SUBSCRIBED'));
            return null;
        }

        $subscription = new CSubscription;
        $subscriptionId = $subscription->Add([
            'EMAIL' => $email,
            'ACTIVE' => 'Y',
            'FORMAT' => 'text',
            'CONFIRMED' => 'Y',
            'SEND_CONFIRM' => 'N',
        ]);

        if ($subscriptionId > 0) {
            return ['message' => Loc::getMessage('ACROWEB_SENDER_SUBSCRIBE_SUCCESS')];
        } else {
            $this->errorCollection[] = new Error($subscription->LAST_ERROR);
            return null;
        }
    }

    public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }
}