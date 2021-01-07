<?php
/**
 * Partial laden, dass die Errors aus der Session ausliest und hübsch rendert.
 */
require __DIR__ . '/../partials/errors.php';
?>

<?php
/**
 * Hier geht es darum, wie man Daten von PHP ans JavaScript übergeben kann.
 *
 * Eine Möglichkeit ist, die Werte ein data-Attribut zu schreiben. Dadurch sind sie nicht im globalen Scope von
 * JavaScript verfügbar, können aber abgerufen werden bei Bedarf. Dabei gilt zu bedenken, dass JSON nur doppelte
 * Anführungszeichen erlaubt und es damit zu Problemen im HTML kommt. Es bietet sich daher an, die Daten auch einem
 * bas64 Encoding zu unterziehen. Die Daten können in JavaScript dann ganz einfach von bas64 wieder zurück in JSON
 * entschlüsselt und weiterverarbeitet werden.
 *
 * s. https://de.wikipedia.org/wiki/Base64
 */
?>
<div class="number-of-results" data-searchresults="<?php echo base64_encode(json_encode($results)); ?>">Sie sucher lieferte <?php echo count($results); ?> Treffer.</div>

<div class="products row">
    <?php
    /**
     * [ ] Produktübersicht
     */

    foreach ($results as $product): ?>

        <div class="col-4 product">
            <?php
            /**
             * ibt es Bilder? Wenn ja, geben wir hier das erste davon als Produktbild aus.
             */
            ?>
            <?php if (count($product->getImages()) > 0): ?>
                <img src="<?php echo $product->getImages()[0]; ?>" alt="<?php echo $product->name ?>" class="img-thumbnail">
            <?php endif; ?>
            <h2><?php echo $product->name; ?></h2>
            <div><?php echo $product->description; ?></div>
            <a href="products/<?php echo $product->id; ?>">more ...</a>
        </div>

    <?php endforeach; ?>

</div>

<?php
/**
 * Hier wird eine weitere Möglichkeit dargestellt, wie Daten von PHP ins JavaScript übergeben werden können.
 *
 * Diese Methode ist einfacher und für kleine Projekte durchaus praktikabel. Hier wird einfach eine globale Konstante
 * erstellt und von PHP mit einer JSON Repräsentation der Daten befüllt. In JavaScript kann die Variable dann direkt
 * angesprochen werden.
 */
?>
<script>
    const _searchresults = <?php echo json_encode($results); ?>
</script>
