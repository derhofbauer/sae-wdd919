<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php
/**
 * Aufgabe:
 * [ ] Schreibe eine Schleife die zufällige Zahlen zwischen [0 und 10] erzeugt und ausgibt so lange bis eine 7 kommt
 */
do {
   $randomNumber = rand(0, 10);
   echo "<pre>$randomNumber</pre>";
} while ($randomNumber != 7);
?>

</body>
</html>
