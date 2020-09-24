<?php

$errors = [];

if (strlen($_POST['name']) < 2) {
    $errors['name'] = "Der Name muss mindestens 2 Zeichen lang sein";
}


if (strpos($_POST['email'], '@') === false) {
    $errors['email'] = "Die E-Mail Adresse muss ein @ beinhalten";
}
// ODER:
if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
    $errors['email'] = "Bitte geben Sie eine valide E-Mail Adresse ein";
}


if (strpos($_POST['email'], '.') === false) {
    $errors['email'] = "Die E-Mail Adresse muss einen . beinhalten";
}
$genders = ['m', 'f', 'nb'];
if (isset($_POST['gender']) !== true || in_array($_POST['gender'], $genders) === false) {
    $errors['gender'] = "Bitte ein Geschlecht aus der Liste auswählen";
}
if (strlen($_POST['message']) < 10) {
    $errors['message'] = "Bitte geben Sie eine odentliche Nachricht ein, ich mein, was soll das?!";
}


if (is_numeric($_POST['phone']) !== true) {
    $errors['phone'] = "Bitte geben Sie eine korrekte Telefonnummer ein";
}
// ODER:
if (preg_match('/^\+?[0-9 ]*$/', $_POST['phone']) !== 1) {
    $errors['phone'] = "Bitte geben Sie eine korrekte Telefonnummer ein";
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
        $errors['phone'] = "Bitte geben Sie eine korrekte Telefonnummer ein";
    }
}

if (isset($_POST['newsletter']) && $_POST['newsletter'] === 'on') {
    echo "<p>Newsletter Anmeldung erfolgreich! :D</p>";
}

if (!empty($errors)) {
    require_once __DIR__ . '/../content/contact.php';
} else {
    require_once __DIR__ . '/../content/thank-you.php';
}
