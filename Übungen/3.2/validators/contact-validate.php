<?php
/**
 * Definieren eines Arrays, in den die Fehler, die später potentiell auftreten, hineingeschrieben werden können. Die
 * Fehler werden weiter unten nur mal in das Array geschrieben und noch nicht direkt ausgegeben.
 */
$errors = [];

/**
 * Wenn die Länge des Strings im POST-Parameter kleiner ist als 2, fügen wir einen Fehler in das zuvor definierte Array
 * mit dem Array-Key 'name' hinzu. 'name' deshalb, damit wir dann direkt unter dem Formularfeld auf $errors['name']
 * zugreifen und die Fehlermeldung spezifisch für dieses Feld ausgeben können.
 */
if (strlen($_POST['name']) < 2) {
    $errors['name'] = "Der Name muss mindestens 2 Zeichen lang sein";
}

/**
 * Wenn der Substring '@' im POST-Parameter nicht gefunden wird --> Fehler
 *
 * Die PHP Funktion strpos() sucht einen String innerhalb eines anderen Strings.
 */
if (strpos($_POST['email'], '@') === false) {
    $errors['email'] = "Die E-Mail Adresse muss ein @ beinhalten";
}
/**
 * ODER:
 *
 * Die PHP Funktion filter_var() bietet die Möglichkeit, Variablen anhand eines gewissen vordefinierten Musters zu
 * überprüfen.
 */
if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
    $errors['email'] = "Bitte geben Sie eine valide E-Mail Adresse ein";
}

/**
 * Kommt kein Punkt in der email vor --> Fehler
 */
if (strpos($_POST['email'], '.') === false) {
    $errors['email'] = "Die E-Mail Adresse muss einen . beinhalten";
}

/**
 * Definition erlaubter Werte für den gender POST-Parameter.
 */
$genders = ['m', 'f', 'nb'];
/**
 * Wurde gender nicht gewählt ODER ist der Wert nicht einer der erlaubten werte aus $genders --> Fehler
 */
if (isset($_POST['gender']) !== true || in_array($_POST['gender'], $genders) === false) {
    $errors['gender'] = "Bitte ein Geschlecht aus der Liste auswählen";
}

/**
 * Ist die message kürzer als 10 Zeichen --> Fehler
 */
if (strlen($_POST['message']) < 10) {
    $errors['message'] = "Bitte geben Sie eine odentliche Nachricht ein, ich mein, was soll das?!";
}

/**
 * Ist die Telefonnummer nicht numerisch --> Fehler
 */
if (is_numeric($_POST['phone']) !== true) {
    $errors['phone'] = "Bitte geben Sie eine korrekte Telefonnummer ein";
}
/**
 * ODER:
 *
 * Trifft die Telefonnummer nicht auf die Regular Expression zu --> Fehler
 */
if (preg_match('/^\+?[0-9 ]*$/', $_POST['phone']) !== 1) {
    $errors['phone'] = "Bitte geben Sie eine korrekte Telefonnummer ein";
}
/**
 * ODER:
 */
if (isset($_POST['phone'])) {
    /**
     * Definition der erlaubten Zeichen einer Telefonnummer
     */
    $allowedChars = ['+', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ' '];
    /**
     * phone POST-Parameter in einen Array aufteilen, bei dem jedes Zeichen ein eigener Wert ist
     */
    $phoneAsArray = str_split($_POST['phone']);
    /**
     * "Schalter", ob ein Zeichen vorkommt, das nicht erlaubt ist
     */
    $hasWrongCharSwitch = false;

    /**
     * Alle Zeichen der eingegebenen Telefonnummer durchgehen
     */
    foreach ($phoneAsArray as $character) {
        /**
         * Ist das aktuell durchlaufene Zeichen NICHT im Array der erlaubten Zeichen?
         */
        if (!in_array($character, $allowedChars)) {
            /**
             * Schalter umlegen und Schleife unterbrechen, weil alle folgenden Zeichen nicht mehr geprüft werden müssen
             */
            $hasWrongCharSwitch = true;
            break;
        }
    }

    /**
     * Ist der Schalter true --> Fehler
     */
    if ($hasWrongCharSwitch === true) {
        $errors['phone'] = "Bitte geben Sie eine korrekte Telefonnummer ein";
    }
}

/**
 * Ist der agb POST-Parameter nicht gesetzt ODER nicht "on" und die Checkbox damit nicht angehakerlt --> Fehler
 */
if (!isset($_POST['agb']) || $_POST['agb'] !== 'on') {
    $errors['agb'] = "Sie müssen die AGB akzeptieren.";
}

/**
 * Ist der newsletter POST-Parameter gesetzt UND "on" und die Checkbox damit angehakerlt --> Erfolgsmeldung
 */
if (isset($_POST['newsletter']) && $_POST['newsletter'] === 'on') {
    echo "<p>Newsletter Anmeldung erfolgreich! :D</p>";
}

/**
 * Sind Fehler aufgetreten so geben wir das Formulat nochmal aus, andernfalls eine Erfolgsmeldung.
 */
if (!empty($errors)) {
    require_once __DIR__ . '/../content/contact.php';
} else {
    require_once __DIR__ . '/../content/thank-you.php';
}
