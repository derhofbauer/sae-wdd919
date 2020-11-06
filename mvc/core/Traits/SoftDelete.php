<?php

namespace Core\Traits;

use Core\Database;

/**
 * Trait SoftDelete
 *
 * Dieser Trait überschreibt einige Methoden des BaseModel, wenn Softdeletes verwendet werden sollen.
 *
 * @package Core\Traits
 */
trait SoftDelete
{

    /**
     * Den zum aktuellen Objekt gehörigen Datensatz in der Datenbank als gelöscht markieren (Vgl. Softdelete).
     *
     * @return array|bool|mixed
     */
    public function delete ()
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
        $result = $db->query("UPDATE $tableName SET deleted_at = ? WHERE id = ?", [
            'i:deleted_at' => time(),
            'i:id' => $this->id
        ]);

        /**
         * Result zurückgeben
         */
        return $result;
    }

    /**
     * Alle Datensätze aus der Datenbank abfragen.
     *
     * Die beiden Funktionsparameter bieten die Möglichkeit die Daten, die abgerufen werden, nach einer einzelnen Spalte
     * aufsteigend oder absteigend direkt über MySQL zu sortieren. Sortierungen sollten, sofern möglich, über die
     * Datenbank durchgeführt werden, weil das wesentlich performanter ist als über PHP.
     * Dabei werden als gelöscht markierte Datensätze nicht abgerufen.
     *
     * @param string $orderbBy
     * @param string $direction
     *
     * @return array
     */
    public static function all (string $orderbBy = '', string $direction = 'ASC'): array
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
         *
         * Wurde in den Funktionsparametern eine Sortierung definiert, so wenden wir sie hier an, andernfalls rufen wir
         * alles ohne sortierung ab.
         */
        if (empty($orderbBy)) {
            $result = $db->query("SELECT * FROM $tableName WHERE deleted_at IS NULL");
        } else {
            $result = $db->query("SELECT * FROM $tableName WHERE deleted_at IS NULL ORDER BY $orderbBy $direction");
        }

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

}
