<?php

namespace App\Models;

use Core\Database;
use Core\Models\BaseModel;

/**
 * Class Payment
 *
 * @package App\Models
 * @todo    : comment
 */
class Payment extends BaseModel
{

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $name = '';
    public string $number = '';
    public string $expires = '';
    public string $ccv = '';
    public int $user_id;

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
        $this->id = (int)$data['id'];
        $this->name = (string)$data['name'];
        $this->number = (string)$data['number'];
        $this->expires = (string)$data['expires'];
        $this->ccv = (string)$data['ccv'];
        $this->user_id = (int)$data['user_id'];
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
         */
        if (!empty($this->id)) {
            return $db->query("UPDATE $tableName SET name = ?, number = ?, expires = ?, ccv = ?,  user_id = ? WHERE id = ?", [
                's:name' => $this->name,
                's:number' => $this->number,
                's:expires' => $this->expires,
                's:ccv' => $this->ccv,
                'i:user_id' => $this->user_id,
                'i:id' => $this->id,
            ]);
        } else {
            $result = $db->query("INSERT INTO $tableName SET name = ?, number = ?, expires = ?, ccv = ?,  user_id = ?", [
                's:name' => $this->name,
                's:number' => $this->number,
                's:expires' => $this->expires,
                's:ccv' => $this->ccv,
                'i:user_id' => $this->user_id
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
}
