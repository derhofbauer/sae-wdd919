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
 * [ ] Wenn beide Zahlen größer sind als 0, gib mit echo "Beide Zahlen sind positiv aus"
 */
if ($randomNumberA > 0 && $randomNumberB > 0) {
    echo "Beide Zahlen sind positiv aus";
}

?>

</body>
</html>
