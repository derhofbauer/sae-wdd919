<?php

$links = [
    'https://google.com',
    'https://sae.edu',
    'https://nasa.gov',
    'https://9gag.com'
];

?>

<main>
    <ul>
        <?php
        foreach ($links as $link) {
            $encodedUrl = urlencode($link);
            $hasBeenClicked = '';

            if (isset($_SESSION['visited']) && array_key_exists($link, $_SESSION['visited'])) {
                $clickCounter = $_SESSION['visited'][$link];
                $hasBeenClicked = " - ${clickCounter}x clicked";
            }

            echo "<li><a href=\"redirector.php?url=$encodedUrl\" target=\"_blank\">$link</a>$hasBeenClicked</li>";
        }
        ?>
    </ul>
</main>
