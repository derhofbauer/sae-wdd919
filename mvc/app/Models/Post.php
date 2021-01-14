<?php

namespace App\Models;

use Core\Database;
use Core\Models\BaseModel;

/**
 * Class Post
 *
 * @package App\Models
 * @todo    : comment
 */
class Post extends BaseModel
{

    /**
     * Wir definieren alle Spalten aus der Tabelle mit den richtigen Datentypen.
     */
    public int $id;
    public string $title = '';
    public string $content = '';
    public int $user_id;
    public int $crdate;
    public int $tstamp;

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
        $this->title = (string)$data['title'];
        $this->content = (string)$data['content'];
        $this->user_id = (int)$data['user_id'];
        $this->crdate = (int)$data['crdate'];
        $this->tstamp = (int)$data['tstamp'];
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
            return $db->query("UPDATE $tableName SET title = ?, content = ?, user_id = ? WHERE id = ?", [
                's:title' => $this->title,
                's:content' => $this->content,
                'i:user_id' => $this->user_id,
                'i:id' => $this->id
            ]);
        } else {
            $result = $db->query("INSERT INTO $tableName SET title = ?, content = ?, user_id = ?", [
                's:title' => $this->title,
                's:content' => $this->content,
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

    /**
     * Content bei Bedarf kürzen und dann Zeilenumbrüche in <br>-Tags umformen.
     *
     * @param int $maxLength
     *
     * @return string
     * @todo: comment
     */
    public function getContent (int $maxLength = 0): string
    {
        $content = $this->content;
        if ($maxLength > 0) {
            $content = substr($content, 0, $maxLength);

            if (strlen($content) !== strlen($this->content)) {
                $content .= ' …';
            }
        }
        $content = trim($content);

        return nl2br($content);
    }

    /**
     * @todo: comment
     * @return string
     */
    public function getSlug (): string
    {
        $replaceWithNothing = ".!?:;#+*";
        $needles = str_split($replaceWithNothing, 1);
        $slug = str_replace($needles, '', $this->title);
        $slug = str_replace(' ', '-', $slug);
        $slug = strtolower($slug);

        return $slug;
    }

    /**
     * Posts durchsuchen
     *
     * @param string $searchterm
     *
     * @return array
     */
    public static function search (string $searchterm): array
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
         * Hier führen wird eine Volltextsuche ausgeführt. MySQL unterstützt dabei nur eine sehr grundlegende Suche,
         * die für unsere Zwecke aber ausreichen ist. Im MATCH() Statement werden die Spalten angegeben, die durchsucht
         * werden sollen. Im AGAINST() Statement wird der Suchbegriff und der Suchmodus übergeben. Wichtig dabei ist,
         * dass die Spalten, die durchsucht werden sollen, in der Datenbank als kombinierter FULLTEXT-Index definiert
         * sind.
         */
        $result = $db->query("SELECT * FROM $tableName WHERE MATCH(title, content) AGAINST(? IN BOOLEAN MODE)", [
            's:term' => $searchterm
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
