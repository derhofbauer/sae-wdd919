<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php
/**
 * Aufgabe:
 * [ ] Schreibe zwei verschachtelte for-Schleifen die gemeinsam folgende Zahlen ausgeben:
 *      1.1 1.2 1.3 2.1 2.2 2.3 3.1 3.2 3.3 4.1 4.2 4.3
 */
for ($i = 1; $i <= 4; $i++) {
    for ($j = 1; $j <= 3; $j++) {
        echo "$i.$j ";
    }
}
?>
<br>
<?php
/**
 * Optionale Aufgabe:
 * [ ] Versuche das gleiche Ergebnis mit nur einer for-Schleife zu erreichen.
 */
for ($i = 11; $i < 44; $i++) {
    echo $i/10 . ' ';

    if ($i % 10 === 3) {
        $i += 7;
    }
}

?>

</body>
</html>
