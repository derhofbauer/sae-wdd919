<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>

<?php

/**
 * Zufällige ganze Zahl zwischen [0 und 10]
 */
$randomNumber = rand(0, 10);
echo "<p>$randomNumber</p>";

/**
 * Aufgabe:
 * [ ] "Die Zahl ist durch 2 und durch 3 teilbar" oder
 * [ ] "Die Zahl ist nur durch 2 teilbar" oder
 * [ ] "Die Zahl ist nur durch 3 teilbar" oder
 * [ ] "Die Zahl ist weder durch 2 noch durch 3 teilbar"
 */
if ($randomNumber % 2 === 0 && $randomNumber % 3 === 0) {
    echo "Die Zahl ist durch 2 und durch 3 teilbar";
} elseif ($randomNumber % 2 === 0) {
    echo "Die Zahl ist nur durch 2 teilbar";
} elseif ($randomNumber % 3 === 0) {
    echo "Die Zahl ist nur durch 3 teilbar";
} else {
    echo "Die Zahl ist weder durch 2 noch durch 3 teilbar";
}

?>

</body>
</html>
