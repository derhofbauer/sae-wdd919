<?php

namespace App\Models;

use Core\Database;
use Core\Models\BaseModel;

/**
 * Class Address
 *
 * @package App\Models
 */
class Category extends BaseModel
{

    /**
     * Normalerweise wird der Name einer Tabelle automatisch anhand des Klassennamens berechnet, indem ein s an den
     * Klassennamen gehängt wird um den Plural zu bilden. Bei 'address' funktioniert das nicht. Daher nutzen wir eine
     * Funktionalität, die wir ganz am Anfang vorbereitet haben. Wenn eine Klasse eine Konstante TABLENAME definiert, so
     * wird der Wert dieser Konstante verwendet und nicht ein berechneter Tabellenname.
     */
    const TABLENAME = 'categories';

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $name = '';

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
            return $db->query("UPDATE $tableName SET name = ? WHERE id = ?", [
                's:name' => $this->name,
                'i:id' => $this->id,
            ]);
        } else {
            $result = $db->query("INSERT INTO $tableName SET name = ?", [
                's:name' => $this->name
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
     * Diese Methode ist eine Mischung zwischen der BaseModel::find() und der BaseModel::all() Methode, weil anhand
     * eines Wertes gesucht wird, aber mehr als ein Datensatz zurückkommen können.
     *
     * @param int $productId
     *
     * @return array
     */
    public static function findByProductId (int $productId): array
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
        $result = $db->query("SELECT categories.* FROM products_categories_mm JOIN categories ON products_categories_mm.category_id = categories.id WHERE products_categories_mm.product_id = ?", [
            'i:product_id' => $productId
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
