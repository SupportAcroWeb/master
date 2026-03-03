<?php

namespace Acroweb\Hermitage\Helper;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use CForm;
use CFormCRM;
use CFormResult;

Loader::includeModule("form");
Loc::loadMessages(__FILE__);

class FormHelper
{
    /**
     * @param array $params
     * @return array
     */
    public static function send(array $params): array
    {
        if (check_bitrix_sessid()) {
            $formErrors = CForm::Check($params['WEB_FORM_ID'], $params, false, "Y", 'Y');

            if (count($formErrors)) {
                $result = ['success' => false, 'errors' => $formErrors];
            } elseif ($RESULT_ID = CFormResult::Add($params['WEB_FORM_ID'], $params)) {
                CFormCRM::onResultAdded($params['WEB_FORM_ID'], $RESULT_ID);
                CFormResult::SetEvent($RESULT_ID);
                CFormResult::Mail($RESULT_ID);

                $result = ['success' => true, 'errors' => []];
            } else {
                $result = ['success' => false, 'errors' => $GLOBALS["strError"]];
            }
        } else {
            $result = ['success' => false, 'errors' => ['sessid' => Loc::getMessage('REFRESH')]];
        }

        return $result;
    }
}