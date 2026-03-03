<?php

namespace Acroweb\Mage\Service;

use CUser;
use Bitrix\Main\Application;

class Favorites
{
    /**
     * @param int|string $id
     * @return array|false
     */
    public static function add(int|string $id): array|false
    {
        if ((int)$id <= 0) {
            return false;
        }

        global $USER;
        if ($USER->IsAuthorized()) {
            $arFavorites = self::getForAuthorized();
            if (!in_array($id, $arFavorites)) {
                $arFavorites[] = intval($id);
                $user = new CUser;
                $user->Update($USER->GetID(), ["UF_FAVORITES" => json_encode($arFavorites)]);
            }
        } else {
            $session = Application::getInstance()->getSession();
            $arFavorites = self::getForNoAuthorized($session);

            if (!in_array($id, $arFavorites)) {
                $arFavorites[] = intval($id);
                $session->set('FAVORITES', json_encode($arFavorites));
            }
        }

        return $arFavorites;
    }

    /**
     * @param int|string $id
     * @return array|false
     */
    public static function del(int|string $id): array|false
    {
        if ((int)$id <= 0) {
            return false;
        }

        global $USER;
        if ($USER->IsAuthorized()) {
            $arFavorites = self::getForAuthorized();
            if (in_array($id, $arFavorites)) {
                $arFavorites = self::removeIdForArray($id, $arFavorites);
                $user = new CUser;
                $user->Update($USER->GetID(), ["UF_FAVORITES" => json_encode($arFavorites)]
                );
            }
        } else {
            $session = Application::getInstance()->getSession();
            $arFavorites = self::getForNoAuthorized($session);

            if (in_array($id, $arFavorites)) {
                $arFavorites = self::removeIdForArray($id, $arFavorites);
                $session->set('FAVORITES', json_encode($arFavorites));
            }
        }

        return $arFavorites;
    }

    /**
     * @return array
     */
    public static function get(): array
    {
        global $USER;

        return $USER->IsAuthorized() ? self::getForAuthorized() : self::getForNoAuthorized();
    }

    /**
     * @param int|string $id
     * @param array $arFavorites
     * @return array
     */
    public static function removeIdForArray(int|string $id, array $arFavorites): array
    {
        $temp = [];
        foreach ($arFavorites as $favorId) {
            if ($favorId != $id) {
                $temp[] = $favorId * 1;
            }
        }

        return $temp;
    }

    /**
     * @return array
     */
    public static function getForAuthorized(): array
    {
        global $USER;
        $arUser = CUser::GetList(
            ["sort"],
            ["asc"],
            ["ID" => $USER->GetID()],
            ["SELECT" => ["UF_FAVORITES"]]
        )->Fetch();

        $arFavorites = json_decode($arUser["UF_FAVORITES"], true);
        if (empty($arFavorites) && !is_array($arFavorites)) {
            $arFavorites = [];
        }
        // Преобразуем в индексированный массив значений для корректной работы с JavaScript
        return array_values($arFavorites);
    }

    /**
     * @param $session
     * @return array
     */
    public static function getForNoAuthorized($session = false): array
    {
        if (!$session) {
            $session = Application::getInstance()->getSession();
        }

        if (isset($session['FAVORITES'])) {
            $arFavorites = json_decode($session['FAVORITES'], true);
        } else {
            $arFavorites = [];
        }

        // Преобразуем в индексированный массив значений для корректной работы с JavaScript
        return array_values($arFavorites);
    }

    /**
     * Очищает все избранное для текущего пользователя
     * @return bool
     */
    public static function deleteAll(): bool
    {
        global $USER;
        
        if ($USER->IsAuthorized()) {
            // Для авторизованного пользователя очищаем поле в БД
            $user = new CUser();
            return (bool)$user->Update($USER->GetID(), ["UF_FAVORITES" => '']);
        } else {
            // Для неавторизованного пользователя очищаем сессию
            $session = Application::getInstance()->getSession();
            $session->remove('FAVORITES');
            return true;
        }
    }

    public static function eventHandler($arUser)
    {
        $session = Application::getInstance()->getSession();
        $arSession = self::getForNoAuthorized($session);
        if (!empty($arSession) && is_array($arSession)) {
            $arFavorites = array_unique(array_merge($arSession, self::getForAuthorized()));
            $user = new CUser;
            $user->Update($arUser['user_fields']["ID"], ["UF_FAVORITES" => json_encode($arFavorites)]);
            $session->remove('FAVORITES');
        }

        return true;
    }
}