<?php

namespace App\Models;

use Core\Database;
use Core\Models\BaseUser;
use Core\Traits\SoftDelete;

class User extends BaseUser
{

    use SoftDelete;

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $email;
    public string $password;
    public string $username;
    public string $firstname;
    public string $lastname;
    public bool $is_admin = false;
    public int $deleted_at;

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
        $this->email = trim((string)$data['email']);
        $this->password = trim((string)$data['password']);
        $this->username = trim((string)$data['username']);
        $this->firstname = trim((string)$data['firstname']);
        $this->lastname = trim((string)$data['lastname']);
        $this->is_admin = (bool)$data['is_admin'];
        $this->deleted_at = (int)$data['deleted_at'];
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
            return $db->query("UPDATE $tableName SET email = ?, password = ?, username = ?, firstname = ?, lastname = ?, is_admin = ?, deleted_at = ? WHERE id = ?", [
                's:email' => $this->email,
                's:password' => $this->password,
                's:username' => $this->username,
                's:firstname' => $this->firstname,
                's:lastname' => $this->lastname,
                'i:is_admin' => $this->is_admin,
                'i:deleted_at' => $this->deleted_at,
                'i:id' => $this->id
            ]);
        } else {
            $result = $db->query("INSERT INTO $tableName SET email = ?, password = ?, username = ?, firstname = ?, lastname = ?, is_admin = ?", [
                's:email' => $this->email,
                's:password' => $this->password,
                's:username' => $this->username,
                's:firstname' => $this->firstname,
                's:lastname' => $this->lastname,
                'i:is_admin' => $this->is_admin
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
