<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php
/**
 * Aufgabe:
 * [ ] Schreibe die for-Schleife um in eine do-while-Schleife
 */
for ($i = -10; $i < 19; $i += 2) {
    echo "$i ";
}

echo "<br>";

$i = -10;
do {
    echo "$i ";
    $i += 2;
} while ($i < 19)

?>

</body>
</html>
