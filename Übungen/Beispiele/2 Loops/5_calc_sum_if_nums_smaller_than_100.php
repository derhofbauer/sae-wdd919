<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php
/**
 * Aufgabe:
 * [ ] Summiere alle positiven ganzen Zahlen kleiner 100 auf und gib das Ergebnis mit echo aus. (4950)
 */
$sum = 0;
for ($i = 1; $i < 100; $i++) {
    $sum = $sum + $i;
}
echo $sum;

?>

</body>
</html>
