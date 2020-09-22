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
if (isset($_POST['newsletter']) && $_POST['newsletter'] === 'on') {
    echo "<p>Newsletter Anmeldung erfolgreich! :D</p>";
}

if ($hasErrorsSwitch === true) {
    require_once __DIR__ . '/../content/contact.php';
} else {
    require_once __DIR__ . '/../content/thank-you.php';
}
