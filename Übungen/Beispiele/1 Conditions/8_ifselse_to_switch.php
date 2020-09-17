<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

/**
 * Zufällige ganze Zahl zwischen [2 und 5]
 */
$randomNumber = rand(2, 5);
echo "<p>$randomNumber</p>";

/**
 * Aufgabe:
 * [ ] Schreib folgenden Code in EIN switch-Statement um
 */
switch ($randomNumber) {
    case 2:
        echo "<p>Die Zahl ist eine ZWEI</p>";
        break;
    case 3:
        echo "<p>Die Zahl ist eine DREI</p>";
        break;
    case 4:
        echo "<p>Die Zahl ist eine VIER</p>";
        break;
    case 5:
        echo "<p>Die Zahl ist eine FÜNF</p>";
        break;
}

?>

</body>
</html>
