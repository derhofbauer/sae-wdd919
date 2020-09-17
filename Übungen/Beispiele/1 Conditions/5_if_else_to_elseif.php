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
 * [ ] Schreibe untenstehendes if/else Konstrukt in ein sauberes if/elseif/else Konstrukt mit der selben Funktionalität um
 */
if ($randomNumber > 5) {
    echo "<p>Die Zahl ist größer als 5</p>";
} elseif ($randomNumber > 3) {
    echo "<p>Die Zahl ist kleiner oder gleich 5, aber größer als 3</p>";
} else {
    echo "<p>Die Zahl ist kleiner oder gleich 3</p>";
}

if ($randomNumber <= 3) {
    echo "<p>Die Zahl ist kleiner oder gleich 3</p>";
} elseif ($randomNumber > 5) {
    echo "<p>Die Zahl ist größer als 5</p>";
} else {
    echo "<p>Die Zahl ist kleiner oder gleich 5, aber größer als 3</p>";
}


?>

</body>
</html>
