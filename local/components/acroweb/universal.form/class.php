<?php

namespace Acroweb\Components;

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use CBitrixComponent;
use CForm;
use CFile;
use Bitrix\Main\Loader;
use Bitrix\Main\SystemException;
use CFormCRM;
use CFormResult;
use Bitrix\Main\Page\Asset;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

Loader::includeModule('form');

/**
 * Universal Form Component
 *
 * This component handles form display and submission for Bitrix forms.
 */
class UniversalFormComponent extends CBitrixComponent implements Controllerable
{
    /** @var ErrorCollection */
    protected $errorCollection;

    /**
     * Constructor.
     *
     * @param CBitrixComponent|null $component
     */
    public function __construct(?CBitrixComponent $component = null)
    {
        parent::__construct($component);
        $this->errorCollection = new ErrorCollection();
    }

    /**
     * Prepare component parameters.
     *
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['FORM_ID'] = isset($arParams['FORM_ID']) ? intval($arParams['FORM_ID']) : 0;
        $arParams['FORM_SID'] = isset($arParams['FORM_SID']) ? trim($arParams['FORM_SID']) : '';
        $arParams['AJAX'] = isset($arParams['AJAX']) && $arParams['AJAX'] === 'Y';
        $arParams['SUCCESS_URL'] = isset($arParams['SUCCESS_URL']) ? $arParams['SUCCESS_URL'] : '';
        $arParams['USE_CAPTCHA'] = isset($arParams['USE_CAPTCHA']) && $arParams['USE_CAPTCHA'] === 'Y';

        return $arParams;
    }

    /**
     * Execute component.
     *
     * @return array
     */
    public function executeComponent(): array
    {
        $this->registerScript();
        try {
            if ($this->startResultCache()) {
                $this->arResult['FORM'] = $this->getForm();
                $this->arResult['QUESTIONS'] = $this->getQuestions();

                if ($this->arResult['FORM']['USE_CAPTCHA'] == 'Y' && !$GLOBALS['APPLICATION']->CaptchaCheckCode(
                        $this->arResult['CAPTCHA_CODE'],
                        $this->arResult['CAPTCHA_WORD']
                    )) {
                    $this->arResult['CAPTCHA_CODE'] = $GLOBALS['APPLICATION']->CaptchaGetCode();
                }

                $this->includeComponentTemplate();
            }
        } catch (SystemException $e) {
            $this->abortResultCache();
            ShowError($e->getMessage());
        }

        return $this->arResult;
    }

    protected function registerScript()
    {
        if (!Loader::includeModule('main')) {
            return;
        }

        Asset::getInstance()->addJs(
            $this->getPath() . '/scripts/universal_form.js'
        );
    }

    /**
     * Get form data.
     *
     * @return array
     * @throws SystemException
     */
    protected function getForm()
    {
        if ($this->arParams['FORM_ID'] > 0) {
            $rsForm = CForm::GetByID($this->arParams['FORM_ID']);
            $identifierType = 'ID';
            $identifier = $this->arParams['FORM_ID'];
        } elseif (!empty($this->arParams['FORM_SID'])) {
            $rsForm = CForm::GetBySID($this->arParams['FORM_SID']);
            $identifierType = 'SID';
            $identifier = $this->arParams['FORM_SID'];
        } else {
            throw new SystemException(Loc::getMessage('FORM_IDENTIFIER_NOT_SET'));
        }

        if ($rsForm) {
            $form = $rsForm->Fetch();
            if ($form !== false) {
                return $form;
            }
        }

        $errorMessage = Loc::getMessage('FORM_NOT_FOUND', [
            '#IDENTIFIER_TYPE#' => $identifierType,
            '#IDENTIFIER#' => $identifier,
        ]);
        $this->errorCollection->setError(new Error($errorMessage));

        throw new SystemException($errorMessage);
    }

    /**
     * Get form questions.
     *
     * @return array
     */
    protected function getQuestions(): array
    {
        $form = $this->getForm();
        $formId = $form['ID'];

        if (!$formId) {
            throw new SystemException(Loc::getMessage('FORM_NOT_FOUND'));
        }

        $questions = [];
        $by = 's_id';
        $order = 'asc';
        $filter = [];
        $isFiltered = false;
        $rsQuestions = CForm::GetFieldList($formId, $by, $order, $filter, $isFiltered);
        if ($rsQuestions && is_object($rsQuestions)) {
            while ($arQuestion = $rsQuestions->Fetch()) {
                $questions[] = $arQuestion;
            }
        }
        return $questions;
    }

    /**
     * Configure actions.
     *
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'submitForm' => [
                'prefilters' => [],
                'postfilters' => [],
                'security' => [
                    'guest' => true,
                ],
            ],
            'refreshCaptcha' => [
                'prefilters' => [],
                'postfilters' => [],
                'security' => [
                    'guest' => true,
                ],
            ],
        ];
    }

    public function refreshCaptchaAction()
    {
        $captchaCode = $GLOBALS['APPLICATION']->CaptchaGetCode();
        return [
            'captchaCode' => $captchaCode,
        ];
    }

    /**
     * Submit form action.
     *
     * @return array
     */
    public function submitFormAction(): array
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $formData = $request->getPostList()->toArray();
        $fileData = $request->getFileList()->toArray();

        if (!check_bitrix_sessid()) {
            return $this->getErrorResponse('FORM_SECURITY_ERROR');
        }

        $formId = $formData['WEB_FORM_ID'] ?? 0;
        if (!$formId) {
            return $this->getErrorResponse('FORM_ID_NOT_FOUND');
        }

        $formData = $this->processFileUploads($formData, $fileData);

        if ($this->arParams['USE_CAPTCHA'] && !$this->checkCaptcha(
                $request->getPost('captcha_word'),
                $request->getPost('captcha_sid')
            )) {
            return $this->getErrorResponse('FORM_CAPTCHA_ERROR');
        }

        $formErrors = CForm::Check($formId, $formData, false, "Y", 'Y');
        if (!empty($formErrors)) {
            return ['success' => false, 'errors' => $formErrors];
        }

        $resultId = CFormResult::Add($formId, $formData);
        if (!$resultId) {
            return $this->getErrorResponse('FORM_SUBMIT_ERROR');
        }

        $this->processFormResult($formId, $resultId);

        return [
            'success' => true,
            'message' => Loc::getMessage('FORM_SUBMIT_SUCCESS'),
            'redirectUrl' => $this->arParams['SUCCESS_URL'],
        ];
    }

    private function processFileUploads(array $formData, array $fileData): array
    {
        foreach ($fileData as $fieldName => $fileInfo) {
            if (!empty($fileInfo['name'])) {
                $formData[$fieldName] = $fileInfo;
            }
        }
        return $formData;
    }

    private function processFormResult(int $formId, int $resultId): void
    {
        CFormCRM::onResultAdded($formId, $resultId);
        CFormResult::SetEvent($resultId);
        CFormResult::Mail($resultId);
    }

    private function getErrorResponse(string $errorCode): array
    {
        return [
            'success' => false,
            'errors' => [Loc::getMessage($errorCode)],
        ];
    }

    /**
     * Check CAPTCHA.
     *
     * @param string $captchaWord
     * @param string $captchaSid
     * @return bool
     */
    protected function checkCaptcha(string $captchaWord, string $captchaSid): bool
    {
        if (!$captchaWord || !$captchaSid) {
            return false;
        }

        return $GLOBALS['APPLICATION']->CaptchaCheckCode($captchaWord, $captchaSid);
    }
}