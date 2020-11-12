<?php

namespace App\Models;

use Core\Database;
use Core\Models\BaseModel;

/**
 * Class Order
 *
 * @package App\Models
 */
class Order extends BaseModel
{

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $crdate;
    public int $user_id;
    public int $address_id;
    public int $payment_id;
    public $products = [];
    public string $status = 'open';

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
    public function fill (array $data)
    {
        $this->id = $data['id'];
        $this->crdate = $data['crdate'];
        $this->user_id = $data['user_id'];
        $this->address_id = $data['address_id'];
        $this->payment_id = $data['payment_id'];
        $this->products = $data['products'];
        $this->status = $data['status'];
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
         * @todo: comment (double encoding!!)
         */
        $products = $this->products;
        if (is_array($this->products)) {
            $products = json_encode($this->products);
        }

        /**
         * Query ausführen.
         *
         * Hier ist es essenziell, dass die Werte in dem zweiten Funktionsparameter von $db->query() in der selben
         * Reihenfolge angegeben werden, wie sie im Query auftreten.
         *
         * Je nachdem, ob das aktuellen Objekt bereits eine ID hat oder nicht, speichern wir Änderungen oder eine neuen
         * Datensatz in die Datenbank. Dadurch können wir die save() Methode verwenden egal ob wir eine Änderung oder
         * ein neues Objekt speichern wollen.
         */
        if (!empty($this->id)) {
            return $db->query("UPDATE $tableName SET user_id = ?, address_id = ?, payment_id = ?, products = ?,  status = ? WHERE id = ?", [
                'i:user_id' => $this->user_id,
                'i:address_id' => $this->address_id,
                'i:payment_id' => $this->payment_id,
                's:products' => $products,
                's:status' => $this->status,
                'i:id' => $this->id,
            ]);
        } else {
            $result = $db->query("INSERT INTO $tableName SET user_id = ?, address_id = ?, payment_id = ?, products = ?,  status = ?, crdate = CURRENT_TIMESTAMP()", [
                'i:user_id' => $this->user_id,
                'i:address_id' => $this->address_id,
                'i:payment_id' => $this->payment_id,
                's:products' => $products,
                's:status' => $this->status
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
    }

    /**
     * @return array
     * @todo: comment
     */
    public static function getOpenOrders ()
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
        $result = $db->query("SELECT * FROM $tableName WHERE status != 'delivered' AND status != 'storno' ORDER BY crdate ASC");

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
     * @return array|mixed
     * @todo: comment
     */
    public function getProducts ()
    {
        if (is_string($this->products)) {
            return json_decode($this->products);
        }
        return $this->products;
    }
}