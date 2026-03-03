<?php

declare(strict_types=1);

namespace Acroweb\Mage\Service;

use Acroweb\Mage\Organization\Service as OrganizationService;
use Bitrix\Main\Context;
use CFile;

/**
 * Сервис обработки регистрации пользователей с организациями
 */
class Registration
{
    /**
     * Обработчик события перед регистрацией пользователя
     * Назначает менеджера и получает данные организации из запроса
     * 
     * @param array $arFields Поля пользователя
     * @return bool
     */
    public static function onBeforeUserRegister(array &$arFields): bool
    {
        global $APPLICATION;
        
        // Устанавливаем LOGIN из EMAIL, если не указан
        if (empty($arFields['LOGIN']) && !empty($arFields['EMAIL'])) {
            $arFields['LOGIN'] = $arFields['EMAIL'];
        }
        
        // Получаем данные из запроса
        $request = Context::getCurrent()->getRequest();
        $inn = trim($request->getPost('ORG_INN') ?: '');
        $orgName = trim($request->getPost('ORG_NAME') ?: '');
        
        // Проверяем наличие ИНН
        if (empty($inn)) {
            $APPLICATION->ThrowException('Необходимо указать ИНН организации');
            return false;
        }
        
        // Проверяем наличие названия организации
        if (empty($orgName)) {
            $APPLICATION->ThrowException('Необходимо указать название организации');
            return false;
        }
        
        // Проверяем формат ИНН
        if (!preg_match('/^\d{10}$|^\d{12}$/', $inn)) {
            $APPLICATION->ThrowException('Неверный формат ИНН. Введите 10 или 12 цифр.');
            return false;
        }
        
        // Назначаем случайного менеджера
        $managerId = Manager::getRandomManagerId();
        if ($managerId) {
            $arFields['UF_MANAGER_ID'] = $managerId;
        }
        
        return true;
    }
    
    /**
     * Обработчик события после регистрации пользователя
     * Создает организацию для зарегистрированного пользователя
     * 
     * @param array $arFields Поля пользователя
     * @return void
     */
    public static function onAfterUserRegister(array $arFields): void
    {
        try {
            // Получаем ID созданного пользователя
            $userId = (int)($arFields['USER_ID'] ?? $arFields['ID'] ?? 0);
            
            if (!$userId) {
                return;
            }
            
            // Получаем данные из запроса
            $request = Context::getCurrent()->getRequest();
            $inn = trim($request->getPost('ORG_INN') ?: '');
            $name = trim($request->getPost('ORG_NAME') ?: '');
            $kpp = trim($request->getPost('ORG_KPP') ?: '');
            $urAddress = trim($request->getPost('ORG_UR_ADDRESS') ?: '');

            // Обрабатываем файл карточки организации
            $fileId = null;
            $files = $request->getFileList();
            
            if (!empty($files['ORG_FILE']) && is_array($files['ORG_FILE'])) {
                $fileId = self::saveOrganizationFile($files['ORG_FILE']);
            }

            // Проверяем, существует ли организация с таким ИНН или названием
            $existingOrganization = OrganizationService::findByInnOrName($inn, $name);

            if (!empty($existingOrganization)) {
                $organizationId = (int)$existingOrganization['ID'];
                OrganizationService::addOwner($organizationId, $userId);

                if ($fileId) {
                    OrganizationService::update($organizationId, [
                        'PROPERTIES' => [
                            OrganizationService::PROP_FILE => [
                                'VALUE' => $fileId,
                                'DESCRIPTION' => '',
                            ],
                            OrganizationService::PROP_STATUS => '',
                        ],
                    ]);

                    Manager::sendOrganizationVerificationEmail($organizationId, $userId);
                }

                return;
            }
            
            // Определяем статус организации
            // Если загружен файл - статус "На проверке", иначе - пустой
            $status = $fileId ? OrganizationService::STATUS_PENDING : '';
            
            // Создаем организацию
            $organizationFields = [
                'NAME' => $name,
                'ACTIVE' => 'Y',
                'PROPERTIES' => [
                    OrganizationService::PROP_INN => $inn,
                    OrganizationService::PROP_KPP => $kpp,
                    OrganizationService::PROP_UR_ADDRESS => $urAddress,
                    OrganizationService::PROP_STATUS => $status,
                    OrganizationService::PROP_USER_ID => [$userId],
                ],
            ];
            
            // Добавляем файл если он был загружен
            if ($fileId) {
                $organizationFields['PROPERTIES'][OrganizationService::PROP_FILE] = $fileId;
            }
            
            $organizationId = OrganizationService::add($organizationFields);
            
            // Если файл загружен и организация создана - отправляем уведомление менеджеру
            if ($fileId && $organizationId) {
                Manager::sendOrganizationVerificationEmail($organizationId, $userId);
            }
        } catch (\Exception $e) {
            // Логируем ошибку, но не прерываем регистрацию пользователя
            AddMessage2Log('Ошибка создания организации при регистрации: ' . $e->getMessage(), 'acroweb.mage');
        }
    }

    /**
     * Сохранить файл карточки организации
     *
     * @param array $file Данные файла
     * @return int|null
     */
    private static function saveOrganizationFile(array $file): ?int
    {
        if (
            empty($file['tmp_name']) ||
            !file_exists($file['tmp_name']) ||
            (int)($file['error'] ?? 0) !== UPLOAD_ERR_OK
        ) {
            return null;
        }

        $arFile = [
            'name' => $file['name'] ?? '',
            'size' => $file['size'] ?? 0,
            'tmp_name' => $file['tmp_name'],
            'type' => $file['type'] ?? '',
            'MODULE_ID' => 'acroweb.mage',
        ];

        $fileId = CFile::SaveFile($arFile, 'organizations');

        return $fileId ? (int)$fileId : null;
    }
}

