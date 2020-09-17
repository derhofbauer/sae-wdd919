<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

/**
 * Zufällige ganze Zahlen zwischen [-5 und 5]
 */
$randomNumberA = rand(-5, 5);
echo "<p>$randomNumberA</p>";
$randomNumberB = rand(-5, 5);
echo "<p>$randomNumberB</p>";

/**
 * Aufgabe:
 * [x] Wenn zumindest eine der Zahlen größer sind als 0, gib mit echo "Eine der Zahlen ist positiv" aus
 * [x] Wenn beide Zahlen größer als 0 sind, gib "Beide Zahlen sind positiv" aus
 * [x] Ist keine Zahl größer als 0, so gib "Beide Zahlen sind kleiner oder gleich 0" aus
 */
if ($randomNumberA > 0 && $randomNumberB > 0) {
    echo "Beide Zahlen sind positiv";
} elseif ($randomNumberA <= 0 && $randomNumberB <= 0) {
    echo "Beide Zahlen sind kleiner oder gleich 0";
} else {
    echo "Eine der Zahlen ist positiv";
}

?>

</body>
</html>
