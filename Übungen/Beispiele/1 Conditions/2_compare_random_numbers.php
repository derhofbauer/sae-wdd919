<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

/**
 * Zufällige ganze Zahlen zwischen [0 und 3]
 */
$randomNumberA = rand(0, 3);
echo "<p>$randomNumberA</p>";
$randomNumberB = rand(0, 3);
echo "<p>$randomNumberB</p>";

/**
 * Aufgabe:
 * [ ] Vergleiche die beiden Zaheln und gib mit echo aus: "A ist größer als B" oder "A ist kleiner als B" oder "A ist gleich B"
 */
if ($randomNumberA > $randomNumberB) {
    echo "A ist größer als B";
} elseif ($randomNumberA < $randomNumberB) {
    echo "A ist kleiner als B";
} else {
    echo "A ist gleich B";
}

?>

</body>
</html>
