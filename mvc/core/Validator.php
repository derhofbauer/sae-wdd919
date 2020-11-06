<?php

namespace Core;

/**
 * Der Validator wird von uns als externe Abhändgigkeit betrachtet - eine Library, die jemand anderer geschrieben hat,
 * die wir aber so cool finden, dass wir sie den Anwender*innen unseres MVC Frameworks mitliefern möchten, damit diese
 * nicht jedes Mal eine Validierungs-Logik bauen müssen, sondern schon eine bekommen, die sie verwenden können.
 */
class Validator
{

    /**
     * Definieren der Datentypen, die validiert werden können.
     *
     * @var string[]
     */
    private $types = [
        'text' => '/^[a-zA-Z ]+$/',
        'int' => '/^[\d]+$/',
        'float' => '/^[\d]+[.,]?[\d]{0,}$/',
        'textnum' => '/^[\w .,#]+$/',
        'email' => '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/',
        'url' => '/^http(s)?:\/\/([\w]{1,20}\.)?[a-z0-9-]{2,65}(\.[a-z]{2,10}){1,2}(\/)?$/',
        'tel' => '/^[\d]+$/',
        'checkbox' => '/^(on|true|checked|1)$/i'
    ];

    /**
     * Definieren einer Property, in die alle aufgetretenen Fehler rein gespeichert werden.
     *
     * @var array
     */
    private $errors = [];

    /**
     * @var string
     */
    private $currentName;

    /**
     * Die compare() Methode erlaubt es uns zwei Werte miteinander zu vergleichen.
     *
     * @param $data1
     * @param $data2
     *
     * @return bool
     */
    public function compare ($data1, $data2)
    {
        /**
         * Hier werden Arrays verglichen, weil für $data1 und $data2 jeweils Wert und Label für die Fehlermeldung
         * übergeben werden können. Index 0 wäre jeweils der Wert und Index 1 das Label für die Fehlermeldung.
         */
        if (is_array($data1) && is_array($data2)) {
            if ($data1[0] !== $data2[0]) {
                $this->setError(4, [$data1[1], $data2[1]]);
                return false;
            }
            return true;
        }

        /**
         * Werden keine Arrays übergeben, so prüfen wir die Werte und verwenden sie auch für die Fehlermeldung.
         */
        if ($data1 !== $data2) {
            $this->setError(4, [$data1, $data2]);
            return false;
        }

        return true;
    }

    /**
     * Die validate() Funktion dient dazu, ein Datum mit einer der $filter-Expressions zu validieren. Siehe die folgende
     * Parameter Liste für die Bedeutung der Funktions-Parameter.
     *
     * @param string $data     Das zu validierende Datum (Datum als Singular von Daten)
     * @param string $name     Der Name, der für eine Fehlermeldung verwendet wird
     * @param bool   $required Pflichtfeld?
     * @param string $type     Welchen Typ sollte $data haben?
     * @param null   $min      Mindestlänge für einen String
     * @param null   $max      Maximallänge für einen String
     *
     * @return bool
     */
    public function validate ($data = "", $name = '', $required = false, $type = "text", $min = null, $max = null)
    {
        /**
         * Um später einfach eine Fehlermeldung generieren zu können, setzen wir den aktuellen $name auf eine Property
         * in dem Objekt.
         */
        $this->currentName = $name;

        /**
         * Wenn das Feld $required ist, prüfen wir ob es leer ist und schreiben dann bei Bedarf einen Fehler vom Typ 0.
         */
        if ($required && empty($data)) {
            $this->setError(0);
            return false;
        }

        /**
         * Wenn die String-Repräsentation von $data kürzer ist als $min, schreiben wir einen Fehler vom Typ 1.
         */
        if ($min !== null && strlen($data) < $min) {
            $this->setError(1, $min);
            return false;
        }

        /**
         * Wenn die String-Repräsentation von $data länger ist als $max, schreiben wir einen Fehler vom Typ 2.
         */
        if ($max !== null && strlen($data) > $max) {
            $this->setError(2, $max);
            return false;
        }

        /**
         * Wenn der als Funktionsparameter übergebene Validierungstyp in den unterstützen Tyoen exisiert ...
         */
        if (array_key_exists($type, $this->types)) {

            /**
             * ... dann validieren wir gegen seine Expression und schreiben im Fehlerfall einen Fehler vom Typ 3.
             */
            if (!preg_match($this->types[$type], $data)) {
                $this->setError(3);
                return false;
            }
        } else {
            /**
             * Kommt der $type nicht im $this->types array vor, schauen wir, ob es sich um eine von uns geschriebene
             * Spezialvalidierung handelt.
             */
            if ($type === 'password') {
                /**
                 * Wenn der String in $data gleich ist mit einer Lowercase Version von sich selbst, dann hatte der
                 * String von vorn herein keine Großbuchstaben. Genauso umgekehrt mit der Uppercase Version. Danach
                 * prüfen wir, ob mindestens eine Ziffer im String vorkommt und danach ob mindestens eines der
                 * Sonderzeichen aus der Liste vorkommt. Danach prüfen wir noch ob die Länge des Strings kleiner ist als
                 * 8 Zeichen.
                 *
                 * Die Kriterien für ein Passwort sind also:
                 * Kleinbuchstaben, Großbuchstaben, Ziffern, Sonderzeichen, mind. 8 Zeichen
                 */
                if (
                    $data === strtolower($data) ||
                    $data === strtoupper($data) ||
                    preg_match('/[0-9]+/', $data) !== 1 ||
                    preg_match('/[!?*:_\-#"\'(){}\[\]]+/', $data) !== 1 ||
                    strlen($data) < 8
                ) {
                    /**
                     * Trifft eines der Kriterien nicht zu, setzen wir einen Fehler vom Typ 7.
                     */
                    $this->setError(7);
                }
            }
        }
    }

    /**
     * Die setError Funktion generiert aus $this->currentName eine menschenlesbarae Fehlermeldung. Es werden die Typen
     * 5 und 6 angeboten, die in der Validator Klasse nicht verwendet werden. Wir können Sie aber bspw. im Rahmen der
     * Registrierung verwenden um Fehler zu generieren.
     *
     * @param      $error
     * @param null $opt
     */
    public function setError ($error, $opt = null)
    {
        /**
         * In einem Switch Statement wird der Wert in den Runden Klammern mit den Werten der einzelnen Cases verglichen.
         * Wichtig ist, dass jeder Case mit einem break endet, da sonst der nächste Case auch noch ausgeführt werden
         * würde. Man könnte den Switch Block auch als langen if/elseif/else Block notieren.
         */
        switch ($error) {
            case 0:
                array_push($this->errors, "{$this->currentName} ist ein Pflichtfeld!");
                break;
            case 1:
                array_push($this->errors, "{$this->currentName} hat zu wenig Zeichen! Mindestens $opt!");
                break;
            case 2:
                array_push($this->errors, "{$this->currentName} hat zu viele Zeichen! Maximal $opt!");
                break;
            case 3:
                array_push($this->errors, "{$this->currentName} ist falsch!");
                break;
            case 4:
                array_push($this->errors, "{$opt[0]} und {$opt[1]} sind nicht gleich!");
                break;
            case 5:
                array_push($this->errors, "Username ist bereits vergeben.");
                break;
            case 6:
                array_push($this->errors, "E-Mailadresse ist bereits vergeben.");
                break;
            case 7:
                array_push($this->errors, "Ein Passwort muss Groß- und Kleinbuchstaben, Ziffern und Sonderzeichen enthalten und mind. 8 Zeichen lang sein.");
            default:
                array_push($this->errors, "{$this->currentName} ist nicht valide!");
        }

    }

    /**
     * Bietet die Möglichkeit an der Stelle, an der der Validator verwendet wird, die entstandenen Fehler zu erhalten
     * und weiterverarbeiten zu können.
     *
     * @return array
     */
    public function getErrors ()
    {
        /**
         * Das folgende Konstrukt ist ein Ternärer Operator (engl. ternary operator) und meint:
         *
         * <bedingung> ? <dann> : <sonst>
         *
         * In diesem Fall also: Wenn count von $this->error größer ist als 0, dann gib $this->error zurück, sonst false.
         */
        return (count($this->errors) > 0) ? $this->errors : [];
    }
}
