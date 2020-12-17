<?php
/**
 * Partial laden, dass die Errors aus der Session ausliest und hübsch rendert.
 */
require __DIR__ . '/../partials/errors.php';
?>

<div class="number-of-results">Sie sucher lieferte <?php echo count($results); ?> Treffer.</div>

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
