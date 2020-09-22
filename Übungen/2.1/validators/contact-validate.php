<?php

$hasErrorsSwitch = false;

if (strlen($_POST['name']) < 2) {
    echo "<p>Der Name muss mindestens 2 Zeichen lang sein</p>";
    $hasErrorsSwitch = true;
}


if (strpos($_POST['email'], '@') === false) {
    echo "<p>Die E-Mail Adresse muss ein @ beinhalten</p>";
    $hasErrorsSwitch = true;
}
// ODER:
if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
    echo "<p>Bitte geben Sie eine valide E-Mail Adresse ein</p>";
    $hasErrorsSwitch = true;
}


if (strpos($_POST['email'], '.') === false) {
    echo "<p>Die E-Mail Adresse muss einen . beinhalten</p>";
    $hasErrorsSwitch = true;
}
$genders = ['m', 'f', 'nb'];
if (isset($_POST['gender']) !== true || in_array($_POST['gender'], $genders) === false) {
    echo "<p>Bitte ein Geschlecht aus der Liste auswählen</p>";
    $hasErrorsSwitch = true;
}
if (strlen($_POST['message']) < 10) {
    echo "<p>Bitte geben Sie eine odentliche Nachricht ein, ich mein, was soll das?!</p>";
    $hasErrorsSwitch = true;
}


if (is_numeric($_POST['phone']) !== true) {
    echo "<p>Bitte geben Sie eine korrekte Telefonnummer ein</p>";
    $hasErrorsSwitch = true;
}
// ODER:
if (preg_match('/^\+?[0-9 ]*$/', $_POST['phone']) !== 1) {
    echo "<p>Bitte geben Sie eine korrekte Telefonnummer ein</p>";
    $hasErrorsSwitch = true;
}
// ODER:
if (isset($_POST['phone'])) {
    $allowedChars = ['+', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ' '];
    $phoneAsArray = str_split($_POST['phone']);
    $hasWrongCharSwitch = false;

    foreach ($phoneAsArray as $character) {
        if (!in_array($character, $allowedChars)) {
            $hasWrongCharSwitch = true;
            break;
        }
    }

    if ($hasWrongCharSwitch === true) {
        echo "<p>Bitte geben Sie eine korrekte Telefonnummer ein</p>";
        $hasErrorsSwitch = true;
    }
}

if (isset($_POST['newsletter']) && $_POST['newsletter'] === 'on') {
    echo "<p>Newsletter Anmeldung erfolgreich! :D</p>";
}

if ($hasErrorsSwitch === true) {
    require_once __DIR__ . '/../content/contact.php';
} else {
    require_once __DIR__ . '/../content/thank-you.php';
}
