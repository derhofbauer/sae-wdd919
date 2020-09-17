<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php
/**
 * Aufgabe:
 * [ ] Gib mit einer while-Schleife und mit echo die Zahlen 0-10 aus
 */
$i = 0;
$output = [];
while ($i < 11) {
    $output[] = $i;
    $i++;
}

echo implode(',', $output);
?>

</body>
</html>
