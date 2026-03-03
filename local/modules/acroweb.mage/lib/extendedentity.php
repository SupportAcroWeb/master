<?php
namespace Acroweb\Mage;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\ORM\Entity;

abstract class ExtendedEntity extends DataManager {
    
    abstract public static function getParentEntity(): Entity;

    public static function getAdditionalFields(): array
    {
        return [];
    }

    public static function getEntity(): Entity
    {
        $entity = clone static::getParentEntity();
        //$entity = clone call_user_func([static::class, 'getParentEntity']);

        foreach(static::getAdditionalFields() as $field)
        {
            $entity->addField($field);
        }

        return $entity;
    }

    public static function query(): Query
    {
        $entity = static::getEntity();
        return new Query($entity);
    }
}