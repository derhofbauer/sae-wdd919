<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

/**
 * Zufällige ganze Zahl zwischen [0 und 10]
 */
$randomNumber = rand(0, 10);
echo "<p>$randomNumber</p>";

/**
 * Aufgabe:
 * [ ] Gibt mit echo aus: "Die Zahl ist größer als 5" oder "die Zahl ist kleiner oder gleich 5"
 */
if ($randomNumber > 5) {
    echo "Die Zahl ist größer als 5";
} else {
    echo "die Zahl ist kleiner oder gleich 5";
}

?>

</body>
</html>
