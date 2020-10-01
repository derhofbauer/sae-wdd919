<?php

/**
 * Datenbankverbindung einbinden
 */
require_once 'dbconnect.php';

/**
 * ACHTUNG:
 *
 * Die Daten müssten validiert werden, bevor sie gespeichert werden. Aus Gründen der Übersichtlichkeit verzichte ich
 * hier auf eine Validierung.
 */

/**
 * Aliases anlegen, damit wir nicht mit der $_POST-Superglobal arbeiten müssen überall - hat nur Bequemlichkeitsgründe.
 */
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];

/**
 * Existiert die E-Mail Adresse schon in der DB?
 *
 * Abfragen aller User*innen IDs mit der eingegebenen E-Mail Adresse.
 */
$userResult = mysqli_query($link, "SELECT id FROM users WHERE email = '$email'");

/**
 * Hat das Ergebnis mehr als 0 Zeilen, so gibt es Datensätze mit der eingegebenen E-Mail Adresse schon und wir brauchen
 * den Datensatz nicht erneut anlegen.
 */
if (mysqli_num_rows($userResult) > 0) {
    /**
     * Existiert der User schon, wandeln wir das Datenbankergebnis in ein assoziatives Array um. Hier können wir direkt
     * mysqli_fetch_assoc() verwenden, weil wir nur eine Zeile im Ergebnis haben.
     */
    $userData = mysqli_fetch_assoc($userResult);
    /**
     * Alias anlegen
     */
    $userId = $userData['id'];
} else {
    /**
     * Existiert kein*e User*in mit der eingegebenen E-Mail Adresse, legen wir einen Datensatz an
     */
    $result = mysqli_query($link, "INSERT INTO users (firstname, lastname, email) VALUES ('$firstname', '$lastname', '$email')");
    /**
     * mysqli_insert_id() gibt die zuletzt automatisch generierte ID für die Verbindung $link zurück.
     */
    $userId = mysqli_insert_id($link);
}

/**
 * Typecasting
 */
$userId = (int)$userId;

/**
 * Alias anlegen
 */
$topics = $_POST['topics'];

/**
 * Alle Topic-Checkboxen durchgehen
 *
 * $topicId ist der Array-Key innerhalb der $_POST Superglobal und $on beinhaltet lediglich den String "on", der
 * übergeben wird, wenn eine Checkbox angehakerlt wurde. $on brauchen wir nicht, wir brauchen nur $topicId.
 */
foreach ($topics as $topicId => $on) {
    /**
     * Zählen, ob der/die User*in schon ein Abo zu diesem Topic hat
     */
    $aboResult = mysqli_query($link, "SELECT COUNT(*) AS count FROM abos WHERE user_id = '$userId' AND topic_id = '$topicId'");

    /**
     * Ergebnis in ein assoziatives Array umwandeln
     */
    $aboData = mysqli_fetch_assoc($aboResult);

    /**
     * Existiert noch kein Abo, legen wir es an
     */
    if ($aboData['count'] <= 0) {
        // Abo existiert noch nicht
        $result = mysqli_query($link, "INSERT INTO abos (topic_id, user_id) VALUES ($topicId, $userId)");
    }
}

/**
 * Redirect auf index.php - an dieser Stelle setzen wir auch den success GET-Parameter auf die E-Mail, die gerade
 * verarbeitet wurde. Wir verwenden urlencode(), damit alle Zeichen, die Probleme machen könnten in URLs, escaped
 * werden.
 */
header("Location: index.php?success=" . urlencode($email));
