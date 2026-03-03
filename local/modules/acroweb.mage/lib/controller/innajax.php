<?php

declare(strict_types=1);

namespace Acroweb\Mage\Controller;

use Acroweb\Mage\Config;
use Acroweb\Mage\Organization\Service as OrganizationService;
use Acroweb\Mage\Service\Request;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;

/**
 * Контроллер для работы с ИНН организаций
 */
class InnAjax extends Controller
{
    /**
     * Конструктор - подключаем необходимые модули
     */
    public function __construct()
    {
        parent::__construct();
        Loader::includeModule('iblock');
    }

    /**
     * Настройка доступа к методам
     * 
     * @return array
     */
    public function configureActions(): array
    {
        return [
            'checkINN' => [
                '-prefilters' => [
                    '\Bitrix\Main\Engine\ActionFilter\Authentication',
                ],
            ],
            'getInfo' => [
                '-prefilters' => [
                    '\Bitrix\Main\Engine\ActionFilter\Authentication',
                ],
            ],
        ];
    }

    /**
     * Проверить, не зарегистрирована ли уже организация с таким ИНН
     * 
     * @param string $inn ИНН организации
     * @return array{status:string,data:array}|array
     */
    public function checkINNAction(string $inn): array
    {
        try {
            $inn = trim($inn);
            
            // Валидация формата ИНН
            if (!preg_match('/^\d{10}$|^\d{12}$/', $inn)) {
                return [
                    'status' => 'error',
                    'message' => 'Неверный формат ИНН. Введите 10 или 12 цифр.',
                    'data' => false,
                ];
            }

            $organization = OrganizationService::findByInnOrName($inn, null);

            return [
                'status' => 'success',
                'data' => [
                    'is_new' => empty($organization),
                    'organization_id' => $organization ? (int)$organization['ID'] : null,
                ],
            ];
        } catch (\Exception $e) {
            $this->addError(new Error('Ошибка при проверке ИНН: ' . $e->getMessage()));
            
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => false,
            ];
        }
    }

    /**
     * Получить информацию об организации по ИНН
     * 
     * @param string $inn ИНН организации
     * @return array
     */
    public function getInfoAction(string $inn): array
    {
        try {
            $inn = trim($inn);
            
            // Валидация формата ИНН
            if (!preg_match('/^\d{10}$|^\d{12}$/', $inn)) {
                return [
                    'status' => 'error',
                    'message' => 'Неверный формат ИНН. Введите 10 или 12 цифр.',
                ];
            }

            $organization = OrganizationService::findByInnOrName($inn, null);

            if ($organization) {
                $props = $organization['PROPERTIES'] ?? [];

                $fileValue = $props[OrganizationService::PROP_FILE]['VALUE'] ?? null;
                $fileId = self::extractFileId($fileValue);

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

            // Запрос к DaData API
            $response = Request::requestApi(
                'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party',
                true,
                Config::API_KEY,
                [
                    'query' => $inn,
                    'branch_type' => 'MAIN'
                ]
            );

            if (empty($response['suggestions'])) {
                return [
                    'status' => 'error',
                    'message' => 'Организация с таким ИНН не найдена в базе данных ЕГРЮЛ',
                ];
            }

            return [
                'status' => 'success',
                'data' => [
                    'source' => 'dadata',
                    'suggestions' => $response['suggestions'] ?? [],
                ],
            ];
        } catch (\Exception $e) {
            $this->addError(new Error('Ошибка при получении данных: ' . $e->getMessage()));
            
            return [
                'status' => 'error',
                'message' => 'Ошибка при получении данных организации: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Получить ID файла из значения свойства
     *
     * @param mixed $value Значение свойства
     * @return int
     */
    private static function extractFileId(mixed $value): int
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
}

