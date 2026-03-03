<?php

declare(strict_types=1);

use Acroweb\Mage\Config;
use Acroweb\Mage\Organization\Service as OrganizationService;
use Acroweb\Mage\Service\Manager;
use Acroweb\Mage\Service\Request;
use Acroweb\Mage\Service\User;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use CBitrixComponent;
use CFile;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Компонент списка организаций пользователя
 */
class OrganizationListComponent extends CBitrixComponent implements Controllerable
{
    /** @var ErrorCollection */
    protected ErrorCollection $errorCollection;

    /**
     * Конструктор
     * 
     * @param CBitrixComponent|null $component
     */
    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->errorCollection = new ErrorCollection();
        
        // Подключаем необходимые модули для AJAX-методов
        Loader::includeModule('iblock');
        Loader::includeModule('acroweb.mage');
    }

    /**
     * Настройка доступа к AJAX-методам компонента
     * 
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'getInfoByInn' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
            'addOrganization' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
            'uploadCard' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
            'sendToVerify' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
            'deleteOrganization' => [
                'prefilters' => [],
                'postfilters' => [],
            ],
        ];
    }

    /**
     * Список ключей для подписи параметров
     * 
     * @return array
     */
    public function listKeysSignedParameters(): array
    {
        return [];
    }

    /**
     * Получить информацию об организации по ИНН из DaData
     * 
     * @param string $inn ИНН организации
     * @return array
     */
    public function getInfoByInnAction(string $inn): array
    {
        try {
            $inn = trim($inn);
            
            // Валидация ИНН
            if (!preg_match('/^\d{10}$|^\d{12}$/', $inn)) {
                return [
                    'status' => 'error',
                    'message' => 'Неверный формат ИНН. Введите 10 или 12 цифр.',
                ];
            }

            $organization = OrganizationService::findByInnOrName($inn, null);

            if (!empty($organization)) {
                $props = $organization['PROPERTIES'] ?? [];
                $fileValue = $props[OrganizationService::PROP_FILE]['VALUE'] ?? null;
                $fileId = $this->extractFileId($fileValue);

                return [
                    'status' => 'success',
                    'data' => [
                        'source' => 'iblock',
                        'organization' => [
                            'ID' => (int)$organization['ID'],
                            'NAME' => $organization['NAME'] ?? '',
                            'INN' => $props[OrganizationService::PROP_INN]['VALUE'] ?? '',
                            'KPP' => $props[OrganizationService::PROP_KPP]['VALUE'] ?? '',
                            'ADDRESS' => $props[OrganizationService::PROP_UR_ADDRESS]['VALUE'] ?? '',
                            'STATUS' => $props[OrganizationService::PROP_STATUS]['VALUE_XML_ID'] ?? '',
                            'FILE_ID' => $fileId,
                            'HAS_FILE' => $fileId > 0,
                        ],
                    ],
                ];
            }

            // Запрос к DaData
            $response = Request::requestApi(
                'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party',
                true,
                Config::API_KEY,
                ['query' => $inn, 'branch_type' => 'MAIN']
            );

            return [
                'status' => 'success',
                'data' => [
                    'source' => 'dadata',
                    'suggestions' => $response['suggestions'] ?? [],
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Ошибка при получении данных: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Добавить организацию
     * 
     * @return array
     */
    public function addOrganizationAction(): array
    {
        try {
            $userId = User::currentUserId();
            
            if (!$userId) {
                return [
                    'status' => 'error',
                    'message' => 'Пользователь не авторизован',
                ];
            }

            // Получаем данные из запроса
            $request = Context::getCurrent()->getRequest();
            $inn = trim($request->getPost('inn') ?: '');
            $name = trim($request->getPost('title') ?: '');
            $kpp = trim($request->getPost('kpp') ?: '');
            $urAddress = trim($request->getPost('address') ?: '');
            $files = $request->getFileList();
            $fileId = $this->saveOrganizationFile($files['file'] ?? null);

            // Проверяем существование организации до загрузки файла
            $existingOrganization = OrganizationService::findByInnOrName($inn, $name);

            if (!empty($existingOrganization)) {
                if (!OrganizationService::userHasAccess($existingOrganization, $userId)) {
                    OrganizationService::addOwner((int)$existingOrganization['ID'], $userId);
                }

                if ($fileId) {
                    OrganizationService::update((int)$existingOrganization['ID'], [
                        'PROPERTIES' => [
                            OrganizationService::PROP_FILE => [
                                'VALUE' => $fileId,
                                'DESCRIPTION' => '',
                            ],
                            OrganizationService::PROP_STATUS => '',
                        ],
                    ]);
                }

                return [
                    'status' => 'success',
                    'message' => 'Организация привязана к вашему профилю',
                    'data' => [
                        'id' => (int)$existingOrganization['ID'],
                    ],
                ];
            }

            // Валидация обязательных полей
            if (empty($inn) || empty($name)) {
                return [
                    'status' => 'error',
                    'message' => 'Заполните обязательные поля: ИНН и Название',
                ];
            }

            // Валидация ИНН
            if (!preg_match('/^\d{10}$|^\d{12}$/', $inn)) {
                return [
                    'status' => 'error',
                    'message' => 'Неверный формат ИНН',
                ];
            }

            // Создаем организацию 
            $fields = [
                'NAME' => $name,
                'ACTIVE' => 'Y',
                'PROPERTIES' => [
                    OrganizationService::PROP_INN => $inn,
                    OrganizationService::PROP_KPP => $kpp,
                    OrganizationService::PROP_UR_ADDRESS => $urAddress,
                    OrganizationService::PROP_STATUS => $fileId ? OrganizationService::STATUS_PENDING : '',
                    OrganizationService::PROP_USER_ID => [$userId],
                ],
            ];
            
            // Добавляем файл если он был загружен
            if ($fileId) {
                $fields['PROPERTIES'][OrganizationService::PROP_FILE] = $fileId;
            }

            $organizationId = OrganizationService::add($fields);

            return [
                'status' => 'success',
                'message' => 'Организация успешно добавлена',
                'data' => [
                    'id' => $organizationId,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Загрузить карточку организации
     * 
     * @return array
     */
    public function uploadCardAction(): array
    {
        try {
            $userId = User::currentUserId();
            
            if (!$userId) {
                return [
                    'status' => 'error',
                    'message' => 'Пользователь не авторизован',
                ];
            }

            // Получаем ID организации из запроса
            $request = Context::getCurrent()->getRequest();
            $organizationId = (int)$request->getPost('organizationId');
            
            if (!$organizationId) {
                return [
                    'status' => 'error',
                    'message' => 'ID организации не указан',
                ];
            }

            // Проверяем принадлежность организации пользователю
            $organization = OrganizationService::getById($organizationId);
            
            if (empty($organization)) {
                return [
                    'status' => 'error',
                    'message' => 'Организация не найдена',
                ];
            }

            if (!OrganizationService::userHasAccess($organization, $userId)) {
                return [
                    'status' => 'error',
                    'message' => 'Нет доступа к этой организации',
                ];
            }

            // Получаем файлы из запроса
            $files = $request->getFileList();
            
            if (!empty($files['file'])) {
                $file = $files['file'];
                
                // Формируем массив для CFile::SaveFile
                $arFile = [
                    'name' => $file['name'],
                    'size' => $file['size'],
                    'tmp_name' => $file['tmp_name'],
                    'type' => $file['type'],
                    'MODULE_ID' => 'acroweb.mage',
                ];

                $fileId = CFile::SaveFile($arFile, 'organizations');
                
                if ($fileId) {
                    // Обновляем файл и сбрасываем статус на "Не проверена" (пустое значение)
                    $updateResult = OrganizationService::update($organizationId, [
                        'PROPERTIES' => [
                            OrganizationService::PROP_FILE => [
                                'VALUE' => $fileId,
                                'DESCRIPTION' => '',
                            ],
                            OrganizationService::PROP_STATUS => '', // Сбрасываем статус
                        ],
                    ]);
                    
                    if ($updateResult) {
                        return [
                            'status' => 'success',
                            'message' => 'Файл успешно загружен',
                        ];
                    } else {
                        return [
                            'status' => 'error',
                            'message' => 'Ошибка при обновлении организации',
                        ];
                    }
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Ошибка при сохранении файла',
                    ];
                }
            }

            return [
                'status' => 'error',
                'message' => 'Файл не выбран',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Отправить организацию на проверку
     * 
     * @param int $organizationId ID организации
     * @return array
     */
    public function sendToVerifyAction(int $organizationId): array
    {
        try {
            $userId = User::currentUserId();
            
            if (!$userId) {
                return [
                    'status' => 'error',
                    'message' => 'Пользователь не авторизован',
                ];
            }

            // Проверяем принадлежность организации пользователю
            $organization = OrganizationService::getById($organizationId);
            
            if (empty($organization)) {
                return [
                    'status' => 'error',
                    'message' => 'Организация не найдена',
                ];
            }

            if (!OrganizationService::userHasAccess($organization, $userId)) {
                return [
                    'status' => 'error',
                    'message' => 'Нет доступа к этой организации',
                ];
            }

            // Меняем статус на "На проверке"
            OrganizationService::update($organizationId, [
                'PROPERTIES' => [
                    OrganizationService::PROP_STATUS => OrganizationService::STATUS_PENDING,
                ],
            ]);

            // Отправляем уведомление менеджеру
            Manager::sendOrganizationVerificationEmail($organizationId, $userId);

            return [
                'status' => 'success',
                'message' => 'Организация отправлена на проверку',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Удалить организацию
     * 
     * @param int $organizationId ID организации
     * @return array
     */
    public function deleteOrganizationAction(int $organizationId): array
    {
        try {
            $userId = User::currentUserId();
            
            if (!$userId) {
                return [
                    'status' => 'error',
                    'message' => 'Пользователь не авторизован',
                ];
            }

            // Проверяем принадлежность организации пользователю
            $organization = OrganizationService::getById($organizationId);
            
            if (empty($organization)) {
                return [
                    'status' => 'error',
                    'message' => 'Организация не найдена',
                ];
            }

            if (!OrganizationService::userHasAccess($organization, $userId)) {
                return [
                    'status' => 'error',
                    'message' => 'Нет доступа к этой организации',
                ];
            }

            // Отвязываем пользователя
            OrganizationService::removeOwner($organizationId, $userId);

            return [
                'status' => 'success',
                'message' => 'Доступ к организации удалён',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Получить ID файла из значения свойства
     *
     * @param mixed $value Значение свойства
     * @return int
     */
    private function extractFileId(mixed $value): int
    {
        if (is_array($value)) {
            if (isset($value['ID'])) {
                return (int)$value['ID'];
            }
            if (isset($value['VALUE'])) {
                return (int)$value['VALUE'];
            }
        }

        return (int)$value;
    }

    /**
     * Сохранить файл карточки организации
     *
     * @param array|null $file Данные файла
     * @return int|null
     */
    private function saveOrganizationFile(?array $file): ?int
    {
        if (
            empty($file) ||
            (int)($file['error'] ?? 0) !== UPLOAD_ERR_OK ||
            empty($file['tmp_name']) ||
            !file_exists($file['tmp_name'])
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

    /**
     * Проверка модулей
     * 
     * @return bool
     */
    protected function checkModules(): bool
    {
        try {
            if (!Loader::includeModule('iblock')) {
                $this->errorCollection->setError(new Error('Модуль "Информационные блоки" не установлен'));
                return false;
            }

            if (!Loader::includeModule('acroweb.mage')) {
                $this->errorCollection->setError(new Error('Модуль "Acroweb Mage" не установлен'));
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $this->errorCollection->setError(new Error($e->getMessage()));
            return false;
        }
    }

    /**
     * Точка входа компонента
     * 
     * @return void
     */
    public function executeComponent(): void
    {
        try {
            // Проверка модулей
            if (!$this->checkModules()) {
                $this->showErrors();
                return;
            }

            // Проверка авторизации
            $userId = User::currentUserId();
            
            if (!$userId) {
                LocalRedirect('/auth/');
                return;
            }

            // Получаем список организаций пользователя
            $organizations = OrganizationService::getListByUser($userId);

            // Подготавливаем данные для шаблона
            $this->arResult = [
                'ORGANIZATIONS' => $organizations,
                'USER_ID' => $userId,
                'SIGNED_PARAMETERS' => $this->getSignedParameters(),
            ];

            $this->includeComponentTemplate();
        } catch (\Exception $e) {
            ShowError('Ошибка работы компонента: ' . $e->getMessage());
        }
    }

    /**
     * Показать ошибки
     * 
     * @return void
     */
    protected function showErrors(): void
    {
        foreach ($this->errorCollection as $error) {
            ShowError($error->getMessage());
        }
    }
}

