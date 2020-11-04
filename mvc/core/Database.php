<?php

namespace Core;

use mysqli;

/**
 * Class Database
 *
 * @package Core
 */
class Database
{
    private object $link;
    private object $stmt;
    /**
     * Hier können wir keinen Typ angeben, weil $lastResult sowohl ein boolscher Wert als auch ein mysql_result sein
     * kann.
     */
    private $lastResult;
    private array $data;

    /**
     * Database constructor.
     */
    public function __construct ()
    {
        /**
         * Datenbankverbindung aufbauen
         */
        $this->link = new mysqli(
            Config::get('database.host'),
            Config::get('database.username'),
            Config::get('database.password'),
            Config::get('database.dbname'),
            Config::get('database.port', 3306),
            Config::get('database.socket')
        );

        /**
         * Charset für dei Daten setzen. Umlaute und sprachspezifische Sonderzeichen werden so relativ problemlos
         * gespeichert und übertragen.
         */
        $this->link->set_charset('utf8');
    }

    /**
     * Anwendung:
     *  + $database->query('SELECT * FROM users WHERE id = ?', ['i:id' => $id]);
     *  + $database->query('SELECT * FROM users WHERE id = ? AND email = ?', ['i:id' => $id, 's:email' => $email]);
     *
     * @param string $query
     * @param array  $params
     *
     * @return array|mixed
     */
    public function query (string $query, array $params = [])
    {
        /**
         * Wenn keine Parameter in $params übergeben wurden an diese Funktion, dann schicken wir den Query einfach so ab,
         * weil wir ihn nicht preparen müssen.
         */
        if (empty($params)) {
            $this->lastResult = $this->link->query($query);
        } else {
            /**
             * Prepared Statement initialisieren
             */
            $this->stmt = $this->link->prepare($query);

            /**
             * Variablen vorbereiten
             */
            $paramTypes = [];
            $paramValues = [];

            /**
             * Funktionsparameter $params durchgehen und die obenstehenden Variablen befüllen.
             */
            foreach ($params as $typeAndName => $value) {
                $paramTypes[] = explode(':', $typeAndName)[0];

                /**
                 * $stmt->bind_param() erwartet eine Referenz als Werte und nicht eine normale Variable, daher müssen
                 * wir in unseren $paramValues Array Referenzen pushen. Das ist eine seltsame aber begründete Eigenheit
                 * von bind_param().
                 */
                $_value = $value;
                $paramValues[] = &$_value;
                unset($_value);
                /**
                 * $paramTypes:  ['i', 'i']
                 * $paramValues: [&1, &0]
                 */
            }

            /**
             * $stmt->bind_param() verlangt als ersten Parameter einen String mit den Typen aller folgenden Parameter.
             * Wir müssen also aus dem Array $paramTypes einen String erstellen.
             */
            $paramString = implode('', $paramTypes);

            /**
             * Gemeinsames Array aus $paramString und $paramValues erstellen, weil $stmt->bind_param() als ersten
             * Parameter einen String aller Typen und als folgende Parameter die einzelnen Werte erwartet.
             *
             * s. https://www.php.net/manual/en/mysqli-stmt.bind-param.php
             */
            array_unshift($paramValues, $paramString);

            /**
             * Query fertig "preparen": $stmt->bind_param() mit den entsprechenden Werten ausführen; aber nur, wenn es
             * sich um einen MySQL Query mit Parametern handelt (s. if-Statement).
             */
            call_user_func_array([$this->stmt, 'bind_param'], $paramValues);

            /**
             * Query an den MySQL Server schicken.
             */
            $this->stmt->execute();
            /**
             * Ergebnis aus dem Query holen.
             */
            $this->lastResult = $this->stmt->get_result();

            /**
             * Ist das Ergebnis false, was bei allen Queries außer SELECT-Queries der Fall ist, ...
             */
            if ($this->lastResult === false) {
                /**
                 * ... so prüfen wir ob ein Fehler aufgetreten ist oder nicht. Ist die Fehlernummer (errno) gleich 0,
                 * ist kein Fehler aufgetreten und wir geben den positiven Wert true zurück, andernfalls false.
                 */
                if ($this->stmt->errno === 0) {
                    $this->lastResult = true;
                } else {
                    $this->lastResult = false;
                }
            }
        }

        /**
         * Das Ergebnis ist idR. nur dann ein bool'scher Wert, wenn ein Fehler auftritt oder ein Query ohne Ergebnis
         * ausgeführt wird (z.B. DELETE).
         */
        if (is_bool($this->lastResult)) {
            return $this->lastResult;
        }

        /**
         * Tritt kein Fehler auf, erstellen wir ein assoziatives Array ...
         */
        $this->data = $this->lastResult->fetch_all(MYSQLI_ASSOC);

        /**
         * ... und geben es zurück.
         */
        return $this->data;
    }

    /**
     * $this->link ist private, damit nur die Database Klasse selbst diese Property verändern kann. Es kann aber
     * passieren, dass wir Funktionalitäten des \mysqli Objekts außerhalb der Database Klasse brauchen, daher bieten
     * wir für unsere Framework Anwender*innen hier die Möglichkeit sich das \mysqli Objekt aus der Database Klasse
     * abzurufen. Eine Veränderung des Rückgabewerts von $this->getLink() verändert aber nicht $this->link, wodurch
     * $this->link weiterhin nur von der Database Klasse selbst veränderbar ist.
     *
     * @return mysqli
     */
    public function getLink (): object
    {
        return $this->link;
    }

    /**
     * Gibt die von MySQL generierte ID aus dem letzten INSERT Query zurück. War der letzte Query kein INSERT Query,
     * wird 0 zurück gegeben.
     *
     * s. https://www.php.net/mysqli.insert_id
     *
     * @return int
     */
    public function getInsertId ()
    {
        /**
         * $this->link ist ein \mysqli Objekt und hat daher eine insert_id Property. $mysqli->insert_id beinhaltet also
         * die ID, die bei einem INSERT Query von MySQL generiert wurde.
         */
        return $this->link->insert_id;
    }

    /**
     * @return object
     */
    public function getLastResult ()
    {
        return $this->lastResult;
    }

    /**
     * @return array
     */
    public function getData (): array
    {
        return $this->data;
    }

    /**
     * Database destructor.
     */
    public function __destruct ()
    {
        $this->link->close();
    }
}
