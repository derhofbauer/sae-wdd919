<?php

namespace Core\Models;

use Core\Database;

/**
 * Class BaseModel
 *
 * @package Core\Models
 */
abstract class BaseModel
{
    /**
     * Hier definieren wir, dass eine Klasse, die BaseModel erweitert, eine fill() Methode haben MUSS.
     *
     * @param array $data
     */
    protected abstract function fill (array $data);

    /**
     * Alle Datensätze aus der Datenbank abfragen
     *
     * @return array
     */
    public static function all (): array
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $db = new Database();

        /**
         * Tabellennamen berechnen.
         */
        $tableName = self::getTableNameFromClassName();

        /**
         * Query ausführen.
         */
        $result = $db->query("SELECT * FROM $tableName");

        /**
         * Ergebnis-Array vorbereiten.
         */
        $objects = [];

        /**
         * Ergebnisse des Datenbank-Queries durchgehen und jeweils ein neues Objekt erzeugen.
         */
        foreach ($result as $object) {
            /**
             * Auslesen, welche Klasse aufgerufen wurde und ein Objekt dieser Klasse erstellen und in den Ergebnis-Array
             * speichern.
             */
            $calledClass = get_called_class();
            $objects[] = new $calledClass($object);
        }

        /**
         * Ergebnisse zurückgeben.
         */
        return $objects;
    }

    /**
     * Einen Datensatz anhand der ID aus der Datenbank abfragen.
     *
     * @param int $id
     *
     * @return false|mixed
     */
    public static function find (int $id)
    {
        /**
         * Datenbankverbindung herstellen.
         */
        $db = new Database();

        /**
         * Tabellennamen berechnen.
         */
        $tableName = self::getTableNameFromClassName();

        /**
         * Query ausführen.
         */
        $result = $db->query("SELECT * FROM $tableName WHERE id = ?", ['i:id' => $id]);

        /**
         * Wurde ein Datensatz gefunden und gibt es somit Ergebnisse?
         */
        if (!empty($result)) {
            /**
             * Auslesen, welche Klasse aufgerufen wurde und ein Objekt dieser Klasse erstellen und zurückgeben.
             */
            $calledClass = get_called_class();
            return new $calledClass($result[0]);
        }

        /**
         * Es wurde kein Datensatz gefunden.
         */
        return false;
    }

    /**
     * Damit diese abstrakte Klasse für alle Models verwendet werden kann, ist es hilfreich, berechnen zu können, welche
     * Tabelle vermutlich zu dem erweiternden Model gehört.
     *
     * @return string
     */
    protected static function getTableNameFromClassName (): string
    {
        /**
         * Name der aufgerufenen Klasse abfragen.
         */
        $calledClass = get_called_class();

        /**
         * Hat die aufgerufene Klasse eine Konstante TABLENAME?
         */
        if (defined("$calledClass::TABLENAME")) {
            /**
             * Wenn ja, dann verwenden wir den Wert dieser Konstante als Tabellenname.
             */
            return $calledClass::TABLENAME;
        }

        /**
         * Wenn nein, dann holen wir uns den Namen der Klasse ohne Namespace, konvertieren ihn in Kleinbuchstaben und
         * fügen hinten ein s dran. So wird bspw. aus App\Models\Product --> products
         */
        $particles = explode('\\', $calledClass);
        $className = array_pop($particles);
        $tableName = strtolower($className) . 's';

        /**
         * Berechneten Tabellennamen zurückgeben.
         */
        return $tableName;
    }
}
