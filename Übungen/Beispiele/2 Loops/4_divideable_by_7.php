<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php
/**
 * Aufgabe:
 * [ ] Gib mit einer Schleife und mit echo alle positiven ganzen Zahlen kleiner 100 aus, die sich ohne Rest durch 7 teilen lassen
 */
for ($i = 1; $i < 100; $i++) {
    if ($i % 7 === 0) {
        echo "$i ";
    }
}

?>

</body>
</html>
