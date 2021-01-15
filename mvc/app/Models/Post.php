<?php

namespace App\Models;

use Core\Database;
use Core\Models\BaseModel;

/**
 * Class Post
 *
 * @package App\Models
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
    /**
     * @var int Zeitstempel der Erstellung.
     */
    public int $crdate;
    /**
     * @var int Zeitstempel der letzten Aktualisierung.
     */
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
     */
    public function getContent (int $maxLength = 0): string
    {
        /**
         * Content kopieren, damit wir ihn verändern können, ohne das Original zu verändern.
         */
        $content = $this->content;

        /**
         * Wenn eine $maxLength gesetzt ist ...
         */
        if ($maxLength > 0) {
            /**
             * ... holen wir den Substring aus dem $content.
             */
            $content = substr($content, 0, $maxLength);

            /**
             * Wenn die Länge Substrings ungleich der Länge des originalen Strings ist, so hängen wir hinten eine
             * horizontale Ellipse an, weil das bedeutet, dass die substr()-Funktion den Content gekürzt hat.
             */
            if (strlen($content) !== strlen($this->content)) {
                $content .= ' …';
            }
        }
        /**
         * Im Anschluss trimmen wir den Content noch ...
         */
        $content = trim($content);

        /**
         * ... und geben eine Version, bei der alle Zeilenumbrüche mit <br>-Tags ersetzt sind zurück.
         *
         * Die nl2br() Funktion ersetzt Zeilenumbrüche mit <br>-Tags. Wir wandeln Zeilenumbrüche ganz am Schluss erst um,
         * weil es sonst passieren könnte, dass ein <br>-Tag durch die substr()-Funktion auseinander geschnitten wird.
         */
        return nl2br($content);
    }

    /**
     * Slug aus dem Titel des Posts berechnen.
     *
     * Wir werden den Slug nur für die URL verwenden, damit direkt aus der URL der Titel des Posts erkennbar ist, wenn
     * die URL bspw. jemandem geschickt wird.
     *
     * @return string
     */
    public function getSlug (): string
    {
        /**
         * Liste der Zeichen erstellen, die aus dem String gelöscht werden sollen.
         */
        $replaceWithNothing = ".!?:;#+*";
        /**
         * Liste der Zeichen in einen Array umformen. Die str_split() Funktion teilt einen String in einzelne Strings
         * einer gewissen länge. In unserem Fall teilen wir den String in Strings mit der Länge 1, dadurch haben wir
         * einen Array, in dem jedes Feld ein Zeichen beinhaltet.
         */
        $needles = str_split($replaceWithNothing, 1);
        /**
         * Die str_replace() Funktion akzeptiert als ersten Parameter auch einen Array. In diesem Fall werden alle
         * Strings aus diesem Array mit dem zweiten Parameter ersetzt.
         */
        $slug = str_replace($needles, '', $this->title);
        /**
         * Nun ersetzen wir Leerzeichen mit Bindestrichen ...
         */
        $slug = str_replace(' ', '-', $slug);
        /**
         * ... und lowercasen den String.
         */
        $slug = strtolower($slug);

        /**
         * Generierten Slug zurückgeben.
         */
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
