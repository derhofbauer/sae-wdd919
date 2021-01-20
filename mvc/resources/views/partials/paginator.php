<?php
/**
 * Grundsätzlich sollen im Template keine Berechnung mehr durchgeführt werden. Hier ist das aber meiner Meinung nach
 * zulässig, weil es sich um Berechnungen zur Darstellung handelt und die Darstellung Aufgabe der Views ist.
 */

/**
 * Zunächst prüfen wir, ob bereits GET Parameter in der URL stehen oder nicht. Nachdem die GET Parameter immer mit
 * einem ? vom Pfad getrennt sind, können wir darauf prüfen.
 */
$hasParams = (strpos($currentUrl, '?') !== false);
/**
 * Standardmäßig wollen wir auf Seite 1 und die Seite links daneben ist somit auch 1, weil es keine Seite 0 gibt, und
 * die Seite rechts daneben ist 2.
 */
$prevPage = 1;
$nextPage = 2;
/**
 * Ist der GET Paramater page gesetzt, so berechnen wir $prevPage und $nextPage anhand dieses Wertes.
 */
if (isset($_GET['page'])) {
    $prevPage = max($_GET['page'] - 1, 1);
    $nextPage = min($_GET['page'] + 1, $numberOfPages);
}

/**
 * Nun stellen wir uns die berechneten page GET Parameter für die Previous und Next Buttons zusammen.
 */
$prevParam = "?page=$prevPage";
$nextParam = "?page=$nextPage";
/**
 * Wenn es bereits Parameter gibt, dann brauchen wir das & als Trenner und nicht das ?.
 */
if ($hasParams === true) {
    $prevParam = "&page=$prevPage";
    $nextParam = "&page=$nextPage";
}
?>
<div class="paginator row">
    <div class="btn-group">
        <a href="<?php echo $currentUrl . $prevParam ?>" class="btn btn-outline-secondary">&lt;</a>

        <?php for ($i = 1; $i <= $numberOfPages; $i++): ?>
            <?php
            /**
             * In der Schleife berechnen wir für jeden Button den zugehörigen page GET Paramater. Auch hier hängt es
             * wieder davon ab, ob wir bereits GET Parameter in der URL stehen oder nicht - der Trenner ändert sich.
             */
            $pageParam = "?page=$i";
            if ($hasParams === true) {
                $pageParam = "&page=$i";
            }

            /**
             * Die Button Klasse setzen, be nachdem ob der aktuell gerenderte Button grade ausgewählt sein soll oder
             * nicht.
             *
             * Wenn der page GET Parameter gesetzt ist und mit dem aktuellen Wert der Zählervariable $i übereinstimmt,
             * so soll der Button aktiv sein. Der Button soll aber auch dann aktiv sein, wenn der page GET Paramater
             * nicht gesetzt ist und wir uns im 1. Schleifendurchlauf befinden.
             */
            $buttonClass = 'btn-outline-secondary';
            if (
                (isset($_GET['page']) && (int)$_GET['page'] === $i)
                ||
                (!isset($_GET['page']) && $i === 1)
            ) {
                $buttonClass = 'btn-secondary';
            }
            ?>
            <a class="btn <?php echo $buttonClass; ?>" href="<?php echo $currentUrl . $pageParam; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <a href="<?php echo $currentUrl . $nextParam ?>" class="btn btn-outline-secondary">&gt;</a>

    </div>
</div>
