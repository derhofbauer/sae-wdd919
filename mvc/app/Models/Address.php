<?php

namespace App\Models;

use Core\Database;
use Core\Models\BaseModel;

/**
 * Class Payment
 *
 * @package App\Models
 */
class Address extends BaseModel
{

    /**
     * Normalerweise wird der Name einer Tabelle automatisch anhand des Klassennamens berechnet, indem ein s an den
     * Klassennamen gehängt wird um den Plural zu bilden. Bei 'address' funktioniert das nicht. Daher nutzen wir eine
     * Funktionalität, die wir ganz am Anfang vorbereitet haben. Wenn eine Klasse eine Konstante TABLENAME definiert, so
     * wird der Wert dieser Konstante verwendet und nicht ein berechneter Tabellenname.
     */
    const TABLENAME = 'addresses';

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public int $user_id;
    public string $country = '';
    public string $city = '';
    public string $zip = '';
    public string $street = '';
    public string $street_nr = '';
    public string $extra = '';

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
        $this->user_id = (int)$data['user_id'];
        $this->country = (string)$data['country'];
        $this->city = (string)$data['city'];
        $this->zip = (string)$data['zip'];
        $this->street = (string)$data['street'];
        $this->street_nr = (string)$data['street_nr'];
        $this->extra = (string)$data['extra'];
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
            return $db->query("UPDATE $tableName SET user_id = ?, country = ?, city = ?, zip = ?,  street = ?, street_nr = ?, extra = ? WHERE id = ?", [
                'i:user_id' => $this->user_id,
                's:country' => $this->country,
                's:city' => $this->city,
                's:zip' => $this->zip,
                's:street' => $this->street,
                's:street_nr' => $this->street_nr,
                's:extra' => $this->extra,
                'i:id' => $this->id,
            ]);
        } else {
            $result = $db->query("INSERT INTO $tableName SET user_id = ?, country = ?, city = ?, zip = ?,  street = ?, street_nr = ?, extra = ?", [
                'i:user_id' => $this->user_id,
                's:country' => $this->country,
                's:city' => $this->city,
                's:zip' => $this->zip,
                's:street' => $this->street,
                's:street_nr' => $this->street_nr,
                's:extra' => $this->extra
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
