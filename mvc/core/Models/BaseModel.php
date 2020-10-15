<?php

namespace Core\Models;

use Core\Database;

/**
 * Class BaseModel
 *
 * @package Core\Models
 * @todo    : comment
 */
abstract class BaseModel
{
    /**
     * @param array $data
     */
    protected abstract function fill (array $data);

    /**
     * Alle Datensätze aus der Datenbank abfragen
     */
    public static function all ()
    {
        $db = new Database();
        $tableName = self::getTableNameFromClassName();
        $result = $db->query("SELECT * FROM $tableName");

        $objects = [];
        foreach ($result as $object) {
            $calledClass = get_called_class();
            $objects[] = new $calledClass($object);
        }

        return $objects;
    }

    /**
     * @param int $id
     *
     * @return false|mixed
     */
    public static function find (int $id)
    {
        $db = new Database();
        $tableName = self::getTableNameFromClassName();
        $result = $db->query("SELECT * FROM $tableName WHERE id = ?", ['i:id' => $id]);

        if (!empty($result)) {
            $calledClass = get_called_class();
            return new $calledClass($result[0]);
        }
        return false;
    }

    private static function getTableNameFromClassName ()
    {
        $calledClass = get_called_class();

        if (defined("$calledClass::TABLENAME")) {
            return $calledClass::TABLENAME;
        }

        $particles = explode('\\', $calledClass);
        $className = array_pop($particles);
        $tableName = strtolower($className) . 's';

        return $tableName;
    }
}
