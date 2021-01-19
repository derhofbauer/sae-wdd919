<?php

namespace App\Models;

use Core\Database;
use Core\Models\BaseModel;

/**
 * Class WishlistItem
 *
 * @todo: comment (Spezialmodel, weil für Mapping Tabelle)
 */
class WishlistItem extends BaseModel
{
    const TABLENAME = 'wishlist';

    public int $id;
    public int $user_id;
    public int $product_id;

    /**
     * Der Konstruktor befüllt das Objekt, sofern Daten übergeben worden sind.
     *
     * @param array $data
     */
    public function __construct (array $data = [])
    {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    /**
     * Diese Methode ermöglicht es uns, die Daten aus einem Datenbankergebnis in nur einer Zeile direkt in ein Objekt
     * zu füllen. Bei der Instanziierung kann über den Konstruktor auch diese Methode verwendet werden.
     *
     * @param array $data
     */
    protected function fill (array $data)
    {
        $this->id = (int)$data['id'];
        $this->user_id = (int)$data['user_id'];
        $this->product_id = (int)$data['product_id'];
    }

    /**
     * Aktuelle Properties dieses Objekts wieder in die Datenbank zurückspeichern.
     */
    public function save ()
    {
        /**
         * Hier rufen wir die save() Methode der Elternklasse auf - in diesem Fall BaseModel. Würden wir das nicht tun,
         * dann würde Product::save() die BaseModel::save() Methode überschreiben, so erweitern wir die Methode quasi.
         */
        parent::save();

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
         * Hier ist es essenziell, dass die Werte in dem zweiten Funktionsparameter von $db->query() in der selben
         * Reihenfolge angegeben werden, wie sie im Query auftreten.
         *
         * Je nachdem, ob das aktuellen Objekt bereits eine ID hat oder nicht, speichern wir Änderungen oder eine neuen
         * Datensatz in die Datenbank. Dadurch können wir die save() Methode verwenden egal ob wir eine Änderung oder
         * ein neues Objekt speichern wollen.
         *
         * @todo: comment
         */
        $result = $db->query("INSERT INTO $tableName SET user_id = ?, product_id = ?", [
            'i:user_id' => $this->user_id,
            'i:product_id' => $this->product_id
        ]);

        /**
         * Neu generierte ID abrufen. (vgl. auto_increment)
         */
        $newId = $db->getInsertId();

        /**
         * Handelt es sich um einen Integer und somit nicht um einen Fehler, aktualisieren wir das aktuelle Objekt.
         */
        if (is_int($newId)) {
            $this->id = $newId;
        }

        /**
         * Ergebnis zurück geben.
         */
        return $result;
    }

    /**
     * @param int $user_id
     * @param int $product_id
     *
     * @return bool
     * @todo: comment
     */
    public static function isOnUsersWishlist (int $user_id, int $product_id): bool
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
        $result = $db->query("SELECT COUNT(*) as count FROM $tableName WHERE user_id = ? AND product_id = ?", [
            'i:user_id' => $user_id,
            'i:product_id' => $product_id
        ]);

        if (!empty($result)) {
            return (int)$result[0]['count'] > 0;
        }

        return false;
    }

    /**
     * @param int $user_id
     * @param int $product_id
     *
     * @return array
     * @todo: comment
     */
    public static function findByUserIdAndProductId (int $user_id, int $product_id): array
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
        $result = $db->query("SELECT * FROM $tableName WHERE user_id = ? AND product_id = ?", [
            'i:user_id' => $user_id,
            'i:product_id' => $product_id
        ]);

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
