<?php

namespace App\Models;

use Core\Database;
use Core\Models\BaseModel;

/**
 * Diese Klasse stellt ein Spezial Model dar, weil das Model keine eigene Tabelle hat sondern sich auf die Wishlist
 * Tabelle bezieht, die auch eine spezielle Tabelle ist. Es handelt sich dabei um eine normale Mapping Tabelle mit einem
 * speziellen Namen.
 */
class WishlistItem extends BaseModel
{
    /**
     * Der Name der Tabelle kann nicht aus dem Namen der Klasse berechnet werden, daher müssen wir ihn selbst definieren.
     */
    const TABLENAME = 'wishlist';

    /**
     * Properties definieren.
     */
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
         * Nachdem wir das Objekt nicht aktualisieren brauchen, reicht uns hier ein INSERT Query.
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
     * Prüfen, ob ein Produkt bereits auf der Wishlist eines/einer User*in ist.
     *
     * @param int $user_id
     * @param int $product_id
     *
     * @return bool
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
         *
         * Ein COUNT Query wie dieser wird immer nur eine Zeile als Ergebnis zurück geben.
         */
        $result = $db->query("SELECT COUNT(*) as count FROM $tableName WHERE user_id = ? AND product_id = ?", [
            'i:user_id' => $user_id,
            'i:product_id' => $product_id
        ]);

        /**
         * Prüfen ob irgendetwas mit dem Query nicht funktioniert hat.
         */
        if (!empty($result)) {
            /**
             * Zurückgeben, ob der count Wert größer ist als 0 oder nicht. Wenn der Wert größer ist, wurde mindestens
             * ein Produkt auf der Wishlist gefunden, dass die übergebene ID hat.
             */
            return (int)$result[0]['count'] > 0;
        }

        /**
         * Hat mit dem Query etwas nicht funktioniert, so geben wir false zurück, damit das Produkt auf die Wishlist
         * geschrieben werden kann. Im schlimmsten Fall haben wir dann doppelte Einträge, aber zumindest keinen
         * Datenverlust.
         */
        return false;
    }

    /**
     * Alle WishlistItems für eine Kombination von User*in und Produkt abfragen.
     *
     * @param int $user_id
     * @param int $product_id
     *
     * @return array
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
