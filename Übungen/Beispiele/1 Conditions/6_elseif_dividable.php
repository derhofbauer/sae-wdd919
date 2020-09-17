<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

/**
 * Zufällige ganze Zahl zwischen [0 und 100]
 */
$randomNumber = rand(0, 100);
echo "<p>$randomNumber</p>";

/**
 * Aufgabe:
 * [ ] Ermittle mit einem if/else Konstrukt durch welche Zahlen zwischen 2 und 5 die zufällige Zahl ohne Rest teilbar
 *       ist
 */
if ($randomNumber % 4 === 0) {
    echo "Die Zahl ist durch 2, 4 teilbar";
} elseif ($randomNumber % 2 === 0) {
    echo "Die Zahl ist durch 2 teilbar";
}
if ($randomNumber % 3 === 0) {
    echo "Die Zahl ist durch 3 teilbar";
}
if ($randomNumber % 5 === 0) {
    echo "Die Zahl ist durch 5 teilbar";
}


?>

</body>
</html>
